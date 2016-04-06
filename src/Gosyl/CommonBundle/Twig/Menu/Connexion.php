<?php
namespace Gosyl\CommonBundle\Twig\Menu;

class Connexion extends AbstractMenu {
	protected $type = 'button';
	
	protected $id = 'btnConnexionUser';
	
	protected $class = '';
	
	protected $label = 'Connexion';
	
	protected $value = 'Connexion';
	
	protected $data = 'common/login';
	
	public function __construct() {
		$this->bDialog = false;
	}
	
	public function getButton() {
		return '<button data-url="' . $this->baseUrl . $this->data . '" type="' . $this->type . '" id="' . $this->id . '" value="' . $this->value . '">' . $this->label . '</button>';
	}
}