<?php
namespace App\Controller;

use Psr\Log\LoggerInterface;
use Projek\Slim\Plates;
use Doctrine\ORM\EntityManager;
use Jenssegers\Optimus\Optimus;
use App\Modules\Stats;

class Status
{
    protected $view;
    protected $logger;
    protected $em;

    /**
     * Use League\Container for auto-wiring dependencies into the controller
     * @param Plates          $view   [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        Stats\Data $statusModule,
        EntityManager $em,
        Optimus $optimus,
        Plates $view,
        LoggerInterface $logger
    ) {
        $this->statusModule = $statusModule;
        $this->em = $em;
        $this->optimus = $optimus;
        $this->view = $view;
        $this->logger = $logger;
    }

    /**
     *
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function display($request, $response, $args)
    {
        try {
            $spindle = null;
            if (isset($args['spindle'])) {
                $args['spindle'] = $this->optimus->decode($args['spindle']);
                // @TODO add user restriction
                $spindle = $this->em->find('App\Entity\Spindle', $args['spindle']);
            }

            $latestData = $this->statusModule->status($spindle);
            // render template
            return $this->view->render('status.php', $latestData);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     *
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function plato4($request, $response, $args)
    {
        $spindle = null;
        if (isset($args['spindle'])) {
            $args['spindle'] = $this->optimus->decode($args['spindle']);
            // @TODO add user restriction
            $spindle = $this->em->find('App\Entity\Spindle', $args['spindle']);
        }
        $plato4Data = $this->statusModule->plato4($spindle);
        // render template
        return $this->view->render('plato4.php', $plato4Data);
    }

    /**
     *
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function plato($request, $response, $args)
    {
        $spindle = null;
        if (isset($args['spindle'])) {
            $args['spindle'] = $this->optimus->decode($args['spindle']);
            // @TODO add user restriction
            $spindle = $this->em->find('App\Entity\Spindle', $args['spindle']);
        }
        $platoData = $this->statusModule->plato($spindle);

        // render template
        return $this->view->render('plato.php', $platoData);
    }

    /**
     *
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function angle($request, $response, $args)
    {
        $spindle = null;
        if (isset($args['spindle'])) {
            $args['spindle'] = $this->optimus->decode($args['spindle']);
            // @TODO add user restriction
            $spindle = $this->em->find('App\Entity\Spindle', $args['spindle']);
        }
        $angleData = $this->statusModule->angle($spindle);

        // render template
        //return $this->view->render('charts.php', $angleData);
        return $this->view->render('angle.php', $angleData);
    }

    /**
     *
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function battery($request, $response, $args)
    {
        $spindle = null;
        if (isset($args['spindle'])) {
            $args['spindle'] = $this->optimus->decode($args['spindle']);
            // @TODO add user restriction
            $spindle = $this->em->find('App\Entity\Spindle', $args['spindle']);
        }
        $latestData = $this->statusModule->status();
        // render template
        return $this->view->render('battery.php', $latestData);
    }
}
