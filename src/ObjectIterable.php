<?php

/**
 * New data type in PHP
 * @author Reza Khoirul Wijaya Putra
 * Created At : 2024-10-15
 */

 namespace App\Helper;

 class ObjectIterable implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
 {
     public function __construct($items = [], $forceArray = false)
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
         return collect(get_object_vars($this))->toArray();
     }

     public function toCollect(): \Illuminate\Support\Collection
     {
         return collect(get_object_vars($this));
     }

     public function jsonSerialize(): mixed
     {
         return get_object_vars($this);
     }

     public function sumGroupBy($groupingCriteria, $sumColumn = null, $castTo = 'int', $callback = null): array {
         $grpData = collect(get_object_vars($this))->groupBy($groupingCriteria);

         $res = [];
         foreach ($grpData as $key => $items) {
                 $key = is_callable($callback) ? $callback($key) : $key;
                 $res[$key] = collect($items)->map(function ($item) use ($sumColumn, $castTo) {
                     if ($castTo == 'int') {
                         return (int)$item[$sumColumn];
                     }
                     if ($castTo == 'float') {
                         return (float)$item[$sumColumn];
                     }
                 })->sum();
         }

         return $res;
     }

     // public function countGroupBy($groupingCriteria) {
     //     $grpData = collect(get_object_vars($this))->groupBy($groupingCriteria);
     //     $res = [];

     //     foreach ($grpData as $key => $items) {
     //         $res[$key] = count($items);
     //     }

     //     return $res;
     // }

     // public function countNotEmptyGroupBy($groupingCriteria, $countColumn) {
     //     $grpData = collect(get_object_vars($this))->groupBy($groupingCriteria);
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
