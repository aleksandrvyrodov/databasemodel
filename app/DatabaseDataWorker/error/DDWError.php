<?php

namespace JrAppBox\DatabaseDataWorker\Error;

use JrAppBox\DatabaseDataWorker\Error\DDWError as ErrorDDWError;

class DDWError extends \Exception
{
  static  \SplObjectStorage $storage_DDWException;
  static ?DDWError $LastError = null;


  public function __construct($message, $code = 0, \Throwable $previous = null)
  {
    self::LoadStorage();
    parent::__construct($message, $code, $previous);

    self::$LastError = $this;
    self::LoadStorage()
      ->attach(
        self::$LastError,
        array_map(
          fn ($tarce) => $tarce['class'] . '::' . $tarce['function'],
          self::$LastError->getTrace()
        )
      );
  }

  static private function LoadStorage()
  {
    if (!isset(self::$storage_DDWException))
      self::$storage_DDWException = new \SplObjectStorage();
    return self::$storage_DDWException;
  }

  static public function Add($message, $code = 0, \Throwable $previous = null)
  {
    return new self($message, $code, $previous);
  }

  static function FindErrorsReason($reason, &$finded = 0): array
  {
    $export_Storage = [];
    $Storage = self::LoadStorage();
    $Storage->rewind();

    while ($Storage->valid()) {
      $object = $Storage->current();
      $data = (array)$Storage->getInfo();

      if (in_array($reason, $data)) {
        $finded = array_unshift($export_Storage, $object);
        $Storage->detach($object);
      } else
        $Storage->next();
    }

    return $export_Storage;
  }

  static public function LastError(): ?DDWError
  {
    $LastError = self::$LastError;
    self::LoadStorage()
      ->detach($LastError);
    self::$LastError = null;
    return $LastError;
  }

  static public function AllErrors(): \SplObjectStorage
  {
    return self::LoadStorage();
  }

  static public function CleanErrors($LastError = true): void
  {
    self::$storage_DDWException = new \SplObjectStorage();
    self::$LastError = null;
  }
}
