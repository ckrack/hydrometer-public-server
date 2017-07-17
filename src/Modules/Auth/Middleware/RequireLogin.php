<?php
/**
 * This library is a PSR-7 Middleware to make sure a user is authenticated
 */
namespace App\Modules\Auth\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 */
class RequireLogin
{
    /**
     * PSR-3 logger
     * @var [type]
     */
    protected $logger;

    /**
     * You can optionally pass a logger into the middleware.
     * @param \Psr\Log\LoggerInterface|null $logger A PSR-3 compliant Logger
     */
    public function __construct(LoggerInterface $logger)
    {
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
            // get auth token
            if (! $request->getAttribute('user') || !($request->getAttribute('user') instanceof \App\Entity\User)) {
                $this->logger->debug('Auth::require: user not authenticated', [$request->getAttribute('user')]);
                // return 401 on missing user
                return $response
                    ->withStatus(401)
                    ->withRedirect('/auth/login');
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), [$e->getMessage()]);
            $response = $response->withStatus(500);
            $response = $next($request, $response);
            return $response;
        }

        $response = $next($request, $response);
        return $response;
    }
}
