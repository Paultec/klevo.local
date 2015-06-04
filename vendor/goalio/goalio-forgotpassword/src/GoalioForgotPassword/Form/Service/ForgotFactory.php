<?php
namespace GoalioForgotPassword\Form\Service;

use GoalioForgotPassword\Form\Forgot;
use GoalioForgotPassword\Form\ForgotFilter;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

class ForgotFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        $options = $serviceLocator->get('goalioforgotpassword_module_options');
        $form = new Forgot(null, $options);
        $validator = new \ZfcUser\Validator\RecordExists(array(
            'mapper' => $serviceLocator->get('zfcuser_user_mapper'),
            'key'    => 'email'
        ));

        $translator = $serviceLocator->get('Translator');

        $validator->setMessage($translator->translate('The email address you entered was not found.'));
        $form->setInputFilter(new ForgotFilter($validator,$options));
        return $form;
    }

}