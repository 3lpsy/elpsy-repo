<?php
namespace Elpsy\Repository\Generators;

use Elpsy\Repository\Generators\Migrations\SchemaParser;

/**
 * Class RepositoryInterfaceGenerator
 * @package Elpsy\Repository\Generators
 */
class EloquentRepositoryInterfaceGenerator extends Generator
{
    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'repository/interface';

    public function getNamespace(){
        
    }

}