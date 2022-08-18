<?php

namespace JrAppBox\DatabaseDataWorker;

use JrAppBox\DatabaseDataWorker\Model\IModel;

abstract class xxxCachedModel extends DefaultModel implements IModel
{

  const STORAGE_ONCE = true;
  const STORAGE_LIST = false;

  static protected array $list_Storage = [];
  static public array $Storage = [];

  #region INITED
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

  static public function Get($key_v = null, $returned = null): ?CachedModel
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
  public function load(?array &$raw = null): CachedModel
  {
    if ($this->ready()) {
      $raw = $this->dataGet();
      if ($raw) {
        $this
          ->_process_raw($raw)
          ->_put_place($this->{(static::class)::P_KEY})
          ->_state_set(self::EXISTS, true);
      }
    }

    return $this;
  }

  public function remove(): CachedModel
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


  protected function _put_place($key, $storage = true): CachedModel
  {
    if (!empty($this->{$key})) {
      self::$list_Storage[static::class][$this->{$key}] = $this;

      $storage
        && empty(self::$Storage[static::class])
        && self::$Storage[static::class] = $this;
    }

    return $this;
  }
}
