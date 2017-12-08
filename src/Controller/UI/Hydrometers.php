<?php
namespace App\Controller\UI;

use Psr\Log\LoggerInterface;
use Projek\Slim\Plates;
use Slim\Csrf\Guard;
use Doctrine\ORM\EntityManager;
use Jenssegers\Optimus\Optimus;
use AdamWathan\BootForms\BootForm;
use Valitron\Validator;
use App\Entity\User;
use App\Entity\Token;
use App\Entity\Hydrometer;
use App\Entity\DataPoint;
use Exception;

class Hydrometers
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
        $this->logger = $logger;
        $this->form = $form;
        $this->csrf = $csrf;
    }

    /**
     * List of available hydrometers
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function display($request, $response, $args)
    {
        try {
            $user = $request->getAttribute('user');

            $hydrometers = $this->em->getRepository('App\Entity\Hydrometer')->findAllWithLastActivity($user);

            $hydrometers = $this->findLastActivity($hydrometers);

            // render template
            return $this->view->render(
                '/ui/hydrometers/index.php',
                [
                    'hydrometers' => $hydrometers,
                    'optimus' => $this->optimus,
                    'user' => $user
                ]
            );
        } catch (Exception $e) {
            return $this->view->render(
                'ui/exception.php',
                ['user' => $user]
            );
        }
    }

    /**
     * Find the last activity for every hydrometer
     * @param  [type] $hydrometers [description]
     * @return [type]              [description]
     */
    protected function findLastActivity($hydrometers)
    {
        foreach ($hydrometers as $key => $hydrometer) {
            if (!empty($hydrometer['last_datapoint_id'])) {
                $activity = $this->em->getRepository(DataPoint::class)->findActivity($hydrometer['last_datapoint_id']);
                $hydrometers[$key] = array_merge($hydrometer, (array) $activity);
            }
        }
        return $hydrometers;
    }

    /**
     * Add new Hydrometer
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function add($request, $response, $args)
    {
        try {
            $post = $request->getParsedBody();
            $user = $request->getAttribute('user');

            $validator = new Validator($post);
            $validator->rule('required', 'name');
            $validator->rule('required', 'metric_temp');
            $validator->rule('required', 'metric_gravity');

            if (! $request->isPost() || ! $validator->validate()) {
                $_SESSION['_old_input'] = $post;
                $this->setErrors($validator->errors());

                $csrf = [
                    $this->csrf->getTokenNameKey() => $request->getAttribute($this->csrf->getTokenNameKey()),
                    $this->csrf->getTokenValueKey() => $request->getAttribute($this->csrf->getTokenValueKey()),
                ];

                // render template
                return $this->view->render(
                    'ui/hydrometers/form.php',
                    [
                        'form' => $this->form,
                        'csrf' => $csrf,
                        'user' => $user
                    ]
                );
            }
            $_SESSION['_old_input'] = $post;

            $token = new Token;
            $token
                ->setType('device')
                ->setValue(bin2hex(random_bytes(getenv('TOKEN_SIZE'))))
                ->setUser($user);

            $this->em->persist($token);

            $hydrometer = new Hydrometer;
            $hydrometer
                ->setName($post['name'])
                ->setMetricTemperature($post['metric_temp'])
                ->setMetricGravity($post['metric_gravity'])
                ->setToken($token)
                ->setUser($user);

            $this->em->persist($hydrometer);
            $this->em->flush();

            return $response->withRedirect('/ui/hydrometers/help/'.$this->optimus->encode($hydrometer->getId()));
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return $this->view->render(
                'ui/exception.php',
                ['user' => $user]
            );
        }
    }

    /**
     * Edit Hydrometer
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function edit($request, $response, $args)
    {
        try {
            $post = $request->getParsedBody();
            $user = $request->getAttribute('user');
            $hydrometer = null;

            if (isset($args['hydrometer'])) {
                $args['hydrometer'] = $this->optimus->decode($args['hydrometer']);
                $hydrometer = $this->em->getRepository('App\Entity\Hydrometer')->findOneByUser($args['hydrometer'], $user);
            }

            $validator = new Validator($post);
            $validator->rule('required', 'name');
            $validator->rule('required', 'metric_temp');
            $validator->rule('required', 'metric_gravity');

            if (! $request->isPost() || ! $validator->validate()) {
                $_SESSION['_old_input'] = $post;
                $this->setErrors($validator->errors());

                $csrf = [
                    $this->csrf->getTokenNameKey() => $request->getAttribute($this->csrf->getTokenNameKey()),
                    $this->csrf->getTokenValueKey() => $request->getAttribute($this->csrf->getTokenValueKey()),
                ];

                // render template
                return $this->view->render(
                    'ui/hydrometers/editForm.php',
                    [
                        'form' => $this->form,
                        'csrf' => $this->csrf,
                        'hydrometer' => $hydrometer,
                        'user' => $user
                    ]
                );
            }
            $_SESSION['_old_input'] = $post;

            $hydrometer
                ->setName($post['name'])
                ->setMetricTemperature($post['metric_temp'])
                ->setMetricGravity($post['metric_gravity']);

            $this->em->persist($hydrometer);
            $this->em->flush();

            return $response->withRedirect('/ui/');
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

    /**
     * issue a new device token and display it, pinging for a new hydrometer
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function help($request, $response, $args)
    {
        try {
            $user = $request->getAttribute('user');
            $hydrometer = null;

            if (isset($args['hydrometer'])) {
                $args['hydrometer'] = $this->optimus->decode($args['hydrometer']);
                $hydrometer = $this->em->getRepository('App\Entity\Hydrometer')->findOneByUser($args['hydrometer'], $user);
            }

            $token = $hydrometer->getToken();

            // render template
            return $this->view->render(
                '/ui/hydrometers/help.php',
                [
                    'token' => $token,
                    'hydrometer' => $hydrometer,
                    'optimus' => $this->optimus,
                    'user' => $user
                ]
            );
        } catch (Exception $e) {
            return $this->view->render(
                'ui/exception.php',
                ['user' => $user]
            );
        }
    }
}
