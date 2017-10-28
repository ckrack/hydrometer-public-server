<?php
namespace App\Modules\OAuth;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

/**
 * Manage OAuth flows and provide methods for logging in and out.
 */
class Manager
{
    public function __construct(
        EntityManager $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Try logging in an oauth-user
     * @param  ResourceOwnerInterface $resourceOwner [description]
     * @return User|bool                [description]
     */
    public function login(ResourceOwnerInterface $resourceOwner)
    {
        try {
            $user = $this->em->getRepository('App\Entity\User')->findByEmail($resourceOwner->getEmail());
            if ($user instanceof \App\Entity\User) {
                return $user;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Register a new user
     * @param  ResourceOwnerInterface $resourceOwner [description]
     * @return User                [description]
     */
    public function register(ResourceOwnerInterface $resourceOwner)
    {
        $user = new User;
        // @TODO check if all return results on these methods or use custom ones in a switch
        $user->setUsername($resourceOwner->getName());

        // @TODO Github does not return an email. what do we do?
        $user->setEmail($resourceOwner->getEmail());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Logout
     * @param  ResponseInterface $response [description]
     * @param  User              $user     [description]
     * @return [type]                      [description]
     */
    public function logout(ResponseInterface $response, User $user)
    {
        unset($_SESSION['userId']);

        return $response;
    }
}
