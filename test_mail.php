<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require 'vendor/autoload.php';

try {
    // Crée le transport MailTrap
    $dsn = 'smtp://test:123456@localhost:25';
    $transport = Transport::fromDsn($dsn);

    // Instancie le service Mailer
    $mailer = new Mailer($transport);

    // Crée un email
    $email = (new Email())
        ->from('test@example.com') // Remplacez par une adresse valide
        ->to('balekialaj@gmail.com') // Remplacez par une adresse valide
        ->subject('Test MailTrap')
        ->text('Ceci est un test d\'envoi d\'email avec MailTrap et Symfony.');

    // Envoie l'email
    $mailer->send($email);

    echo "Email envoyé avec succès !\n";
} catch (\Exception $e) {
    echo "Une erreur s'est produite : " . $e->getMessage() . "\n";
}
