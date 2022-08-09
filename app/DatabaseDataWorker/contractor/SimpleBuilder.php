<?php

namespace JrAppBox\DatabaseDataWorker\Contractor;

class SimpleBuilder
{
  const BUILD = true;

  const ACTION = 0b111 << 5;
  const STORAGE = 0b100 << 5;
  const CLEAN = 0b010 << 5;
  const CLEAN_STORAGE = 0b1 << 5;

  const SLEEP_STORAGE = true;

  const CHAIN = 0b1 << 4;
  const FREE  = 0b1 << 3;
  const UNLIMIT  = 0;
  const LIMIT  = 'LIMIT';
  const OFFSET = 0b1 << 2;
  const WHERE  = 'WHERE';
  const ORDER  = 'ORDER BY';

  const RAND = 'RAND()';
  const AND  = 'AND';
  const OR  = 'OR';
  const ASC  = 'ASC';
  const DESC  = 'DESC';

  private array $where = [];
  private array $order = [];
  private int $offset = 0;
  private int $limit = 0;
  private string $free = '';

  private array $_storage = [];
  private bool  $_storage_sleep = false;

  private function prop(&$prop, ...$val)
  {
    $cache = $prop;
    if (count($val)) {
      $prop = $val[0];
      return $this;
    } else
      switch (gettype($prop)) {
        case 'integer':
          $prop = 0;
          break;
        case 'array':
          $prop = [];
          break;
        case 'string':
          $prop = '';
          break;
        default:
          $prop = null;
          break;
      }
    return $cache;
  }

  private function &propIterator(): \Generator
  {
    foreach (array_filter((new \ReflectionClass($this))
        ->getProperties(
          \ReflectionProperty::IS_PRIVATE
        ),
      fn ($RefProp) => !($RefProp->name[0] == '_')
    ) as $RefProp)
      yield $RefProp->name => $this->{$RefProp->name};
  }

  public function option($params)
  {
    switch (true) {
      case $params === self::BUILD:
        return $this->build();
        break;
      case is_array($params):
        $fn_storage = fn () => true;
        if (array_key_exists(self::ACTION, $params))
          switch ($params) {
            case $params[self::ACTION] & self::CLEAN:
              $this->clean($params[self::ACTION] & self::CLEAN_STORAGE);
              break;
            case $params[self::STORAGE] & self::STORAGE:
              $fn_storage = fn () => $this->storage();
              break;
          }

        if (array_key_exists(self::FREE, $params))
          $this->free($params[self::FREE]);
        else {
          $fn = function ($key, $reason) use ($params) {
            if (is_string($params[$key]))
              $this->{$reason}($params[$key]);
            elseif (is_array($params[$key])) {
              $$reason = $params[$key];
              if (!is_array($$reason[0]))
                $$reason = [$$reason];
              foreach ($$reason as $item)
                if (isset($item[1]))
                  $this->{$reason}($item[0], $item[1]);
                else
                  $this->{$reason}($item[0]);
            }
          };

          if (array_key_exists(self::WHERE, $params))
            $fn(self::WHERE, 'where');

          if (array_key_exists(self::ORDER, $params))
            $fn(self::ORDER, 'order');

          if (array_key_exists(self::LIMIT, $params)) {
            if (is_numeric($params[self::LIMIT]))
              $this->limitation($params[self::LIMIT]);
            elseif (is_array($params[self::LIMIT])) {
              $limitation = $params[self::LIMIT];
              $offset = $limitation[1] ?? 0;
              $limit = $limitation[0] ?? self::UNLIMIT;
              $this->limitation($limit, $offset);
            }
          }
        }

        $fn_storage();

        if (array_key_exists(self::CHAIN, $params))
          return $params[self::CHAIN];
        break;
    }

    return $this;
  }

  public function storage(bool $sleep = false)
  {
    $this->_storage_sleep = true;
    if (!$sleep)
      foreach ($this->propIterator() as $name => &$prop)
        $this->_storage[$name] = $prop;

    return $this;
  }

  public function clean(bool $clean_storage = false)
  {
    if ($clean_storage)
      $this->_storage = [];
    foreach ($this->propIterator() as &$prop)
      $this->prop($prop);

    return $this;
  }

  public function chain($chain)
  {
    return $chain;
  }

  public function where(string $where = null, string $cond = self::AND)
  {
    if (is_null($where)) {
      if (!$this->_storage_sleep)
        $storage = $this->_storage['where'] ?? [];
      $where = array_merge(
        $storage ?? [],
        $this->prop($this->where)
      );
      if (empty($where))
        return null;
      $where = array_map(fn ($where_item) => implode(' ', $where_item), $where);
      return self::WHERE . ' 1 ' . implode(' ', $where);
    } else
      return $this->prop($this->where[], [$cond, $where]);
  }

  public function order(?string $order = null, ?string $direction = null)
  {
    if (is_null($order)) {
      if (!$this->_storage_sleep)
        $storage = $this->_storage['order'] ?? [];
      $order = array_merge(
        $storage ?? [],
        $this->prop($this->order)
      );
      if (empty($order))
        return null;
      $order = array_map(fn ($order_item) => implode(' ', $order_item), $order);
      return self::ORDER . ' ' . implode(',', $order);
    } else
      return $this->prop($this->order[], [$order, $direction]);
  }

  public function limitation(?int $limit = null, int $offset = 0)
  {
    if (is_null($limit)) {
      $limit = $this->prop($this->limit);
      $offset = $this->prop($this->offset);
      if ($limit === self::UNLIMIT) {
        if (!$this->_storage_sleep)
          $limit = $this->_storage['limit'] ?? self::UNLIMIT;
        if ($limit === self::UNLIMIT)
          return null;
      }
      $limitation = [];
      if ($offset)
        $limitation[] = $offset;
      elseif (!$this->_storage_sleep && isset($this->_storage['offset']))
        $limitation[] = $this->_storage['offset'];
      $limitation[] = $limit;
      return self::LIMIT . ' ' . implode(',', $limitation);
    } else
      return $this
        ->prop($this->limit, $limit)
        ->prop($this->offset, $offset);
  }

  public function free(?string $free = null)
  {
    if (is_null($free)) {
      $free = $this
        ->prop($this->free);
      if (empty($free))
        $free = $this->_storage['free'] ?? '';
      return $free;
    } else
      return $this
        ->clean()
        ->prop($this->free, $free);
  }

  public function build()
  {
    if (($free = $this->free()))
      $returned =  $free;
    else
      $returned = trim(implode(' ', [
        $this->where(),
        $this->order(),
        $this->limitation()
      ]));

    $this->_storage_sleep = false;

    return $returned;
  }
}
