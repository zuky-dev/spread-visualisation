<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand as GenCommand;

abstract class GeneratorCommand extends GenCommand
{
    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);
        $nameClean = str_replace($this->type, '', $class);

        $str = str_replace(['{{ name }}', '{{name}}'], $nameClean, $stub);

        return str_replace(['DummyClass', '{{ class }}', '{{class}}'], $class, $str);
    }
}
