<?php

/*
 * This file is part of the hydrometer public server project.
 *
 * @author Clemens Krack <info@clemenskrack.com>
 */

namespace App\Controller\UI;

use AdamWathan\BootForms\BootForm;
use App\Entity\Fermentation;
use App\Entity\Hydrometer;
use App\Entity\User;
use App\Modules\Stats;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;
use Jenssegers\Optimus\Optimus;
use Projek\Slim\Plates;
use Psr\Log\LoggerInterface;
use Slim\Csrf\Guard;
use Valitron\Validator;

class Fermentations
{
    /**
     * Use League\Container for auto-wiring dependencies into the controller.
     *
     * @param Plates          $view   [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        EntityManager $em,
        Optimus $optimus,
        Stats\Data $statsModule,
        Plates $view,
        BootForm $form,
        Guard $csrf,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->optimus = $optimus;
        $this->statsModule = $statsModule;
        $this->form = $form;
        $this->csrf = $csrf;
        $this->view = $view;
        $this->logger = $logger;
    }

    /**
     * List of fermentations.
     *
     * @param [type] $request  [description]
     * @param [type] $response [description]
     * @param [type] $args     [description]
     *
     * @return [type] [description]
     */
    public function display($request, $response, $args)
    {
        try {
            $user = $request->getAttribute('user');

            $data = $this->em->getRepository('App\Entity\Fermentation')->findAllByUser($user);

            // render template
            return $this->view->render(
                '/ui/fermentations/list.php',
                [
                    'data' => $data,
                    'optimus' => $this->optimus,
                    'user' => $user,
                ]
            );
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->view->render(
                'ui/exception.php',
                ['user' => $user]
            );
        }
    }

    /**
     * @param [type] $request  [description]
     * @param [type] $response [description]
     * @param [type] $args     [description]
     *
     * @return [type] [description]
     */
    public function details($request, $response, $args)
    {
        try {
            $fermentation = null;
            $user = $request->getAttribute('user');
            if (isset($args['fermentation'])) {
                $args['fermentation'] = $this->optimus->decode($args['fermentation']);
                $fermentation = $this->em->getRepository('App\Entity\Fermentation')->findOneByUser($args['fermentation'], $user);
            }

            $latestData = $this->em->getRepository('App\Entity\DataPoint')->findByFermentation($fermentation);

            $platoData = $this->statsModule->platoCombined($latestData, $fermentation->getHydrometer());

            $stableSince = $this->statsModule->stableSince($latestData, 'gravity', 0.09);

            // render template
            return $this->view->render(
                '/ui/fermentations/details.php',
                array_merge(
                    $platoData,
                    [
                        'user' => $user,
                        'stable' => $stableSince,
                        'fermentation' => $fermentation,
                    ]
                )
            );
        } catch (Exception $e) {
            return $this->view->render(
                'ui/exception.php',
                ['user' => $user]
            );
        }
    }

    /**
     * Show a fermentation publicly.
     *
     * @param [type] $request  [description]
     * @param [type] $response [description]
     * @param [type] $args     [description]
     *
     * @return [type] [description]
     */
    public function show($request, $response, $args)
    {
        try {
            $fermentation = null;
            $user = $request->getAttribute('user');

            if (isset($args['fermentation'])) {
                $args['fermentation'] = $this->optimus->decode($args['fermentation']);
                $fermentation = $this->em->getRepository('App\Entity\Fermentation')->find($args['fermentation']);
            }

            if (!$fermentation->isPublic()) {
                throw new \Exception('Fermentation is not public');
            }

            $latestData = $this->em->getRepository('App\Entity\DataPoint')->findByFermentation($fermentation);

            $platoData = $this->statsModule->platoCombined($latestData, $fermentation->getHydrometer());

            $stableSince = $this->statsModule->stableSince($latestData, 'gravity', 0.09);

            // render template
            return $this->view->render(
                '/ui/fermentations/public.php',
                array_merge(
                    $platoData,
                    [
                        'stable' => $stableSince,
                        'fermentation' => $fermentation,
                    ]
                )
            );
        } catch (Exception $e) {
            return $this->view->render(
                'ui/exception.php',
                ['user' => $user]
            );
        }
    }

    /**
     * Add new fermentation.
     *
     * @param [type] $request  [description]
     * @param [type] $response [description]
     * @param [type] $args     [description]
     *
     * @return [type] [description]
     */
    public function add($request, $response, $args)
    {
        try {
            $post = $request->getParsedBody();
            $user = $request->getAttribute('user');

            $validator = new Validator($post);
            $validator->rule('required', 'name');
            $validator->rule('integer', 'hydrometer_id');
            $validator->rule('date', 'begin');
            $validator->rule('optional', 'end');

            if (!$request->isPost() || !$validator->validate()) {
                $_SESSION['_old_input'] = $post;
                $this->setErrors($validator->errors());

                $csrf = [
                    $this->csrf->getTokenNameKey() => $request->getAttribute($this->csrf->getTokenNameKey()),
                    $this->csrf->getTokenValueKey() => $request->getAttribute($this->csrf->getTokenValueKey()),
                ];

                // render template
                return $this->view->render(
                    'ui/fermentations/form.php',
                    [
                        'form' => $this->form,
                        'csrf' => $csrf,
                        'hydrometers' => $this->em->getRepository('App\Entity\Hydrometer')->formByUser($user),
                        'user' => $user,
                    ]
                );
            }
            $_SESSION['_old_input'] = $post;

            $hydrometer = $this->em
                ->getRepository('App\Entity\Hydrometer')
                ->findOneByUser($post['hydrometer_id'], $user);

            $end = null;
            $begin = DateTime::createFromFormat('Y-m-d\TH:i', $post['begin']);
            if (!empty($post['end'])) {
                $end = DateTime::createFromFormat('Y-m-d\TH:i', $post['end']);
            }

            $fermentation = new Fermentation();
            $fermentation
                ->setName($post['name'])
                ->setBegin($begin)
                ->setEnd($end)
                ->setHydrometer($hydrometer)
                ->setUser($user);

            $this->em->persist($fermentation);
            $this->em->flush();

            $this->em->getRepository('App\Entity\DataPoint')->addToFermentation($fermentation, $hydrometer);

            return $response->withRedirect('/ui/fermentations');
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->view->render(
                'ui/exception.php',
                ['user' => $user]
            );
        }
    }

    /**
     * Edit Fermentation.
     *
     * @param [type] $request  [description]
     * @param [type] $response [description]
     * @param [type] $args     [description]
     *
     * @return [type] [description]
     */
    public function edit($request, $response, $args)
    {
        try {
            $post = $request->getParsedBody();
            $user = $request->getAttribute('user');

            $fermentation = null;

            if (isset($args['fermentation'])) {
                $args['fermentation'] = $this->optimus->decode($args['fermentation']);
                $fermentation = $this->em->getRepository('App\Entity\Fermentation')->findOneByUser($args['fermentation'], $user);
            }

            $validator = new Validator($post);
            $validator->rule('required', 'name');
            $validator->rule('integer', 'hydrometer_id');
            $validator->rule('integer', 'public');
            $validator->rule('date', 'begin');
            $validator->rule('optional', 'end');
            $validator->rule('optional', 'public');

            if (!$request->isPost() || !$validator->validate()) {
                $_SESSION['_old_input'] = $post;
                $this->setErrors($validator->errors());

                $csrf = [
                    $this->csrf->getTokenNameKey() => $request->getAttribute($this->csrf->getTokenNameKey()),
                    $this->csrf->getTokenValueKey() => $request->getAttribute($this->csrf->getTokenValueKey()),
                ];

                // render template
                return $this->view->render(
                    'ui/fermentations/editForm.php',
                    [
                        'form' => $this->form,
                        'csrf' => $csrf,
                        'hydrometers' => $this->em->getRepository('App\Entity\Hydrometer')->formByUser($user),
                        'fermentation' => $fermentation,
                        'user' => $user,
                    ]
                );
            }
            $_SESSION['_old_input'] = $post;

            $hydrometer = $this->em
                ->getRepository('App\Entity\Hydrometer')
                ->findOneByUser($post['hydrometer_id'], $user);

            $end = null;
            $begin = DateTime::createFromFormat('Y-m-d\TH:i', $post['begin']);
            if (!empty($post['end'])) {
                $end = DateTime::createFromFormat('Y-m-d\TH:i', $post['end']);
            }

            $fermentation
                ->setName($post['name'])
                ->setBegin($begin)
                ->setEnd($end)
                ->setPublic($post['public'])
                ->setHydrometer($hydrometer);

            $this->em->persist($fermentation);
            $this->em->flush();

            // remove datapoints outside the date-range
            $this->em->getRepository('App\Entity\DataPoint')->removeFromFermentation($fermentation, $begin, $end);

            // add datapoints inside the date-range
            $this->em->getRepository('App\Entity\DataPoint')->addToFermentation($fermentation, $hydrometer);

            return $response->withRedirect('/ui/fermentations');
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->view->render(
                'ui/exception.php',
                ['user' => $user]
            );
        }
    }

    /**
     * Delete Fermentation.
     *
     * @param [type] $request  [description]
     * @param [type] $response [description]
     * @param [type] $args     [description]
     *
     * @return [type] [description]
     */
    public function delete($request, $response, $args)
    {
        try {
            $post = $request->getParsedBody();
            $user = $request->getAttribute('user');

            $fermentation = null;

            if (isset($args['fermentation'])) {
                $args['fermentation'] = $this->optimus->decode($args['fermentation']);
                $fermentation = $this->em->getRepository('App\Entity\Fermentation')->findOneByUser($args['fermentation'], $user);
            }

            if (!$request->isPost()) {
                $_SESSION['_old_input'] = $post;

                $csrf = [
                    $this->csrf->getTokenNameKey() => $request->getAttribute($this->csrf->getTokenNameKey()),
                    $this->csrf->getTokenValueKey() => $request->getAttribute($this->csrf->getTokenValueKey()),
                ];

                // render template
                return $this->view->render(
                    'ui/fermentations/deleteForm.php',
                    [
                        'form' => $this->form,
                        'csrf' => $csrf,
                        'fermentation' => $fermentation,
                        'user' => $user,
                    ]
                );
            }
            $_SESSION['_old_input'] = $post;

            $this->em->getRepository('App\Entity\DataPoint')->removeFromFermentation($fermentation);

            $this->em->remove($fermentation);
            $this->em->flush();

            return $response->withRedirect('/ui/fermentations');
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return $this->view->render(
                'ui/exception.php',
                ['user' => $user]
            );
        }
    }

    protected function setErrors($errors)
    {
        foreach ($errors as $key => $value) {
            $_SESSION['errors'][$key] = implode($value, '. ');
        }
    }
}
