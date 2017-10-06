<?php
namespace Gosyl\CommonBundle\Twig\Menu;

class Inscription extends AbstractMenu {
    protected $sUrl = 'gosyl_common_inscription';
    protected $sTitle = 'Inscription';
    protected $bVerifRole = true;
    protected $bForAnonymousOnly = true;
    protected $aRoles = null;
    protected $aSubMenu = null;
}