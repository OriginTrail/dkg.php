<?php

namespace Dkg\Communication\Exceptions;

use Exception;
use Throwable;

class NodeProxyException extends Exception
{
    private $data;

    public function __construct($message = "", $data = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
