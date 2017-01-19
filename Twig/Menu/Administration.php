<?php

namespace Gosyl\CommonBundle\Twig\Menu;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Administration extends AbstractMenu {
    protected $sUrl = null;
    protected $sTitle = 'Administration';
    protected $bVerifRole = true;
    protected $aRoles = array('ROLE_ADMIN');
    protected $aSubMenu = array(
        'Profil',
        'Utilisateurs'
    );
}