<?php

namespace Data\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Form\Annotation\AnnotationBuilder;

use Data\Entity\PaymentMethod as DeliveryPaymentEntity;
use Data\Model\PaymentMethod;

class PaymentMethodController extends AbstractActionController
{
    const PAYMENT_METHOD = 'Data\Entity\PaymentMethod';

    /**
     * @var
     */
    protected $em;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $payment = $this->getEntityManager()->getRepository(self::PAYMENT_METHOD)->findAll();

        return new ViewModel(array(
                'payment' => $payment
            ));
    }

    /**
     * @return ViewModel
     */
    public function addAction()
    {
        $form = $this->getForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $payment = new DeliveryPaymentEntity();

            $postData = $request->getPost();

            $form->setData($postData);
            if ($form->isValid()) {
                // Вызов сервиса транслитерации
                $postData = $form->getData();

                $payment->populate($postData);

                $this->getEntityManager()->persist($payment);
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('data/payment-method');
            }
        }

        return new ViewModel(array(
                'form' => $form
            ));
    }

    /**
     * @return ViewModel
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('data');
        }

        $payment = $this->getEntityManager()->find(self::PAYMENT_METHOD, $id);

        $form = $this->getForm();
        $form->setData($payment->getArrayCopy());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $postData = $form->getData();

                $payment->populate($postData);

                $this->getEntityManager()->persist($payment);
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('data/payment-method');
            }
        }

        return new ViewModel(array(
                'form' => $form,
                'id'   => $id
            ));
    }

    /**
     * @return ViewModel
     */
    public function hideAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('brand');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            $hide = $request->getPost('hide', 'Нет');

            if ($hide == 'Да') {
                $id = (int)$request->getPost('id');

                $payment = $this->getEntityManager()->find(self::PAYMENT_METHOD, $id);

                // set status (null or 3 for show, 4 for hidden)
                if (!is_null($payment->getIdStatus())) {
                    $idStatus = $payment->getIdStatus()->getId();

                    if (is_null($idStatus) || $idStatus == 3) {
                        $idStatus = 4;
                    } else {
                        $idStatus = 3;
                    }
                } else {
                    $idStatus = 4;
                }

                $qb = $this->getEntityManager()->createQueryBuilder();

                $qu = $qb->update(self::PAYMENT_METHOD, 'p')
                    ->set('p.idStatus', '?1')
                    ->where('p.id = ?2')
                    ->setParameter(1, $idStatus)
                    ->setParameter(2, $id)
                    ->getQuery();
                $qu->execute();
            }

            return $this->redirect()->toRoute('data/payment-method');
        }

        return new ViewModel(array(
                'id'        => $id,
                'payment'   => $this->getEntityManager()->find(self::PAYMENT_METHOD, $id)
            ));
    }

    /**
     * @return \Zend\Form\ElementInterface|\Zend\Form\FieldsetInterface|\Zend\Form\Form|\Zend\Form\FormInterface
     */
    protected function getForm()
    {
        $entity  = new PaymentMethod();
        $builder = new AnnotationBuilder();
        $form    = $builder->createForm($entity);

        return $form;
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
}