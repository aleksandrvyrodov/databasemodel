<?php

namespace JrAppBox\DatabaseDataWorker;

use JrAppBox\DatabaseDataWorker\Contractor\Core\Connector;
use JrAppBox\DatabaseDataWorker\Contractor\SimpleBuilder as SB;
use JrAppBox\DatabaseDataWorker\Contractor\SimpleQuery;
use JrAppBox\DatabaseDataWorker\Model\Core;
use JrAppBox\DatabaseDataWorker\Model\IModel;

abstract class DoubleKeyModel extends Core implements IModel
{
  static array $Builder = [];

  #region INITED
  static protected function InitBuilder()
  {

    empty(self::$Builder[static::class])
      && self::$Builder[static::class] = new SB;

    return self::$Builder[static::class];
  }
  #endregion

  #region ENTRANCE

  static public function Get($key_v = null, $returned = null): ?DoubleKeyModel
  {
    self::InitBuilder();

    if (is_null($key_v) && $returned !== self::TEMPL)
      return null;
    else
      return static::Create($key_v);
  }

  static public function GetAll(int $limit = 0, int $offset = 0): array
  {
    $Query = self::InitBuilder();
    $Query
      ->clean()
      ->limitation($limit, $offset);

    return self::_ListRawToData($Query->list());
  }

  static public function SetParam(?array $params = null, bool $chain = false): SB
  {
    $chain && $params = array_merge($params, [
      SB::CHAIN => static::class
    ]);
    $Query = self::InitBuilder();
    return $Query->option($params);
  }

  static public function List()
  {
    $Query = self::InitBuilder();
    return self::_ListRawToData($Query->list());
  }

  static public function Create($key = null, array $arg = []): DoubleKeyModel
  {
    self::InitBuilder();

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

    foreach ($list_raw as $raw)
      $returned[] = new static($raw, self::EXISTS, false);


    return $returned;
  }

  protected function __construct(array $raw = [], $state = null, $storage = true)
  {
    $this->_process_raw($raw);
    $this->_state_set($state);
  }
  #endregion

  #region INTERFACE
  public function load(?array &$raw = null): DoubleKeyModel
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

  public function save(): DoubleKeyModel
  {
    $key = (static::class)::P_KEY;
    $dataDo = fn () => is_null($this->{$key})
      ? $this->dataInsert()
      : $this->dataUpdate();

    if ($dataDo())
      $this->_state_set(self::EXISTS);

    return $this;
  }

  public function remove(): DoubleKeyModel
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
