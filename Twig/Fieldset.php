<?php
namespace Gosyl\CommonBundle\Twig;

class Fieldset extends \Twig_Extension {
	public function getFunctions() {
		return array(
				new \Twig_SimpleFunction('fieldset', array($this, 'fieldsetFunction'), array('is_safe' => array('html')))
		);
	}
	
	public function fieldsetFunction($data, $bIsAlreadyRendered = false) {
		if($bIsAlreadyRendered) {
			return $data;
		} else {
			$contenu = '';
			/** Format attendu
			 $data = array(
			 	'legend' => '',
			 	'contenu' => '',
			 	'options => array(
			 		'fieldsetAttr => array();
			 		'legendAttr' => array();
			 	),
			 )
			 */
			$contenu .= '<fieldset' . (isset($data['options']['fieldsetAttr']) ? $this->getAttrValue($data['options']['fieldsetAttr']) : '') . '>';
			
				$contenu .= '<legend' . (isset($data['options']['legendAttr']) ? $this->getAttrValue($data['options']['legendAttr']) . '>' . $data['legend'] : '') . '</legend>';
				
				$contenu .= $data['contenu'];
			
			$contenu .= '</fieldset>';
			return $contenu;
		}
	}
	
	public function getName() {
		return 'twig.extension.fieldset';
	}
	
	protected function getAttrValue($aAttributes) {
		$contenu = '';
		
		foreach ($aAttributes as $attr => $value) {
			$contenu .= ' '.$attr.'="'.$value.'"';
		}
		
		return $contenu;
	}
}