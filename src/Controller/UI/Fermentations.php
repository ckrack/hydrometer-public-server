<?php
namespace App\Controller\UI;

use Psr\Log\LoggerInterface;
use Projek\Slim\Plates;
use Doctrine\ORM\EntityManager;
use Jenssegers\Optimus\Optimus;
use AdamWathan\BootForms\BootForm;
use Valitron\Validator;
use App\Entity\Fermentation;
use App\Entity\Spindle;
use App\Entity\User;

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
        Plates $view,
        BootForm $form,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->optimus = $optimus;
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

    public function add($request, $response, $args)
    {
        try {
            $post = $request->getParsedBody();
            $user = $request->getAttribute('user');

            $validator = new Validator($post);
            $validator->rule('required', 'name');
            $validator->rule('integer', 'spindle_id');
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
                        'spindles' => $this->em->getRepository('App\Entity\Spindle')->formByUser($user),
                        'user' => $user
                    ]
                );
            }
            $_SESSION['_old_input'] = $post;

            $spindle = $this->em
                ->getRepository('App\Entity\Spindle')
                ->findOneByUser($post['spindle_id'], $user);

            $fermentation = new Fermentation;
            $fermentation
                ->setName($post['name'])
                ->setBegin(\DateTime::createFromFormat('Y-m-d\TH:i', $post['begin']))
                ->setEnd(\DateTime::createFromFormat('Y-m-d\TH:i', $post['end']))
                ->setSpindle($spindle)
                ->setUser($user);

            $this->em->persist($fermentation);
            $this->em->flush();

            return $this->em->getRepository('App\Entity\DataPoint')->addToFermentation($fermentation, $spindle);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    protected function setErrors($errors)
    {
        foreach ($errors as $key => $value) {
            $_SESSION['errors'][$key] = implode($value, '. ');
        }
    }
}
