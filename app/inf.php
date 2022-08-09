<?php

use JrAppBox\Autoloader;

require_once __DIR__ . '/Autoloader.php';

(new Autoloader)
  ->addNamespace('JrAppBox\DatabaseDataWorker', __DIR__ . '/DatabaseDataWorker')
  ->register();
