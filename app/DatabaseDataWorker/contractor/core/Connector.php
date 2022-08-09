<?php

namespace JrAppBox\DatabaseDataWorker\Contractor\Core;

class Connector
{
  const CLEAR   = 0b01;
  const STORAGE = 0b10;
  const REFRESH = 0b11;

  protected         \PDO   $Connecton;
  protected static ?object $Instance = null;

  #region Connection
  protected function __construct()
  {
    $this->mount_connect();
  }

  protected static function init()
  {
    if (empty(static::$Instance))
      static::$Instance = new static();

    return static::$Instance;
  }

  private function connect_pdo(): \PDO
  {
    $db_host = 'localhost';
    $db_name = 'models';
    $db_user = 'root';
    $db_pass = '';

    return new \PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8;", $db_user, $db_pass, array(
      \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
      \PDO::ATTR_EMULATE_PREPARES   => false,
      \PDO::ATTR_STRINGIFY_FETCHES  => false
    ));
  }

  private function mount_connect(): Connector
  {
    $this->Connecton = $this->connect_pdo();
    return $this;
  }

  static public function Connect(int $mode = Connector::STORAGE): \PDO
  {
    switch ($mode) {
      case Connector::CLEAR:
        return self::init()
          ->connect_pdo();
      case Connector::STORAGE:
        return self::init()
          ->Connecton;
      case Connector::REFRESH:
        return self::init()
          ->mount_connect()
          ->Connecton;
      default:
        throw new \Exception(__LINE__ . ':UNDEFINED_MODE_CONNECT');
        break;
    }
  }
  #endregion
}
