<?php

namespace Models;

use JrAppBox\DatabaseDataWorker\PkOnlyModel;

class PkOnly extends PkOnlyModel
{
  const TABLE = 'pk_only';
  const AI    = false;
  const PK    = 'phone';
  const FK    = false;
  const INDEX = false;

  protected ?int $phone = null;
  protected ?string $name = null;
}
