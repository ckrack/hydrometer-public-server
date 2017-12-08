<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Modules\Lang;

use App\Entity\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * This class sets lang to a language for the user.
 */
class UserLangMiddleware
{
    /**
     * PSR-3 logger.
     *
     * @var [type]
     */
    protected $logger;

    /**
     * [__construct description].
     *
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
            // get user
            $user = $request->getAttribute('user');

            $lang = 'en';
            if ($user instanceof \App\Entity\User) {
                $userLang = $user->getLanguage();
                if (!empty($userLang)) {
                    $lang = $userLang;
                }
            }

            // set user lang
            $this->lang
                ->setLang($lang)
                ->setPath(getenv('LANGUAGE_PATH'))
                ->setTextdomain('hydrometer');

            $response = $next($request, $response);

            return $response;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), [$e->getMessage()]);
            $response = $next($request, $response);

            return $response;
        }
    }
}
