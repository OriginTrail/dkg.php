<?php

namespace Dkg\Services;

class Constants
{
    public const PUBLISH_DEFAULT_EPOCH_NUM = 5;
    public const PUBLISH_DEFAULT_TOKEN_AMOUNT = 15;
    public const PUBLISH_DEFAULT_TRIPLES_NUMBER = 10;
    public const PUBLISH_DEFAULT_CHUNKS_NUMBER = 10;
    public const PUBLISH_DEFAULT_EPOCHS_NUM = 5;

    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PUBLIC_VALUE = 1;
    public const VISIBILITY_PRIVATE = 'private';
    public const VISIBILITY_PRIVATE_VALUE = 0;

    public const HTTP_DEFAULT_MAX_NUM_OF_RETRIES = 5;
    public const HTTP_DEFAULT_FREQUENCY_IN_SEC = 5;

    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_FAILED = 'FAILED';

    public const BLOCKCHAIN_DEFAULT_TIMEOUT_TIME_IN_SEC = 60;
}
