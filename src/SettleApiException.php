<?php

namespace SettleApi;

use Exception;
use Throwable;

/**
 * Class SettleApiException
 * @package SettleApi
 */
class SettleApiException extends Exception
{
    /**
     * @var array A list of error messages for every field (array key)
     */
    protected $validationErrors;

    public function __construct($message = "", $code = 0, Throwable $previous = null, $validationErrors = [])
    {
        parent::__construct($message, $code, $previous);

        $this->validationErrors = $validationErrors;
    }

    /**
     * @return array A list of error messages for every field (array key)
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }
}
