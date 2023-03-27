<?php

/**
 * Return an array containing the required dependencies.
 * 
 * @author B Moss <P2595849@mydmu.ac.uk>
 * Date: 02/01/23
 */

declare(strict_types = 1);

use App\Interface\AuthInterface;
use Slim\App;
use function DI\get;
use Slim\Views\Twig;
use App\Support\Config;
use function DI\create;
use App\Support\Session;
use App\Service\LocalAuthService;
use Doctrine\ORM\ORMSetup;
use Slim\Factory\AppFactory;
use \Doctrine\DBAL\Types\Type;
use App\Service\DeviceService;
use App\Service\TicketService;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use App\Interface\SessionInterface;
use App\Service\LocalAccountService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use App\Interface\LocalAccountProviderInterface;
use App\Interface\LocalAuthInterface;
use App\Service\AddressService;
use App\Service\AuthService;
use App\Service\CustomerService;
use App\Support\Guardian;

return [
    App::class => function (ContainerInterface $container)
    {
        AppFactory::setContainer($container);

        $middleware = require CONFIG_PATH . '/middleware.php';
        $app_routes = require CONFIG_PATH . '/routes/app.php';
        $portal_routes = require CONFIG_PATH . '/routes/portal.php';

        $app = AppFactory::create();

        $app_routes($app);
        $portal_routes($app);
        $middleware($app);

        return $app;
    },

    Config::class => create(Config::class)->constructor
    (
        require CONFIG_PATH . '/app.php',
    ),

    EntityManager::class => function (Config $config)
    {
        Type::addType('uuid', 'Ramsey\Uuid\Doctrine\UuidType');

        $orm_config = ORMSetup::createAttributeMetadataConfiguration(
            (array) $config->get('doctrine.entity_dir'),
            $config->get('doctrine.dev_mode')
        );

        $connection = DriverManager::getConnection($config->get('doctrine.connection'));

        return new EntityManager($connection, $orm_config);
    },

    Twig::class => function (Config $config)
    {
        $twig = Twig::create(VIEWS_PATH, [
            'debug' => true,
            'cache' => STORAGE_PATH . '/cache/twig',
            'auto_reload' => true
        ]);

        $twig->addExtension(new \Twig\Extension\DebugExtension());

        $twig->getEnvironment()->addGlobal(
            'globals', [
                'base_url' => BASE_URL,
                'favicon_url' => FAVICON_URL,
                'css_url' => CSS_URL,
                'assets_url' => ASSETS_URL,
                'icons_url' => ICONS_URL,
                'htmx_url' => HTMX_URL
            ]
        );

        if($config->get('debugEnabled')){
            $twig->getEnvironment()->addGlobal(
                'debug', [
                    'enabled' => true,
                    'phpversion' => phpversion()
                ]
            );
        }
        
        return $twig;
    },

    ResponseFactoryInterface::class => fn(App $app) => $app->getResponseFactory(),

    LocalAccountProviderInterface::class => function(ContainerInterface $container)
    {
        return new LocalAccountService($container->get(EntityManager::class));
    },

    CustomerService::class => function(ContainerInterface $container)
    {
        return new CustomerService($container->get(EntityManager::class));
    },
    AddressService::class => function(ContainerInterface $container)
    {
        return new AddressService($container->get(EntityManager::class));
    },
    TicketService::class => function(ContainerInterface $container)
    {
        return new TicketService($container->get(EntityManager::class));
    },
    DeviceService::class => function(ContainerInterface $container)
    {
        return new DeviceService($container->get(EntityManager::class));
    },
    SessionInterface::class => function(Config $config)
    {
        return new Session($config->get('session'));
    },

    AuthInterface::class => function(ContainerInterface $c, Config $config)
    {
        $keyname = $config->get('guardian.key_name');

        if(file_exists(__DIR__ . '/../keys/'.$keyname) && file_exists(__DIR__ . '/../keys/'.$keyname.'.pub'))
        {
            $keys = array(
                'private_key' => file_get_contents(__DIR__ . '/../keys/'.$keyname),
                'public_key' => file_get_contents(__DIR__ . '/../keys/'.$keyname.'.pub')
            );
        }

        return new AuthService(
            $c,
            $keys
        );
    },

    LocalAuthInterface::class => function(ContainerInterface $container)
    {
        return new LocalAuthService($container->get(LocalAccountService::class), $container->get(SessionInterface::class));
    },
];