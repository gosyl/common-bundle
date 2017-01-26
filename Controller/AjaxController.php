<?php

namespace Gosyl\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Gosyl\CommonBundle\Entity\ParamUsers;
use Gosyl\CommonBundle\Service\Users;
use Symfony\Component\HttpFoundation\Request;
use Gosyl\CommonBundle\Service\Mail;
use Gosyl\CommonBundle\Constantes;
use Gosyl\CommonBundle\Form\UserUpdateType;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AjaxController extends Controller {
	/**
	 * @var ParamUsers
	 */
	private $_oUser;
	
	/**
	 * @var array $aPrivileges
	 */
	private $aPrivileges;
	private $privilege;
	
	/**
	 * @Route("/ajax/banutilisateur", name="gosyl_common_ajax_banutilisateur")
	 */
	public function banutilisateurAction(Request $request) {
		/**
		 * @var Mail $oSrvMail
		 */
		$oSrvMail = $this->get('gosyl.common.service.mail');
		
		$oUserAdmin = $this->getUser();
		
		/**
		 * @var Users $oUser
		 */
		$oUser = $this->get('gosyl.common.service.user');
		
		$iIdUser = $request->get('id');
		$sMode = $request->get('mode');
		
		$aResult = $oUser->banUtilisateur($iIdUser, $sMode);
		$oUserInfo = $oUser->getUserById($iIdUser);
		
		if($sMode == 'active') {
			$oSrvMail->envoyerMailCompteValide($oUserInfo, $oUserAdmin);
		} else {
			$oSrvMail->envoyerMailCompteInvalide($oUserInfo, $oUserAdmin);
		}
		
		return $this->_sendJson($aResult);
		
	}

	/**
	 * @Route("/ajax/listerutilisateur/{id}", name="gosyl_common_ajax_listerutilisateur")
	 */
	public function listerutilisateurAction($id = null) {
		$oActualUser = $this->getUser();
		
		/**
		 * @var Users $oUser
		 */
		$oUser = $this->get('gosyl.common.service.user');
		$aResults = $oUser->listerUtilisateursForDataTable($oActualUser, $id);
		
		return $this->_sendJson($aResults);
	}

	/**
	 * @Route("/ajax/modifierutilisateur", name="gosyl_common_ajax_modifierutilisateur")
	 */
	public function modifierutilisateurAction(Request $request) {
		$aReturn = array();
		
		/**
		 * @var Users $oServUser
		 */
		$oServUser = $this->get('gosyl.common.service.user');
		
		$aPrivileges = Constantes::$aPrivileges;
        $aPost = $request->request->all();
        $oUser = $this->getDoctrine()->getRepository(ParamUsers::class)->findOneBy(array('id' => $aPost['id']));


        if (isset($aPost['password']['first']) && isset($aPost['password']['second'])) {
			if($aPost['password']['first'] != $aPost['password']['second']) {
				$aReturn = array('error' => true, 'result' => null, 'reasons' => array('password_first' => array('notEqual' => 'Les mots de passe ne sont pas identiques')));
				return $this->_sendJson($aReturn);
			} else {
				$encoder = $this->container->get('security.password_encoder');
				$encoded = $encoder->encodePassword($oUser, $aPost['password']['first']);
				
				$oServUser->updatePassword($encoded, $this->getUser()->getId());
				$aPost['password'] = 'done';
			}
		}
		
		if(isset($aPost['oldRole'])) {
			$oldRole = $aPost['oldRole'];
			unset($aPost['oldRole']);
		}

        $oForm = $this->createForm(UserUpdateType::class, $oUser);

        $oForm->handleRequest($request);

		if($oForm->isValid()) {
            unset($aPost['_token']);
            /**
             * @var ParamUsers $oNewUser
             */
            $oNewUser = $oForm->getData();

            /**
             * @var ValidatorInterface $oValidator
             */
            $oValidator = $this->get('validator');
            $errors = $oValidator->validate($oNewUser);
            if (count($errors) > 0) {
                $aReasons = array();
                foreach ($errors as $error) {
                    $aReasons[$error->getPropertyPath()] = $error->getMessage();
                }

                $aReturn = array('error' => true, 'reasons' => $aReasons);
            } else {
                if (count($aPost) == 1 && isset($aPost['id']) && !isset($aPost['password'])) {
                    $aReturn = array('error' => true, 'result' => null, 'noResult' => true, 'reasons' => array('form' => 'Aucune modification effectuée'));
                } elseif (count($aPost) != 1 && !is_null($aPost)) {
                    $aReturn = $oServUser->updateUtilisateur($aPost, isset($oldRole) ? $oldRole : null);
                } elseif (count($aPost) == 1 && isset($aPost['id']) && $aPost['password'] == 'done') {
                    $aReturn = array('error' => false, 'result' => null, 'reasons' => null);
                }
            }
		} else {
			$aReturn = array('error' => true, 'result' => null, 'reasons' => null);
			foreach ($oForm->getErrors(true) as $key => $value) {
				$aReturn['reasons'][$key] = $value;
			}
		}

		return $this->_sendJson($aReturn);
    }

    /**
     * @Route("/ajax/restaurerutilisateur", name="gosyl_common_ajax_restaurerutilisateur")
     */
	public function restaurerutilisateurAction(Request $request) {
		/**
		 * @var Users $oUser
		 */
		$oUser = $this->get('gosyl.common.service.user');
		$result = $oUser->restaurerUtilisateur($request->get('id'));
		
		return $this->_sendJson($result);
	}

	/**
	 * @Route("/ajax/supprimerutilisateur", name="gosyl_common_ajax_supprimerutilisateur")
	 */
	public function supprimerutilisateurAction(Request $request) {
		/**
		 * 
		 * @var Users $oUser
		 */
		$oUser = $this->get('gosyl.common.service.user');
		
		/**
		 * @var Mail $oSrvMail
		 */
		$oSrvMail = $this->get('gosyl.common.service.mail');
		
		$oUserAdmin = $this->getUser();
		
		$iIdUser = $request->get('id');
		
		$result = $oUser->supprimerUtilisateur($request->get('id'));
		$oUserInfo = $oUser->getUserById($iIdUser);
		
		$oSrvMail->envoyerMailCompteSupprime($oUserInfo, $oUserAdmin);
		
		return $this->_sendJson($result);
	}
	
	/**
	 * @param mixed $data
	 * @return Response
	 */
	protected function _sendJson($data) {
		$response = new Response(json_encode($data));
		$response->headers->set('Content-type', 'application/json');
	
		return $response;
	}
}
