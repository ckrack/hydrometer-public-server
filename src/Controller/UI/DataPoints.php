<?php
namespace App\Controller\UI;

use Psr\Log\LoggerInterface;
use Projek\Slim\Plates;
use Doctrine\ORM\EntityManager;
use Jenssegers\Optimus\Optimus;

class DataPoints
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
     * List of datapoints
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function display($request, $response, $args)
    {
        $user = $request->getAttribute('user');
        $spindle = null;
        if (isset($args['spindle'])) {
            $args['spindle'] = $this->optimus->decode($args['spindle']);
            // @TODO add user restriction
            $spindle = $this->em->getRepository('App\Entity\Spindle')->findOneByUser($args['spindle'], $user);
        }

        $data = $this->em->getRepository('App\Entity\DataPoint')->findAllByUser($user, $spindle);

        // render template
        return $this->view->render(
            '/ui/data.php',
            [
                'data' => $data,
                'spindle' => $spindle,
                'optimus' => $this->optimus,
                'user' => $user,
                'logger' => $this->logger
            ]
        );
    }
}
