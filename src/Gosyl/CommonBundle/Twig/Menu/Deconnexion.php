<?php
namespace Gosyl\CommonBundle\Twig\Menu;

class Deconnexion extends AbstractMenu {
	protected $type = 'button';
	
	protected $id = 'btnDeconnexion';
	
	protected $class = '';
	
	protected $label = 'Déconnexion';
	
	protected $value = 'Deconnexion';
	
	protected $data = 'common/logout';
	
	public function getButton() {
		return '<button data-url="' . $this->baseUrl . $this->data . '" type="' . $this->type . '" id="' . $this->id . '" value="' . $this->value . '">' . $this->label . '</button>';
	}
}