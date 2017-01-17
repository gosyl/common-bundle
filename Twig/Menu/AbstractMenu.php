<?php
namespace Gosyl\CommonBundle\Twig\Menu;

abstract class AbstractMenu {
	protected $type;
	
	protected $id;
	
	protected $class = 'btnMenu';
	
	protected $label;
	
	protected $value;
	
	protected $data;
	
	protected $subMenu = false;
	
	protected $baseUrl = '';
	
	/**
	 * Bouton qui ouvre un sous-menu
	 * @var boolean
	 */
	protected $showSubMenu = false;
	
	/**
	 * Bouton qui ouvre une boite de dialolgue
	 * @var boolean
	 */
	protected $bDialog = false;
	
	public function getType() {
		return $this->type;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getClass() {
		return $this->class;
	}
	
	public function getLabel() {
		return $this->label;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function getSubMenu() {
		return $this->subMenu;
	}
	
	public function getShowSubMenu() {
		return $this->showSubMenu;
	}
	
	public function getDialog() {
		return $this->bDialog;
	}
	
	public function getButton() {
		return '<button data-url="' . $this->data . '" type="' . $this->type . '" id="' . $this->id . '" value="' . $this->value . '">' . $this->label . '</button>';
	}
	
	public function setBaseUrl($baseUrl) {
		$this->baseUrl = $baseUrl;
		
		return $this;
	}
}