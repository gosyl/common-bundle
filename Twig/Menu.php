<?php
namespace Gosyl\CommonBundle\Twig;

use Gosyl\CommonBundle\Entity\ParamUsers;
use Gosyl\FileserverBundle\Constantes;

class Menu extends \Twig_Extension {
	protected $isAuthenticatedAnonymously = 0;
	
	/**
	 * @var ParamUsers
	 */
	protected $ident;
	
	protected $role = array();
	
	protected $_aButton = array(
		0 => array(
			'Connexion' => null,
			'Inscription' => null,
		),
		'ROLE_USER' => array(
			'Administration' => array(
					'Profil' => null,
			),
			null,
			'Deconnexion' => null,
		),
		'ROLE_ADMIN' => array(
			'Administration' => array(
					'Profil' => null,
					'Utilisateurs' => null,
			),
			null,
			'Deconnexion' => null,
		),
	);
	
	protected $_aBundles;
	
	protected $_basePath = '';
	
	public function getFunctions() {
		return array(
			new \Twig_SimpleFunction('menu', array($this, 'menuFunction'), array('is_safe' => array('html')))
		);
	}
	
	public function menuFunction($ident, $sbasePath = '') {
		$this->_basePath = $sbasePath;
		
		if(is_null($ident)) {
			$this->role[0] = $this->isAuthenticatedAnonymously;
		}//var_dump($this->role);die;
		
		$this->ident = $ident;
		
		$this->_integreMenuBundle();
		
		return $this->_getButtons();
	}
	
	public function getName() {
		return 'twig.extension.menu';
	}
	
	protected function _integreMenuBundle() {
		foreach ($this->_aBundles as $bundle) {
			$constantes = $bundle . '\\Constantes';
			if(class_exists($constantes)) {
				$this->_aButton = array_merge_recursive($constantes::$aMenu, $this->_aButton); 
			}
		}
	}
	
	protected function _getButtons($subMenu = null) {
		if(!is_null($this->ident)) {
			$this->role = $this->ident->getRoles();
		}
		
		
		$sContenu = '';
		if(is_null($subMenu)) {
			$aBtn = $this->_aButton[$this->role[0]];
		} else {
			$aBtn = $subMenu;
		}
		
		foreach ($aBtn as $nomClasse => $value) {
			if(is_int($nomClasse) && is_null($value) && !is_null($nomClasse)) {
				// espace vide
				$sContenu .= '<div>&nbsp;</div>';
			} else {
				$sClasse = $this->_getClasse($nomClasse);
				
				if(!$sClasse) {
					throw new \Exception('Classe ' . $nomClasse . " n'est pas trouvée !");
				}
				$oButton = new $sClasse;
				$oButton->setBaseUrl($this->_basePath);
				$sCssClass = '';
				
				if($oButton->getSubMenu() && $oButton->getShowSubMenu()) {
					$sCssClass .= 'btnSubMenu';
				} elseif($oButton->getSubMenu() && !$oButton->getShowSubMenu()) {
					$sCssClass .= 'btnSubMenu';
				} else {
                    $sCssClass .= 'btnMenu';
                }
                
                $sContenu .= '<div id="div' . ucfirst($oButton->getId()) . '" class="' . $sCssClass . '">';
                $sContenu .= $oButton->getButton('submit');
                
                if(!is_null($value) && is_array($value)) {
                	$sContenu .= $this->_getButtons($value);
                }
                
                $sContenu .= '</div>';
                $sContenu .= $this->_makeJs($oButton);
			}
		}
		
		return $sContenu;
	}
	
	protected function _getClasse($nomClasse) {
		$aDomaineRecherche = array(
			__NAMESPACE__ . '\\Menu\\' . $nomClasse,
		);
		
		foreach ($this->_aBundles as $bundle) {
			$constante = $bundle.'\\Constantes';
			if(isset($constante::$_menuBundlePrefix)) {
				$aDomaineRecherche[] = $constante::$_menuBundlePrefix . $nomClasse;
			} else {
				$aDomaineRecherche[] = $bundle . '\\Twig\\Menu\\' . $nomClasse;
			}
		}
		
		foreach ($aDomaineRecherche as $class) {
			if(class_exists($class)) {
				return $class;
			}
		}
		
		return false;
	}
	
	protected function _makeJs($oButton) {
		$sContenu = '<script type="text/javascript">';
		$sContenu .= '$(document).ready(function() {';
		if($oButton->getShowSubMenu()) {
			$sContenu .= '$("#' . $oButton->getId() . '").click(function() {
                          $("#div' . ucfirst($oButton->getId()) . ' > .btnSubMenu").toggle("slow");
                      });';
		} elseif ($oButton->getDialog()) {
			$sContenu .= '$("#' . $oButton->getId() . '").click(function() {
                          $("#dialog' . $oButton->getValue() . '").dialog("open");
                      });';
		} else {
			$sContenu .= '$("#' . $oButton->getId() . '").click(function() {
                          document.location = /*Gosyl.Common.rootPath + */$("#' . $oButton->getId() . '").data("url");
                      });';
		}
		$sContenu .= '});';
		$sContenu .= '</script>';
		
		return $sContenu;
	}
}