<?php

namespace JrAppBox\DatabaseDataWorker\Model;

interface IModel
{
  function load(?array &$raw = null): IModel;
  function save(): IModel;
  function remove(): IModel;

  public function prop(string $name);
  public function setProp($name = '', $value, $mirror = null): IModel;
}
