<?php
namespace App\Controller\UI;

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
        Stats\Data $statsModule,
        EntityManager $em,
        Optimus $optimus,
        Plates $view,
        LoggerInterface $logger
    ) {
        $this->statsModule = $statsModule;
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
            $user = $request->getAttribute('user');
            if (isset($args['spindle'])) {
                $args['spindle'] = $this->optimus->decode($args['spindle']);

                $spindle = $this->em->getRepository('App\Entity\Spindle')->findOneByUser($args['spindle'], $user);
            }

            $latestData = $this->statsModule->status($spindle);

            // render template
            return $this->view->render(
                '/ui/status.php',
                array_merge(
                    $latestData,
                    ['user' => $user]
                )
            );
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
    public function plato($request, $response, $args)
    {
        $spindle = null;
        $user = $request->getAttribute('user');
        if (isset($args['spindle'])) {
            $args['spindle'] = $this->optimus->decode($args['spindle']);
            $spindle = $this->em->getRepository('App\Entity\Spindle')->findOneByUser($args['spindle'], $user);
        }
        $platoData = $this->statsModule->platoCombined($spindle);

        // render template
        return $this->view->render(
            '/ui/plato.php',
            array_merge(
                $platoData,
                ['user' => $user]
            )
        );
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
        $user = $request->getAttribute('user');
        if (isset($args['spindle'])) {
            $args['spindle'] = $this->optimus->decode($args['spindle']);
            $spindle = $this->em->getRepository('App\Entity\Spindle')->findOneByUser($args['spindle'], $user);
        }
        $angleData = $this->statsModule->angle($spindle);

        // render template
        return $this->view->render(
            '/ui/angle.php',
            array_merge(
                $angleData,
                ['user' => $user]
            )
        );
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
        $user = $request->getAttribute('user');
        if (isset($args['spindle'])) {
            $args['spindle'] = $this->optimus->decode($args['spindle']);
            $spindle = $this->em->getRepository('App\Entity\Spindle')->findOneByUser($args['spindle'], $user);
        }
        $latestData = $this->statsModule->status($spindle);
        // render template
        return $this->view->render(
            '/ui/battery.php',
            array_merge(
                $latestData,
                ['user' => $user]
            )
        );
    }
}
