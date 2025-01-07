<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $to, string $subject, string $htmlContent)
    {
        $email = (new Email())
            ->from('noreply@groupegeh.com') // L'adresse de l'expéditeur
            ->to($to)                        // L'adresse du destinataire
            ->subject($subject)              // Le sujet
            ->html($htmlContent);            // Contenu HTML

        $this->mailer->send($email);
    }
}
