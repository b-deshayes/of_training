<?php

namespace App\PackTrack\Infrastructure\EventSubscriber;

use App\PackTrack\Domain\Event\OrderStatusChangedEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
#[AsMessageHandler]
class SendOrderStatusChangeEmailHandler
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(OrderStatusChangedEvent $event): void
    {
        $email = (new Email())
            ->from('expediteur@example.com')
            ->to('destinataire@example.com')
            ->subject('Statut de votre commande mis à jour')
            ->html(
                <<<HTML
                Bonjour,

                Le statut de votre commande REF: {$event->getOrderReference()} a été mis à jour.

                Ancien statut: {$event->getOldStatusName()}
                Nouveau statut: {$event->getNewStatusName()}

                Merci,
                Votre service client
                HTML
            );

        $this->mailer->send($email);
    }
}
