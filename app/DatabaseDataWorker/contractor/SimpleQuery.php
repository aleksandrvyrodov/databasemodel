<?php

namespace JrAppBox\DatabaseDataWorker\Contractor;

use JrAppBox\DatabaseDataWorker\Contractor\Core\Connector;
use JrAppBox\DatabaseDataWorker\Contractor\SimpleBuilder as SB;
use JrAppBox\DatabaseDataWorker\Error\DDWError;
use JrAppBox\DatabaseDataWorker\Error\DDWException;

class SimpleQuery
{
  protected string $table;
  private SB $SimpleBuilder;

  public function __construct(string $table)
  {
    $this->SimpleBuilder = new SB;
    $this->table  = $table;
  }

  private function query(string $sql, array $param, \Closure $prism, ?\PDOException &$error = null)
  {
    try {
      $PDO = Connector::Connect();
      $PDOSt = $PDO->prepare($sql);
      $PDOSt->execute($param);
      return $prism($PDO, $PDOSt);
    } catch (\PDOException $PDOEx) {
      DDWError::Add('Fail query', 1000, $PDOEx);
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

  public function select()
  {
    $where = $this
      ->params()
      ->where(SB::WHERE);

    $table = $this->table;
    $sql = <<<SQL
      SELECT *
      FROM $table
      $where
      LIMIT 1
      SQL;

    $res = $this->query(
      $sql,
      [],
      fn ($PDO, $PDOSt) => $PDOSt->fetch()
    );
    return $res;
  }


  public function update($data)
  {
    $update = $data;
    $fields = implode(
      ',',
      (fn ($update) => array_walk($update, fn (&$item, $key) => $key && $item = "`$key` = :$key") ? $update : false)($update)
    );
    $param = $update; /* array_merge($update, [
      'k_' . $p_key  => $key,
    ]); */

    $where = $this
      ->params()
      ->where(SB::WHERE);

    $table = $this->table;
    $sql = <<<SQL
      UPDATE $table
      SET
        $fields
      $where
      LIMIT 1
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

  public function remove()
  {
    $table = $this->table;
    $where = $this
      ->params()
      ->where(SB::WHERE);
    $sql = <<<SQL
      DELETE
      FROM $table
      $where
      LIMIT 1
      SQL;

    $res = $this->query(
      $sql,
      [],
      fn ($PDO, $PDOSt) => (bool)$PDOSt->rowCount()
    );

    return $res;
  }

  public function list()
  {
    [$head_query, $foot_query] = $this->params(SB::BUILD);

    $table = $this->table;
    $sql = <<<SQL
      $head_query
      FROM $table
      $foot_query
      SQL;

    $res = $this->query(
      $sql,
      [],
      fn ($PDO, $PDOSt) => $PDOSt->fetchAll()
    );

    return $res;
  }
}
