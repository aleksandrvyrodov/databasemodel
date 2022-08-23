<?php

namespace JrAppBox\DatabaseDataWorker\Contractor\Storage;

use JrAppBox\DatabaseDataWorker\Error\DDWError;
use JrAppBox\DatabaseDataWorker\Model\IModel;
use SplObjectStorage;

class IndexedStorage extends SimpleStorage
{
  public static array $indexed = []; // FAR
  public static IndexedStorage $IndexedStorage; // FAR

  public static function Init(): IndexedStorage
  {
    if (!isset(self::$IndexedStorage))
      self::$IndexedStorage = new self();

    return self::$IndexedStorage;
  }

  public function Set(IModel $Model): bool
  {
    if (parent::Set($Model)) {
      $Model->_indexstore = [];

      foreach (array_keys(self::$indexed[get_class($Model)]) as $index)
        $Model->_indexstore[$index] = $Model->{$index};

      return $this->SetIndex($Model);
    } else
      return false;
  }

  public function Remove(IModel $Model): bool
  {
    if (parent::Remove($Model))
      return $this->RemoveIndex($Model);
    else
      return false;
  }

  public function Move(IModel $Model): bool
  {
    if (parent::Move($Model))
      return $this->MoveIndex($Model);
    else
      return false;
  }

  public function InitStorageIndex($model): IndexedStorage
  {
    if (!isset(self::$indexed[$model])) {
      self::$indexed[$model] = [];

      foreach (['PK', 'FK', 'INDEX'] as $cur_index)
        try {
          if (
            ($const = new \ReflectionClassConstant($model, $cur_index))
            && ($props = $const->getValue())
          ) {
            gettype($props) === 'string'
              && $props = [$props];

            foreach ($props as $prop)
              self::$indexed[$model][$prop] = [];
          }
          #
        } catch (\ReflectionException $Re) {
        }
    }
    return $this;
  }

  private function MoveIndex(IModel $Model): bool
  {
    $indexed = &self::$indexed[get_class($Model)];

    foreach ($Model->_indexstore as $key => $val) {
      if ($Model->{$key} !== $val) {
        $this->_remove($Model, $indexed, $key, $val);
        $this->_set($Model, $indexed, $key, $Model->{$key});
        $Model->_indexstore[$key] = $Model->{$key};
      }
    }

    return true;
  }

  private function SetIndex(IModel $Model): bool
  {
    $indexed = &self::$indexed[get_class($Model)];

    foreach ($Model->_indexstore as $key => $val)
      $this->_set($Model, $indexed, $key, $val);

    return true;
  }

  private function _set(IModel $Model, &$indexed, $key, $val)
  {
    if (!isset($indexed[$key][$val]))
      $indexed[$key][$val] = new SplObjectStorage();

    $indexed[$key][$val]->attach($Model);
  }

  private function RemoveIndex(IModel $Model): bool
  {
    $indexed = &self::$indexed[get_class($Model)];

    foreach ($Model->_indexstore as $key => $val)
      $this->_remove($Model, $indexed, $key, $val);

    return true;
  }

  private function _remove(IModel $Model, &$indexed, $key, $val)
  {
    $indexed[$key][$val]->detach($Model);

    if (!$indexed[$key][$val]->count())
      unset($indexed[$key][$val]);
  }
}
