<?php

namespace Digikala;

use Digikala\Application\CommandHandlerInterface;
use Digikala\Application\QueryHandlerInterface;
use Enqueue\Bundle\EnqueueBundle;
use League\Tactician\Bundle\TacticianBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel implements ContainerAwareInterface
{
    use MicroKernelTrait;
    use ContainerAwareTrait;

    const CONFIG_EXTS = '.{yml}';

    public function registerBundles()
    {
        return array(
            new FrameworkBundle(),
            new TwigBundle(),
            new TacticianBundle(),
            new EnqueueBundle(),
        );
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.dumper.inline_class_loader', true);
        $container->setParameter('kernel.project_dir', $this->getProjectDir());
        $this->setContainer($container);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{config}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{config}_'.$this->environment.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{parameters}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{parameters}_'.$this->environment.self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
    }

    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return $this->getProjectDir().'/var/log';
    }

    protected function build(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(QueryHandlerInterface::class)
            ->addTag('tactician.handler', [
                'typehints' => true,
                'bus' => 'query',
            ])
        ;

        $container->registerForAutoconfiguration(CommandHandlerInterface::class)
            ->addTag('tactician.handler', [
                'typehints' => true,
                'bus' => 'command',
            ])
        ;



//        $container
//            ->registerForAutoconfiguration(ServiceRepositoryInterface::class)
//            ->addTag('digikala.service_repository')
//        ;
    }
}