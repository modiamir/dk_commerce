<?php

namespace Digikala\Console;

use Digikala\Application\Command\RegisterUser\RegisterUserCommand;
use Digikala\Elastic\ProductTransformer;
use Digikala\Entity\Product;
use Digikala\Repository\ProductRepository;
use Elastica\Type;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAdminUser extends ContainerAwareCommand
{
    /**
     * @var \League\Tactician\CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        parent::__construct();
        $this->commandBus = $commandBus;
    }

    protected function configure()
    {
        $this
            ->setName('digikala:admin:create')
            ->setDescription('Create admin user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $registerUserCommand = new RegisterUserCommand(
            'admin',
            'admin@digikalatest.com',
            'admin',
            'ROLE_ADMIN',
            true,
            true
        );
        $this->commandBus->handle($registerUserCommand);

        $output->writeln('admin user created successfully');
    }
}