<?php

namespace Gosyl\CommonBundle\Twig\Menu;

class Utilisateurs extends AbstractMenu {
    protected $sUrl = 'gosyl_common_utilisateurs';
    protected $sTitle = 'Utilisateurs';
    protected $bVerifRole = true;
    protected $aRoles = array('ROLE_ADMIN');
    protected $aSubMenu = null;
}