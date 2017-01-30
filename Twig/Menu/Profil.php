<?php

namespace Gosyl\CommonBundle\Twig\Menu;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class Profil extends AbstractMenu {
    protected $sUrl = 'gosyl_common_profilutilisateur';
    protected $sTitle = 'Mon profil';
    protected $bVerifRole = true;
    protected $aRoles = array('ROLE_ADMIN', 'ROLE_USER');
    protected $aSubMenu = null;

    public function __construct(Router $router, AuthorizationChecker $autorization, $namespace) {
        parent::__construct($router, $autorization, $namespace);
    }
}