<?php

namespace Digikala\Worker;

use Enqueue\Client\CommandSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Swift_Message;
use Symfony\Component\Routing\RouterInterface;

class SendVerificationEmailWorker implements PsrProcessor, CommandSubscriberInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $siteUrl;

    public function __construct(\Swift_Mailer $mailer, RouterInterface $router, string $siteUrl)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->siteUrl = $siteUrl;
    }

    public static function getSubscribedCommand()
    {
        return self::class;
    }

    public function process(PsrMessage $message, PsrContext $context) {
        $data = json_decode($message->getBody(), true);
        $message = (new Swift_Message('Email vaerification'))
            ->setFrom(['noreply@digikala.com' => 'Digikala'])
            ->setTo([$data['email']])
            ->setBody(
                sprintf(
                    'Click to verify your email: %s%s',
                    $this->siteUrl,
                    $this->router->generate('security_verify_email', ['code' => $data['email_verification_code']])
                )
            );

        $this->mailer->send($message);

        return self::ACK;
    }
}