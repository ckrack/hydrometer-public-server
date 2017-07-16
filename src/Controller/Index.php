<?php
namespace App\Controller;

use Psr\Log\LoggerInterface;
use Projek\Slim\Plates;
use Doctrine\ORM\EntityManager;
use Jenssegers\Optimus\Optimus;

class Index
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
        if (empty($args)) {
            $this->logger->debug('Hello::world: No arguments passed');
        }

        $spindles = $this->em->getRepository('App\Entity\Spindle')->findAllWithLastActivity();

        // render template
        return $this->view->render('index.php', ['spindles' => $spindles, 'optimus' => $this->optimus]);
    }
}
