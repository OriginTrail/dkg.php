<?php

namespace Dkg\Config;

class Constants
{
    public const PUBLISH_DEFAULT_EPOCH_NUM = 5;
    public const PUBLISH_DEFAULT_TOKEN_AMOUNT = 15;
    public const PUBLISH_DEFAULT_HASH_FUNCTION_ID = 0;

    public const HTTP_DEFAULT_MAX_NUM_OF_RETRIES = 80;
    public const HTTP_DEFAULT_POLL_FREQUENCY_IN_MS = 750;

    public const BLOCKCHAIN_DEFAULT_NUM_OF_RETRIES = 60;
    public const BLOCKCHAIN_DEFAULT_POLL_FREQUENCY_IN_MS = 750;

    const PUBLISH_TYPE_ASSET = 'asset';
}
