<?php
namespace App\Controller\UI;

use Psr\Log\LoggerInterface;
use Projek\Slim\Plates;
use Slim\Csrf\Guard;
use AdamWathan\BootForms\BootForm;
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
        BootForm $form,
        Guard $csrf,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->optimus = $optimus;
        $this->view = $view;
        $this->form = $form;
        $this->csrf = $csrf;
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
            $hydrometer = $this->em->getRepository('App\Entity\Hydrometer')->findOneByUser($args['hydrometer'], $user);
        }

        $data = $this->em->getRepository('App\Entity\DataPoint')->findAllByUser($user, $hydrometer);

        // render template
        return $this->view->render(
            '/ui/datapoints/index.php',
            [
                'data' => $data,
                'hydrometer' => $hydrometer,
                'optimus' => $this->optimus,
                'user' => $user,
                'logger' => $this->logger
            ]
        );
    }


    /**
     * Delete datapoint
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function delete($request, $response, $args)
    {
        try {
            $post = $request->getParsedBody();
            $user = $request->getAttribute('user');

            $datapoint = null;

            if (isset($args['datapoint'])) {
                $args['datapoint'] = $this->optimus->decode($args['datapoint']);
                $datapoint = $this->em->find('App\Entity\DataPoint', $args['datapoint']);
            }

            if ($datapoint->getHydrometer()->getUser()->getId() !== $user->getId()) {
                throw new \Exception("Can not access datapoint.");
            }

            if (! $request->isPost()) {
                $_SESSION['_old_input'] = $post;

                $csrf = [
                    $this->csrf->getTokenNameKey() => $request->getAttribute($this->csrf->getTokenNameKey()),
                    $this->csrf->getTokenValueKey() => $request->getAttribute($this->csrf->getTokenValueKey()),
                ];

                // render template
                return $this->view->render(
                    'ui/datapoints/deleteForm.php',
                    [
                        'form' => $this->form,
                        'csrf' => $csrf,
                        'datapoint' => $datapoint,
                        'user' => $user
                    ]
                );
            }

            $_SESSION['_old_input'] = $post;

            $this->em->remove($datapoint);
            $this->em->flush();

            return $response->withRedirect('/ui/data');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->view->render(
                'ui/exception.php',
                ['user' => $user]
            );
        }
    }
}
