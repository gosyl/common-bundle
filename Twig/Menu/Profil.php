<?php

namespace Gosyl\CommonBundle\Twig\Menu;

class Profil extends AbstractMenu {
    protected $type = 'button';
    
    protected $id = 'btnProfil';
    
    protected $label = 'Mon profil';
    
    protected $value = 'Profil';
    
    protected $data = 'common/utilisateurs/profil';
    
    public function __construct() {
        $this->subMenu = true;
    }
    
    public function getButton() {
        return '<button data-url="' . $this->baseUrl . $this->data . '" type="' . $this->type . '" id="' . $this->id . '" value="' . $this->value . '">' . $this->label . '</button>';
    }
}