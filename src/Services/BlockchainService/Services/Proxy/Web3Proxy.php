<?php

namespace Dkg\Services\BlockchainService\Services\Proxy;

use Dkg\Exceptions\BlockchainException;
use Dkg\Services\BlockchainService\Dto\BlockchainConfig;
use Dkg\Services\BlockchainService\Services\AbiManager\AbiManager;
use Dkg\Services\BlockchainService\Services\AbiManager\Dto\AbiEvent;
use Dkg\Services\BlockchainService\Services\Proxy\Dto\BlockchainInfo;
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

    public function increaseAllowance(float $amount, BlockchainConfig $config)
    {
        // bid amount is passed in ether, blockchain expects wei
        $amount = $this->toWei($amount, 'ether');
        $this->executeContractFunction(
            $this->tokenContract,
            $config,
            'increaseAllowance',
            $this->serviceAgreementStorageContract->getToAddress(),
            $amount,
        );
    }

    public function createAsset(array $args, BlockchainConfig $config): array
    {
        $bidIndex = count($args) - 1;
        // bidAmount is passed in ether, blockchain expects wei
        $args[$bidIndex] = $this->toWei($args[$bidIndex], 'ether');

        $receipt = $this->executeContractFunction(
            $this->contentAssetContract,
            $config,
            'createAsset',
            ...$args
        );

        $abiEvent = AbiManager::getInstance()->getAbiEvents(AbiManager::CONTENT_ASSET)['AssetCreated'];

        $decoded = $this->decodeLogs($receipt, $abiEvent, $this->contentAssetContract);

        if (!count($decoded)) {
            throw new BlockchainException("No logs decoded. Tx hash: {$receipt->transactionHash}");
        }

        return $decoded;
    }

    public function getLatestAssertionId(int $tokenId): string
    {
        $response = $this->callContractFunction($this->contentAssetContract, 'getAssertionsLength', $tokenId);
        $length = $response[0];
        $length = (int)$length->toString();

        [$assertion] = $this->callContractFunction($this->contentAssetContract, 'getAssertionByIndex', $tokenId, $length - 1);

        return $assertion;
    }

    /**
     * @throws BlockchainException
     */
    public function updateAsset(array $args, BlockchainConfig $config)
    {
        $bidIndex = count($args) - 1;
        // bidAmount is passed in ether, blockchain expects wei
        $args[$bidIndex] = $this->toWei($args[$bidIndex], 'ether');

        $this->executeContractFunction(
            $this->contentAssetContract,
            $config,
            'updateAsset',
            ...$args
        );

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
        return $res[0];
    }

    /**
     * @param Contract $contract
     * @param ...$arguments
     * @return array
     */
    private function callContractFunction(Contract $contract, ...$arguments): array
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

        return $response;
    }

    /**
     * @param Contract $contract
     * @param BlockchainConfig $config
     * @param ...$args
     * @return object
     * @throws BlockchainException
     */
    private function executeContractFunction(Contract $contract, BlockchainConfig $config, ...$args): object
    {
        $from = $config->getPublicKey();
        $to = $contract->getToAddress();
        $chainId = $this->blockchainInfo->getChainId();
        $gasPrice = $this->getGasPrice($contract);
        $rawTransactionData = '0x' . $contract->getData(...$args);
        $transactionCount = $this->getTransactionCount($contract, $config->getPublicKey());

        $txParams = [
            'from' => $from,
            'to' => $to,
            'chainId' => $chainId,
            'gasPrice' => '0x' . dechex($gasPrice),
            'data' => $rawTransactionData,
            'nonce' => "0x" . dechex($transactionCount)
        ];

        $gasLimit = $this->getGasLimit($contract, $txParams);
        $txParams['gasLimit'] = '0x' . dechex($gasLimit);

        return $this->sendTransaction($contract, $txParams, $config);
    }

    /**
     * @param Contract $contract
     * @return int
     * @throws BlockchainException
     */
    private function getGasPrice(Contract $contract): int
    {
        $gasPrice = null;

        if ($this->blockchainInfo->getName() === 'otp') {
            $contract->getEth()->gasPrice(function ($err, $result) use (&$gasPrice) {
                if ($err) {
                    throw new BlockchainException($err->getMessage());
                }
                $gasPrice = $result;
            });

            $gasPrice = new BigInteger((int)$gasPrice->toString());
            $gasPrice = (int)$gasPrice->toString();
        } else {
            $gasPrice = $this->toWei("100", "Gwei");
        }

        return $gasPrice;
    }

    /**
     * @param Contract $contract
     * @param string $ownerAccount
     * @return int
     * @throws BlockchainException
     */
    private function getTransactionCount(Contract $contract, string $ownerAccount): int
    {
        $transactionCount = null;

        $contract->getEth()->getTransactionCount($ownerAccount, function ($err, $transactionCountResult) use (&$transactionCount) {
            if ($err) {
                throw new BlockchainException($err->getMessage());
            }
            $transactionCount = $transactionCountResult;
        });

        return (int)$transactionCount->toString();
    }

    /**
     * @param Contract $contract
     * @param $txParams
     * @return int
     * @throws BlockchainException
     */
    private function getGasLimit(Contract $contract, $txParams): int
    {
        $contract->getEth()->estimateGas($txParams, function ($err, $gas) use (&$estimatedGas) {
            if ($err) {
                throw new BlockchainException($err->getMessage());
            }
            $estimatedGas = $gas;
        });

        $estimatedGas = (int)$estimatedGas->toString();

        if (!$estimatedGas) {
            return $this->toWei(1000, 'kwei');
        }

        return $estimatedGas;
    }

    /**
     * @param Contract $contract
     * @param array $txParams
     * @param BlockchainConfig $config
     * @return object
     * @throws BlockchainException
     */
    private function sendTransaction(Contract $contract, array $txParams, BlockchainConfig $config): object
    {
        $tx = new Transaction($txParams);
        $signedTx = '0x' . $tx->sign($config->getPrivateKey());

        $txHash = null;

        $contract->getEth()->sendRawTransaction($signedTx, function ($err, $txResult) use (&$txHash) {
            if ($err) {
                throw new BlockchainException($err->getMessage());
            }
            $txHash = $txResult;
        });

        return $this->pollForTxReceipt($contract, $txHash, $config);
    }

    /**
     * @param Contract $contract
     * @param string $txHash
     * @param BlockchainConfig $config
     * @return object
     * @throws BlockchainException
     */
    public function pollForTxReceipt(Contract $contract, string $txHash, BlockchainConfig $config): object
    {
        $txReceipt = null;

        for ($i = 0; $i <= $config->getNumOfRetries(); $i++) {
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

            // usleep accept microseconds while pollFrequency is in milliseconds
            usleep($config->getPollFrequency() * 1000);
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

    /**
     * @param $amount mixed amount of tokens in ether
     * @param string $unit
     * @return int
     */
    private function toWei($amount, string $unit): int
    {
        return (int)Utils::toWei((string)$amount, $unit)->toString();
    }
}
