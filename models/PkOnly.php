<?php

namespace Models;

use JrAppBox\DatabaseDataWorker\SingleKeyModel;

class PkOnly extends SingleKeyModel
{
  const TABLE  = 'pk_only';
  const AI     = false;
  const PK     = 'phone';
  const FK     = false;
  const UNIQUE = false;
  const INDEX  = 'name';

  protected ?int $phone = null;
  protected ?string $name = null;

  // static protected ?SingleKeyModel $first_Model = null;
}
