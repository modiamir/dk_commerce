<?php

namespace Digikala\EventListener;

use Digikala\Elastic\GetSearchResultEvent;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ElasticSearchSubscriber implements EventSubscriberInterface
{
    const CACHE_TTL = 60;
    /**
     * @var \Symfony\Component\Validator\Mapping\Cache\CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            GetSearchResultEvent::ELASTIC_SEARCH_BEFORE => 'beforeSearch',
            GetSearchResultEvent::ELASTIC_SEARCH_AFTER => 'afterSearch',
        ];
    }

    /**
     * @param \Digikala\Elastic\GetSearchResultEvent $event
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function beforeSearch(GetSearchResultEvent $event)
    {
        $cacheKey = $this->getCacheKey($event);

        $result = $this->cache->get($cacheKey);

        if ($result) {
            $event->setResult(unserialize($result));
        }
    }

    /**
     * @param \Digikala\Elastic\GetSearchResultEvent $event
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function afterSearch(GetSearchResultEvent $event)
    {
        $cacheKey = $this->getCacheKey($event);

        $this->cache->set(
            $cacheKey,
            serialize($event->getResult()),
            self::CACHE_TTL
        );
    }

    private function getCacheKey(GetSearchResultEvent $event) {
        $arguments = [
            $event->getCriteria(),
            $event->getOrderBy(),
            $event->getOffset(),
            $event->getLimit(),
        ];
        return hash('sha256', sprintf('search.%s.%s',$event->getClassName(), json_encode($arguments)));
    }
}