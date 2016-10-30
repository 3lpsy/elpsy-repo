<?php
namespace Elpsy\Repository\Repositories;

use Exception;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Elpsy\Repository\Presenter\PresentableInterface;
use Elpsy\Repository\Presenter\PresenterInterface;
use Elpsy\Repository\Contracts\RepositoryInterface;
use Elpsy\Repository\Exception\RepositoryException;

/**
 * Class BaseRepository
 * @package Prettus\Repository\Eloquent
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Model
     */
    protected $resource;

    /**
     * @var Model
     */
    protected $presenters;

    /**
     * @var Model
     */
    protected $presenter;

    /**
     * @var Model
     */
    protected $skipPresenter;

    /**
     * @var Model
     */
    protected $results;


    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->boot();
    }

    /**
     *
     */
    public function boot()
    {
        $this->bootResource();
        $this->bootPresenters();
        return $this;
    }


    protected function bootResource()
    {
        $resource = $this->makeResource();
        $this->setResource($resource);
    }

    /**
     * @return Model
     * @throws RepositoryException
     */
    protected function makeResource()
    {
        $resource = $this->app->make($this->resource());
        return $resource;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @throws RepositoryException
     */
    public function resetResource()
    {
        $this->makeResource();
    }

    /**
     * [bootPresenters description]
     * @return [type] [description]
     */
    protected function bootPresenters()
    {
        $this->presenters = new Collection;
        $this->presenterIncludes = [];

        $this->presenters();
    }

    /**
     * [addPresenter description]
     * @param [type] $key       [description]
     * @param [type] $presenter [description]
     */
    protected function addPresenter($presenter, $key, $activate = false)
    {
        if ($presenter = $this->makePresenter($presenter)) {
            $this->presenters->put($key, $presenter);
            return $activate || ! $this->presenter ? $this->presenter($key): $this;
        }
    }

    public function presenter($key)
    {
        $this->presenter = $this->getPresenter($key);
        return $this;
    }

    public function include($include)
    {
        $this->presenter->include($include);
        return $this;
    }

    public function includeWith($includeWith)
    {
        return $this->include($includeWith);
   }


    public function getPresenter($key) {
        if (! $this->presenters->has($key)) {
            $this->throwsNoPresenterExists();
        }
        return $this->presenters->get($key);
    }

    /**
     * @param null $presenter
     *
     * @return PresenterInterface
     * @throws RepositoryException
     */
    protected function makePresenter($presenter)
    {
        if (!is_null($presenter)) {
            $presenter = is_string($presenter) ? $this->app->make($presenter) : $presenter;
            if (! $presenter instanceof PresenterInterface) {
                return $this->throwsNeedsPresenterClass($presenter);
            }
            return $presenter;
        }
        return $this->throwsNeedsPresenterClass($presenter);
    }



    /**
     * Wrapper result data
     *
     * @param mixed $result
     *
     * @return mixed
     */
    public function parse()
    {
        $results = $this->results;
        if (!$this->skipPresenter && $this->presenter instanceof PresenterInterface) {
            if ($results instanceof Collection || $results instanceof LengthAwarePaginator) {
                $results->each(function ($model) {
                    if ($model instanceof PresentableInterface) {
                        $model->presenter($this->presenter);
                    }
                    return $model;
                });
            } elseif ($results instanceof Presentable) {
                $results = $results->presenter($this->presenter);
            }
            if (!$this->skipPresenter) {
                return $this->presenter->present($results);
            }
        }
        return $results;
    }

    public function throwsNeedsPresenterClass($presenter) {
        $presenter = get_class($presenter);
        throw new RepositoryException("Class {$presenter} must be an instance of \Elpsy\Repository\\Presenters\PresenterInterace");
    }
    public function throwsNoPresenterExists($key) {
        throw new RepositoryException("Presenter: {$key} does not exists.");
    }


}