<?php


namespace Weak;


use Exception;


class NotifierException extends Exception
{
    private $exceptions = [];

    /**
     * Get exceptions thrown from notifiers
     *
     * @return array
     */
    public function getExceptions() : array
    {
        return $this->exceptions;
    }
}
