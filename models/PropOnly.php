<?php

namespace Models;

use JrAppBox\DatabaseDataWorker\LackKeyModel;

class PropOnly extends LackKeyModel
{
  const TABLE = 'prop_only';

  protected string $prop  = '';
  protected string $value = '';
}
