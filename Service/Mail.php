<?php
namespace Gosyl\CommonBundle\Service;

use Gosyl\CommonBundle\Entity\ParamUsers;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;

class Mail {
	/**
	 * @var \Swift_Message
	 */
	private $_swiftInstance;
	
	/**
	 * @var \Swift_Mailer
	 */
	private $_mailer;
	
	/**
	 * @var Container
	 */
	private $_oContainer;
	
	/**
	 * @var string
	 */
	private $_contentType = 'text/html';
	
	/**
	 * @var string
	 */
	private $_charset = 'UTF-8';
	
	/**
	 * 
	 * @param unknown $mailer
	 * @param UtilisateursController $ctrl
	 */
	
	public function __construct(\Swift_Mailer $mailer, Container $oContainer) {
		$this->_swiftInstance = \Swift_Message::newInstance();
		
		$this->_mailer = $mailer;
		
		$this->_oContainer = $oContainer;
	}
	
	/**
	 * Envoi d'un mail à chaque admin du site lors d'une inscripion
	 * 
	 * @param string $sUsername
	 * @param string $sNom
	 * @param string $sPrenom
	 * @param string $sMail
	 * @param array $aListeAdmin
	 * @return void
	 */
	public function envoyerMailValiderNouvelUtilisateur($sUsername, $sNom, $sPrenom, $sMail, array $aListeAdmin) {
		$this->_swiftInstance->setSubject("Inscription d'un nouvel utilisateur");
		$addresses = array();
		foreach ($aListeAdmin as $aAdmin) {
			$this->_swiftInstance->addTo($aAdmin['email'], $aAdmin['username']);
			$addresses[] = $aAdmin['email'];
		}
		$this->_swiftInstance->addFrom($sMail, $sUsername)
			->setBody(
				$this->renderView(
					'GosylCommonBundle:Mail:newUserAlert.html.twig',
					array(
						'newUsername' => $sUsername,
						'newName' => $sNom,
						'newUserFirstname' => $sPrenom
					)
				),
				$this->_contentType,
				$this->_charset
			);
		
		$this->_swiftInstance->setReplyTo($addresses);
			
		$nbRecept = $this->_sendMail();
		if($nbRecept != count($aListeAdmin)) {
			return array('error' => true);
		} else {
			return array('error' => false);
		}
	}
	
	/**
	 * Envoi d'un mail d'information à un utilisateur - Compte activé
	 * 
	 * @param ParamUsers $oInfoUser
	 * @param ParamUsers $oUserAdmin
	 */
	public function envoyerMailCompteValide(ParamUsers $oInfoUser, ParamUsers $oUserAdmin) {
		$this->_swiftInstance->setSubject("Validation de votre compte");
		
		$sUsername = $oInfoUser->getUsername();
		$sEmail = $oInfoUser->getEmail();
		
		$this->_swiftInstance->setFrom($oUserAdmin->getEmail(), $oUserAdmin->getUsername());
		
		$this->_swiftInstance->setTo($sEmail, $sUsername)
			 ->setBody(
			 	$this->renderView(
			 		'GosylCommonBundle:Mail:userIsValid.html.twig',
			 		array(
			 			'username' => $sUsername
			 		)
			 	),
			 	$this->_contentType,
			 	$this->_charset
			 );
		
		$this->_sendMail();
	}
	
	/**
	 * Envoi d'un mail d'information à un utilisateur - Compte desactivé
	 *
	 * @param ParamUsers $oInfoUser
	 * @param ParamUsers $oUserAdmin
	 */
	public function envoyerMailCompteInvalide(ParamUsers $oInfoUser, ParamUsers $oUserAdmin) {
		$this->_swiftInstance->setSubject("Désactivation de votre compte");
	
		$sUsername = $oInfoUser->getUsername();
		$sEmail = $oInfoUser->getEmail();
		
		$this->_swiftInstance->setFrom($oUserAdmin->getEmail(), $oUserAdmin->getUsername());
	
		$this->_swiftInstance->setTo($sEmail, $sUsername)
		->setBody(
				$this->renderView(
						'GosylCommonBundle:Mail:userIsInvalid.html.twig',
						array(
				 			'username' => $sUsername
						)
				 	),
				$this->_contentType,
				$this->_charset
				);
	
		$this->_sendMail();
	}
	
	/**
	 * Envoi d'un mail d'information à un utilisateur - Compte supprimé
	 *
	 * @param ParamUsers $oInfoUser
	 * @param ParamUsers $oUserAdmin
	 */
	public function envoyerMailCompteSupprime(ParamUsers $oInfoUser, ParamUsers $oUserAdmin) {
		$this->_swiftInstance->setSubject("Suppression de votre compte");
	
		$sUsername = $oInfoUser->getUsername();
		$sEmail = $oInfoUser->getEmail();
	
		$this->_swiftInstance->setFrom($oUserAdmin->getEmail(), $oUserAdmin->getUsername());
	
		$this->_swiftInstance->setTo($sEmail, $sUsername)
		->setBody(
				$this->renderView(
						'GosylCommonBundle:Mail:userIsDeleted.html.twig',
						array(
							'username' => $sUsername
						)
					),
				$this->_contentType,
				$this->_charset
				);
	
		$this->_sendMail();
	}
	
	/**
     * Returns a rendered view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     */
    protected function renderView($view, array $parameters = array())
    {
        if ($this->_oContainer->has('templating')) {
            return $this->_oContainer->get('templating')->render($view, $parameters);
        }

        if (!$this->_oContainer->has('twig')) {
            throw new \LogicException('You can not use the "renderView" method if the Templating Component or the Twig Bundle are not available.');
        }

        return $this->_oContainer->get('twig')->render($view, $parameters);
    }
	
	/**
	 * Envoi du mail
	 * 
	 * @param void
	 * @return int
	 */
	protected function _sendMail() {
		return $this->_mailer->send($this->_swiftInstance);
	}
}