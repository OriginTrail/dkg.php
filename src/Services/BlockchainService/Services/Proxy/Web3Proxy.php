<?php

namespace Dkg\Services\BlockchainService\Services\Proxy;

use Dkg\Exceptions\BlockchainException;
use Dkg\Services\BlockchainService\Services\AbiManager\AbiManager;
use Dkg\Services\BlockchainService\Services\AbiManager\Dto\AbiEvent;
use Dkg\Services\BlockchainService\Services\Proxy\Dto\BlockchainInfo;
use Dkg\Services\Constants;
use phpseclib\Math\BigInteger;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;
use Web3\Utils;
use Web3\Web3;
use Web3p\EthereumTx\Transaction;

class Web3Proxy implements Web3ProxyInterface
{
    /** @var Contract */
    private $hubContract;
    /** @var Contract */
    private $serviceAgreementStorageContract;
    /** @var Contract */
    private $contentAssetContract;
    /** @var Contract */
    private $assertionRegistryContract;
    /** @var Contract */
    private $tokenContract;

    /** @var BlockchainInfo */
    private $blockchainInfo;

    /** @var Web3 */
    private $web3;

    /** @var AbiManager */
    private $abiManager;

    private function __construct(BlockchainInfo $blockchainInfo)
    {
        $this->blockchainInfo = $blockchainInfo;
        $this->web3 = new Web3(new HttpProvider(new HttpRequestManager($blockchainInfo->getRpc())));
        $this->abiManager = AbiManager::getInstance();
        $this->initContracts($blockchainInfo->getHubContract());
    }

    public static function init(BlockchainInfo $blockchainInfo): Web3ProxyInterface
    {
        return new Web3Proxy($blockchainInfo);
    }

    public function increaseAllowance(float $amount, string $publicKey, string $privateKey)
    {
        $this->executeContractFunction(
            $this->tokenContract,
            $publicKey,
            $privateKey,
            'increaseAllowance',
            $this->serviceAgreementStorageContract->getToAddress(),
            $amount,
        );
    }

    public function createAsset(array $args, string $publicKey, string $privateKey): array
    {
        $receipt = $this->executeContractFunction(
            $this->contentAssetContract,
            $publicKey,
            $privateKey,
            'createAsset',
            ...$args
        );

        $abiEvent = AbiManager::getInstance()->getAbiEvents(AbiManager::CONTENT_ASSET)['AssetCreated'];

        $decoded = $this->decodeLogs($receipt, $abiEvent, $this->contentAssetContract);

        if (count($decoded)) {
            throw new BlockchainException("No logs decoded. Tx hash: {$receipt->transactionHash}");
        }

        return $decoded;
    }

    /**
     * @param string $hubContractAddress
     * @return void
     */
    private function initContracts(string $hubContractAddress)
    {

        $this->hubContract = new Contract(
            $this->web3->getProvider(),
            $this->abiManager->getAbiStringified(AbiManager::HUB)
        );
        $this->hubContract->at($hubContractAddress);

        $serviceAgreementStorageContractAddress = $this->getContractAddress(AbiManager::SERVICE_AGREEMENT_STORAGE);
        $this->serviceAgreementStorageContract = new Contract(
            $this->web3->getProvider(),
            $this->abiManager->getAbiStringified(AbiManager::SERVICE_AGREEMENT_STORAGE)
        );
        $this->serviceAgreementStorageContract->at($serviceAgreementStorageContractAddress);

        $contentAssetContractAddress = $this->getContractAddress(AbiManager::CONTENT_ASSET, 'getAssetContractAddress');
        $this->contentAssetContract = new Contract(
            $this->web3->getProvider(),
            $this->abiManager->getAbiStringified(AbiManager::CONTENT_ASSET)
        );
        $this->contentAssetContract->at($contentAssetContractAddress);

        $assertionRegistryAddress = $this->getContractAddress(AbiManager::ASSERTION_REGISTRY);
        $this->assertionRegistryContract = new Contract(
            $this->web3->getProvider(),
            $this->abiManager->getAbiStringified(AbiManager::ASSERTION_REGISTRY)
        );
        $this->assertionRegistryContract->at($assertionRegistryAddress);

        $tokenAddress = $this->getContractAddress(AbiManager::ERC20_TOKEN);
        $this->tokenContract = new Contract(
            $this->web3->getProvider(),
            $this->abiManager->getAbiStringified(AbiManager::ERC20_TOKEN)
        );
        $this->tokenContract->at($tokenAddress);
    }

    private function getContractAddress(string $contractName, string $fn = 'getContractAddress'): string
    {
        $res = $this->callContractFunction($this->hubContract, $fn, $contractName);
        $res = (array)$res;
        return $res[0];
    }

    /**
     * @param Contract $contract
     * @param ...$arguments
     * @return object
     */
    private function callContractFunction(Contract $contract, ...$arguments): object
    {
        $cb = function ($err, $result) use (&$response) {
            if ($err) {
                throw new BlockchainException($err->getMessage());
            }

            $response = $result;

        };

        $response = null;
        $args = array_merge($arguments, [$cb]);

        $contract->call(...$args);

        return (object)$response;
    }

    /**
     * @param Contract $contract
     * @param string $publicKey
     * @param string $privateKey
     * @param ...$args
     * @return object
     * @throws BlockchainException
     */
    private function executeContractFunction(Contract $contract, string $publicKey, string $privateKey, ...$args): object
    {
        $from = $publicKey;
        $to = $contract->getToAddress();
        $chainId = $this->blockchainInfo->getChainId();
        $gasPrice = $this->getGasPrice($contract);
        $rawTransactionData = '0x' . $contract->getData(...$args);
        $transactionCount = $this->getTransactionCount($contract, $publicKey);

        $txParams = [
            'from' => $from,
            'to' => $to,
            'chainId' => $chainId,
            'gasPrice' => '0x' . dechex((int)$gasPrice->toString() * 10),
            'data' => $rawTransactionData,
            'nonce' => "0x" . dechex($transactionCount->toString())
        ];

        $gasLimit = $this->getGasLimit($contract, $txParams);
        $txParams['gasLimit'] = '0x' . dechex((int)$gasLimit->toString() * 10);

        return $this->sendTransaction($contract, $txParams, $privateKey);
    }

    /**
     * @param Contract $contract
     * @return BigInteger
     * @throws BlockchainException
     */
    private function getGasPrice(Contract $contract): BigInteger
    {
        $gasPrice = null;

        if ($this->blockchainInfo->getName() === 'otp') {
            $contract->getEth()->gasPrice(function ($err, $result) use (&$gasPrice) {
                if ($err) {
                    throw new BlockchainException($err->getMessage());
                }
                $gasPrice = $result;
            });

            $gasPrice = new BigInteger((int)$gasPrice->toString() * 1000000);
        } else {
            $gasPrice = Utils::toWei("100", "Gwei");
        }

        return $gasPrice;
    }

    /**
     * @param Contract $contract
     * @param string $ownerAccount
     * @return BigInteger
     * @throws BlockchainException
     */
    private function getTransactionCount(Contract $contract, string $ownerAccount): BigInteger
    {
        $transactionCount = null;

        $contract->getEth()->getTransactionCount($ownerAccount, function ($err, $transactionCountResult) use (&$transactionCount) {
            if ($err) {
                throw new BlockchainException($err->getMessage());
            }
            $transactionCount = $transactionCountResult;
        });

        return $transactionCount;
    }

    /**
     * @param Contract $contract
     * @param $txParams
     * @return BigInteger
     * @throws BlockchainException
     */
    private function getGasLimit(Contract $contract, $txParams): BigInteger
    {
        $estimatedGas = null;

        $contract->getEth()->estimateGas($txParams, function ($err, $gas) use (&$estimatedGas) {
            if ($err) {
                throw new BlockchainException($err->getMessage());
            }
            $estimatedGas = $gas;
        });

        if (!$estimatedGas->toString()) {
            return Utils::toWei("1000", "Kwei");
        }

        return $estimatedGas;
    }

    /**
     * @param Contract $contract
     * @param array $txParams
     * @param string $privateKey
     * @return object
     * @throws BlockchainException
     */
    private function sendTransaction(Contract $contract, array $txParams, string $privateKey): object
    {
        $tx = new Transaction($txParams);
        $signedTx = '0x' . $tx->sign($privateKey);

        $txHash = null;

        $contract->getEth()->sendRawTransaction($signedTx, function ($err, $txResult) use (&$txHash) {
            if ($err) {
                throw new BlockchainException($err->getMessage());
            }
            $txHash = $txResult;
        });

        return $this->pollForTxReceipt($contract, $txHash);
    }

    /**
     * @param Contract $contract
     * @param string $txHash
     * @return object
     * @throws BlockchainException
     */
    public function pollForTxReceipt(Contract $contract, string $txHash): object
    {
        for ($i = 0; $i <= Constants::BLOCKCHAIN_DEFAULT_TIMEOUT_TIME_IN_SEC; $i++) {
            $contract->getEth()
                ->getTransactionReceipt($txHash, function ($err, $txReceiptResult) use (&$txReceipt) {
                    if ($err) {
                        throw new BlockchainException($err->getMessage());
                    }
                    $txReceipt = $txReceiptResult;
                });

            if ($txReceipt) {
                break;
            }

            sleep(1);
        }
        return $txReceipt;
    }

    /**
     * @param object $receipt
     * @param AbiEvent $event
     * @param Contract $contract
     * @return array
     */
    private function decodeLogs(object $receipt, AbiEvent $event, Contract $contract): array
    {
        $decoded = [];

        if (!count($receipt->logs)) {
            return [];
        }

        foreach ($receipt->logs as $log) {
            $topics = $log->topics;
            $mainTopicHash = $topics[0];

            if ($mainTopicHash === $event->getHash()) {
                array_shift($topics);
                $inputTypes = $event->getInputTypes();

                foreach ($topics as $i => $t) {
                    $decoded[] = $contract
                        ->getEthabi()
                        ->decodeParameter($inputTypes[$i], $t);
                }
            }
        }

        return $decoded;
    }
}
