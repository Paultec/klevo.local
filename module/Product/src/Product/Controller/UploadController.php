<?php
namespace Product\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use GoSession;

use Product\Form;

class UploadController extends AbstractActionController
{
    private $currentSession;

    public function __construct()
    {
        $this->currentSession = new Container();
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function indexAction()
    {
        $this->clearUploadDir();

        $form = new Form\UploadForm('file-form');

        if ($this->getRequest()->isPost()) {
            $data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($data);
//            $request = $this->getRequest();

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
     * @param $data
     *
     * @return mixed
     */
    protected function redirectToSuccessPage($data)
    {
        $this->currentSession->parseType = $data['type'];
        $response = $this->redirect()->toRoute('parse');
        $response->setStatusCode(303);

        return $response;
    }

    /**
     *
     * @return ViewModel
     */
    protected function clearUploadDir()
    {
        /**
         * @todo use cron
         */
        array_map('unlink', glob('./data/upload/*'));

        return;
    }
}