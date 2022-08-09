<?php

namespace JrAppBox\DatabaseDataWorker\Contractor;

use JrAppBox\DatabaseDataWorker\Contractor\Core\Connector;
use JrAppBox\DatabaseDataWorker\Contractor\SimpleBuilder as SB;

class SimpleQuery implements IQuery
{
  public string $table;
  public string $p_key;
  private SB $SimpleBuilder;

  public function __construct(string $table, string $p_key)
  {
    $this->SimpleBuilder = new SB;
    $this->table  = $table;
    $this->p_key  = $p_key;
  }

  private function query(string $sql, array $param, \Closure $prism, ?\PDOException &$error = null)
  {
    try {
      $PDO = Connector::Connect();
      $PDOSt = $PDO->prepare($sql);
      $PDOSt->execute($param);
      return $prism($PDO, $PDOSt);
    } catch (\PDOException $PDOEx) {
      $error = $PDOEx;
      return false;
    }
  }

  public function params($opt = null)
  {
    if (is_null($opt))
      return $this->SimpleBuilder;
    else
      return $this
        ->SimpleBuilder
        ->option($opt);
  }

  public function select($key)
  {
    $p_key = $this->p_key;
    $param = [$key];
    $table = $this->table;
    $sql = <<<SQL
      SELECT *
      FROM $table
      WHERE `$p_key` = ?
      LIMIT 1
      SQL;

    $res = $this->query(
      $sql,
      $param,
      fn ($PDO, $PDOSt) => $PDOSt->fetch()
    );
    return $res;
  }


  public function update($key, $data)
  {
    $p_key = $this->p_key;
    $update = $data;

    $fields = implode(
      ',',
      (fn ($update) => array_walk($update, fn (&$item, $key) => $key && $item = "`$key` = :$key") ? $update : false)($update)
    );
    $param = array_merge($update, [
      'k_' . $p_key  => $key,
    ]);

    $table = $this->table;
    $sql = <<<SQL
      UPDATE $table
      SET
        $fields
      WHERE `$p_key` = :k_$p_key
      SQL;

    $res = $this->query(
      $sql,
      $param,
      fn () => true
    );

    return $res;
  }

  public function insert($data)
  {
    $insert = $data;
    $insert_fields = array_keys($insert);
    $fields = implode(',', $insert_fields);
    $values = implode(',', array_map(fn ($value) => ':' . $value, $insert_fields));
    $param = $insert;

    $table = $this->table;
    $sql = <<<SQL
      INSERT INTO $table ($fields)
      VALUES ($values)
      SQL;

    $res = $this->query(
      $sql,
      $param,
      fn ($PDO) => $PDO->lastInsertId()
    );

    return $res;
  }

  public function remove($key)
  {
    $p_key = $this->p_key;
    $param = [$key];
    $table = $this->table;
    $sql = <<<SQL
      DELETE FROM $table WHERE `$p_key` = ? LIMIT 1
      SQL;

    $res = $this->query(
      $sql,
      $param,
      fn ($PDO, $PDOSt) => (bool)$PDOSt->rowCount()
    );

    return $res;
  }


  public function list()
  {
    $param = $this->params(SB::BUILD);
    $table = $this->table;
    $sql = <<<SQL
      SELECT * FROM $table
      $param
      SQL;

    $res = $this->query(
      $sql,
      [],
      fn ($PDO, $PDOSt) => $PDOSt->fetchAll()
    );
    return $res;
  }
}
