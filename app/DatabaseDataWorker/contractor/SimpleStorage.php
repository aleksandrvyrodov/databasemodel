<?php

namespace JrAppBox\DatabaseDataWorker\Contractor;

use JrAppBox\DatabaseDataWorker\Error\DDWError;
use JrAppBox\DatabaseDataWorker\Model\IModel;

class SimpleStorage
{
  private \SplObjectStorage $Storage;
  private static SimpleStorage $SimpleStorage;
  private static array $hash_table = [];

  private function __construct()
  {
    $this->Storage = new \SplObjectStorage();
  }

  public static function Init(): SimpleStorage
  {
    if (!isset(self::$SimpleStorage))
      self::$SimpleStorage = new self();

    return self::$SimpleStorage;
  }

  private function CheckHash(IModel $Model): bool
  {
    if (is_null($Model->getHash())) {
      DDWError::Add('Hash model is null', 400);
      return false;
    } else
      return true;
  }

  public function Set(IModel &$Model): SimpleStorage
  {
    $Storage = $this->Storage;

    if ($this->CheckHash($Model)) {
      $Storage->attach($Model, $Model->getHash());
      self::$hash_table[$Model->getHash()] = $Model;
    }

    return $this;
  }

  public function Get(string $hash): ?IModel
  {
    if (isset(self::$hash_table[$hash])) {
      $Model = self::$hash_table[$hash];
      if ($this->Exists($Model))
        return $Model;
      else
        unset(self::$hash_table[$hash]);
    }
    return null;
  }

  public function Update(IModel $Model): SimpleStorage
  {
    $Storage = $this->Storage;
    $hash = $Model->getHash();

    if ($Model->error())
      $this->Remove($Model);
    elseif (!$this->Exists($Model))
      $this->Set($Model);
    else {
      unset(self::$hash_table[$Storage[$Model]->getInfo()]);
      $Storage[$Model]->setInfo($hash);
      self::$hash_table[$hash] = get_class($Model);
    }

    return $this;
  }

  public function Exists(IModel $Model): bool
  {
    $Storage = $this->Storage;

    return $Storage->contains($Model);
  }

  public function Remove(IModel $Model): SimpleStorage
  {
    $Storage = $this->Storage;
    $hash = $Model->getHash();

    if ($this->Exists($Model)) {
      $Storage->detach($Model);
      unset(self::$hash_table[$hash]);
    }

    return $this;
  }
}
