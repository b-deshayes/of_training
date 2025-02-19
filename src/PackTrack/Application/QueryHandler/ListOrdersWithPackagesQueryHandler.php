<?php

namespace App\PackTrack\Application\QueryHandler;

use App\PackTrack\Application\Query\ListOrdersWithPackagesQuery;
use App\PackTrack\Domain\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
class ListOrdersWithPackagesQueryHandler
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(ListOrdersWithPackagesQuery $query): array
    {
        return $this->orderRepository->findOrdersWithPackagesPaginated(
            $query->getPage(),
            $query->getPageSize()
        );
    }
}
