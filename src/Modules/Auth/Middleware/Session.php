<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Auth\Middleware;

use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Valitron\Validator;

/**
 * This class simply looks for a userId in the session and adds a user entity to request.
 */
class Session
{
    /**
     * PSR-3 logger.
     *
     * @var [type]
     */
    protected $logger;

    /**
     * @param EntityManager   $em     [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        EntityManager $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Act as an invokable class.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request  PSR-7 request
     * @param \Psr\Http\Message\ResponseInterface      $response PSR-7 response
     * @param callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        try {
            // get userId from session
            if (isset($_SESSION['userId'])) {
                $this->logger->debug('Auth::session: userid in session', (array) $_SESSION);

                // validate userId
                $validator = new Validator($_SESSION);
                $validator->rule('integer', 'userId');
                if (!$validator->validate()) {
                    $response = $next($request, $response);

                    return $response;
                }

                // find user with this id
                $user = $this->em->find('App\Entity\User', $_SESSION['userId']);
                if ($user instanceof \App\Entity\User) {
                    // set the user into the request attribute, making it available later on
                    $request = $request->withAttribute('user', $user);
                }
                $this->logger->debug('Auth::session: user', [$user]);
            }

            $response = $next($request, $response);

            return $response;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), [$e->getMessage()]);
            $response = $response->withStatus(500);
            $response = $next($request, $response);

            return $response;
        }
    }
}
