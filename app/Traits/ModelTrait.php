<?php

namespace App\Traits;

use App\Classes\JsonClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Illuminate\Support\Str;

trait ModelTrait
{
    use HasFactory;
    use SoftDeletes;

    protected $json = [];

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->relations)) {
            $value = parent::getAttribute($key);
        } else {
            $value = parent::getAttribute(Str::snake($key));
        }

        if (in_array($key, $this->json) || in_array(Str::snake($key), $this->json)) {
            return new JsonClass($value, $this->defaultJsonField);
        } else {
            return $value;
        }
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        return parent::setAttribute(Str::snake($key), $value);
    }
}
