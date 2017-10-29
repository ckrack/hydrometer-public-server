<?php
/**
 * This library is a PSR-7 Middleware to authenticate a user via a userId that is in the session
 */
namespace App\Modules\Lang;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Modules\Lang\Gettext;
use App\Entity\User;


/**
 * This class sets lang to a language for the user
 */
class UserLangMiddleware
{
    /**
     * PSR-3 logger
     * @var [type]
     */
    protected $logger;

    /**
     * [__construct description]
     * @param Gettext         $lang   [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        Gettext $lang,
        LoggerInterface $logger
    ) {
        $this->lang = $lang;
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
            // get user
            $user = $request->getAttribute('user');

            // set user lang
            $this->lang
                ->setLang($user->getLanguage())
                ->setPath(getenv('LANGUAGE_PATH'))
                ->setTextdomain('hydrometer');

            $this->logger->debug('Set language', [$user->getLanguage()]);

            $response = $next($request, $response);
            return $response;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), [$e->getMessage()]);
            $response = $next($request, $response);
            return $response;
        }
    }
}
