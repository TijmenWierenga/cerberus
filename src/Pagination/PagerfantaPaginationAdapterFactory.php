<?php

namespace Cerberus\Pagination;

use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\RouterInterface;

class PagerfantaPaginationAdapterFactory
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function create(Pagerfanta $paginator, ServerRequestInterface $request): PagerfantaPaginatorAdapter
    {
        return new PagerfantaPaginatorAdapter($paginator, function (int $page) use ($request) {
            $route = $request->getAttributes()['_route'];
            $inputParams = $request->getAttributes()['_route_params'];
            $newParams = array_merge($inputParams, $request->getQueryParams());
            $newParams['page'] = $page;
            return $this->router->generate($route, $newParams, 0);
        });
    }
}
