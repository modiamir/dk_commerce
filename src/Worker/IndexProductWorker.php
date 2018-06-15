<?php

namespace Digikala\Worker;

use Digikala\Application\Command\IndexProduct\IndexProductCommand;
use Enqueue\Client\CommandSubscriberInterface;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use League\Tactician\CommandBus;
use Swift_Message;
use Symfony\Component\Routing\RouterInterface;

class IndexProductWorker implements PsrProcessor, CommandSubscriberInterface
{
    /**
     * @var \League\Tactician\CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public static function getSubscribedCommand()
    {
        return self::class;
    }

    public function process(PsrMessage $message, PsrContext $context) {
        $data = json_decode($message->getBody(), true);

        $indexProductCommand = new IndexProductCommand($data['product_id']);
        $this->commandBus->handle($indexProductCommand);

        return self::ACK;
    }
}