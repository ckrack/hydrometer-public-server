<?php
namespace App\Controller\Auth;

use Psr\Log\LoggerInterface;
use Projek\Slim\Plates;
use App\Modules\Auth\PasswordLess;
use Hashids\Hashids;
use Doctrine\ORM\EntityManager;
use AdamWathan\BootForms\BootForm;
use Valitron\Validator;

class Register
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
     * Start registration by showing the form to enter an email
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function form($request, $response, $args)
    {
        // render template
        return $this->view->render(
            'auth/register/form.php',
            ['form' => $this->form]
        );
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
                $this->logger->debug('Auth::register: No arguments passed');
                throw new \Exception(_('Token is missing'), 1);
            }

            list($tokenId, $userId) = $this->hash->decode($args['ids']);

            $user = $this->passwordLess->confirm($args['token'], $tokenId, $userId, 'register', '3 days ago');
            if ($user instanceof \App\Entity\User) {
                $_SESSION['userId'] = $userId;
                return $response->withRedirect('/auth/register/success');
            }
        } catch (\Exception $e) {
            // render template
            return $this->view->render('auth/register/error.php', ['msg' => $e->getMessage()]);
        }
    }

    /**
     * Register new user
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
        $validator->rule('required', ['email', 'username']);
        $validator->rule('email', 'email');

        if (! $validator->validate()) {
            $_SESSION['_old_input'] = $post;
            $this->setErrors($validator->errors());
            return $response->withRedirect('/auth/register');
        }

        // check if email exists already and run login flow instead
        if ($this->em->getRepository('App\Entity\User')->findByEmail($post['email'])) {
            $this->passwordLess->init($post['email']);
            return $this->view->render('auth/check_mail.php', ['email' => $post['email']]);
        }

        $this->passwordLess->register($post['email'], $post['username']);

        return $this->view->render('auth/check_mail.php', ['email' => $post['email']]);
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
        $user = $this->em->find('App\Entity\User', $_SESSION['userId']);
        if ($user instanceof \App\Entity\User) {
            return $this->view->render('auth/register/success.php', ['user' => $user]);
        }
        return $this->view->render('auth/register/error.php');
    }

    protected function setErrors($errors)
    {
        foreach ($errors as $key => $value) {
            $_SESSION['errors'][$key] = implode($value, '. ');
        }
    }
}
