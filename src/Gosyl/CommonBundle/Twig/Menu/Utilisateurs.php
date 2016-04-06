<?php

namespace Gosyl\CommonBundle\Twig\Menu;

class Utilisateurs extends AbstractMenu {
    protected $type = 'button';
    
    protected $id = 'btnUtilisateur';
    
    protected $label = 'Utilisateurs';
    
    protected $value = 'Utilisateurs';
    
    protected $data = 'common/utilisateurs';
    
    public function __construct() {
        $this->subMenu = true;
    }
    
    public function getButton() {
        return '<button data-url="' . $this->baseUrl . $this->data . '" type="' . $this->type . '" id="' . $this->id . '" value="' . $this->value . '">' . $this->label . '</button>';
    }
}