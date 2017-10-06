<?php
namespace Gosyl\CommonBundle\Twig\Menu;

class Deconnexion extends AbstractMenu {
    protected $sUrl = 'logout';
    protected $sTitle = 'Déconnexion';
    protected $bVerifRole = true;
    protected $bForAnonymousOnly = true;
    protected $aRoles = array('IS_AUTHENTICATED_FULLY');
    protected $aSubMenu = null;
}