<?php

namespace Gosyl\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Gosyl\CommonBundle\Service\Users;
use Gosyl\CommonBundle\Constantes;
use Gosyl\CommonBundle\Entity\ParamUsers;
//use Gosyl\CommonBundle\Form\UserUpdateType;
use Symfony\Component\HttpFoundation\Request;
use Gosyl\CommonBundle\Service\Mail;

class UtilisateursController extends Controller {

	/**
	 * @Route("/utilisateurs", name="gosyl_common_utilisateurs")
	 */
	public function indexAction() {
		// Récupération des utilisateurs
		
		/**
		 * @var Users $oUsers
		 */
		$oUsers = $this->get('gosyl.common.service.user');
        $aResultsAllUsers = $oUsers->getAllUserForDataTable($this->generateUrl('gosyl_common_ajax_listerutilisateur')); // echo "<pre>"; var_dump($aResultsAllUsers);die('</pre>');
		                                                       
		// Récupération des privilèges
		$this->aPrivileges = Constantes::$aPrivileges;
		
		// Récupération du privilège du connecté
		/*
		 * $this->_oUser = $this->getUser();
		 * $aPrivilege = $this->_oUser->getRoles();
		 * $this->privilege = $aPrivilege[0];
		 */
		
		// Création du formulaire de modification d'un utilisateur
		$oFormModifUser = $this->_createFormModifUser(null);
		
		// Envoie des données à la vue
		return $this->render('GosylCommonBundle:Utilisateurs:index.html.twig', array(
				'aResultsAllUsers' => $aResultsAllUsers,
            'oFormModifUser' => $oFormModifUser->createView()
		));
	}

	/**
	 * @Route("/utilisateurs/profil", name="gosyl_common_profilutilisateur")
	 */
	public function profilAction(Request $request) {
    	//Récupération de l'utilisateur en cours
    	
    	/**
    	 * @var ParamUsers $oUser
    	 */
    	$oUser = $this->getUser();
    	
    	// Service Users
    	/**
    	 * @var Users $oSrvUsers
    	 */
    	$oSrvUsers = $this->get('gosyl.common.service.user');
        $aResultOneUser = $oSrvUsers->getOneUserForDataTable($oUser->getId(), $this->generateUrl('gosyl_common_ajax_listerutilisateur'));
    	
    	// Récupération des privilèges
    	$this->aPrivileges = Constantes::$aPrivileges;
    	
    	// Création du formulaire de modification d'un utilisateur
    	$oFormModifUser = $this->_createFormModifUser($oUser);
    	
    	return $this->render('GosylCommonBundle:Utilisateurs:profil.html.twig', array(
    		'aResultsAllUsers' => $aResultOneUser,
    		'oFormModifUser' => $oFormModifUser->createView()
    	));
	}

	/**
	 * @Route("/register", name="gosyl_common_inscription")
	 */
	public function registerAction(Request $request) {
    	$user = new ParamUsers();
    	/**
    	 * @var Mail $oSrvMail
    	 */
    	$oSrvMail = $this->get('gosyl.common.service.mail');
    	
    	/**
    	 * @var Users $oSrvUser
    	 */
    	$oSrvUser = $this->get('gosyl.common.service.user');
    	
    	$form = $this->createForm('Gosyl\CommonBundle\Form\RegistrationType', $user);
    	
    	$form->handleRequest($request);
    	
    	if($form->isSubmitted() && $form->isValid()) {
    		$password = $this->get('security.password_encoder')
    			->encodePassword($user, $user->getPassword());
    		
    		$user->setPassword($password);
    		
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($user);
    		$em->flush();
    		
    		// Envoi d'un e-mail à l'utilisateur
    		$oSrvMail->envoyerMailValiderNouvelUtilisateur($user->getUsername(), $user->getName(), $user->getPrenom(), $user->getEmail(), $oSrvUser->getAdmin());
    		
    		return $this->render('GosylCommonBundle:Utilisateurs:inscription.html.twig', array(
    			'oFormInscription' => $form->createView(),
    			'aDialogInscription' => Constantes::$aDialogInscription,
    			'messageInscriptionOk' => true,
    			'msg' => 'inscriptionOk',
    		));
    	}
    	
    	return $this->render('GosylCommonBundle:Utilisateurs:inscription.html.twig', array(
    		'oFormInscription' => $form->createView(),
    		'aDialogInscription' => Constantes::$aDialogInscription,
    		'messageInscriptionOk' => false,
    	));
	}
	
	protected function _createFormModifUser(ParamUsers $oUser = null) {
		$oUser = is_null($oUser) ? new ParamUsers(): $oUser;

        $oForm = $this->createForm('Gosyl\CommonBundle\Form\UserUpdateType', $oUser);//array('oUser' => $oUser, 'aPrivileges' => $this->aPrivileges));
	
		return $oForm;
	}
}
