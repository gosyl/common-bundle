<?php
namespace Gosyl\CommonBundle\Twig;

use Gosyl\CommonBundle\Entity\ParamUsers;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Gosyl\CommonBundle\Constantes;

class UsersActive extends \Twig_Extension {
	/**
	 * @var EntityManager
	 */
	protected $_oEntityManager;
	
	/**
	 * @var TokenStorage;
	 */
	protected $_oTokenStorage;
	
	public function __construct(EntityManager $oEM, TokenStorage $oToken) {
		$this->_oEntityManager = $oEM;
		$this->_oTokenStorage = $oToken;
	}
	
	public function getFunctions() {
		return array(
			new \Twig_SimpleFunction('menu', array($this, 'usersActiveFunction'), array('is_safe' => array('html')))
		);
	}
	
	public function usersActiveFunction() {
		$sContenu = '';
		
		if($this->_oTokenStorage->getToken()) {
			if($this->_oTokenStorage->getToken()->getUser()->getRoles()[0] == Constantes::ROLE_ADMIN ) {
				$aUsersActive = $this->_oEntityManager->getRepository('GosylCommonBundle:ParamUsers')->getActive();
				
				$sContenu = '<fieldset id="utilisateursConnectes">';
				
				$iNbUtilisateurs = count($aUsersActive);
				$bPlusieursUtilisateurs = $iNbUtilisateurs > 1;
				
				$sContenu .= '<legend>Utilisateur' . ($bPlusieursUtilisateurs ? 's' : '') . ' Connecté' . ($bPlusieursUtilisateurs ? 's' : '') . '</legend>';
				/**
				 * @var ParamUsers $oUser
				 */
				$i = 1;
				foreach ($aUsersActive as $oUser) {
					if($oUser->getRoles()[0] == Constantes::ROLE_ADMIN) {
						$sContenu .= '<span class="userAdmin">' . $oUser->getUsername() . '</span>';
					} else {
						$sContenu .= '<span>' . $oUser->getUsername() . '</span>';
					}
					if($i < $iNbUtilisateurs) {
						$sContenu .= ', ';
					}
					
					$i++;
				}
				$sContenu .= '</fieldset>';
			}
		}
		
		return $sContenu;
	}
	
	public function getName() {
		return 'twig.extension.usersActive';
	}
	
	
}