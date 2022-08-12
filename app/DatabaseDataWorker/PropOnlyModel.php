<?php

namespace JrAppBox\DatabaseDataWorker;

use JrAppBox\DatabaseDataWorker\Contractor\SimpleBuilder;
use JrAppBox\DatabaseDataWorker\Contractor\SimpleQuery;
use JrAppBox\DatabaseDataWorker\Error\DDWError;
use JrAppBox\DatabaseDataWorker\Model\Core;
use JrAppBox\DatabaseDataWorker\Model\IModel;

abstract class PropOnlyModel extends Core implements IModel
{

  #region INITED
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
    $Query = self::InitQuery();
    return $Query->params($params);
  }

  static public function GetAll(int $limit = 0, int $offset = 0): array
  {
    $Query = self::InitQuery();
    $Query
      ->params()
      ->clean()
      ->limitation($limit, $offset);

    return self::_ListRawToData($Query->list());
  }

  static public function List()
  {
    $Query = self::InitQuery();
    return self::_ListRawToData($Query->list());
  }

  protected static function _ListRawToData(array $list_raw)
  {
    $returned = [];

    foreach ($list_raw as $raw)
      $returned[] = new static($raw, self::EXISTS, false);


    return $returned;
  }

  static public function Get($key_v = null, $returned = null): ?PropOnlyModel //FAR
  {
    self::InitQuery();

    if (is_null($key_v) && $returned !== self::TEMPL)
      return null;
    else
      return static::Create($key_v);
  }

  static public function Create(): PropOnlyModel
  {
    self::InitQuery();

    return new static();
  }

  protected function __construct(array $raw = [], $state = self::TEMPL)
  {
    $this
      ->_process_raw($raw)
      ->_state_set($state);
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
          ->_state_set(self::EXISTS, true);
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
      $this->_state_set(self::EXISTS);

    return $this;
  }

  public function remove(): PropOnlyModel
  {
    $key = (static::class)::P_KEY;
    $res = $this->dataRemove();
    if (!$res)
      1;

    $this->_state_set(self::VOID);
    $this->{$key} = null;

    return $this;
  }
  #endregion


  #region QUERY
  protected function dataInsert(): bool
  {
    $data = $this->_get_maked_data();

    self::$Query[static::class]->insert($data);
    $list_Error = DDWError::FindErrorsReason(__METHOD__, $count_Error);

    return !$count_Error;
  }

  protected function dataUpdate()
  {
    exit();
    $data = $this->_get_maked_data();
    $key = $this->{(static::class)::P_KEY};

    $res = self::$Query[static::class]->update($key, $data);
    return $res;
  }

  protected function dataGet()
  {
    $key = $this->{(static::class)::P_KEY};

    $res = self::$Query[static::class]->select($key);
    return $res;
  }

  protected function dataRemove()
  {
    $key = $this->{(static::class)::P_KEY};

    $res = self::$Query[static::class]->remove($key);
    return $res;
  }
  #endregion
}
