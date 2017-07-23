<?php
namespace App\Controller\UI;

use Psr\Log\LoggerInterface;
use Projek\Slim\Plates;
use Doctrine\ORM\EntityManager;
use Jenssegers\Optimus\Optimus;
use App\Entity\User;
use App\Entity\Token;

class Spindles
{
    /**
     * Use League\Container for auto-wiring dependencies into the controller
     * @param Plates          $view   [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        EntityManager $em,
        Optimus $optimus,
        Plates $view,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->optimus = $optimus;
        $this->view = $view;
        $this->logger = $logger;
    }

    /**
     * List of available spindles
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function display($request, $response, $args)
    {
        $user = $request->getAttribute('user');

        $spindles = $this->em->getRepository('App\Entity\Spindle')->findAllWithLastActivity($user);

        // render template
        return $this->view->render(
            '/ui/index.php',
            [
                'spindles' => $spindles,
                'optimus' => $this->optimus,
                'user' => $user
            ]
        );
    }

    /**
     * issue a new device token and display it, pinging for a new spindle
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function token($request, $response, $args)
    {
        $user = $request->getAttribute('user');
        $user = $this->em->find(get_class($user), $user->getId());

        $token = new Token;
        $token
            ->setType('device')
            ->setValue(bin2hex(random_bytes(10)))
            ->setUser($user);

        $this->em->persist($token);
        $this->em->flush();

        // render template
        return $this->view->render(
            '/ui/add_spindle_token.php',
            [
                'token' => $token,
                'optimus' => $this->optimus,
                'user' => $user
            ]
        );
    }
}
