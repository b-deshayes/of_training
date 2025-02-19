<?php

namespace App\PackTrack\Infrastructure\EventSubscriber;

use App\PackTrack\Domain\Event\OrderStatusChangedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Filesystem\Filesystem;

#[AsMessageHandler]
class GenerateOrderStatusChangeFileHandler
{
    private string $exportDir;

    public function __construct(
        private Filesystem $filesystem,
        string $exportDir = '/tmp/order-status-changes'
    ) {
        $this->exportDir = $exportDir;
    }

    public function __invoke(OrderStatusChangedEvent $event): void
    {
        $content = sprintf(
            "Commande: %s\nAncien statut: %s\nNouveau statut: %s\nDate: %s\n",
            $event->getOrderReference(),
            $event->getOldStatusName(),
            $event->getNewStatusName(),
            (new \DateTime())->format('Y-m-d H:i:s')
        );

        $this->filesystem->mkdir($this->exportDir, 0755);
        $filename = sprintf(
            '%s/status-change-%s-%s.txt',
            $this->exportDir,
            $event->getOrderReference(),
            (new \DateTime())->format('Y-m-d-H-i-s')
        );

        $this->filesystem->dumpFile($filename, $content);
    }
}
