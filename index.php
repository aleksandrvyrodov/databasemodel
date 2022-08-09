<?php

#region INITAL
use JrAppBox\Autoloader;
use JrAppBox\DatabaseDataWorker\Contractor\SimpleBuilder;
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

$SB = new SimpleBuilder;

$SB
  /* ->select('COND(a,\'\',b)', 'fork')
  ->select('SUM(c,b)', 'sum') */
  /* ->where('xx > 5')
  ->where('xx < 7')
  ->where('yy = 1', 'OR')
  ->order('name', 'ASC')
  ->order('id', 'DESC')
  ->limitation(1) */
  ;

echo "<pre>";
var_dump($SB->build());
var_dump($PkOnly);
var_dump($PkOnlyAi);
echo "</pre>";
