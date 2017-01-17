<?php
namespace Gosyl\CommonBundle\Twig\Message;

class MessageOptions {
	public static $aDefaultOptions = array(
			'options' => array(
					'appendTo' => null,
					'autoOpen' => true,
					'buttons' => array(
							0 => array(
									'text' => 'Ok',
									'click' => array(
											'function' => '{
                            					$(this).dialog("close");
                        					}',
									),
							),
					),
					'closeOnEscape' => false,
					'closeText' => null,
					'dialogClass' => 'noClose',
					'draggable' => false,
					'height' => null,
					'hide' => null,
					'maxHeight' => null,
					'maxWidth' => null,
					'minHeight' => null,
					'minWidth' => null,
					'modal' => true,
					'position' => null,
					'resizable' => false,
					'show' => null,
					'title' => '',
					'width' => null,
			),
			'events' => array(
					'beforeClose' => null,
					'close' => null,
					'create' => null,
					'drag' => null,
					'dragStart' => null,
					'dragStop' => null,
					'focus' => null,
					'open' => array(
							'function' => '{
		                         $(".ui-widget-overlay").attr("style","z-index: 100; background: none repeat scroll 0 0 #000000; height: 100%; left: 0; position: fixed; top: 0; width: 100%; opacity: 0.5;");
		                         $(".ui-dialog").css("z-index","101");
		                     }',
					),
					'resize' => null,
					'resizeStart' => null,
					'resizeStop' => null,
			),
	);
	
	
	public static function getOptions(array $message) {
		$contenu = '';
		$aOptions = $message['dialogOptions'];
	
		if(isset($aOptions['options'])) {
			$contenu = self::decomposeOption($aOptions['options']);
		}
	
		if(isset($aOptions['events'])) {
			$contenu .= self::decomposeOption($aOptions['events']);
		}
	
		return $contenu;
	}
	
	public static function decomposeOption(array $aOption) {
		$contenu = '';
		foreach ($aOption as $key => $value) {
			if(!is_null($value)) {
				if(is_bool($value)) {
					$contenu .= $key . ': ';
					$contenu .= $value ? ('true,' . "\n") : ('false,'."\n");
				} elseif(is_string($value) && $key != 'fusion') {
					$contenu .= $key . ': "'. $value . '",'."\n";
				} elseif(is_array($value)) {
					if(!empty($value)) {
						$bFct = true;
						foreach (array_keys($value) as $arrayKey) {
							if(is_string($arrayKey)) {
								$bFct = true;
							} elseif(is_int($arrayKey)) {
								$bFct = false;
							}
						}
						if($bFct) {
							foreach ($value as $subKey => $subValue) {
								if(is_string($subKey)) {
									$contenu .= $key . ': ' . $subKey . '(event, ui) ' . $subValue . ", \n";
								}
							}
						} else {
							$contenu .= $key . ': [';
							foreach ($value as $subKey => $subValue) {
								if (is_int($subKey)) {
									$contenu .= '{' . self::decomposeOption($subValue). '},';
								}
							}
							$contenu .= '],'."\n";
						}
					} else {
						$contenu .= $key . ': null,' . "\n";
					}
				}
			}
		}
	
		return $contenu;
	}
}