<?php
namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Product\Form;

class UploadController extends AbstractActionController
{
    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function indexAction()
    {
        $this->clearAction();

        $form = new Form\UploadForm('file-form');

        if ($this->getRequest()->isPost()) {
            $data = array_merge_recursive(
            //$this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($data);
            $request = $this->getRequest();

            if ($form->isValid()) {
                //
                // ...Save the form...
                //
                return $this->redirectToSuccessPage($form->getData());
            }
        }

        return new ViewModel(array(
            'form' => $form
        ));
    }

    /**
     * @return ViewModel
     */
    public function successAction()
    {
        return new ViewModel(array(
            'ok' => 'ok'
        ));
    }

    /**
     * @return \Zend\Http\Response
     */
    protected function redirectToSuccessPage()
    {
        $response = $this->redirect()->toRoute('fileupload/success');
        $response->setStatusCode(303);

        return $response;
    }

    /**
     *
     * @return ViewModel
     */
    public function clearAction()
    {
        /**
         * @todo use cron
         */
        array_map('unlink', glob('./data/upload/*'));

        return;
    }
}