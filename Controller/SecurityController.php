<?php

namespace Gosyl\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Gosyl\CommonBundle\Form\LoginType;
use Gosyl\CommonBundle\Constantes;

class SecurityController extends Controller {

	/**
	 * @Route("/login_check", name="login_check")
	 */
	public function checkAction() {
		throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
	}

	/**
	 * @Route("/login", name="login")
	 */
	public function loginAction(Request $request) {
		$session = $request->getSession();
		
		if($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
		} else {
			$error = $session->get(Security::AUTHENTICATION_ERROR);
			$session->remove(Security::AUTHENTICATION_ERROR);
		}
		
		$lastUsername = $session->get(Security::LAST_USERNAME);
		
		$sLoginType = 'Gosyl\CommonBundle\Form\LoginType';
		
		$oFormLogin = $this->createForm($sLoginType, array(
				'lastUsername' => $lastUsername, 
				'action' => $this->generateUrl('login_check') 
		))->createView();
		
		return $this->renderLogin(array(
				'oFormLogin' => $oFormLogin, 
				'last_username' => $lastUsername, 
				'error' => $error, 
				'aDialogConnexion' => Constantes::$aDialogConnexion 
		));
	}

	/**
	 * @Route("/logout", name="logout")
	 */
	public function logoutAction() {
		throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
	}

	/**
	 * Renders the login template with the given parameters.
	 * Overwrite this function in
	 * an extended controller to provide additional data for the login template.
	 *
	 * @param array $data
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	protected function renderLogin(array $data) {
		return $this->render('GosylCommonBundle:Security:login.html.twig', $data);
	}
}
