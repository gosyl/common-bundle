<?php
namespace Gosyl\CommonBundle\Twig;

use Symfony\Component\Form\FormBuilder;

class AfficheForm extends \Twig_Extension {
	/**
	 * @var string
	 */
	private $_legendFieldset = '';	
	
	/**
	 * 
	 * @var string $_sName
	 */
	private $_sName = '';
	
	/**
	 * @var \stdClass $_oJson
	 */
	private $_oJson;
	
	/**
	 * 
	 * @var array
	 */
	private $_aTypes;
	
	public function getFunctions() {
		return array(
				new \Twig_SimpleFunction('afficheForm', array($this, 'afficheFormFct'), array('is_safe' => array('html')))
		);
	}
	
	public function afficheFormFct(FormBuilder $oForm, $data = null) {
		if(!is_null($oForm->getAttribute('data-info'))) {
			$this->_getDataInfo($oForm->getAttribute('data-info'));
		}
		
		$contenu = '<fieldset id="fieldset_' . $this->_sName . '"><legend>' . $this->_legendFieldset . '</legend>';
		
		$contenu .= $this->_openFormTag($oForm);
		
		$this->_getTypes($oForm);
		foreach ($this->_aTypes as $oType) {
			
			switch (get_class($oType)) {
				case 'Symfony\Component\Form\FormBuilder':
					switch(get_class($oType->getType()->getInnerType())) {
						case 'Symfony\Component\Form\Extension\Core\Type\HiddenType':
							$contenu .= $this->_createInput($oType, false, true);
							break;
							
						case 'Symfony\Component\Form\Extension\Core\Type\CheckboxType':
						case 'Symfony\Component\Form\Extension\Core\Type\DateType':
						case 'Symfony\Component\Form\Extension\Core\Type\EmailType':
						case 'Symfony\Component\Form\Extension\Core\Type\TextType':
						case 'Symfony\Component\Form\Extension\Core\Type\PasswordType':
							$contenu .= $this->_createInput($oType);
							break;
							
						case 'Symfony\Component\Form\Extension\Core\Type\ChoiceType':
							$contenu .= $this->_createChoice($oType);
							break;
					}
					break;
					
				case 'Symfony\Component\Form\SubmitButtonBuilder':
					$contenu .= $this->_createButton($oType);
					break;
					
				case 'Symfony\Component\Form\ButtonBuilder':
					$contenu .= $this->_createButton($oType);					
					break;
			}
		}
		
		$contenu .= $this->_closeFormTag();
		
		$contenu .= '</fieldset>';
		
		return $contenu;
		
	}
	
	public function getName() {
		return 'twig.extension.afficheForm';
	}
	
	protected function _getDataInfo($sJson) {
		$oJson = json_decode($sJson);
		if(property_exists ( $oJson , 'legend' )) {
			$this->_legendFieldset = $oJson->legend;
		}
		
		if(property_exists($oJson, 'name')) {
			$this->_sName = $oJson->name;
		}
	}
	
	protected function _openFormTag(FormBuilder $oForm) {
		$contenu  = '<form';
		
		$aAttributes = $oForm->getAttributes();
		
		foreach ($aAttributes as $attr => $value) {
			if(!is_array($value) && !($attr == 'data-info')) {
				$contenu .= ' ' . $attr . '="' . $value . '"';
			}
		}
		
		$contenu .= '>';
		return $contenu;
	}
	
	protected function _closeFormTag() {
		return '</form>';
	}
	
	protected function _getTypes(FormBuilder $oForm) {
		$this->_aTypes = $oForm->all();		
	}
	
	protected function _createButton($oElement) {
		$sBtn = '<button';
		
		$sBtn .= ' id="'.$oElement->getName().'"';
		
		$sBtn .= ' name="'.$oElement->getName().'"';
		
		$sBtn .= ' type="'.$oElement->getType()->getName().'"';
				
		$aAttributes = $oElement->getAttributes();
		
		if(array_key_exists('attr', $aAttributes['data_collector/passed_options'])) {
			foreach ($aAttributes['data_collector/passed_options']['attr'] as $attr => $value) {
				if(!is_array($value)) {
					$sBtn .= ' ' . $attr . '="' . $value . '"';
				}
			}
		}
		$sBtn .= '>'.$oElement->getOption('label').'</button>';
		return $sBtn;
	}
	
	protected function _createInput($oElement, $message = false, $bHidden = false) {
		$sInput = '';
		
		if($message) {
			
		}
		
		$sInput .= '<div id="' . $oElement->getName() . '_element">';
		
		if(!$bHidden) {
			$sInput .= $this->_createLabel($oElement);
		}
		
		$sInput .= '<input';
		
		$sInput .= ' type="' . $oElement->getType()->getName() . '"';
		
		$sInput .= ' name="' . $oElement->getName() . '"';
		
		foreach ($oElement->getAttributes() as $attr => $value) {
			if(!is_array($value)) {
				$sInput .= ' ' . $attr . '="' . $value . '"';
			} else {
				if(array_key_exists('attr', $value)) {
					foreach ($value['attr'] as $prop => $val) {
						$sInput .= ' ' . $prop . '="' . $val . '"';
					}
				}
			}
		}
		
		$sInput .= ' />';
		
		$sInput .= '</div>';
		
		if(!$bHidden) {
			$sInput .= $this->_addEspace();
		}
		
		return $sInput;
	}
	
	protected function _createChoice($oElement) {
		$sElement = '';
		
		$bExpanded = $oElement->getOption('expanded');
		$bMultiple = $oElement->getOption('multiple');
		
		if(!$bExpanded) {
			$sElement .= $this->_createSelect($oElement, $bMultiple);
		} else {
			if($bMultiple) {
				$sElement .= $this->_createCheckbox($oElement);
			} else {
				$sElement .= $this->_createBtnRadio($oElement);
			}
		}
		
		return $sElement;
	}
	
	protected function _createSelect($oElement, $bMultiple) {
		$aAttr = $oElement->getOption('attr');
		
		$sSelect = '<div id="' . $oElement->getName() . '_element">';
		
		$sSelect .= $this->_createLabel($oElement);
		
		$sSelect .= '<select name="' . $oElement->getName() . '"  id="' . $aAttr['id'] . '"' . ($bMultiple ? ' multiple ': '') . '>';
		
		foreach ($oElement->getOption('choices') as $key => $value) {
			$sSelect .= '<option value="'.$key.'">'.$value.'</option>';
		}
		
		$sSelect .= '</select>';
		
		$sSelect .= '</div>' . $this->_addEspace();
		
		return $sSelect;
	}
	
	protected function _createCheckbox($oElement) {
		$sCheckBox = '';
	
		return $sCheckBox;
	}
	
	protected function _createBtnRadio($oElement) {
		$sBtnRadio = '';
	
		return $sBtnRadio;
	}
	
	protected function _createLabel($oElement) {
		$sLabel = '<label for="' . $oElement->getName() . '">';
		
		$sLabel .= $oElement->getOption('label') . ' : ';
		
		$sLabel .= '</label>';
		
		return nl2br($sLabel);
	}
	
	protected function _addEspace() {
		return '<div class="espace">&nbsp;</div>';
	}
}