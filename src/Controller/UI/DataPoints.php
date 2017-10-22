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
        $hydrometer = null;
        if (isset($args['hydrometer'])) {
            $args['hydrometer'] = $this->optimus->decode($args['hydrometer']);
            // @TODO add user restriction
            $hydrometer = $this->em->getRepository('App\Entity\Hydrometer')->findOneByUser($args['hydrometer'], $user);
        }

        $data = $this->em->getRepository('App\Entity\DataPoint')->findAllByUser($user, $hydrometer);

        // render template
        return $this->view->render(
            '/ui/data.php',
            [
                'data' => $data,
                'hydrometer' => $hydrometer,
                'optimus' => $this->optimus,
                'user' => $user,
                'logger' => $this->logger
            ]
        );
    }
}
