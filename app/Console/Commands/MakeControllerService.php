<?php

namespace App\Console\Commands;

class MakeControllerService extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:controller-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Controller';

    /**
     * Class type that is being created.
     * If command is executed successfully you'll receive a
     * message like this: $type created succesfully.
     * If the file you are trying to create already
     * exists, you'll receive a message
     * like this: $type already exists!
     */
    protected $type = 'Controller';

    /**
     * Specify your Stub's location.
     */
    protected function getStub()
    {
        return  base_path() . '/stubs/controller-service.stub';
    }

    /**
     * The root location where your new file should
     * be written to.
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers';
    }
}
