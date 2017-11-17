<?php
namespace Gosyl\CommonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LoginType extends AbstractType {
    //private $_csrfToken;
    private $_builder;
	private $_lastUsername;	
	private $action;
	
	/*public function __construct(array $aParams) {//$lastUsername, $action) {
		$this->_lastUsername = $aParams['lastUsername'];
		$this->action = $aParams['action'];
	}*/
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$this->_lastUsername = $options['data']['lastUsername'];
		$this->action = $options['data']['action'];
		
		$builder->setAttribute('method', $options['method'])
				->setAttribute('id', $options['id'])
				->setAttribute('legend', $options['legend'])
				->setAttribute('action', $options['data']['action'])
        		//->add('_csrf_token', 'hidden', array('data' => $options['csrfToken']))
        		->add('_username', TextType::class, array('label' => "Nom d'utilisateur", 'data' => $options['data']['lastUsername']))
        		->add('_password', PasswordType::class, array('label' => 'Mot de passe'))
        		->add('_remember_me', CheckboxType::class,array('label' => 'Se souvenir de moi', 'required' => false))
        		->add('sendFormLogin', SubmitType::class, array('label' => 'Connexion'))
                ->add('btnInscription', ButtonType::class, array('label' => 'Inscription', 'attr' => array('onclick' => 'javascript: btnInscriptionFromLogin();')))
                ->add('btnCancel', ResetType::class, array('label' => 'Annuler', 'attr' => array('id' => 'btnCancel')))
        ;
		
        $this->_builder = $builder;
    }

    public function getName() {
        //return 'gosyl_user_login';
        return $this->getBlockPrefix();
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options) {
    	foreach ($options as $key => $value) {
    		$view->vars[$key] = $value;
    	}
    }
    
    public function configureOptions(OptionsResolver $resolver) {
    	$resolver->setDefaults(array(
    		'method' => 'POST',
    		'id' => 'connexion',
    		'legend' => 'Connexion',
    		'lastUsername' => $this->_lastUsername,
    		'action' => $this->action
    	));
    }
    
    public function getBlockPrefix() {
    	return '';
    }
}