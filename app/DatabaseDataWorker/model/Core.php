<?php

namespace JrAppBox\DatabaseDataWorker\Model;

use JrAppBox\DatabaseDataWorker\Error\DDWException;

abstract class Core implements IModel, \Stringable
{
  const UPDATE = 0b0001;
  const CHECK  = 0b0010;
  const TEMPL  = 0b0100;
  const EXISTS = 0b1000;
  const VOID = 0b0;

  const CHAIN = true;


  const SET = 0;
  const GET = 1;

  public array $freeQ = [];
  protected int $_state = self::VOID;

  static protected array $Query = [];

  public function __toString()
  {
    $export = $this->_get_maked_data();

    return json_encode($export);
  }


  #region DATA SET/GET
  protected function _data_transformation($prop, $data, $do)
  {
    return $data;
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

  protected function &_prop_iterator()
  {
    foreach (array_filter((new \ReflectionClass($this))
        ->getProperties(
          \ReflectionProperty::IS_PROTECTED
        ),
      fn ($RefProp) => $RefProp->class === static::class
        && $RefProp->name !== (static::class)::P_KEY
        && $RefProp->name[0] !== '_'
    ) as $RefProp)
      yield $RefProp->name => $this->{$RefProp->name};
  }

  public function _get_maked_data(): array
  {
    $prepare = [];
    foreach ($this->_prop_iterator() as $name => $value) {
      $prop = $this->_data_transformation($name, $value, self::GET);
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

  public function setProp($name = '', $value, $mirror = null): IModel
  {
    try {
      if (property_exists($this, $name))
        $this->{$name} = $mirror =  $value;
      else
        $this->freeQ[$name] = $value;
    } catch (DDWException $me) {
      1; //$me->storage('Mismatch of types', 1);
    } finally {
      return $this;
    }
  }
  #endregion

  #region STATE
  protected function _state_set($state, $force = false)
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
      default:
        DDWException::Add("Undefinded state",   2);
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
  #endregion

}
