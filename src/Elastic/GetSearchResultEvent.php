<?php

namespace Digikala\Elastic;

use Elastica\ResultSet;
use Symfony\Component\EventDispatcher\Event;

class GetSearchResultEvent extends Event
{
    const ELASTIC_SEARCH_BEFORE = 'search.before';
    const ELASTIC_SEARCH_AFTER = 'search.after';

    /**
     * @var string
     */
    private $className;

    /**
     * @var array
     */
    private $criteria;

    /**
     * @var array
     */
    private $orderBy;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var ResultSet
     */
    private $result;

    public function __construct(
        string  $className,
        array $criteria = null,
        array $orderBy = null,
        int $limit = null,
        int $offset = null,
        ResultSet $result = null
    ) {
        $this->className = $className;
        $this->criteria = $criteria;
        $this->orderBy = $orderBy;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return array
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @return array
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return ResultSet
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param ResultSet $result
     */
    public function setResult(ResultSet $result)
    {
        $this->result = $result;
    }
}
