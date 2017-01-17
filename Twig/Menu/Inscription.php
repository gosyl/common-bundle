<?php
namespace Gosyl\CommonBundle\Twig\Menu;

class Inscription extends AbstractMenu {
protected $type = 'button';
    
    protected $id = 'btnInscription';
    
    protected $class = '';
    
    protected $label = 'Inscription';
    
    protected $value = 'Inscription';
    
    protected $data = 'common/register';
    
    public function __construct() {
        $this->bDialog = false;
    }
    
    public function getButton() {
        return '<button data-url="' . $this->baseUrl . $this->data . '" type="' . $this->type . '" id="' . $this->id . '" value="' . $this->value . '">' . $this->label . '</button>';
    }
}