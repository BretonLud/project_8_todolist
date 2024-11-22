<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

readonly class EmailService
{
    public function __construct(private MailerInterface $mailer)
    {
    }
    
    /**
     * @throws TransportExceptionInterface
     */
    public function sendMail(User $user, string $template, string $subject): void
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@todolist.fr')
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->context([
                'user' => $user
            ]);
        
        $this->mailer->send($email);
    }
}
