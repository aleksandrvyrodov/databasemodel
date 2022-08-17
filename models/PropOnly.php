<?php

namespace Models;

use JrAppBox\DatabaseDataWorker\PropOnlyModel;

class PropOnly extends PropOnlyModel
{
  const TABLE = 'prop_only';

  protected string $prop  = '';
  protected string $value = '';
  protected string $valuex = '';
}
