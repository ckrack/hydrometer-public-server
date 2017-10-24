<?php
namespace App\Controller\Auth;

use Psr\Log\LoggerInterface;
use Projek\Slim\Plates;
use App\Modules\Auth\PasswordLess;
use Hashids\Hashids;
use Doctrine\ORM\EntityManager;
use AdamWathan\BootForms\BootForm;
use Valitron\Validator;

class Login
{
    protected $view;
    protected $logger;
    protected $passwordLess;

    /**
     * Use League\Container for auto-wiring dependencies into the controller
     * @param Plates          $view   [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        EntityManager $em,
        Hashids $hash,
        PasswordLess $passwordLess,
        BootForm $form,
        Plates $view,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->hash = $hash;
        $this->passwordLess = $passwordLess;
        $this->form = $form;
        $this->view = $view;
        $this->logger = $logger;
    }

    /**
     * Start authentication by showing the form to enter an email
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function form($request, $response, $args)
    {
        $email = $request->getQueryParam('email');
        // render template
        return $this->view->render('auth/login/form.php', ['email' => $email, 'form' => $this->form]);
    }

    /**
     * Send email and show form to enter token or reloading wait screen
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function post($request, $response, $args)
    {
        $post = $request->getParsedBody();
        $_SESSION['_old_input'] = [];
        $_SESSION['errors'] = [];

        $validator = new Validator($post);
        $validator->rule('required', 'email');
        $validator->rule('email', 'email');

        if (! $validator->validate()) {
            $_SESSION['_old_input'] = $post;
            $this->setErrors($validator->errors());

            return $response->withRedirect('/auth/login');
        }

        // save a note for the user to save successfull login in cookie if desired
        unset($_SESSION['passwordless_cookie']);
        if (! empty($post['cookies'])) {
            $this->logger->debug('Auth::post: Save cookies');
            $_SESSION['passwordless_cookie'] = 1;
        }

        $this->passwordLess->init($post['email']);

        return $this->view->render('auth/check_mail.php', ['email' => $post['email']]);
    }

    /**
     * Confirm given token
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function token($request, $response, $args)
    {
        try {
            if (empty($args)) {
                $this->logger->debug('Auth::login: No arguments passed');
                throw new \Exception(_('Token is missing'), 1);
            }

            list($tokenId, $userId) = $this->hash->decode($args['ids']);

            // confirm the login
            $user = $this->passwordLess->confirm($args['token'], $tokenId, $userId);

            if ($user instanceof \App\Entity\User) {
                $this->logger->info('Auth::passwordless: Login by', [$user->getEmail(), $user->getUsername(), $_SESSION]);

                // save the userId in session
                $_SESSION['userId'] = $userId;

                return $response->withRedirect('/auth/success');
            }
        } catch (\Exception $e) {
            // render template
            return $this->view->render('auth/login/error.php', ['msg' => $e->getMessage()]);
        }
    }

    /**
     * Confirm successful login
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function success($request, $response, $args)
    {
        $this->view->setResponse($response);
        $user = $request->getAttribute('user');

        if ($user instanceof \App\Entity\User) {
            // set cookies if wanted
            if (isset($_SESSION['passwordless_cookie']) && $_SESSION['passwordless_cookie'] == 1) {
                $response = $this->passwordLess->saveCookies($response, $user);
            }
            return $this->view->render('auth/login/success.php', ['user' => $user]);
        }
        return $this->view->render('auth/login/error.php');
    }

    /**
     * Logout
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function logout($request, $response, $args)
    {
        $user = $request->getAttribute('user');

        if ($user instanceof \App\Entity\User) {
            $response = $this->passwordLess->logout($response, $user);
            return $response->withRedirect('/');
        }
        return $this->view->render('auth/login/error.php');
    }

    protected function setErrors($errors)
    {
        foreach ($errors as $key => $value) {
            $_SESSION['errors'][$key] = implode($value, '. ');
        }
    }
}
