<?php

namespace JrAppBox\DatabaseDataWorker\Model;

interface IModel
{
  function state(): int;
  function ready(): bool;
  function exists(): bool;
  function error(): bool;

  function load(?array &$raw = null): IModel;
  function save(): IModel;
  function remove(): IModel;

  function __isset($name): bool;
  function __get(string $name);

  function getHash(): ?string;
  function prop(string $name);
  function setProp(string $name = '', $value, &$mirror = null): IModel;
}
