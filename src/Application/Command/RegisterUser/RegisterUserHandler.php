<?php

namespace Digikala\Application\Command\RegisterUser;

use Digikala\Application\CommandHandlerInterface;
use Digikala\Worker\SendVerificationEmailWorker;
use Digikala\Entity\User;
use Digikala\Repository\UserRepository;
use Enqueue\Client\ProducerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class RegisterUserHandler implements CommandHandlerInterface
{
    /**
     * @var \Digikala\Repository\UserRepository
     */
    private $userRepository;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
     */
    private $encoderFactory;

    /**
     * @var \Enqueue\Client\ProducerInterface
     */
    private $producer;

    public function __construct(
        UserRepository $userRepository,
        EncoderFactory $encoderFactory,
        ProducerInterface $producer
    ) {
        $this->userRepository = $userRepository;
        $this->encoderFactory = $encoderFactory;
        $this->producer = $producer;
    }

    public function __invoke(RegisterUserCommand $command)
    {
        $newUser = User::registerNewUser(
            $command->getUsername(),
            $command->getEmail(),
            $this->encoderFactory->getEncoder(User::class)->encodePassword($command->getPassword(), null),
            null,
            $command->getRole(),
            $command->getIsEmailVerified(),
            $command->getisActive()
        );

        $this->userRepository->save($newUser);

        if (!$command->getIsEmailVerified()) {
            $this->producer->sendCommand(SendVerificationEmailWorker::class, [
                'username' => $newUser->getUsername(),
                'email_verification_code' => $newUser->getEmailVerificationCode(),
                'email' => $newUser->getEmail(),
            ]);
        }
    }
}