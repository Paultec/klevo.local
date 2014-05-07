<?php

namespace Register\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Form\Annotation\AnnotationBuilder;
use Register\Model\Register;
use Zend\Authentication\AuthenticationService;
use Doctrine\ORM\EntityManager;
use Register\Entity\Register as RegisterEntity;

class RegisterController extends AbstractActionController
{
    const OPERATION_ENTITY = 'Data\Entity\Operation';
    const PAYMENT_TYPE_ENTITY = 'Data\Entity\PaymentType';
    const STATUS_ENTITY = 'Data\Entity\Status';
    const STORE_ENTITY = 'Data\Entity\Store';
    const USER_ENTITY = 'User\Entity\User';

    /**
     * @var
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array|object
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }

        return $this->em;
    }

    /**
     * @return \Zend\Form\ElementInterface|\Zend\Form\FieldsetInterface|\Zend\Form\Form|\Zend\Form\FormInterface
     */
    protected function getForm()
    {
        $entity  = new Register();
        $builder = new AnnotationBuilder();
        $form    = $builder->createForm($entity);

        return $form;
    }

    /**
     * @return array
     */
    protected function setOptionItems($entity)
    {
        $optionList = $this->getEntityManager()
            ->getRepository($entity)->findAll();

        $optionArray = array();

        for ($i = 0, $option = count($optionList); $i < $option; $i++) {
            $optionArray[$i]['id']   = $optionList[$i]->getId();
            $optionArray[$i]['name'] = $optionList[$i]->getName();
        }

        array_unshift($optionArray, array(
            'id'   => null,
            'name' => 'Выберите из списка'
        ));

        return $optionArray;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * @return ViewModel
     */
    public function addAction()
    {
        $auth = new AuthenticationService();
        $currentUser = $auth->getIdentity();

        $form = $this->getForm();

        $request = $this->getRequest();

        if ($request->isPost()){

            $registerNote = new RegisterEntity();
            $form->setData($request->getPost());

            if ($form->isValid()){

                $formData = $form->getData();
                var_dump($formData);

                $formData['idStoreFrom'] = $this->getEntityManager()->
                    find(self::STORE_ENTITY, $formData['idStoreFrom']);
                $formData['idStoreTo'] = $this->getEntityManager()->
                    find(self::STORE_ENTITY, $formData['idStoreTo']);
                $formData['idOperation'] = $this->getEntityManager()->
                    find(self::OPERATION_ENTITY, $formData['idOperation']);
                $formData['idPaymentType'] = $this->getEntityManager()->
                    find(self::PAYMENT_TYPE_ENTITY, $formData['idPaymentType']);
                $formData['idStatus'] = $this->getEntityManager()->
                    find(self::STATUS_ENTITY, $formData['idStatus']);
                $formData['idUser'] = $this->getEntityManager()->
                    find(self::USER_ENTITY, $currentUser);

                $registerNote->populate($formData);
                var_dump($registerNote);

                $this->getEntityManager()->persist($registerNote);
                $this->getEntityManager()->flush();

//                return $this->redirect()->toRoute('register');

            }
        }

        return new ViewModel(array(
            'form'       => $form,
            'storeFrom' => $this->setOptionItems(self::STORE_ENTITY),
            'storeTo' => $this->setOptionItems(self::STORE_ENTITY),
            'operation' => $this->setOptionItems(self::OPERATION_ENTITY),
            'paymentType' => $this->setOptionItems(self::PAYMENT_TYPE_ENTITY),
            'status' => $this->setOptionItems(self::STATUS_ENTITY),
        ));
    }


}

