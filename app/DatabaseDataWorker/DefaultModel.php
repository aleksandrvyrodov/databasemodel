<?php

namespace JrAppBox\DatabaseDataWorker;

use JrAppBox\DatabaseDataWorker\Contractor\SimpleBuilder;
use JrAppBox\DatabaseDataWorker\Contractor\SimpleQuery;
use JrAppBox\DatabaseDataWorker\Contractor\Storage\IStorage;
use JrAppBox\DatabaseDataWorker\Contractor\Storage\SimpleStorage;
use JrAppBox\DatabaseDataWorker\Error\DDWError;
use JrAppBox\DatabaseDataWorker\Model\Core;

abstract class DefaultModel extends Core
{

  static protected IStorage $Vault;

  #region INITED
  static public function Init(string $returnded, ...$argc)
  {
    try {
      $Query = static::InitQuery();
      $Vault = static::InitVault();
    } catch (\Throwable $Th) {
      DDWError::Add('Fail inited model (' . static::class . ')', 1000, $Th);
      return false;
    }

    switch ($returnded) {
      case get_class($Query):
        return $Query;
      case get_class($Vault):
        return $Vault;
      case static::class:
        return new static();
      default:
        return true;
    }
  }

  static protected function InitVault(): SimpleStorage
  {
    !isset(self::$Vault)
      && self::$Vault = SimpleStorage::Init();

    return self::$Vault;
  }

  static protected function InitQuery(): SimpleQuery
  {
    empty(self::$Query[static::class])
      && self::$Query[static::class] = new SimpleQuery((static::class)::TABLE);

    return self::$Query[static::class];
  }
  #endregion

  #region ENTRANCE
  static public function SetParam(?array $params = null, bool $chain = false): SimpleBuilder
  {
    $chain && $params = array_merge($params, [
      SimpleBuilder::CHAIN => static::class
    ]);
    $Query = self::Init(SimpleQuery::class);
    return $Query->params($params);
  }

  static public function GetAll(int $limit = 0, int $offset = 0): array
  {
    $Query = self::Init(SimpleQuery::class);
    $Query
      ->params()
      ->clean()
      ->limitation($limit, $offset);

    return self::_ListRawToData($Query->list());
  }

  static public function List()
  {
    $Query = self::Init(SimpleQuery::class);
    return self::_ListRawToData($Query->list());
  }

  protected static function _ListRawToData(array $list_raw): array
  {
    $returned = [];

    foreach ($list_raw as $raw)
      $returned[] = static::$Vault->Get(self::GenerateHash($raw))
        ?? new static($raw, self::EXISTS, true);
    // $returned[] = new static($raw, self::EXISTS, true);

    return $returned;
  }

  static public function Get($key_v = null, $returned = null): ?DefaultModel
  {
    if (
      $returned === self::TEMPL
    )
      return static::Create();
    else
      return null;
  }

  static public function Create(): DefaultModel
  {
    return self::Init(static::class);
  }

  protected function __construct(array $raw = [], $state = self::TEMPL, $impression = false)
  {
    $this
      ->_process_raw($raw)
      ->_state_set($state);

    if ($impression) {
      $this->_storage_raw($raw);
      static::$Vault->Set($this);
    }
  }
  #endregion
}
