<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require __DIR__.'/vendor/autoload.php';

$settings = include __DIR__.'/src/settings.php';
$settings = $settings['settings']['doctrine'];

$config = new \Doctrine\ORM\Configuration;

if (getenv('APP_ENV') == "development") {
    $cache = new \Doctrine\Common\Cache\ArrayCache;
    $config->setAutoGenerateProxyClasses(false);
} else {
    $cache = new \Doctrine\Common\Cache\ApcCache;
    $config->setAutoGenerateProxyClasses(true);
}

$config->setMetadataCacheImpl($cache);
$driverImpl = $config->newDefaultAnnotationDriver($settings['meta']['entity_path'], false);
$config->setMetadataDriverImpl($driverImpl);
$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);
$config->setProxyDir($settings['meta']['proxy_dir']);
$config->setProxyNamespace($settings['meta']['proxy_namespace']);

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

// $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
//     $settings['meta']['entity_path'],
//     $settings['meta']['auto_generate_proxies'],
//     $settings['meta']['proxy_dir'],
//     $cache,
//     false
// );
#return \Doctrine\ORM\EntityManager::create($settings['connection'], $config);

$em = \Doctrine\ORM\EntityManager::create($settings['connection'], $config, $evm);

return ConsoleRunner::createHelperSet($em);
