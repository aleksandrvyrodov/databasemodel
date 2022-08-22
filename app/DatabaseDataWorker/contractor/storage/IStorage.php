<?php

namespace JrAppBox\DatabaseDataWorker\Contractor\Storage;

use JrAppBox\DatabaseDataWorker\Model\IModel;

interface IStorage
{
  function Set(IModel $Model): bool;
  function Get(string $hash): ?IModel;
  function Remove(IModel $Model): bool;
  function Update(IModel $Model): bool;
  function Move(IModel $Model): bool;
  function Exists(IModel $Model): bool;
}
