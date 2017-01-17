<?php

namespace Gosyl\CommonBundle\Form;

// use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
// use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gosyl\CommonBundle\Entity\ParamUsers;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
// use FOS\UserBundle\Form\Type\ProfileFormType;
class RegistrationType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->setAttribute('method', $options['method'])->setAttribute('legend', $options['legend'])->setAttribute('id', $options['id'])->add('_username', TextType::class, array(
				'label' => 'Nom d\'utilisateur', 
				'attr' => array(
						'id' => 'username' 
				) 
		))->add('_password', RepeatedType::class, array(
				'type' => PasswordType::class, 
				'first_options' => array(
						'label' => 'Mot de passe' 
				), 
				'second_options' => array(
						'label' => 'Répétez le mot de passe' 
				) 
		))->add('name', TextType::class, array(
				'label' => 'Nom', 
				'attr' => array(
						'id' => 'nom' 
				) 
		))->add('prenom', TextType::class, array(
				'label' => 'Prenom', 
				'attr' => array(
						'id' => 'prenom' 
				) 
		))->add('email', EmailType::class, array(
				'label' => 'Adresse e-mail', 
				'attr' => array(
						'id' => 'email' 
				) 
		))->add('dateNaissance', DateType::class, array(
				'label' => 'Date de naissance', 
				'widget' => 'single_text', 
				'format' => 'dd/MM/yyyy', 
				'input' => 'datetime', 
				'error_bubbling' => true, 
				'attr' => array(
						'id' => 'dateNaissance' 
				) 
		))->add('sendForm', ButtonType::class, array(
				'label' => 'Enregister', 
				'attr' => array(
						'type' => 'submit' 
				) 
		))->add('btnReset', ButtonType::class, array(
				'label' => 'Effacer', 
				'attr' => array(
						'type' => 'button' 
				) 
		))->add('btnQuit', ButtonType::class, array(
				'label' => 'Fermer', 
				'attr' => array(
						'type' => 'button' 
				) 
		));
		
		$this->_builder = $builder;
	}

	public function buildView(FormView $view, FormInterface $form, array $options) {
		$view->vars['method'] = $options['method'];
		$view->vars['id'] = $options['id'];
		$view->vars['legend'] = $options['legend'];
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
				'method' => 'POST', 
				'id' => 'inscription', 
				'legend' => "Inscription", 
				'data_class' => 'Gosyl\CommonBundle\Entity\ParamUsers', 
				'cascade_validation' => true, 
				'error_bubbling' => true, 
				"validation_groups" => array(
						"Inscription" 
				) 
		));
	}

	/*
	 * public function getBlockPrefix() {
	 * return '';
	 * }
	 */
	
	// For Symfony 2.x
	public function getName() {
		return 'inscription';
	}
}