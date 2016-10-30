<?php

namespace Elspy\Repository;

use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection as IlluminateCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\Serializer\DataArraySerializer;
use Elspy\Repository\Fractal\Adapter\IlluminatePaginatorAdapter;
use Elspy\Repository\Contracts\PresenterInterface;
use Elspy\Repository\Exceptions\PresenterException;

abstract class AbstractPresenter implements PresenterInterface
{
    /**
     * @var string
     */
    protected $resourceKeyItem = null;
    /**
     * @var string
     */
    protected $resourceKeyCollection = null;
    /**
     * @var \League\Fractal\Manager
     */
    protected $fractal = null;
    /**
     * @var \League\Fractal\Resource\Collection
     */
    protected $resource = null;
    /**
     * @throws Exception
     */

    public $includes = [];

    public function __construct()
    {
        $this->fractal = new Manager();
        $this->includes = new IlluminateCollection();
        $this->boot();
    }
    
    public function boot() {
        $this->bootSerializer();
    }

    public function bootSerializer()
    {
        $serializer = $this->serializer();
        if (! $serializer instanceof SerializerAbstract) {
            return $this->throwsNeedsSerializerAbstract($serializer);
        }
        $this->fractal->setSerializer(new $serializer());
    }

    public function include($include)
    {
        if (is_array($include)) {
            return collect($include)->each(function($inc){
                $this->pushInclude($inc);
            });
        }
        return $this->pushInclude($include);
    }

    public function pushInclude($include) {
        if ($this->includes->contains($include)){
            return $this;
        }
        $this->includes->push($include);
    }
    /**
     * @return $this
     */
    protected function parseIncludes()
    {
        $this->fractal->parseIncludes($this->includes->toArray());
        return $this;

    }
    /**
     * Get Serializer
     *
     * @return SerializerAbstract
     */
    public function serializer()
    {
        return new DataArraySerializer;
    }
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    abstract public function getTransformer();
    /**
     * Prepare data to present
     *
     * @param $data
     *
     * @return mixed
     * @throws Exception
     */
    public function present($data)
    {
        $this->parseIncludes();
        if ($data instanceof EloquentCollection) {
            $this->resource = $this->transformCollection($data);
        } elseif ($data instanceof AbstractPaginator) {
            $this->resource = $this->transformPaginator($data);
        } else {
            $this->resource = $this->transformItem($data);
        }
        return $this->fractal->createData($this->resource)->toArray();
    }
    /**
     * @param $data
     *
     * @return Item
     */
    protected function transformItem($data)
    {
        return new Item($data, $this->getTransformer(), $this->resourceKeyItem);
    }
    /**
     * @param $data
     *
     * @return \League\Fractal\Resource\Collection
     */
    protected function transformCollection($data)
    {
        return new Collection($data, $this->getTransformer(), $this->resourceKeyCollection);
    }
    /**
     * @param AbstractPaginator|LengthAwarePaginator|Paginator $paginator
     *
     * @return \League\Fractal\Resource\Collection
     */
    protected function transformPaginator($paginator)
    {
        $collection = $paginator->getCollection();
        $resource = new Collection($collection, $this->getTransformer(), $this->resourceKeyCollection);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        return $resource;
    }

    public function throwsNeedsSerializerAbstract($serializer)
    {
        throw new PresenterException("Needs Serializer Abstract Class");
    }
}