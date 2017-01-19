<?php
namespace Gosyl\CommonBundle\Twig\Menu;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

abstract class AbstractMenu {
    /**
     * @var Router
     */
    protected $router = null;

    /**
     * @var AuthorizationChecker
     */
    protected $autorization = null;

    /**
     * @var string|null
     */
    protected $sUrl;

    /**
     * @var string
     */
    protected $sTitle;

    /**
     * @var boolean
     */
    protected $bVerifRole;

    /**
     * @var boolean
     */
    protected $bRouteActuelle;

    /**
     * @var boolean
     */
    protected $bForAnonymousOnly;

    /**
     * @var array|null
     */
    protected $aRoles;

    /**
     * @var array|null
     */
    protected $aSubMenu;

    /**
     * @return string
     */
    public function getUrl() {
        return $this->sUrl;
    }

    /**
     * @param string $sUrl
     */
    public function setUrl($sUrl) {
        $this->sUrl = $sUrl;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->sTitle;
    }

    /**
     * @param string $sTitle
     */
    public function setTitle($sTitle) {
        $this->sTitle = $sTitle;
    }

    /**
     * @return bool
     */
    public function isVerifRole() {
        return $this->bVerifRole;
    }

    /**
     * @return boolean
     */
    public function isRouteActuelle() {
        return $this->bRouteActuelle;
    }

    /**
     * @param boolean $bRouteActuelle
     */
    public function setBRouteActuelle($bRouteActuelle) {
        $this->bRouteActuelle = $bRouteActuelle;
    }

    /**
     * @param bool $bVerifRole
     */
    public function setVerifRole($bVerifRole) {
        $this->bVerifRole = $bVerifRole;
    }

    /**
     * @return boolean
     */
    public function isForAnonymousOnly() {
        return $this->bForAnonymousOnly;
    }

    /**
     * @param boolean $bForAnonymousOnly
     */
    public function setForAnonymousOnly($bForAnonymousOnly) {
        $this->bForAnonymousOnly = $bForAnonymousOnly;
    }

    /**
     * @return array|null
     */
    public function getRoles() {
        return $this->aRoles;
    }

    /**
     * @param array|null $aRoles
     */
    public function setARoles($aRoles) {
        $this->aRoles = $aRoles;
    }

    /**
     * @return array|null
     */
    public function getSubMenu() {
        return $this->aSubMenu;
    }

    /**
     * @param array|null $aSubMenu
     */
    public function setSubMenu($aSubMenu) {
        $this->aSubMenu = $aSubMenu;
    }

    public function __construct(Router $router, AuthorizationChecker $autorization) {
        if(is_null($this->router)) {
            $this->router = $router;
        }

        if(is_null($this->autorization)) {
            $this->autorization = $autorization;
        }
    }

    /**
     * @param boolean $bIsSeparator
     * @return string
     */
    public function getLink($bIsSeparator = false) {
        if(is_array($this->aRoles) && $this->bVerifRole) {
            $bAuth = false;
            foreach($this->aRoles as $role) {
                if($this->autorization->isGranted($role)) {
                    $bAuth = true;
                    break;
                }
            }

            if($bAuth && is_array($this->aSubMenu)) {
                return $this->_getSousMenu();
            } elseif($bAuth && !is_array($this->aSubMenu)) {
                return $this->_getLien($bIsSeparator);
            } else {
                return '';
            }
        } elseif ($this->bVerifRole && is_null($this->aRoles)) {
            if ($this->autorization->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')) {
                if (is_array($this->aSubMenu)) {
                    return $this->_getSousMenu();
                } elseif (!is_array($this->aSubMenu)) {
                    return $this->_getLien($bIsSeparator);
                } else {
                    return '';
                }
            } else {
                return '';
            }
        } elseif(!$this->bVerifRole) {
            if(is_array($this->aSubMenu)) {
                return $this->_getSousMenu();
            } elseif(!is_array($this->aSubMenu)) {
                return $this->_getLien($bIsSeparator);
            } else {
                return '';
            }
        }
    }

    /**
     * @return string
     */
    protected function _getSousMenu() {
        $sContenu = '';
        $sContenu .= '<li class="dropdown">';
        $sContenu .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . $this->sTitle . '</a>';
        // ouverture du sous menu
        $sContenu .= '<ul class="dropdown-menu">';
        foreach($this->aSubMenu as $sousMenu) {
            if(is_null($sousMenu)) {
                $bSeparation = true;
            } else {
                /**
                 * @var AbstractMenu $oSousMenu
                 */
                $sClasse = "\\" . __NAMESPACE__ . '\\' . $sousMenu;
                $oSousMenu = new $sClasse($this->router, $this->autorization);
                $bSeparation = false;
            }
            $sContenu .= $oSousMenu->getLink($bSeparation);
        }
        $sContenu .= '</ul>';
        $sContenu .= '</li>';
        return $sContenu;
    }

    /**
     * @return string
     */
    protected function _getLien($bIsSeparator) {
        if($bIsSeparator) {
            return '<li class="divider" role="separator"></li>';
        }

        $sLien = $this->router->generate($this->sUrl);
        return '<li><a href="'.$sLien.'">' . $this->sTitle . '</a></li>';
    }
}