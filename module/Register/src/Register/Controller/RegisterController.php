<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;
use Register\Model\RegisterNote;

class RegisterController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function addAction()
    {
        $registerNote = new RegisterNote();
        $builder = new AnnotationBuilder();
        $form = $builder->createForm($registerNote);

        $request = $this->getRequest();
        if ($request->isPost()){
            $form->bind($registerNote);
            $form->setData($request->getPost());
            if ($form->isValid()){
                print_r($form->getData());
            }
        }

        return array('form' => $form);
    }


}

