<?php

namespace App\Utils;

use Illuminate\Support\Collection;

class ObjectIterable implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    private $items = [];

    public function __construct($items = [])
    {
        if (gettype($items) != 'array' && gettype($items) == 'string') {
            throw new \Exception('yah , constructor ObjectIterable string euy');
        }

        $items = json_decode(json_encode($items), true);

        if (is_array($items) && !empty($items) && array_keys($items) !== range(0, count($items) - 1)) {
            $this->items[] = $items; 
        } else {
            $this->items = $items; 
        }
    }

    public function __get($name)
    {
        $name = strtolower($name); 
        foreach ($this->items as $key => $value) {
            if (is_array($value) && array_key_exists($name, array_change_key_case($value, CASE_LOWER))) {
                return $value[$name];
            }
        }
        return null;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        $data = $this->items[$offset];
        if (is_array($data)) {
            return new RowData($data);
        }
        return null;
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    public function getIterator(): \Traversable
    {
        $res = new \ArrayIterator(array_map(function ($item) {
            $data = new RowData($item);

            return $data;
        }, $this->items));

        return $res;
    }

    public function count(): int
    {
        return count($this->items);
    }

    function toArray()
    {
        return $this->items;
    }

    function toCollect(): Collection
    {
        return collect($this->items);
    }

    public function jsonSerialize(): mixed
    {
        return $this->items; 
    }
}

class RowData implements \ArrayAccess, \IteratorAggregate, \JsonSerializable
{
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $property = strtolower($key);
            $this->{$property} = $value; 
        }
    }

    public function __get($name)
    {
        $name = strtolower($name);
        return $this->{$name} ?? null;
    }

    public function offsetExists($offset): bool
    {
        $offset = strtolower($offset);
        return property_exists($this, $offset);
    }

    public function offsetGet($offset): mixed
    {
        $offset = strtolower($offset);
        return $this->{$offset} ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $offset = strtolower($offset);
        $this->{$offset} = $value;
    }

    public function offsetUnset($offset): void
    {
        $offset = strtolower($offset);
        unset($this->{$offset});
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator(get_object_vars($this));
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
