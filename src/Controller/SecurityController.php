<?php

namespace Digikala\Controller;

use Digikala\Application\Command\RegisterUser\RegisterUserCommand;
use Digikala\Application\Command\VerifyEmail\VerifyEmailCommand;
use Digikala\Form\UserRegisterType;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    public function register(Request $request, CommandBus $commandBus)
    {
        $registerUserCommand = new RegisterUserCommand();
        $form = $this->createForm(UserRegisterType::class, $registerUserCommand);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandBus->handle($registerUserCommand);

            $this->addFlash('success', 'You are registered successfully. Please check your mail to verify your email before login.');

            return $this->redirectToRoute('home');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function verifyEmail($code, CommandBus $commandBus)
    {
        $verifyEmailCommand = new VerifyEmailCommand($code);

        try {
            $commandBus->handle($verifyEmailCommand);
            $this->addFlash('success', 'Your email verified.');
            $verified = true;
        } catch (\Throwable $exception) {
            $verified = false;
            $this->addFlash('danger', 'Either your email has been verified before or verification code is invalid.');
        }

        return $this->render('security/verify.html.twig', compact('verified'));
    }

    public function loginCheck()
    {
        die('error');
    }

    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    public function logout()
    {
        die('error');
    }
}