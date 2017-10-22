<?php
namespace App\Controller\UI;

use Psr\Log\LoggerInterface;
use Projek\Slim\Plates;
use Doctrine\ORM\EntityManager;
use Jenssegers\Optimus\Optimus;
use AdamWathan\BootForms\BootForm;
use Valitron\Validator;
use App\Entity\Fermentation;
use App\Entity\Hydrometer;
use App\Entity\User;
use App\Modules\Stats;

class Fermentations
{
    /**
     * Use League\Container for auto-wiring dependencies into the controller
     * @param Plates          $view   [description]
     * @param LoggerInterface $logger [description]
     */
    public function __construct(
        EntityManager $em,
        Optimus $optimus,
        Stats\Data $statsModule,
        Plates $view,
        BootForm $form,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->optimus = $optimus;
        $this->statsModule = $statsModule;
        $this->form = $form;
        $this->view = $view;
        $this->logger = $logger;
    }

    /**
     * List of fermentations
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function display($request, $response, $args)
    {
        $user = $request->getAttribute('user');

        $data = $this->em->getRepository('App\Entity\Fermentation')->findAllByUser($user);

        // render template
        return $this->view->render(
            '/ui/fermentations/list.php',
            [
                'data' => $data,
                'optimus' => $this->optimus,
                'user' => $user
            ]
        );
    }

    /**
     *
     * @param  [type] $request  [description]
     * @param  [type] $response [description]
     * @param  [type] $args     [description]
     * @return [type]           [description]
     */
    public function details($request, $response, $args)
    {
        $fermentation = null;
        $user = $request->getAttribute('user');
        if (isset($args['fermentation'])) {
            $args['fermentation'] = $this->optimus->decode($args['fermentation']);
            $fermentation = $this->em->getRepository('App\Entity\Fermentation')->findOneByUser($args['fermentation'], $user);
        }

        $latestData = $this->em->getRepository('App\Entity\DataPoint')->findByFermentation($fermentation);

        $platoData = $this->statsModule->platoCombined($latestData, $fermentation->getHydrometer());

        // render template
        return $this->view->render(
            '/ui/fermentations/details.php',
            array_merge(
                $platoData,
                [
                    'user' => $user,
                    'fermentation' => $fermentation
                ]
            )
        );
    }

    /**
     * Add new fermentation
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
            $user = $this->em->find(get_class($user), $user->getId());

            $validator = new Validator($post);
            $validator->rule('required', 'name');
            $validator->rule('integer', 'hydrometer_id');
            $validator->rule('date', 'begin');
            $validator->rule('date', 'end');
            $validator->rule('optional', 'end');

            if (! $request->isPost() || ! $validator->validate()) {
                $_SESSION['_old_input'] = $post;
                $this->setErrors($validator->errors());

                // render template
                return $this->view->render(
                    'ui/fermentations/form.php',
                    [
                        'form' => $this->form,
                        'hydrometers' => $this->em->getRepository('App\Entity\Hydrometer')->formByUser($user),
                        'user' => $user
                    ]
                );
            }
            $_SESSION['_old_input'] = $post;

            $hydrometer = $this->em
                ->getRepository('App\Entity\Hydrometer')
                ->findOneByUser($post['hydrometer_id'], $user);

            $fermentation = new Fermentation;
            $fermentation
                ->setName($post['name'])
                ->setBegin(\DateTime::createFromFormat('Y-m-d\TH:i', $post['begin']))
                ->setEnd(\DateTime::createFromFormat('Y-m-d\TH:i', $post['end']))
                ->setHydrometer($hydrometer)
                ->setUser($user);

            $this->em->persist($fermentation);
            $this->em->flush();

            $this->em->getRepository('App\Entity\DataPoint')->addToFermentation($fermentation, $hydrometer);

            return $response->withRedirect('/ui/fermentations');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    protected function setErrors($errors)
    {
        foreach ($errors as $key => $value) {
            $_SESSION['errors'][$key] = implode($value, '. ');
        }
    }
}
