<?php
namespace App\Modules\Auth;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Projek\Slim\Plates;
use Doctrine\ORM\EntityManager;
use Hashids\Hashids;
use App\Entity\User;
use App\Entity\Token;
use Slim\Router;
use PHPMailer;
use Dflydev\FigCookies;

class PasswordLess
{
    /**
     * @param Plates          $view   [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        Router $router,
        EntityManager $em,
        PHPMailer $mailer,
        Hashids $hash,
        Plates $view,
        LoggerInterface $logger
    ) {
        $this->router = $router;
        $this->em = $em;
        $this->mailer = $mailer;
        $this->hash = $hash;
        $this->view = $view;
        $this->logger = $logger;
    }

    /**
     * initialize the login flow by generating a token for a user identified by email
     * and send a link with a login token to this email address.
     * @param  [type] $email [description]
     * @return [type]        [description]
     */
    public function init($email)
    {
        // first: look for the email

        $user = $this->em->getRepository('App\Entity\User')->findByEmail($email);

        if (! $user instanceof \App\Entity\User) {
            throw new \Exception(sprintf(_('A User with this email was not found (%s)'), $email), 404);
        }

        $token = new Token;
        $token
            ->setType('login')
            ->setValue(bin2hex(random_bytes(24)))
            ->setUser($user);

        $this->em->persist($token);
        $this->em->flush();

        // generate link with login-token
        $link = $this->router->pathFor('auth-login-token', ['ids' => $this->hash->encode([$token->getId(), $user->getId()]), 'token' => $token->getValue()]);

        $this->logger->debug('Auth::passwordless: '.$link);

        // send email with link
        return $this->email($user, $link, 'login');
    }

    /**
     * confirm the login with token
     * @param  [type] $token [description]
     * @return [type]        [description]
     */
    public function confirm($tokenStr, $tokenId, $userId, $type = 'login', $maxAge = '15 minutes ago')
    {
        // get token by value
        $token = $this->em->getRepository('App\Entity\Token')->findByValue($tokenStr);

        $this->logger->debug('passwordless::token: '. $tokenStr, [$token]);

        if (! $token instanceof \App\Entity\Token) {
            throw new \Exception("Not a valid token");

        }

        // check type
        if ($token->getType() !== $type) {
            throw new \Exception(_("Token type differs."), 1);
        }

        // check usage for login and register token
        if (in_array($token->getType(), ['login', 'register']) && $token->getWasUsed()) {
            throw new \Exception(_("Token was already used."), 2);
        }

        // check token age
        $valid = new \DateTime($maxAge);
        if ($valid > $token->getCreated()) {
            throw new \Exception(_("Token is too old"), 3);
        }

        // verify with IDs passed in as hashid decoded string
        if ($token->getType() != 'api' && $token->getType() != 'device' &&
            ($token->getId() != $tokenId || $token->getUser()->getId() != $userId)
        ) {
            throw new \Exception("Token User was not verified", 4);
        }

        $token->setWasUsed(true);
        $this->em->persist($token);
        $this->em->flush();
        return $token->getUser();
    }

    /**
     * save new user
     * @param  [type] $email    [description]
     * @param  [type] $username [description]
     * @return [type]           [description]
     */
    public function register($email, $username)
    {
        $user = new User;
        $user->setUsername($username);
        $user->setEmail($email);

        $token = new Token;
        $token
            ->setType('register')
            ->setValue(bin2hex(random_bytes(16)))
            ->setUser($user);

        $this->em->persist($user);
        $this->em->persist($token);
        $this->em->flush();

        // send email with register-token
        $link = $this->router->pathFor('auth-register-token', ['ids' => $this->hash->encode([$token->getId(), $user->getId()]), 'token' => $token->getValue()]);

        return $this->email($user, $link, 'register');
    }

    /**
     * send the email containing a login token to the specified user
     * @param  [type] $user [description]
     * @param  [type] $link [description]
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    protected function email($user, $link, $type)
    {
        $this->mailer->setFrom(getenv('MAIL_FROM'), getenv('SITE_TITLE'));
        $this->mailer->addAddress($user->getEmail());

        $this->mailer->isHTML(true);

        $this->mailer->Subject = getenv('SITE_TITLE') . ' ' . ucfirst($type);
        $this->mailer->Body    = $this->view->getPlates()->render('auth/'.$type.'/mail.php', ['user' => $user, 'link' => getenv('URL_DOMAIN').$link]);
        $this->mailer->AltBody = strip_tags($this->mailer->Body);

        if (!$this->mailer->send()) {
            $this->logger->error('PHPMailer: Message could not be sent.', [$this->mailer->ErrorInfo]);
            return false;
        } else {
            return true;
        }
    }

    /**
     * save cookies for a persisting login
     * @param  ResponseInterface $response [description]
     * @param  User              $user     [description]
     * @return ResponseInterface                      [description]
     */
    public function saveCookies(ResponseInterface $response, User $user)
    {
        $token = new Token;
        $token
            ->setType('cookie')
            ->setValue(bin2hex(random_bytes(32)))
            ->setUser($user);

        $this->em->persist($token);
        $this->em->flush();

        // generate expiration time (1 month)
        $expires = new \DateTime('now');
        $expires = $expires->add(new \DateInterval('P1M'));

        $response = FigCookies\FigResponseCookies::set(
            $response,
            FigCookies\SetCookie::create('passwordless_hash')
                ->withValue($this->hash->encode([$token->getId(), $user->getId()]))
                ->withExpires($expires)
        );

        $response = FigCookies\FigResponseCookies::set(
            $response,
            FigCookies\SetCookie::create('passwordless_token')
                ->withValue($token->getValue())
                ->withExpires($expires)
        );

        $this->logger->debug('Auth::passwordless: Saving cookies', [$token->getId(), $token->getValue()]);

        return $response;
    }

    /**
     * detect cookies being set already
     * @param  RequestInterface $request [description]
     * @return [type]                    [description]
     */
    protected function detectCookies(RequestInterface $request)
    {
        $cookies = Cookies::fromRequest($request);
        if ($cookies->has('passwordless_token') && $cookies->has('passwordless_hash')) {
            $this->logger->debug('Auth::passwordless: Cookies detected');
            return true;
        }
        return false;
    }

    /**
     * logout
     * @param  User $user [description]
     * @return [type]          [description]
     */
    public function logout(ResponseInterface $response, User $user)
    {
        $response = FigCookies\FigResponseCookies::set(
            $response,
            FigCookies\SetCookie::create('passwordless_hash')
                ->withValue('')
                ->expire()
        );

        $response = FigCookies\FigResponseCookies::set(
            $response,
            FigCookies\SetCookie::create('passwordless_token')
                ->withValue('')
                ->expire()
        );

        unset($_SESSION['userId']);

        $tokens = $this->em->getRepository('App\Entity\Token')->findBy(['user' => $user, 'type' => ['cookie', 'login', 'register']]);
        foreach ($tokens as $token) {
            $this->logger->debug('Auth::logout: Removing tokens', [$token->getValue()]);
            $this->em->remove($token);
        }
        $this->em->flush();

        return $response;
    }
}
