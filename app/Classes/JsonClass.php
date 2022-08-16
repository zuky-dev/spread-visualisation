<?php

namespace App\Classes;

class JsonClass
{
   private ?object $attributes;
   private ?string $default;

   public function __construct(?string $json, $defaultField = null)
   {
       $this->attributes = is_null($json) ? null : json_decode($json);
       $this->default = $defaultField;
   }

   public function __get($name)
   {
        if (!is_null($this->attributes) && isset($this->attributes->{$name})) {
            return $this->attributes->{$name};
        }
        else {
            if (!is_null($this->default)) {
                return $this->attributes->{$this->default};
            } else {
                return null;
            }
        }
   }

   public function toArray(): ?array
   {
        if (is_null($this->attributes)) {
            return null;
        }

        $array = [];

        foreach ($this->attributes as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
   }
}