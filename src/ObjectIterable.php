<?php

/**
 * New data type in PHP
 * @author Reza Khoirul Wijaya Putra
 * Created At : 2024-10-15
 */

namespace RKWP\Utils;

class ObjectIterable implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    public function __construct($items = [])
    {
        $items = json_decode(json_encode($items), true);

        foreach ($items as $key => $value) {
            $property = strtolower($key);
            if (is_array($value)) {
                $this->{$property} = new ObjectIterable($value);
            } else {
                $this->{$property} = $value;
            }
        }
    }

    public function assign($items = []) {
        foreach ($items as $key => $value) {
            $property = strtolower($key);
            if (is_array($value)) {
                $this->{$property} = new ObjectIterable($value);
            } else {
                $this->{$property} = $value;
            }
        }
        return $this;
    }

    public function __get($name)
    {
        $name = strtolower($name);
        return $this->{$name} ?? null;
    }

    public function __set($name, $value)
    {
        $name = strtolower($name);
        $this->{$name} = $value;
    }

    public function offsetExists($offset): bool
    {
        return property_exists($this, strtolower($offset));
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

    public function count(): int
    {
        return count(get_object_vars($this));
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function toCollect(): \Illuminate\Support\Collection
    {
        return collect(get_object_vars($this));
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}