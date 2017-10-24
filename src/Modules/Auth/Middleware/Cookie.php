<?php
/**
 * This library is a PSR-7 Middleware to authenticate a user via a token that is in the cookie
 */
namespace App\Modules\Auth\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use App\Modules\Auth\PasswordLess;
use Valitron\Validator;
use Hashids\Hashids;

/**
 */
class Cookie
{
    /**
     * PSR-3 logger
     * @var [type]
     */
    protected $logger;

    /**
     * PassWordLess Auth Module
     * @var [type]
     */
    protected $passwordLess;

    /**
     *
     * @param PasswordLess    $passwordLess [description]
     * @param LoggerInterface $logger       [description]
     */
    public function __construct(
        PasswordLess $passwordLess,
        Hashids $hash,
        LoggerInterface $logger
    ) {
        $this->passwordLess = $passwordLess;
        $this->hash = $hash;
        $this->logger = $logger;
    }

    /**
     * Act as an invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR-7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR-7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        try {
            // check if we are already authenticated
            if ($request->getAttribute('user') instanceof \App\Entity\User) {
                $this->logger->debug('Auth::cookie: already authenticated');

                $response = $next($request, $response);
                return $response;
            }

            // get auth token from cookie
            if (! isset($_COOKIE['passwordless_token']) || !isset($_COOKIE['passwordless_hash'])) {
                $this->logger->debug('Auth::cookie: no tokens set', (array) $_COOKIE);
                // do nothing if not found
                $response = $next($request, $response);
                return $response;
            }

            // validate userId
            $validator = new Validator($_COOKIE);
            $validator->rule('alphaNum', 'passwordless_token');
            $validator->rule('alphaNum', 'passwordless_hash');
            $validator->rule('lengthMin', 'passwordless_hash', '6');
            if (! $validator->validate()) {
                // do nothing if not validated
                $response = $next($request, $response);
                return $response;
            }

            // compare token
            list($tokenId, $userId) = $this->hash->decode($_COOKIE['passwordless_hash']);

            // confirm the login
            $user = $this->passwordLess->confirm($_COOKIE['passwordless_token'], $tokenId, $userId, 'cookie', '1 month ago');

            $this->logger->debug('Auth::cookie: getting user', [$tokenId, $userId, $user]);

            if ($user instanceof \App\Entity\User) {
                // save in session
                $_SESSION['userId'] = $user->getId();
                // set the user into the request attribute, making it available later on
                $request = $request->withAttribute('user', $user);

                $this->logger->debug('Auth::cookie: found user');
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), [$e->getMessage()]);
        }

        $response = $next($request, $response);
        return $response;
    }

}
