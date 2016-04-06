<?php

namespace Gosyl\CommonBundle\Twig\Menu;

class Administration extends AbstractMenu {
    protected $type = 'button';
    
    protected $id = 'btnAdministration';
    
    protected $label = 'Administration';
    
    protected $value = 'Administration';
    
    protected $data = 'administration';
    
    public function __construct() {
        $this->showSubMenu = true;
    }
    
    public function getButton() {
        return '<button data-url="' . $this->baseUrl . $this->data . '" type="' . $this->type . '" id="' . $this->id . '" value="' . $this->value . '">' . $this->label . '</button>';
    }
}