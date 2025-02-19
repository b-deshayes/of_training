<?php

namespace App\PackTrack\Presentation\Controller;

use App\PackTrack\Application\Command\CreateOrderCommand;
use App\PackTrack\Application\Command\UpdateOrderStatusCommand;
use App\PackTrack\Domain\Repository\OrderRepositoryInterface;
use App\PackTrack\Domain\Repository\OrderStatusHistoryRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\PackTrack\Application\Query\ListOrdersWithPackagesQuery;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\PackTrack\Application\DTO\UpdateOrderStatusCommandDTO;
use App\PackTrack\Domain\Repository\PackageRepositoryInterface;
use App\PackTrack\Application\Command\CreatePackageCommand;
use App\PackTrack\Application\DTO\CreatePackageCommandDTO;
#[Route('/api/orders', name: 'api_orders_')]
class OrderController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private OrderRepositoryInterface $orderRepository,
        private OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private MessageBusInterface $queryBus,
        private PackageRepositoryInterface $packageRepository
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createOrder(#[MapRequestPayload] CreateOrderCommand $command): JsonResponse
    {
        $this->commandBus->dispatch($command);
        return $this->json(['status' => 'queued'], Response::HTTP_CREATED);
    }

    #[Route('/{id}/status', name: 'update_status', methods: ['PATCH'])]
    public function updateOrderStatus(
        int $id,
        #[MapRequestPayload] UpdateOrderStatusCommandDTO $commandDTO
    ): JsonResponse {
        $command = new UpdateOrderStatusCommand($id, $commandDTO->getStatus());
        $violations = $this->validator->validate($command);
        if (count($violations) > 0) {
            return $this->json($violations, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $this->commandBus->dispatch($command);
        return $this->json(['status' => 'queued'], Response::HTTP_CREATED);
    }

    #[Route('/{id}/status-history', name: 'status_history', methods: ['GET'])]
    public function getOrderStatusHistory(int $id): JsonResponse
    {
        $order = $this->orderRepository->findById($id);
        if (!$order) {
            return $this->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }
        $history = $this->orderStatusHistoryRepository->findByOrder($order);
        $normalizedHistory = [];
        foreach ($history as $statusHistory) {
            $normalizedHistory[] = [
                'status' => $statusHistory->getStatus()->getName(),
                'changeDate' => $statusHistory->getChangeDate()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($normalizedHistory, Response::HTTP_OK);
    }

    #[Route('', name: 'api_orders_list', methods: ['GET'])]
    public function listOrdersWithPackages(Request $request): JsonResponse
    {
        $query = new ListOrdersWithPackagesQuery(
            $request->query->getInt('page', 1),
            $request->query->getInt('pageSize', 10)
        );

        $envelope = $this->queryBus->dispatch($query);
        $handledStamp = $envelope->last(HandledStamp::class);
        $paginatedOrdersResult = $handledStamp->getResult();

        return $this->json(
            [
                'orders' => $paginatedOrdersResult['orders'],
                'total' => $paginatedOrdersResult['total'],
                'page' => $query->getPage(),
                'pageSize' => $query->getPageSize(),
            ],
            Response::HTTP_OK,
            [],
            ['groups' => ['order:read']]
        );
    }

    #[Route('/{id}/package', name: 'create_package', methods: ['POST'])]
    public function createPackage(
        int $id,
        #[MapRequestPayload] CreatePackageCommandDTO $commandDTO
    ): JsonResponse {
        $order = $this->orderRepository->findById($id);
        if (!$order) {
            return $this->json(['error' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        if ($order->getPackage() !== null) {
            return $this->json(['error' => 'Order already has a package'], Response::HTTP_BAD_REQUEST);
        }

        $command = new CreatePackageCommand($id, $commandDTO->getTrackingNumber());
        $this->commandBus->dispatch($command);
        return $this->json(['status' => 'queued'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'get_order', methods: ['GET'])]
    public function getOrder(int $id): JsonResponse
    {
        $order = $this->orderRepository->findById($id);
        if (!$order) {
            return $this->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($order, Response::HTTP_OK, [], ['groups' => ['order:read']]);
    }
}
