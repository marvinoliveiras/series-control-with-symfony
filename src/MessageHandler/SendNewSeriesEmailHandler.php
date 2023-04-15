<?php
declare(strict_types=1);
namespace App\MessageHandler;

use App\Entity\User;
use App\Message\SeriesWasCreated;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendNewSeriesEmailHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private MailerInterface $mailer
    ){}

    public function __invoke(
        SeriesWasCreated $message
    ){
        $users =  $this->userRepository->findAll();
        $usersEmails = array_map(fn(User $user) => $user->getEmail(), $users);
        $series = $message->series;
        $email = (new TemplatedEmail())
            ->from('example@example.com')
            ->to(...$usersEmails)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('New series created!')
            ->text("Series {$series->getName()}, created successfully")
            ->htmlTemplate('emails/series-created.html.twig')
            ->context(['series' => $series]);

        $this->mailer
            ->send($email);
    }
}