<?php
namespace Elpsy\Repository\Generators\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Elpsy\Repository\Exception\FileExistsException;
use Elpsy\Repository\Generators\EloquentRepositoryGenerator;
use Elpsy\Repository\Generators\EloquentRepositoryInterfaceGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RepositoryCommand extends Command
{
    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'ep:make:repository';
    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new repository.';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';
    /**
     * @var Collection
     */
    protected $generators = null;
    /**
     * Execute the command.
     *
     * @return void
     */
    public function fire()
    {
        $this->generators = new Collection();
        if ($this->option('plain')) {
            return $this->firePlain();
        }
        return $this->fireEloquent();
    }

    public function fireEloquent() {

        $this->generators->push(new EloquentRepositoryInterfaceGenerator(
            $this->argument('name'),
            $this->option('force')
        ));

        $this->generators->push(new EloquentRepositoryGenerator([
            'name'  => $this->argument('name'),
            'plain' => $this->option('plain'),
            'force' => $this->option('force')
        ]));

        foreach ($this->generators as $generator) {
            $generator->run();
        }
    }
    /**
     * The array of command arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return [
            [
                'name',
                InputArgument::REQUIRED,
                'The name of class being generated.',
                null
            ],
        ];
    }
    /**
     * The array of command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            [
                'plain',
                null,
                InputOption::VALUE_OPTIONAL,
                'Avoid Eloquent',
                null
            ],
            [
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force the creation if file already exists.',
                null
            ]
        ];
    }
}