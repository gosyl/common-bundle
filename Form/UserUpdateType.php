<?php
namespace Gosyl\CommonBundle\Form;


use Gosyl\CommonBundle\Constantes;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\FormBuilderInterface;
use Gosyl\CommonBundle\Entity\ParamUsers;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Validator\Constraints as Assert;


class UserUpdateType extends AbstractType {
    protected $_aPrivileges;

    /**
     * @var ParamUsers
     */
    protected $_oUser;

    /*public function __construct(array $aPrivileges, ParamUsers $oUser = null) {

        $this->_oUser = $oUser;
    }*/

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->_aPrivileges = Constantes::$aPrivileges;
        $this->_oUser = $options['data'];
        //$this->_aPrivileges = $options['data']['aPrivileges'];
        $this->buildUserForm($builder, $options);
    }

    public function buildUserForm(FormBuilderInterface $builder, array $options) {
        $builder->setAttribute('method', $options['method'])
            ->setAttribute('id', $options['id'])
            ->setAttribute('legend', $options['legend'])
            ->add('id', HiddenType::class, array('attr' => array('id' => 'id')))
            ->add('username', TextType::class, array('label' => "Nom d'utilisateur", 'attr' => array('id' => 'username')))
            ->add('name', TextType::class, array('label' => 'Nom', 'attr' => array('id' => 'nom')))
            ->add('prenom', TextType::class, array('label' => 'Prenom', 'attr' => array('id' => 'prenom')))
            ->add('dateNaissance', DateType::class, array('label' => 'Date de naissance', 'widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input'  => 'datetime', 'error_bubbling' => true, 'attr' => array('id' => 'dateNaissance')))
            ->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options'  => array('label' => 'Mot de passe'),
                    'second_options' => array('label' => 'Répétez le mot de passe'),
                    'mapped' => false
                )
            )
            ->add('email', EmailType::class, array(
                'label' => 'Adresse e-mail',
                'attr' => array(
                    'id' => 'email',
                    'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,63}$'
                ),
                'data' => ParamUsers::class
            ));

        if(is_null($this->_oUser->getId())) {
            $builder->add('oldRole', HiddenType::class, array('attr' => array('id' => 'oldRole'), 'label' => 'oldRole'))
                ->add('roles', ChoiceType::class, array('choices' => $this->_aPrivileges, 'expanded' => false, 'multiple' => false, 'choices_as_values' => true, 'mapped' => false));
        }

        $builder->add('sendForm', ButtonType::class, array('label' => 'Enregister', 'attr' => array('type' => 'button')))
            ->add('btnCancel', ResetType::class, array(
                'label' => 'Effacer'
            ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        foreach ($options as $key => $value) {
            $view->vars[$key] = $value;
        }
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'method' => 'POST',
            'id' => 'modification',
            'legend' => "Modification",
            'data_class' => ParamUsers::class,
            "validation_groups" => array("Update"),
            'action' => ''
        ));
    }

    // For Symfony 2.x
    public function getName() {
        return 'gosyl_user_modification';
    }

    public function getBlockPrefix() {
        return '';
    }
}