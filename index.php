<pre>
<?php
set_time_limit(0);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

#region INITAL
use JrAppBox\Autoloader;
use JrAppBox\DatabaseDataWorker\Contractor\SimpleBuilder;
use JrAppBox\DatabaseDataWorker\Error\DDWError;
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

// $PkOnly = new PkOnly;
// $PkOnlyAi = new PkOnlyAi;

/*-----------------------------------------*/

/* var_dump(
  PropOnly::Create()
    ->setProp('prop', 'table-2')
    ->setProp('value', 'color')
    ->load()
    ->generateHash()
); */

/* $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';


for ($i = 0; $i < 50000; $i++) {
  PropOnly::Init(PropOnly::class)
    ->setProp('prop', substr(str_shuffle($permitted_chars), 0, 16))
    ->setProp('value', substr(str_shuffle($permitted_chars), 0, 16))
    ->save();
} */

/*-----------------------------------------*/

try {
  // $PkOnly = PkOnly::Get(258);

  PkOnly::GetAll(3);
  $list_PkOnly = PkOnly::GetAll(3);


  $list_PkOnly[0]
    ->setProp('xxx', 123)
    ->setProp('name', 'Xibi')
    ->save();

  // var_dump($list_PkOnly);

  // $list_PkOnly[1]->remove();

  /* $list_PkOnly[0]
    ->setProp('name', 'Vax_UPD')
    ->save();

  $list_PkOnly[0]
    ->setProp('name', 'Vax')
    ->save();

  $list_PkOnly[0]
    ->setProp('name', 'Xibi')
    ->save(); */


  var_dump(PkOnly::GV()::$IndexedStorage);
  var_dump(PkOnly::GV()::$indexed);
  // var_dump($list_PkOnly[0]);
  // var_dump(PkOnly::GetAll(3));
  // var_dump(PropOnly::GetAll(1)[0]);


  #
} catch (\Throwable $th) {
  var_dump($th->getMessage());
  var_dump($th->getTrace());
  var_dump(DDWError::AllErrors());
}

/*-----------------------------------------*/
