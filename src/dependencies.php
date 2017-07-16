<?php
// DIC configuration
$container = $app->getContainer();

// Slim router
$container->add('Slim\Router', function () use ($container) {
    return $container->get('router');
});

// monolog
$container->add('Psr\Log\LoggerInterface', function () use ($container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());

    if (getenv('APP_ENV') == "development") {
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    } else {
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], (integer)getenv('LOG_LEVEL')));
    }
    return $logger;
});

// Leauge\Plates via Bridge
$container->add('Projek\Slim\Plates', function () use ($container) {
    $settings = $container->get('settings')['view'];
    $view = new \Projek\Slim\Plates($settings);

    // Set \Psr\Http\Message\ResponseInterface object
    $view->setResponse($container->get('response'));

    // Instantiate and add Slim specific extension
    $view->loadExtension(new Projek\Slim\PlatesExtension(
        $container->get('router'),
        $container->get('request')->getUri()
    ));

    return $view;
});

// Hash-IDs
$container->add('Hashids\Hashids', function () use ($container) {
    $settings = $container->get('settings');
    return new \Hashids\Hashids($settings['hashids']['salt'], $settings['hashids']['minlength']);
});

// Optimus-IDs
$container->add('Jenssegers\Optimus\Optimus', function () use ($container) {
    $settings = $container->get('settings');
    return new Jenssegers\Optimus\Optimus($settings['optimus']['prime'], $settings['optimus']['inverse'], $settings['optimus']['random']);
});

// PHPMailer
$container->add('PHPMailer', function () use ($container) {
    $settings = $container->get('settings');
    $mail = new \PHPMailer;
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Username = $settings['smtp']['username'];
    $mail->Password = $settings['smtp']['password'];
    $mail->Port = $settings['smtp']['port'] ?? 25;
    $mail->Host = $settings['smtp']['server'];
    return $mail;
});

// Doctrine
$container->add('Doctrine\ORM\EntityManager', function () use ($container) {
    $settings = $container->get('settings');
     $config = new \Doctrine\ORM\Configuration;

    if (getenv('APP_ENV') == "development") {
        $cache = new \Doctrine\Common\Cache\ArrayCache;
        $config->setAutoGenerateProxyClasses(true);
    } else {
        $cache = new \Doctrine\Common\Cache\ApcCache;
        $config->setAutoGenerateProxyClasses(false);
    }

    $config->setMetadataCacheImpl($cache);
    $driverImpl = $config->newDefaultAnnotationDriver($settings['doctrine']['meta']['entity_path'], false);
    $config->setMetadataDriverImpl($driverImpl);

    // standard annotation reader
    $annotationReader = new Doctrine\Common\Annotations\AnnotationReader();
    $cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
        $annotationReader, // use reader
        $cache // and a cache driver
    );

    // create a driver chain for metadata reading
    $driverChain = new \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain();
    // load superclass metadata mapping only, into driver chain
    // also registers Gedmo annotations.NOTE: you can personalize it
    \Gedmo\DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
        $driverChain, // our metadata driver chain, to hook into
        $cachedAnnotationReader // our cached annotation reader
    );

    // now we want to register our application entities,
    // for that we need another metadata driver used for Entity namespace
    $annotationDriver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(
        $cachedAnnotationReader, // our cached annotation reader
        array(__DIR__.'/src/Entity') // paths to look in
    );
    // NOTE: driver for application Entity can be different, Yaml, Xml or whatever
    // register annotation driver for our application Entity fully qualified namespace
    $driverChain->addDriver($annotationDriver, 'App\Entity');

    // Third, create event manager and hook prefered extension listeners
    $evm = new \Doctrine\Common\EventManager();

    // timestampable
    $timestampableListener = new \Gedmo\Timestampable\TimestampableListener();
    $timestampableListener->setAnnotationReader($cachedAnnotationReader);
    $evm->addEventSubscriber($timestampableListener);

    // mysql set names UTF-8 if required
    $evm->addEventSubscriber(new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit());

    $config->addFilter('softdeleteable', 'Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter');

    $config->setQueryCacheImpl($cache);
    $config->setProxyDir($settings['doctrine']['meta']['proxy_dir']);
    $config->setProxyNamespace($settings['doctrine']['meta']['proxy_namespace']);
    $config->setCustomStringFunctions(array(
                'LEAST'             => 'DoctrineExtensions\Query\Mysql\Least',
                'GREATEST'          => 'DoctrineExtensions\Query\Mysql\Greatest',
                'LPAD'              => 'DoctrineExtensions\Query\Mysql\Lpad',
                'REPLACE'           => 'DoctrineExtensions\Query\Mysql\Replace',
                'RPAD'              => 'DoctrineExtensions\Query\Mysql\Rpad',
                'SUBSTRING_INDEX'   => 'DoctrineExtensions\Query\Mysql\SubstringIndex',
                'DATE_FORMAT'       => 'DoctrineExtensions\Query\Mysql\DateFormat',
                'UNIX_TIMESTAMP'    => 'DoctrineExtensions\Query\Mysql\UnixTimestamp',
                'NOW'               => 'DoctrineExtensions\Query\Mysql\Now'
            ));
    return \Doctrine\ORM\EntityManager::create($settings['doctrine']['connection'], $config, $evm);
});

// Language
$container->add('App\Module\Lang\Gettext', function () use ($container) {
    $settings = $container->get('settings');
    return new App\Module\Lang\Gettext(
        $settings['languages']['list'],
        $settings['languages']['path']
    );
});
