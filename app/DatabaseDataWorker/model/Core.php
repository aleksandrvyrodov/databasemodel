<?php

namespace JrAppBox\DatabaseDataWorker\Model;

use JrAppBox\DatabaseDataWorker\Contractor\SimpleStorage;
use JrAppBox\DatabaseDataWorker\Error\DDWError;

abstract class Core implements IModel
{
  const UPDATE = 0b0001;
  const CHECK  = 0b0010;
  const TEMPL  = 0b0100;
  const EXISTS = 0b1000;
  const ERROR  = 0b10000;
  const VOID = 0b0;

  const CHAIN = true;

  const EMPTY_MD5_HASH = 'd751713988987e9331980363e24189ce';

  const SET = 0;
  const GET = 1;

  public     array  $freeQ = [];
  protected  int    $_state = self::VOID;
  protected  array  $_raw = [];
  protected ?string $_hash = null;

  static protected array $Query = [];
  static protected SimpleStorage $Vault;

  abstract static public function Init(string $returned, ...$argc);


  public function __toString()
  {
    $export = $this->_get_maked_data();

    return json_encode($export);
  }


  #region DATA SET/GET
  public function getHash(): ?string
  {
    return $this->_hash;
  }

  public static function GenerateHash(array $data)
  {
    $hash = md5(json_encode($data)) . '|' . (static::class)::TABLE;
    if (self::EMPTY_MD5_HASH !== $hash)
      return $hash;
    else
      return false;
  }

  protected function _set_hash(): IModel
  {
    ($hash = self::GenerateHash($this->_raw))
      && $this->_hash = $hash;

    return $this;
  }

  protected function _data_transformation(string $prop, $data, int $do)
  {
    return $data;
  }

  protected function _storage_raw(?array $raw = null)
  {
    if (is_null($raw))
      return $this->_raw;

    $this->_raw = $raw;
    $this->_set_hash();

    return $this;
  }

  protected function _process_raw(array $raw)
  {
    foreach ($raw as $name => $value)
      $this->setProp(
        $name,
        $this->_data_transformation($name, $value, self::SET)
      );

    return $this;
  }

  protected function &_prop_iterator(): \Generator
  {
    foreach (array_filter((new \ReflectionClass($this))
        ->getProperties(
          \ReflectionProperty::IS_PROTECTED
        ),
      fn ($RefProp) => $RefProp->class === static::class
        && $RefProp->name[0] !== '_'
    ) as $RefProp)
      yield $RefProp->name => $this->{$RefProp->name};
  }

  public function _get_maked_data(): array
  {
    $prepare = [];
    foreach ($this->_prop_iterator() as $name => $value) {
      $prop = $this->_data_transformation((string)$name, $value, self::GET);
      if ($prop !== false)
        $prepare[$name] = $prop;
    }

    return $prepare;
  }
  #endregion


  #region PROP
  public function __isset($name): bool
  {
    return property_exists($this, $name);
  }

  public function __get(string $name)
  {
    return $this->prop($name);
  }

  public function prop(string $name)
  {
    if (property_exists($this, $name))
      return $this->{$name};
    else
      return null;
  }

  public function setProp(string $name = '', $value, &$mirror = null): IModel
  {
    try {
      if (property_exists($this, $name))
        $this->{$name} = $mirror =  $value;
      else
        $this->freeQ[$name] = $value;
    } catch (DDWError $me) {
      1; //$me->storage('Mismatch of types', 1);
    } finally {
      return $this;
    }
  }
  #endregion

  #region STATE
  protected function _state_set(int $state, bool $force = false)
  {
    switch ($state) {
      case self::EXISTS:
        if ($force)
          $this->_state = self::EXISTS;
        else
          $this->_state |= self::EXISTS;
        break;
      case self::TEMPL:
        $this->_state = self::TEMPL;
        break;
      case self::VOID:
        $this->_state = self::VOID;
        break;
      case self::ERROR:
        $this->_state |= self::ERROR;
        break;
      default:
        DDWError::Add("Undefinded state",   2);
        break;
    }
    return $this;
  }

  public function state(): int
  {
    return $this->_state;
  }

  public function ready(): bool
  {
    return (bool)(
      ($this->_state & self::EXISTS)
      || ($this->_state & self::TEMPL)
    );
  }

  public function exists(): bool
  {
    return (bool)($this->_state & self::EXISTS);
  }

  public function error(): bool
  {
    return (bool)($this->_state & self::ERROR);
  }
  #endregion
}
