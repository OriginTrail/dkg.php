<?php

namespace Dkg\Services\BlockchainService\Proxy;

use Dkg\Exceptions\BlockchainException;
use Dkg\Services\BlockchainService\Proxy\Dto\BlockchainInfo;
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
    private const ASSERTION_REGISTRY = 'AssertionRegistry';
    private const ASSET_REGISTRY = 'AssetRegistry';
    private const ERC20_TOKEN = 'ERC20Token';
    private const HUB = 'Hub';
    private const SHARDING_TABLE = 'ShardingTable';
    private const UAI_REGISTRY = 'UaiRegistry';

    /** @var Contract */
    private $hubContract;
    /** @var Contract */
    private $assetRegistryContract;
    /** @var Contract */
    private $assertionRegistryContract;
    /** @var Contract */
    private $uaiRegistryContract;
    /** @var Contract */
    private $tokenContract;

    /** @var BlockchainInfo */
    private $blockchainInfo;

    /** @var Web3 */
    private $web3;

    private function __construct(BlockchainInfo $blockchainInfo)
    {
        $this->blockchainInfo = $blockchainInfo;
        $this->web3 = new Web3(new HttpProvider(new HttpRequestManager($blockchainInfo->getRpc())));
        $this->initContracts($blockchainInfo->getHubContract());
    }

    public static function init(BlockchainInfo $blockchainInfo): Web3ProxyInterface
    {
        return new Web3Proxy($blockchainInfo);
    }


    /**
     * @throws BlockchainException
     */
    public function increaseAllowance(float $amount, string $publicKey, string $privateKey)
    {
        $this->executeContractFunction(
            $this->tokenContract,
            $privateKey,
            'increaseAllowance',
            $amount,
            $publicKey,
            $privateKey
        );
    }

    private function initContracts($hubContract)
    {
        $this->hubContract = new Contract($this->web3->getProvider(), $this->loadAbi(self::HUB));
        $this->hubContract->at($hubContract);

        $assetRegistryAddress = $this->getContractAddress('AssetRegistry');
        $this->assetRegistryContract = new Contract($this->web3->getProvider(), $this->loadAbi(self::ASSET_REGISTRY));
        $this->assetRegistryContract->at($assetRegistryAddress);

        $assertionRegistryAddress = $this->getContractAddress('AssertionRegistry');
        $this->assertionRegistryContract = new Contract($this->web3->getProvider(), $this->loadAbi(self::ASSERTION_REGISTRY));
        $this->assertionRegistryContract->at($assertionRegistryAddress);

        $uaiRegistryAddress = $this->getContractAddress('UAIRegistry');
        $this->uaiRegistryContract = new Contract($this->web3->getProvider(), $this->loadAbi(self::UAI_REGISTRY));
        $this->uaiRegistryContract->at($uaiRegistryAddress);

        $tokenAddress = $this->getContractAddress('Token');
        $this->tokenContract = new Contract($this->web3->getProvider(), $this->loadAbi(self::ERC20_TOKEN));
        $this->tokenContract->at($tokenAddress);
    }

    private function getContractAddress(string $contractName): string
    {
        $response = $this->callContractFunction($this->hubContract, 'getContractAddress', $contractName);
        $fieldName = 'selectedContractAddress';
        return $response->$fieldName;
    }

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
     * @throws BlockchainException
     */
    private function executeContractFunction(Contract $contract, ...$args): object
    {
        $privateKey = array_pop($args);
        $from = array_pop($args);
        $to = $contract->getToAddress();
        $chainId = $this->blockchainInfo->getChainId();
        $gasPrice = $this->getGasPrice($this->tokenContract);
        $rawTransactionData = '0x' . $this->tokenContract->getData(...$args);

        $txParams = [
            'from' => $from,
            'to' => $to,
            'chainId' => $chainId,
            'gasPrice' => '0x' . dechex($gasPrice->toString()),
            'data' => $rawTransactionData,
        ];

        $gasLimit = $this->getGasLimit($contract, $txParams);
        $txParams['gasLimit'] = '0x' . dechex($gasLimit->toString());

        return $this->sendTransaction($contract, $txParams, $privateKey);
    }

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

    private function loadAbi($abiName)
    {
        return file_get_contents(__DIR__ . "/../abi/$abiName.json");
    }
}
