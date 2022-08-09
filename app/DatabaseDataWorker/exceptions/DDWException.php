<?php

namespace JrAppBox\DatabaseDataWorker\Error;

class DDWException extends \Exception
{

  public function __construct($message, $code = 0, \Throwable $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }

  static public function Add($message, $code = 0)
  {
    1;
  }
}
