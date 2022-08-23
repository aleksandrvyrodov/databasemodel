<?php

namespace JrAppBox\DatabaseDataWorker\Contractor\Storage;

use JrAppBox\DatabaseDataWorker\Error\DDWError;
use JrAppBox\DatabaseDataWorker\Model\IModel;

class SimpleStorage implements IStorage
{
  protected \SplObjectStorage $Storage;
  protected static SimpleStorage $SimpleStorage;
  public static array $hash_table = []; //FAR

  protected function __construct()
  {
    $this->Storage = new \SplObjectStorage();
  }

  public static function Init(): SimpleStorage
  {
    if (!isset(self::$SimpleStorage))
      self::$SimpleStorage = new self();

    return self::$SimpleStorage;
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

  public function Set(IModel $Model): bool
  {
    $Storage = $this->Storage;

    if ($this->CheckHash($Model) && !$this->Exists($Model)) {
      $Storage->attach($Model, $Model->getHash());
      self::$hash_table[$Model->getHash()] = $Model;
      return true;
    } else
      return false;
  }

  public function Remove(IModel $Model): bool
  {
    $Storage = $this->Storage;
    $hash = $Model->getHash();

    if ($this->Exists($Model)) {
      $Storage->detach($Model);
      unset(self::$hash_table[$hash]);
      return true;
    } else
      return false;
  }

  public function Move(IModel $Model): bool
  {
    $Storage = $this->Storage;
    $hash = $Model->getHash();

    if (isset(self::$hash_table[$Storage[$Model]])) {
      unset(self::$hash_table[$Storage[$Model]]);
      $Storage[$Model] = $hash;
      self::$hash_table[$hash] = get_class($Model);
      return true;
    } else
      return false;
  }

  public function Update(IModel $Model): bool
  {
    if ($Model->error())
      return $this->Remove($Model);
    elseif (!$this->Exists($Model))
      return $this->Set($Model);
    elseif (!$Model->modif())
      return $this->Move($Model);

    return false;
  }

  protected function CheckHash(IModel $Model): bool
  {
    if (is_null($Model->getHash())) {
      DDWError::Add('Hash model is null', 400);
      return false;
    } else
      return true;
  }

  public function Exists(IModel $Model): bool
  {
    $Storage = $this->Storage;
    return $Storage->contains($Model);
  }
}
