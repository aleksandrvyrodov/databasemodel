<?php

namespace JrAppBox\DatabaseDataWorker\Contractor;

use JrAppBox\DatabaseDataWorker\Error\DDWError;
use JrAppBox\DatabaseDataWorker\Model\IModel;

class SimpleStorage
{
  private static \SplObjectStorage $Storage;

  private static function Init(): \SplObjectStorage
  {
    if (!isset(self::$Storage))
      self::$Storage = new \SplObjectStorage();

    return self::$Storage;
  }

  static private function CheckHash(IModel $Model): bool
  {
    if (is_null($Model->getHash())) {
      DDWError::Add('Hash model is null', 400);
      return false;
    } else
      return true;
  }

  static public function Set(IModel &$Model)
  {
    $Storage = self::Init();

    if (self::CheckHash($Model) && !self::Exists($Model))
      $Storage->attach($Model, $Model->getHash());

    return self::class;
  }

  static public function &Get(string $hash): ?IModel
  {
    $Storage = self::Init();

    while ($Storage->valid()) {
      if ($hash === $Storage->getInfo())
        return $Storage->current();
      else
        $Storage->next();
    }

    return null;
  }

  static public function Update(IModel $Model)
  {
    $Storage = self::Init();

    if (self::Exists($Model))
      $Storage->attach($Model, $Model->getHash());

    return self::class;
  }

  static public function Exists(IModel $Model): bool
  {
    $Storage = self::Init();

    return $Storage->contains($Model);
  }


  static public function Remove(IModel $Model)
  {
    $Storage = self::Init();

    if (self::Exists($Model))
      $Storage->detach($Model);

    return self::class;
  }
}
