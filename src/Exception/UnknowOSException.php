<?php

namespace TextUI\Exception;

/**
 * Description of UnknowOSException
 *
 * @author Everton
 */
class UnknowOSException extends \Exception
{
    public function __construct(
        string $message = "Operating System unknow.",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
