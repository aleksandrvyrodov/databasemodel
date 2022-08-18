<?php

namespace JrAppBox\DatabaseDataWorker\Model;

interface IActionModel extends IModel
{
  function load(?array &$raw = null): IActionModel;
  function save(): IActionModel;
  function remove(): IActionModel;
}
