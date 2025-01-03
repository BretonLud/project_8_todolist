<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\EmailService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class EmailServiceTest extends TestCase
{
    private MailerInterface $mailer;
    private EmailService $emailService;
    
    /**
     * @throws Exception
     */
    public function testSendMailSendsEmail(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn('test@example.com');
        
        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($email) use ($user) {
                return $email instanceof TemplatedEmail &&
                    $email->getTo()[0]->getAddress() === $user->getEmail() &&
                    $email->getFrom()[0]->getAddress() === 'no-reply@todolist.fr';
            }));
        
        $template = 'emails/test_template.twig';
        $subject = 'Test Subject';
        
        try {
            $this->emailService->sendMail($user, $template, $subject);
        } catch (TransportExceptionInterface $e) {
            $this->fail("Exception should not be thrown: " . $e->getMessage());
        }
    }
    
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->emailService = new EmailService($this->mailer);
    }
}
