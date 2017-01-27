<?php
namespace Gosyl\CommonBundle\Twig;

use Gosyl\CommonBundle\Entity\ParamUsers;
use Doctrine\ORM\EntityManager;
use Gosyl\CommonBundle\Entity\ParamUsersRepository;
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
			new \Twig_SimpleFunction('usersActive', array($this, 'usersActiveFunction'), array('is_safe' => array('html')))
		);
	}
	
	public function usersActiveFunction() {
		$sContenu = '';

        if ($this->_oTokenStorage->getToken() && $this->_oTokenStorage->getToken()->getUser() != 'anon.') {
			if($this->_oTokenStorage->getToken()->getUser()->getRoles()[0] == Constantes::ROLE_ADMIN ) {
                /**
                 * @var ParamUsersRepository $oParamUserRepos
                 */
                $oParamUserRepos = $this->_oEntityManager->getRepository('GosylCommonBundle:ParamUsers');
                $aUsersActive = $oParamUserRepos->getActive();

                $sContenu = '<div id="utilisateursConnectes" class="panel panel-primary">';
				
				$iNbUtilisateurs = count($aUsersActive);
				$bPlusieursUtilisateurs = $iNbUtilisateurs > 1;

                $sContenu .= '<div class="panel-heading">Utilisateur' . ($bPlusieursUtilisateurs ? 's' : '') . ' Connecté' . ($bPlusieursUtilisateurs ? 's' : '') . '</div>';
				/**
				 * @var ParamUsers $oUser
				 */
				$i = 1;
                $sContenu .= '<div class="panel-body">';
				foreach ($aUsersActive as $aUser) {
					if($aUser['roles'][0] == Constantes::ROLE_ADMIN) {
						$sContenu .= '<span class="userAdmin">' . $aUser['username'] . '</span>';
					} else {
						$sContenu .= '<span>' . $aUser['username'] . '</span>';
					}
					if($i < $iNbUtilisateurs) {
						$sContenu .= ', ';
					}
					
					$i++;
				}
                $sContenu .= '</div></div>';
			}
		}
		
		return $sContenu;
	}
	
	public function getName() {
		return 'twig.extension.usersActive';
	}
	
	
}