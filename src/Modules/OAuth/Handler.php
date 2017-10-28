<?php
namespace App\Modules\OAuth;

use Psr\Log\LoggerInterface;
use League\Container\Container;
use Doctrine\ORM\EntityManager;

/**
 * Handler class for OAuth
 */
class Handler
{
    public function __construct(
        $settings,
        Container $container,
        LoggerInterface $logger
    ) {
        $this->settings = $settings;
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * Get the provider class for a named oauth server
     * @param  [type] $providerName [description]
     * @return [type]               [description]
     */
    public function getProvider($providerName)
    {
        if (! array_key_exists($providerName, $this->settings)) {
            throw new \Exception("Provider not available");
        }

        $this->logger->debug('Creating new OAuth provider', $this->settings[$providerName]);

        return new $this->settings[$providerName]['className']($this->settings[$providerName]);
    }

    /**
     * get list of available providers
     * @return array [description]
     */
    public function getAvailable()
    {
        return $this->settings;
    }
}