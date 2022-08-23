<?php

namespace JrAppBox\DatabaseDataWorker;

use JrAppBox\DatabaseDataWorker\Contractor\Storage\IndexedStorage;
use JrAppBox\DatabaseDataWorker\Contractor\Storage\IStorage;
use JrAppBox\DatabaseDataWorker\Model\IActionModel;
use JrAppBox\DatabaseDataWorker\Error\DDWError;

abstract class MultipleKeyModel extends DefaultModel implements IActionModel
{
}
