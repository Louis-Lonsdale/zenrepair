<?php

declare(strict_types = 1);

use RobThree\Auth\Algorithm;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;

return [

    // Application Configuration
    'app_name' => 'zenrepair',
    'app_version' => '1.1.0',
    'app_version_codename' => 'crowntail',
    'app_stream' => 'dev',

    // Configuration for Slim Framework.
    'slim' => [
        'display_error_details' => true,
        'log_errors' => true,
        'log_error_details' => true
    ],

    // Configuration for Doctrine EntityManager
    'doctrine' => [
        'dev_mode' => true,
        'cache_dir' => __DIR__ . '/../var/cache/doctrine',
        'entity_dirs' => [
            __DIR__ . '/../src/Domain/Entity'
        ],
        'connection' => [
            'driver' => 'pdo_pgsql',
            'host' => $_ENV['DOCTRINE_DATABASE_HOST'],
            'port' => $_ENV['DOCTRINE_DATABASE_PORT'],
            'dbname' => $_ENV['DOCTRINE_DATABASE_NAME'],
            'user' => $_ENV['DOCTRINE_USER'],
            'password' => $_ENV['DOCTRINE_PASSWORD']
        ]
    ],

    // Authenticator Configuration
    'authenticator' => [
        'tfa' => [
            'enforce' => false,
            'issuer' => 'zenRepair',
            'digits' => 6,
            'period' => 30,
            'algo' => Algorithm::Sha1,
            'qrCodeProvider' => BaconQrCodeProvider::class,
            'qrCodePath' => __DIR__ . '/../app/qrcodes'
        ],
        'crypto' => [
            'pepper' => $_ENV['APP_CRYPT_PEPPER'],
            'algo' => PASSWORD_ARGON2ID,
            'options' => [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads' => PASSWORD_ARGON2_DEFAULT_THREADS
            ]
        ]
    ],

    // InputGuard Settings
    'inputguard' => [
        'ruleset_path' => __DIR__ . '/inputguard'
    ],

    // Twig Rendering Engine Settings
    'twig' => [
        'templates' => __DIR__ . '/../templates',
        'debug' => true,
        'cache_dir' => __DIR__ . '/../var/cache/twig',
        'auto_reload'=> true
    ],

    // Client Session Configuration
    'session' => [
        'name' => 'zenrepair_app',
        'lifetime' => 7200,
        'path' => null,
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'cache_limiter' => 'nocache',
        'cookie_samesite' => 'lax',
        'cookie_secure' => 'false'
    ],

    // Configuration for Monolog Logger
    'logger' => [
        'path' => __DIR__ . '/../var/log',
        'level' => \Monolog\Logger::DEBUG
    ]
];