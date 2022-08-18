<?php

namespace JrAppBox\DatabaseDataWorker;

use JrAppBox\DatabaseDataWorker\Contractor\SimpleBuilder;
use JrAppBox\DatabaseDataWorker\Contractor\SimpleQuery;
use JrAppBox\DatabaseDataWorker\Model\Core;
use JrAppBox\DatabaseDataWorker\Model\IModel;

abstract class xxxDefaultModel extends Core implements IModel
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
  static public function GetAll(int $limit = 0, int $offset = 0): array
  {
    $Query = self::InitQuery();
    $Query
      ->params()
      ->clean()
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

  protected static function _ListRawToData(array $list_raw)
  {
    $returned = [];

    foreach ($list_raw as $raw)
      $returned[] = new static($raw, self::EXISTS, false);

    return $returned;
  }

  protected function __construct(array $raw = [])
  {
    $this->_process_raw($raw);
  }
  #endregion

  #region INTERFACE

  public function save(): DefaultModel
  {
    $key = (static::class)::P_KEY;
    $dataDo = fn () => is_null($this->{$key})
      ? $this->dataInsert()
      : $this->dataUpdate();

    if ($dataDo())
      $this->_state_set(self::EXISTS);

    return $this;
  }

  public function remove(): DefaultModel
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
}
