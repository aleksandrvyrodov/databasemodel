<?php

namespace JrAppBox\DatabaseDataWorker;

use JrAppBox\DatabaseDataWorker\Contractor\SimpleBuilder;
use JrAppBox\DatabaseDataWorker\Contractor\SimpleQuery;
use JrAppBox\DatabaseDataWorker\Error\DDWException;
use JrAppBox\DatabaseDataWorker\Model\Core;
use JrAppBox\DatabaseDataWorker\Model\IModel;
use PDO;
use SCCT\Model\Model;

abstract class DefaultModel extends Core implements IModel
{

  #region INITED
  static protected function InitQuery()
  {
    empty(self::$Query[static::class])
      && self::$Query[static::class] = new SimpleQuery((static::class)::TABLE, (static::class)::P_KEY);

    return self::$Query[static::class];
  }

  static protected function InitStorage()
  {
    !array_key_exists(static::class, self::$list_Storage)
      && self::$list_Storage[static::class] = [];
    !array_key_exists(static::class, self::$Storage)
      && self::$Storage[static::class] = null;
  }
  #endregion

  #region ENTRANCE
  static public function Storage($key = self::STORAGE_ONCE)
  {
    self::InitStorage();

    $returned = self::STORAGE_LIST === $key ? [] : null;

    switch (true) {
      case $key === self::STORAGE_LIST:
        if (isset(self::$list_Storage[static::class]))
          return self::$list_Storage[static::class];
      case $key === self::STORAGE_ONCE:
        if (isset(self::$Storage[static::class]))
          return self::$Storage[static::class];
      default:
        if (isset(self::$list_Storage[static::class][$key]))
          return self::$list_Storage[static::class][$key];
    }

    return $returned;
  }

  static public function Get($key_v = null, $returned = null): ?DefaultModel
  {
    self::InitQuery();

    if (is_null($key_v)) {
      if ($returned == self::TEMPL)
        return static::Create($key_v);
      else
        return self::Storage(self::STORAGE_ONCE);
    } else {
      $Model = self::Storage($key_v);
      if (is_null($Model)) {
        $Model = static::Create($key_v);
        if ($Model->exists() || $returned == self::TEMPL)
          return $Model;
        elseif ($returned == self::TEMPL)
          return null;
      } else
        return $Model;
    }
  }

  static public function GetAll(int $limit = 0, int $offset = 0): array
  {
    $Query = self::InitQuery();
    $Query
      ->params()
      ->clean()
      ->storage(SimpleBuilder::SLEEP_STORAGE)
      ->limitation($limit, $offset);

    return self::_ListRawToData($Query->list());
  }

  static public function SetParam(?array $params = null, bool $chain = false): SimpleBuilder
  {
    $chain && $params = array_merge($params, [
      SimpleBuilder::CHAIN => static::class
    ]);
    $Query = self::InitQuery();
    return $Query->params($params);
  }

  static public function List()
  {
    $Query = self::InitQuery();
    return self::_ListRawToData($Query->list());
  }

  static public function Create($key = null, array $arg = []): DefaultModel
  {
    self::InitQuery();

    $key = empty($key) && array_key_exists((static::class)::P_KEY, $arg)
      ? $arg[(static::class)::P_KEY]
      : $key;

    $arg = !is_null($key)
      ? array_merge([(static::class)::P_KEY => $key], $arg)
      : $arg;

    return (new static($arg))
      ->load();
  }

  protected static function _ListRawToData(array $list_raw)
  {
    $returned = [];
    $key = (static::class)::P_KEY;

    foreach ($list_raw as $raw) {
      $key_v = $raw[$key];
      if (($storage = self::Storage($key_v))) {
        $returned[] = $storage;
      } else {
        $returned[] = new static($raw, self::EXISTS, false);
      }
    }

    return $returned;
  }

  protected function __construct(array $raw = [], $state = null, $storage = true)
  {
    self::InitStorage();

    $this->_process_raw($raw);

    $key = (static::class)::P_KEY;
    if (is_null($state))
      $state = $this->{$key} ? self::TEMPL : self::VOID;

    $this
      ->_put_place($key, $storage)
      ->_state_set($state);
  }
  #endregion

  #region INTERFACE
  public function load(?array &$raw = null): DefaultModel
  {
    if ($this->ready()) {
      $raw = $this->dataGet();
      if ($raw) {
        $this
          ->_process_raw($raw)
          ->_state_set(self::EXISTS, true);
      }
    }

    return $this;
  }

  public function save(): DefaultModel
  {
    $dataDo = fn () => is_null($this->id)
      ? $this->dataInsert()
      : $this->dataUpdate();

    if ($dataDo())
      $this->_state_set(self::EXISTS);

    return $this;
  }

  public function remove(): DefaultModel
  {
    $key = (static::class)::P_KEY;
    switch (true) {
      case $this->exists():
        $res = $this->dataRemove();
        if (!$res)
          1;
      case $this->ready():
        if (
          array_key_exists(static::class, self::$list_Storage)
          && array_key_exists($this->{$key}, self::$list_Storage[static::class])
        ) unset(self::$list_Storage[static::class][$this->{$key}]);

        if (
          array_key_exists(static::class, self::$Storage)
          && self::$Storage[static::class]->{$key} == $this->{$key}
        ) self::$Storage[static::class] = null;
        break;
      default:
        1;
        break;
    }

    $this->_state_set(self::VOID);
    $this->{$key} = null;

    return $this;
  }
  #endregion




  protected function _put_place($key, $storage = true): DefaultModel
  {
    if (!empty($this->{$key})) {
      self::$list_Storage[static::class][$this->{$key}] = $this;

      $storage
        && empty(self::$Storage[static::class])
        && self::$Storage[static::class] = $this;
    }

    return $this;
  }

  #region QUERY
  protected function dataInsert()
  {
    $data = $this->_get_maked_data();

    $key = self::$Query[static::class]->insert($data);
    if ($key) {
      $this->{(static::class)::P_KEY} = $key;
      return true;
    } else
      return false;
  }

  protected function dataUpdate()
  {
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
