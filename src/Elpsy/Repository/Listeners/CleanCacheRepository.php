<?php
namespace Elpsy\Repository\Listeners;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Elpsy\Repository\Contracts\RepositoryInterface;
use Elpsy\Repository\Events\AbstractRepositoryEvent;
use Elpsy\Repository\Helpers\CacheKeys;
/**
 * Class CleanCacheRepository
 * @package Elpsy\Repository\Listeners
 */
class CleanCacheRepository
{
    /**
     * @var CacheRepository
     */
    protected $cache = null;
    /**
     * @var RepositoryInterface
     */
    protected $repository = null;
    /**
     * @var Model
     */
    protected $model = null;
    /**
     * @var string
     */
    protected $action = null;
    /**
     *
     */
    public function __construct()
    {
        $this->cache = app(config('elpsy.cache.repository', 'cache'));
    }
    /**
     * @param RepositoryEventBase $event
     */
    public function handle(AbstractRepositoryEvent $event)
    {
        try {
            $cleanEnabled = config("elpsy.cache.clean.enabled", true);
            if ($cleanEnabled) {
                $this->repository = $event->getRepository();
                $this->model = $event->getModel();
                $this->action = $event->getAction();
                if (config("elpsy.cache.clean.on.{$this->action}", true)) {
                    $cacheKeys = CacheKeys::getKeys(get_class($this->repository));
                    if (is_array($cacheKeys)) {
                        foreach ($cacheKeys as $key) {
                            $this->cache->forget($key);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}