<?php

namespace Dkg\Services;

class Constants
{
    public const PUBLISH_DEFAULT_HOLDING_TIME_IN_YEARS = 2;
    public const PUBLISH_DEFAULT_TOKEN_AMOUNT = 15;

    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PRIVATE = 'private';

    public const HTTP_DEFAULT_MAX_NUM_OF_RETRIES = 5;
    public const HTTP_DEFAULT_FREQUENCY_IN_SEC = 5;

    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_FAILED = 'FAILED';
}
