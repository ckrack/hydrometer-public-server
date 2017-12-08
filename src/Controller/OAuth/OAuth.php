<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\OAuth;

use App\Modules\OAuth\Handler;
use App\Modules\OAuth\Manager;
use Projek\Slim\Plates;
use Psr\Log\LoggerInterface;

class OAuth
{
    protected $view;
    protected $logger;
    protected $handler;
    protected $manager;

    /**
     * Use League\Container for auto-wiring dependencies into the controller.
     *
     * @param Plates          $view   [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        Handler $handler,
        Manager $manager,
        Plates $view,
        LoggerInterface $logger
    ) {
        $this->handler = $handler;
        $this->manager = $manager;
        $this->view = $view;
        $this->logger = $logger;
    }

    /**
     * Start authentication by showing the form to enter an email.
     *
     * @param [type] $request  [description]
     * @param [type] $response [description]
     * @param [type] $args     [description]
     *
     * @return [type] [description]
     */
    public function init($request, $response, $args)
    {
        $this->provider = $this->handler->getProvider($args['provider']);

        // Fetch the authorization URL from the provider; this returns the
        // urlAuthorize option and generates and applies any necessary parameters
        // (e.g. state).
        $authorizationUrl = $this->provider->getAuthorizationUrl();

        // Get the state generated for you and store it to the session.
        $_SESSION['oauth2state'] = $this->provider->getState();

        // Redirect the user to the authorization URL.
        return $response->withRedirect($authorizationUrl);
    }

    /**
     * Send email and show form to enter token or reloading wait screen.
     *
     * @param [type] $request  [description]
     * @param [type] $response [description]
     * @param [type] $args     [description]
     *
     * @return [type] [description]
     */
    public function confirm($request, $response, $args)
    {
        // If we don't have an authorization code then get one
        if (!$request->getQueryParam('code', false)) {
            return $this->init($request, $response, $args);
        }

        if (!$request->getQueryParam('state', false) || ($request->getQueryParam('state') !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            throw new \InvalidArgumentException('Invalid state');
        }

        try {
            $this->provider = $this->handler->getProvider($args['provider']);

            // Try to get an access token using the authorization code grant.
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $request->getQueryParam('code', false),
            ]);

            // Using the access token, we may look up details about the
            // resource owner.
            $resourceOwner = $this->provider->getResourceOwner($accessToken);

            $user = $this->manager->login($resourceOwner);

            if ($user instanceof \App\Entity\User) {
                // save the userId in session
                $_SESSION['userId'] = $user->getId();

                return $response->withRedirect('/auth/success');
            }

            // new registration
            $user = $this->manager->register($resourceOwner);

            if ($user instanceof \App\Entity\User) {
                // save the userId in session
                $_SESSION['userId'] = $user->getId();

                return $response->withRedirect('/auth/success/register');
            }

            var_dump($resourceOwner->toArray(), $resourceOwner);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            // Failed to get the access token or user details.
            // render template
            return $this->view->render('oauth/register/error.php', ['msg' => $e->getMessage()]);
        } catch (\Exception $e) {
            // render template
            return $this->view->render('oauth/register/error.php', ['msg' => $e->getMessage()]);
        }
    }

    /**
     * Confirm successful login.
     *
     * @param [type] $request  [description]
     * @param [type] $response [description]
     * @param [type] $args     [description]
     *
     * @return [type] [description]
     */
    public function success($request, $response, $args)
    {
        $this->view->setResponse($response);
        $user = $request->getAttribute('user');

        $template = 'oauth/login/success.php';
        if (isset($args['register'])) {
            $template = 'oauth/register/success.php';
        }

        if ($user instanceof \App\Entity\User) {
            return $this->view->render($template, ['user' => $user]);
        }

        return $this->view->render('oauth/login/error.php');
    }

    /**
     * Logout.
     *
     * @param [type] $request  [description]
     * @param [type] $response [description]
     * @param [type] $args     [description]
     *
     * @return [type] [description]
     */
    public function logout($request, $response, $args)
    {
        $user = $request->getAttribute('user');

        if ($user instanceof \App\Entity\User) {
            $response = $this->manager->logout($response, $user);

            return $response->withRedirect('/');
        }

        return $this->view->render('auth/login/error.php');
    }
}
