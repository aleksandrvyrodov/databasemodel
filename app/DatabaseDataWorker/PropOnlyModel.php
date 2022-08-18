<?php

namespace JrAppBox\DatabaseDataWorker;

use JrAppBox\DatabaseDataWorker\Error\DDWError;
use JrAppBox\DatabaseDataWorker\Model\IActionModel;

abstract class PropOnlyModel extends DefaultModel implements IActionModel
{
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
