<?php

#region INITAL
use JrAppBox\Autoloader;
use Models\{
  FkOnly,
  FkPk,
  FkPkTwice,
  FkThisPk,
  PkOnly,
  PkOnlyAi,
  PkTwice,
  PkTwiceAi,
  PropOnly,
  Table1,
  Table2,
  Table3,
  Table4,
  Table5,
};

require_once __DIR__ . '/app/inf.php';

(new Autoloader)
  ->addNamespace('Models', __DIR__ . '/models/')
  ->register();
#endregion

$PkOnly = new PkOnly;
$PkOnlyAi = new PkOnlyAi;

echo "<pre>";
var_dump($PkOnly);
var_dump($PkOnlyAi);
echo "</pre>";
