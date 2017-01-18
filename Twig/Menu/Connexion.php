<?php
namespace Gosyl\CommonBundle\Twig\Menu;

class Connexion extends AbstractMenu {
    protected $sUrl = 'login';
    protected $sTitle = 'Connexion';
    protected $bVerifRole = true;
    protected $bForAnonymousOnly = true;
    protected $aRoles = null;
    protected $aSubMenu = null;
}