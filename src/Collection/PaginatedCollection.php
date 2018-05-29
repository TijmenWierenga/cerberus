<?php

namespace Cerberus\Collection;

use Pagerfanta\Pagerfanta;

class PaginatedCollection
{
    /**
     * @var Pagerfanta
     */
    private $paginator;
    /**
     * @var iterable
     */
    private $items;

    public function __construct(iterable $items, Pagerfanta $paginator)
    {
        $this->paginator = $paginator;
        $this->items = $items;
    }

    /**
     * @return Pagerfanta
     */
    public function getPaginator(): Pagerfanta
    {
        return $this->paginator;
    }

    /**
     * @return iterable
     */
    public function getItems(): iterable
    {
        return $this->items;
    }
}
