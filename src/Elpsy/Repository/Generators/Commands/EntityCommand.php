<?php
namespace Elpsy\Repository\Generators\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class EntityCommand extends Command
{
    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'ep:make:entity';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new model, repostiory, presenter, transformer, and binding.';

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

        if ($this->ask('Would you like to create a Model? [y|N]')) {
            $this->call('ep:make:model', [
                'name'        => $this->argument('name'),
                '--plain'     => $this->option('plain'),
                '--force'     => $this->option('force')
            ]);
        }

        if ($this->ask('Would you like to create a Repository? [y|N]')) {
            $this->call('ep:make:repository', [
                'name'        => $this->argument('name'),
                '--plain'     => $this->option('plain'),
                '--force'     => $this->option('force')
            ]);
        }

        if ($this->ask('Would you like to create a Presenter? [y|N]')) {
            $this->call('ep:make:presenter', [
                'name'    => $this->argument('name'),
                '--plain' => $this->option('plain'),
                '--force' => $this->option('force'),
            ]);
        }

        if ($this->ask('Would you like to create a Transformer? [y|N]')) {
            $this->call('ep:make:transformer', [
                'name'    => $this->argument('name'),
                '--plain' => $this->option('plain'),
                '--force' => $this->option('force')
            ]);
        }

        if ($this->ask('Would you like to create a bindings? [y|N]')) {
            $this->call('ep:make:transformer', [
                'name'    => $this->argument('name'),
                '--plain' => $this->option('plain'),
                '--force' => $this->option('force')
            ]);
        }
    }

    public function ask($question)
    {
        if ($this->option('yes')){
            return $this->confirm($question);
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
                'Avoid Eloquent.',
                null
            ],
            [
                'yes',
                'y',
                InputOption::VALUE_NONE,
                'Silent.',
                null
            ],
            [
                'force',
                'null',
                InputOption::VALUE_NONE,
                'Force the creation if file already exists.',
                null
            ]
        ];
    }
}