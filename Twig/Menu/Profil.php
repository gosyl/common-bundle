<?php

namespace Gosyl\CommonBundle\Twig\Menu;

class Profil extends AbstractMenu {
    protected $sUrl = 'gosyl_common_profilutilisateur';
    protected $sTitle = 'Mon profil';
    protected $bVerifRole = true;
    protected $aRoles = array('ROLE_ADMIN', 'ROLE_USER');
    protected $aSubMenu = null;
}