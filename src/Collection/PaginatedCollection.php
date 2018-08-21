<?php

namespace Cerberus\Collection;

use Pagerfanta\Pagerfanta;

class PaginatedCollection
{
    /**
     * @var Pagerfanta
     */
    private $paginator;

    public function __construct(Pagerfanta $paginator)
    {
        $this->paginator = $paginator;
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
        return $this->paginator->getCurrentPageResults();
    }
}
