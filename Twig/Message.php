<?php
namespace Gosyl\CommonBundle\Twig;

use Gosyl\CommonBundle\Twig\Message\MessageOptions;

class Message extends \Twig_Extension {
	/**
	 * Constantes définissant le niveau d'alerte d'un message
	 * @var int
	 */
	const INFO = 1;
	const WARNING = 2;
	const ERROR = 3;
	const ALERT = 4;
	const QUESTION = 5;
	
	/**
	 * Tableaux qui relie les niveaux d'alerte à leurs noms utilisés dans le helper
	 * @var array
	 */
	public static $aTypeMessage = array(
			self::INFO => 'info',
			self::WARNING => 'warning',
			self::ALERT => 'alert',
			self::ERROR => 'error',
			self::QUESTION => 'question',
	
	);
	
	/**
	 * Tableau des messages par défaut
	 * @var array
	 */
	protected $_aMessage = array(
			'inscriptionOk' => array(
					'message' => 'Un email a été envoyé à l\'administrateur afin qu\'il valide l\'inscription.
				          <br />Un email vous sera envoyé lorsque votre compte sera actif.
				          <br />Merci de bien vouloir patienter...',
					'type' => self::INFO,
					'dialogOptions' => array(
							'events' => array(
									'close' => array(
											'data' => array(
													'function' => '{
						                                document.location = GLOBAL_basePath;
						                            }',
											),
											'ecrasement' => true
									),
							),
					),
			),
			'suppressionUtilisateur' => array(
					'message' => 'Êtes-vous sûr de vouloir supprimer cet utilisateur ?',
					'type' => self::QUESTION,
					'dialogOptions' => array(
							'options' => array(
									'autoOpen' => array(
											'data' => false,
											'ecrasement' => true,
									),
									'buttons' => array(
											'data' => array(
													0 => array(
															'text' => 'Oui',
															'click' => array(
																	'function' => '{
								                                        suppressionUtilisateur($(this).data(\'dataUtilisateur\'));
								                                    }',
															),
													),
													1 => array(
															'text' => 'Non',
															'click' => array(
																	'function' => '{
								                                        $(this).dialog(\'close\');
								                                    }',
															),
													),
											),
											'ecrasement' => true,
									),
							),
							//'events' => null/*array(
	
							//)*/
					),
					'idDialog' => 'dmdSupprUtilisateur',
			),
			'restaurationUtilisateur' => array(
					'message' => 'Êtes-vous sûr de vouloir restaurer cet utilisateur ?',
					'type' => self::QUESTION,
					'dialogOptions' => array(
							'options' => array(
									'autoOpen' => array(
											'data' => false,
											'ecrasement' => true,
									),
									'buttons' => array(
											'data' => array(
													0 => array(
															'text' => 'Oui',
															'click' => array(
																	'function' => '{
								                                        restoreUser($(this).data(\'dataUtilisateur\'));
								                                    }',
															),
													),
													1 => array(
															'text' => 'Non',
															'click' => array(
																	'function' => '{
								                                        $(this).dialog(\'close\');
								                                    }',
															),
													),
											),
											'ecrasement' => true,
									),
							),
					),
					'idDialog' => 'dmdRestUtilisateur',
			),
			'activationUtilisateur' => array(
					'message' => 'Êtes-vous sûr de vouloir activer cet utilisateur ?',
					'type' => self::QUESTION,
					'dialogOptions' => array(
							'options' => array(
									'autoOpen' => array(
											'data' => false,
											'ecrasement' => true,
									),
									'buttons' => array(
											'data' => array(
													0 => array(
															'text' => 'Oui',
															'click' => array(
																	'function' => '{
								                                        activeUser($(this).data(\'dataUtilisateur\'));
								                                    }',
															),
													),
													1 => array(
															'text' => 'Non',
															'click' => array(
																	'function' => '{
								                                        $(this).dialog(\'close\');
								                                    }',
															),
													),
											),
											'ecrasement' => true,
									),
							),
					),
					'idDialog' => 'dmdActUtilisateur',
			),
			'desactivationUtilisateur' => array(
					'message' => 'Êtes-vous sûr de vouloir desactiver cet utilisateur ?',
					'type' => self::QUESTION,
					'dialogOptions' => array(
							'options' => array(
									'autoOpen' => array(
											'data' => false,
											'ecrasement' => true,
									),
									'buttons' => array(
											'data' => array(
													0 => array(
															'text' => 'Oui',
															'click' => array(
																	'function' => '{
								                                        desactiveUser($(this).data(\'dataUtilisateur\'));
								                                    }',
															),
													),
													1 => array(
															'text' => 'Non',
															'click' => array(
																	'function' => '{
								                                        $(this).dialog(\'close\');
								                                    }',
															),
													),
											),
											'ecrasement' => true,
									),
							),
					),
					'idDialog' => 'dmdDesactiveUtilisateur',
			),
			'supprDossier' => array(
					'message' => 'Êtes-vous sûr de vouloir supprimer : <span id="listeSelection"></span> ?',
					'type' => self::QUESTION,
					'dialogOptions' => array(
							'options' => array(
									'autoOpen' => array(
											'data' => false,
											'ecrasement' => true,
									),
									'buttons' => array(
											'data' => array(
													0 => array(
															'text' => 'Oui',
															'click' => array(
																	'function' => '{
								                                        Gosyl.Fileserver.GestionFichiers.Ajax.supprDossiersAction();
								                                	}',
															),
													),
													1 => array(
															'text' => 'Non',
															'click' => array(
																	'function' => '{
								                                        $(this).dialog(\'close\');
								                                	}',
															),
													),
											),
											'ecrasement' => true,
									),
							),
					),
					'idDialog' => 'dmdSupprDossier',
			),
			'errorMsg' => array(
					'type' => self::ERROR,
					'dialogOptions' => array(
							'options' => array(
									'autoOpen' => array(
											'data' => false,
											'ecrasement' => true,
									),
									'buttons' => array(
											'data' => array(
													0 => array(
															'text' => 'Ok',
															'click' => array(
																	'function' => '{
								                                        $(this).dialog(\'close\');
								                                    }',
															),
													),
											),
											'ecrasement' => true,
									),
							),
					),
			),
			'inconnu' => array(
					'message' => 'Nom de message inconnu',
					'type' => self::ERROR,
			),
			'aucunMessage' => array(
					'message' => 'Aucun message à afficher',
					'type' => self::WARNING,
			),
			'param' => array(
					'message' => '',
					'type' => self::INFO,
			),
	);
	
	public function getFunctions() {
		return array(
				new \Twig_SimpleFunction('message', array($this, 'messageFct'), array('is_safe' => array('html')))
		);
	}
	
	public function messageFct($nomMessage = null, $message = null, $typeMessage = self::INFO, array $messageOptions = null) {
		$contenu = '';
		
		if(is_null($nomMessage)) {
			// On affiche un message non enregistré
			if(is_null($message)) {
				// Erreur aucun message parametré
				$aMessage = $this->_aMessage['aucunMessage'];
			} else {
				if(is_null($messageOptions)) {
					// On prend les options par défaut
					$aOptions = MessageOptions::$aDefaultOptions;
					$aMessage = array('type' => !is_null($typeMessage) ? $typeMessage : self::INFO, 'message' => $message, $aOptions);
				} else {
					// Options redéfinies
					$aOptions = $messageOptions;
					$aMessage = array_merge(array('type' => !is_null($typeMessage) ? $typeMessage : self::INFO, 'message' => $message), $aOptions);
				}
				// Construction du tableau
			
			
			}
		} else {
            $aMessage = $this->_aMessage[$nomMessage];
            
            if(!is_null($message)) {
                $aMessage['message'] = $message;
                if(isset($messageOptions)) {
                    $aMessage['dialogOptions'] = array_merge($aMessage['dialogOptions'], $messageOptions['dialogOptions']);
                    if(isset($messageOptions['idDialog'])) {
                        $aMessage['idDialog'] = $messageOptions['idDialog'];
                    }
                }
            }
        }
        
        if(!isset($aMessage['dialogOptions'])) {
        	// Si pas d'option défini
        	$aMessage['dialogOptions'] = MessageOptions::$aDefaultOptions;
        } else {
        
        
        	$aMessageDefault = MessageOptions::$aDefaultOptions;
        
        	// on fusionne les deux tableaux d'options
        	if(isset($aMessage['dialogOptions']['options'])) {
        		$aMessage['dialogOptions']['options'] = $this->_integreOptions('options', $aMessageDefault, $aMessage['dialogOptions']['options']);
        	} else {
        		$aMessage['dialogOptions']['options'] = MessageOptions::$aDefaultOptions['options'];
        	}
        
        	if(isset($aMessage['dialogOptions']['events']) && !is_null($aMessage['dialogOptions']['events'])) {
        		$aMessage['dialogOptions']['events'] = $this->_integreOptions('events', $aMessageDefault, $aMessage['dialogOptions']['events']);
        	} else {
        		$aMessage['dialogOptions']['events'] = MessageOptions::$aDefaultOptions['events'];
        	}
        }
        
        $aMessage['dialogOptions'] = $this->_integreIconeTypeAlerte($aMessage['dialogOptions'], $aMessage['type']);
        
        // Création du message
        $contenu .= $this->_makeDiv($aMessage);
        
        // Création du js qui gère le message
        $contenu .= $this->_makeJs($aMessage);
		
		return $contenu;
	}
	
	public function getName() {
		return 'twig.extension.message';
	}
	
	protected function _integreIconeTypeAlerte($aOptions, $type) {
		// Permet d'ajouter un titre avec une image définissant le niveau d'alerte
		$aOptions['events']['open']['function'] = substr_replace(
				$aOptions['events']['open']['function'],
				'if($(".ui-dialog .ui-dialog-titlebar .ui-dialog-title img[src=\"/bundles/gosylcommon/images/' . self::$aTypeMessage[$type] . '.png\"]").length == 0) {
                 $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title img").remove();
                 $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").prepend("<img src=\"/bundles/gosylcommon/images/' . self::$aTypeMessage[$type] . '.png\"> ");
             }',
				-1,
				0
				);
	
		return $aOptions;
	}
	
	protected function _integreOptions($typeOption, $aMessageDefaut, $aMessage) {
		$aMessageTemp = array();
		// Si les options du modal sont présentes
		foreach ($aMessage as $key => $aValue) {
			// On éclate le tableau d'option par défaut
			if(!is_null($aValue)) {
				// Si le paramètre est présent
				if(!$aValue['ecrasement']) {
					// on ajoute le nouveau paramètre aux paramètres existant
					if(is_array($aValue['data'])) {
						foreach ($aValue['data'] as $subKey => $subValue) {
							if(is_string($subKey)) {
								// cas des fonctions
								$aMessage[$key]['function'] = array();
								$aMessage[$key]['function'] = substr_replace(
										$aMessageDefaut[$typeOption][$key]['function'],
										substr($aMessage[$key]['data']['function'], 1),
										-1,
										strlen($aMessage[$key]['data']['function']) - 1
										);
								unset($aMessage[$key]['data']);
								unset($aMessage[$key]['ecrasement']);
							} elseif (is_int($subKey)) {
								// cas des boutons penser à mettre les index à partir de 1 !
								$aMessageTemp[$key] = array_merge($aMessage[$key]['data'], $aMessageDefaut[$typeOption][$key]);
							}
						}
					}
				} else {
					// On écrase les paramètres par défaut
					foreach ($aMessageDefaut[$typeOption] as $cle => $valeur) {
						if($cle == $key) {
							if(isset($aMessage[$cle])) {
								if(isset($aMessage[$cle]['data'])) {
									if(!is_null($aMessage[$cle]['data'])) {
										// le paramètre est trouvé on réorganise le tableau
										$param = $aMessage[$cle]['data'];
										//unset($aMessage['dialogOptions'][$typeOption][$cle]);
										$aMessageTemp[$cle] = $param;
									}
								} elseif(is_null($aMessage[$cle]['data'])) {
									$aMessageTemp[$cle] = null;
								}
							}
						}
					}
				}
			}
		}
	
	
		$aMessage = array_merge($aMessage, $aMessageTemp);
	
		// on récupère les options par défaut
		foreach ($aMessageDefaut[$typeOption] as $key => $value) {
			if(!isset($aMessage[$key])) {
				$aMessage[$key] = $value;
			}
		}
	
		return $aMessage;
	}
	
	/**
	 * Création du conteneur du message
	 *
	 * @param array $paramMessage paramètres du message
	 * @return string
	 */
	protected function _makeDiv(array $paramMessage) {
		$contenu = '';
	
		$idDialog = isset($paramMessage['idDialog']) ? $paramMessage['idDialog'] : ('dialog' . ucfirst(self::$aTypeMessage[$paramMessage['type']]));
	
		$contenu .= '<div title="' . ucfirst(self::$aTypeMessage[$paramMessage['type']]) . '" id="' . $idDialog . '" class="message ' . self::$aTypeMessage[$paramMessage['type']] . '"><p>' . $paramMessage['message'] . '</p></div>';
	
		return $contenu;
	}
	
	/**
	 * Création du js qui gère le message
	 *
	 * @param array $message options du message
	 */
	protected function _makeJs(array $message) {
		$contenu = '<script type="text/javascript">';
		$idDialog =  isset($message['idDialog']) ? $message['idDialog'] : ('dialog' . ucfirst(self::$aTypeMessage[$message['type']]));
		$contenu .= '$(document).ready(function() {
                  $("#' . $idDialog . '").dialog({';
		$contenu .= MessageOptions::getOptions($message);
		$contenu .= '});
              });';
		$contenu .= '</script>';
		
		return $contenu;
	}
}