<?php

/**
 * New data type in PHP
 * @author Reza Khoirul Wijaya Putra
 * Created At : 2024-10-15
 */

namespace RKWP\Utils;

use ReflectionProperty;

class ObjectIterable implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    private $items = [];
    private $options;
    public function __construct($items = [], $caseInsensitive = true)
    {
       $this->options['caseInsensitive'] = $caseInsensitive;
        $items = json_decode(json_encode($items), true);
        foreach ($items as $key => $value) {
            $property = $caseInsensitive ? strtolower($key) : $key;
            if (is_array($value)) {
               $this->setItem($property, new ObjectIterable($value, $caseInsensitive));
            } else {
               $this->setItem($property, $value);
            }
        }
    }

    public function assign($items = [], $childClass = null, $caseInsensitive = true)
    {
        $items = json_decode(json_encode($items), true);

        $this->options['caseInsensitive'] = $caseInsensitive;
        foreach ($items as $key => $value) {
            $property = $caseInsensitive ? strtolower($key) : $key;
            if (is_array($value)) {
                if (property_exists($this, $property)) {
                    $reflection = new \ReflectionProperty($this, $property);
                    if ($reflection->hasType() && !$reflection->getType()->isBuiltin()) {
                        $className = $reflection->getType()->getName();
                        $this->setItem($property, (new $className())->set($value));
                    }
                } else {
                       $this->setItem($property, $childClass ? (new $childClass)->set($value) : new ObjectIterable($value, $caseInsensitive));
                }
            } else {
               $this->setItem($property, $value);
            }
        }
        return $this;
    }

    function setItem($key, $value): void {
       $this->items[$key] = $value;
    }

    function getItems() {
       return $this->items;
    }

    public function __get($name)
    {
        $name = $this->options['caseInsensitive'] ? strtolower($name) : $name;
        return $this->items[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $name = $this->options['caseInsensitive'] ? strtolower($name) : $name;
        $this->items[$name] = $value;
    }

    public function offsetExists($offset): bool
    {
       return array_key_exists($this->options['caseInsensitive'] ? strtolower($offset) : $offset, $this->getItems());
    }

    public function offsetGet($offset): mixed
    {
        $offset = $this->options['caseInsensitive'] ? strtolower($offset) : $offset;
        return $this->items[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $offset = $this->options['caseInsensitive'] ? strtolower($offset) : $offset;
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        $offset = $this->options['caseInsensitive'] ? strtolower($offset) : $offset;
        unset($this->items[$offset]);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->getItems());
    }

    public function count(): int
    {
        return count($this->getItems());
    }

    public function toArray()
    {
        return collect($this->getItems())->toArray();
    }

    public function forceArray() {
       $items = json_decode(json_encode(collect($this->getItems())->toArray()), true);
       return $items;
   }

    public function toCollect(): \Illuminate\Support\Collection
    {
        return collect($this->getItems());
    }

    public function jsonSerialize(): mixed
    {
        return $this->getItems();
    }

    public function sumGroupBy($groupingCriteria, $sumColumn = null, $castTo = 'int', $callback = null): array
    {
        $grpData = collect($this->getItems())->groupBy($groupingCriteria);

        $res = [];
        foreach ($grpData as $key => $items) {
            $key = is_callable($callback) ? $callback($key) : $key;
            $res[$key] = collect($items)
                ->map(function ($item) use ($sumColumn, $castTo) {
                    if ($castTo == 'int') {
                        return (int) $item[$sumColumn];
                    }
                    if ($castTo == 'float') {
                        return (float) $item[$sumColumn];
                    }
                })
                ->sum();
        }

        return $res;
    }

    function __destruct()
    {
        unset($this->childClass);
    }

    // public function countGroupBy($groupingCriteria) {
    //     $grpData = collect($this->items)->groupBy($groupingCriteria);
    //     $res = [];

    //     foreach ($grpData as $key => $items) {
    //         $res[$key] = count($items);
    //     }

    //     return $res;
    // }

    // public function countNotEmptyGroupBy($groupingCriteria, $countColumn) {
    //     $grpData = collect($this->items)->groupBy($groupingCriteria);
    //     $res = [];

    //     foreach ($grpData as $key => $items) {
    //         $res[$key] = collect($items)->map(function ($item) use ($countColumn) {
    //             if ($item[$countColumn]) {
    //                 return $item;
    //             }
    //         })->count();
    //     }

    //     return $res;
    // }
}