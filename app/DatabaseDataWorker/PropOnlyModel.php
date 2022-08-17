<?php

namespace JrAppBox\DatabaseDataWorker;

use JrAppBox\DatabaseDataWorker\Contractor\SimpleBuilder;
use JrAppBox\DatabaseDataWorker\Contractor\SimpleQuery;
use JrAppBox\DatabaseDataWorker\Contractor\SimpleStorage;
use JrAppBox\DatabaseDataWorker\Error\DDWError;
use JrAppBox\DatabaseDataWorker\Model\Core;
use JrAppBox\DatabaseDataWorker\Model\IModel;

abstract class PropOnlyModel extends Core implements IModel
{

  #region INITED
  static public function Init(string $returnded, ...$argc)
  {
    try {
      $Query = self::InitQuery();
      $Vault = self::InitVault();
    } catch (\Throwable $Th) {
      DDWError::Add('Fail inited model (' . static::class . ')', 1000, $Th);
      return false;
    }

    switch ($returnded) {
      case SimpleQuery::class:
        return $Query;
      case SimpleStorage::class:
        return $Vault;
      case static::class:
        return new static();
      default:
        return true;
    }
  }

  static protected function InitVault()
  {
    !isset(self::$Vault)
      && self::$Vault = SimpleStorage::Init();

    return self::$Vault;
  }

  static protected function InitQuery()
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

  protected static function _ListRawToData(array $list_raw)
  {
    $returned = [];

    foreach ($list_raw as $raw)
      $returned[] = self::$Vault->Get(self::GenerateHash($raw))
        ?? new static($raw, self::EXISTS, true);
      // $returned[] = new static($raw, self::EXISTS, true);


    return $returned;
  }

  static public function Get($key_v = null, $returned = null): ?PropOnlyModel //FAR
  {
    if ($returned === self::TEMPL)
      return static::Create();
    else
      return null;
  }

  static public function Create(): PropOnlyModel
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
      self::$Vault->Set($this);
    }
  }
  #endregion

  #region INTERFACE
  public function load(?array &$raw = null): PropOnlyModel
  {
    if ($this->ready()) {
      $raw = $this->dataGet();
      if ($raw) {
        $this
          ->_storage_raw($raw)
          ->_process_raw($raw)
          ->_state_set(self::EXISTS);
        self::$Vault->Update($this);
      }
    }

    return $this;
  }

  public function save(): PropOnlyModel
  {
    $dataDo = fn () => $this->exists()
      ? $this->dataUpdate()
      : $this->dataInsert();

    if ($dataDo())
      $this->load();
    else
      $this->_state_set(self::ERROR);

    return $this;
  }

  public function remove(): PropOnlyModel
  {
    $res = $this->dataRemove();
    if ($res) {
      $this->_state_set(self::VOID);
      self::$Vault->Remove($this);
    } else
      $this->_state_set(self::ERROR);
    return $this;
  }
  #endregion


  #region QUERY
  private function _prepare_query($data)
  {
    $Query = self::$Query[static::class];
    $SB = $Query
      ->params()
      ->clean();

    foreach ($data as $p => $v)
      $SB->where(<<<WH
        `$p` = '$v'
        WH);
    return $Query;
  }

  protected function dataInsert(): bool
  {
    $data = $this->_get_maked_data();

    self::$Query[static::class]->insert($data);
    $list_Error = DDWError::FindErrorsReason(__METHOD__, false, $count_Error);

    return !$count_Error;
  }

  protected function dataGet()
  {
    $data = $this->_get_maked_data();
    $Query = $this->_prepare_query($this->_get_maked_data());

    $res = $Query->select($data);

    return $res;
  }

  protected function dataUpdate()
  {
    $data = $this->_get_maked_data();
    $Query = $this->_prepare_query($this->_storage_raw());

    $res = $Query->update($data);

    return $res;
  }

  protected function dataRemove()
  {
    $Query = $this->_prepare_query($this->_storage_raw());

    $res = $Query->remove();

    return $res;
  }
  #endregion
}
