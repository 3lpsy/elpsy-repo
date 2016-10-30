<?php
namespace Elpsy\Repository\Contracts;

use Exception;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Elpsy\Repository\Presenter\PresentableInterface;
use Elpsy\Repository\Presenter\PresenterInterface;
use Elpsy\Repository\Exception\RepositoryException;

/**
 * Class BaseRepository
 * @package Prettus\Repository\Eloquent
 */
interface RepositoryInterface
{

    public function boot();

    public function setResource($resource);

    public function getResource();

    public function resetResource();

    public function presenter($key);

    public function include($include);

    public function includeWith($includeWith);

    public function getPresenter($key);
    
    public function parse($presenter = null);

    public function throwsNeedsPresenterClass($presenter);

    public function throwsNoPresenterExists($key);


}