<?php

namespace App\Libraries;

class ArrayLibrary
{
    protected  $array;

    public function __construct($array = null)
    {
        $this->array = $array;
    }


    public function filter($callback)
    {
        $this->array = array_filter($this->array,  $callback);
        return $this;
    }

    public function map($callback)
    {
        $this->array = array_map($callback, $this->array);
        return $this;
    }
    public function reduce($callback)
    {
        $this->array = array_reduce($this->array, $callback);
        return $this;
    }
  
    public function get()
    {
        return $this->array;
    }

    public function toSnakeCase()
    {
       $keys = array_keys($this->array); 
       $values = array_values($this->array); 

       $newKeys = array_map(fn($key)=> $key.'_', $this->array);

       return $keys;
    }
}
