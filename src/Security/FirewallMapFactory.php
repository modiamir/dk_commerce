<?php

namespace Digikala\Security;

use Digikala\Kernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\FirewallMap;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symfony\Component\Security\Http\Firewall\ChannelListener;
use Symfony\Component\Security\Http\Firewall\ContextListener;
use Symfony\Component\Security\Http\Firewall\LogoutListener;
use Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener;
use Symfony\Component\Security\Http\Firewall\AnonymousAuthenticationListener;
use Symfony\Component\Security\Http\Firewall\AccessListener;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Http\AccessMap;
use Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategy;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class FirewallMapFactory
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    /**
     * @var \Symfony\Component\Security\Http\HttpUtils
     */
    private $httpUtils;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var \Digikala\Kernel
     */
    private $kernel;

    /**
     * @var \Digikala\Security\EntityUserProvider
     */
    private $userProvider;

    /**
     * @var \Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface
     */
    private $sessionAuthenticationStrategy;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    /**
     * @var \Symfony\Component\Security\Core\User\UserChecker
     */
    private $userChecker;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
     */
    private $encoderFactory;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface
     */
    private $authenticationTrustResolver;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        HttpUtils $httpUtils,
        TokenStorageInterface $tokenStorage,
        KernelInterface $kernel,
        EntityUserProvider $userProvider,
        SessionAuthenticationStrategyInterface $sessionAuthenticationStrategy,
        AccessDecisionManagerInterface $accessDecisionManager,
        UserChecker $userChecker,
        EncoderFactory $encoderFactory,
        AuthenticationTrustResolverInterface $authenticationTrustResolver
    ) {
        $this->dispatcher = $dispatcher;
        $this->httpUtils = $httpUtils;
        $this->tokenStorage = $tokenStorage;
        $this->kernel = $kernel;
        $this->userProvider = $userProvider;
        $this->sessionAuthenticationStrategy = $sessionAuthenticationStrategy;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->userChecker = $userChecker;
        $this->encoderFactory = $encoderFactory;
        $this->authenticationTrustResolver = $authenticationTrustResolver;
    }

    public function create()
    {
        $firewallMap = new FirewallMap();

        $this->addToMap(
            $firewallMap,
            '^/admin',
            'admin',
            [
                '^/admin/security/login_check' => ['IS_AUTHENTICATED_ANONYMOUSLY'],
                '^/admin/security/login' => ['IS_AUTHENTICATED_ANONYMOUSLY'],
                '^/admin' => ['ROLE_ADMIN'],
            ],
            [
                'check_path' => '/admin/security/login_check',
                'login_path' => '/admin/security/login',
                'require_previous_session' => false,
                'post_only' => true,
            ],
            [
                'logout_path' => '/admin/security/logout',
            ],
            [
                'default_target_path' => '/admin/dashboard',
            ]
        );

        $this->addToMap(
            $firewallMap,
            '^/',
            'frontend',
            [
                '^/security/register' => ['IS_AUTHENTICATED_ANONYMOUSLY'],
                '^/security/login_check' => ['IS_AUTHENTICATED_ANONYMOUSLY'],
                '^/security/login' => ['IS_AUTHENTICATED_ANONYMOUSLY'],
                '^/' => ['ROLE_USER'],
            ],
            [
                'check_path' => '/security/login_check',
                'login_path' => '/security/login',
                'require_previous_session' => false,
                'post_only' => true,
            ],
            [
                'logout_path' => '/security/logout',
            ],
            [
                'default_target_path' => '/',
            ]
        );

        return $firewallMap;
    }

    private function addToMap(
        FirewallMap &$firewallMap,
        string $pattern,
        string $contextKey,
        array $accessMapArray = [],
        array $loginOptions = [],
        array $logoutOptions = [],
        array $successOptions = [],
        array $failureOptions = []
    ) {
        $authenticationEntryPoint = new FormAuthenticationEntryPoint(
            $this->kernel,
            $this->httpUtils,
            $loginOptions['login_path']
        );
        $exceptionListener = new ExceptionListener(
            $this->tokenStorage,
            $this->authenticationTrustResolver,
            $this->httpUtils,
            $contextKey,
            $authenticationEntryPoint
        );

        $requestMatcher = new RequestMatcher($pattern);

        $accessMap = new AccessMap();

        foreach ($accessMapArray as $accessPattern => $roles) {
            $accessRequestMatcher = new RequestMatcher($accessPattern);
            $accessMap->add($accessRequestMatcher, $roles);
        }

        $daoAuthenticationProvider = new DaoAuthenticationProvider($this->userProvider, $this->userChecker, $contextKey, $this->encoderFactory);
        $authenticationManager = new AuthenticationProviderManager([$daoAuthenticationProvider], false);

        $listeners = array(
            new ChannelListener(
                $accessMap,
                new FormAuthenticationEntryPoint($this->kernel, $this->httpUtils, $loginOptions['login_path'])
            ),
            new ContextListener(
                $this->tokenStorage,
                array($this->userProvider),
                $contextKey,
                null,
                $this->dispatcher
            ),
            new LogoutListener(
                $this->tokenStorage,
                $this->httpUtils,
                new DefaultLogoutSuccessHandler($this->httpUtils),
                $logoutOptions
            ),
            new UsernamePasswordFormAuthenticationListener(
                $this->tokenStorage,
                $authenticationManager,
                $this->sessionAuthenticationStrategy,
                $this->httpUtils,
                $contextKey,
                new DefaultAuthenticationSuccessHandler($this->httpUtils, $successOptions),
                new DefaultAuthenticationFailureHandler($this->kernel, $this->httpUtils, $failureOptions),
                $loginOptions
            ),
            new AnonymousAuthenticationListener($this->tokenStorage, ''),
            new AccessListener(
                $this->tokenStorage,
                $this->accessDecisionManager,
                $accessMap,
                $authenticationManager
            ),
        );

        $firewallMap->add($requestMatcher, $listeners, $exceptionListener);
    }
}