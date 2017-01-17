<?php
namespace Gosyl\CommonBundle;

class Constantes {
	/**
	 * Boîtes de dialogue connexion et inscription
	 */
	public static $aDialogConnexion = array(
			'dialogOptions' => array(
					'options' => array(
							'autoOpen' => array(
									'data' => false,
									'ecrasement' => true,
							),
							'resizable' => array(
									'data' => false,
									'ecrasement' => true,
							),
							'width' => array(
									'data' => "auto",
									'ecrasement' => true,
							),
							'buttons' => array(
									'data' => array(),
									'ecrasement' => true,
							),
					),
					'events' => array(
							'open' => array(
									'data' => array(
											'function' => '{
					                            $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").css("display", "none");
					    	                    $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").parent().css("display", "none");
					                        }',
									),
									'ecrasement' => false,
							),
							'close' => array(
									'data' => array(
											'function' => '{
					                            $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").css("display", "block");
					    	                    $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").parent().css("display", "block");
					                            $("#dialogConnexion .error").remove();
					                        }',
									),
									'ecrasement' => true,
							),
					),
			),
			'idDialog' => 'dialogConnexion',
	);
	
	public static $aDialogInscription = array(
			'dialogOptions' => array(
					'options' => array(
							'autoOpen' => array(
									'data' => false,
									'ecrasement' => true,
							),
							'resizable' => array(
									'data' => false,
									'ecrasement' => true,
							),
							'width' => array(
									'data' => "auto",
									'ecrasement' => true,
							),
							'buttons' => array(
									'data' => array(),
									'ecrasement' => true,
							),
					),
					'events' => array(
							'open' => array(
									'data' => array(
											'function' => '{
					                            $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").css("display", "none");
					    	                    $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").parent().css("display", "none");
					                    		Gosyl.Inscription.loadDatePicker();
					                    	}',
									),
									'ecrasement' => false,
							),
							'close' => array(
									'data' => array(
											'function' => '{
					                            $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").css("display", "block");
					    	                    $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").parent().css("display", "block");
					                            $("#dialogInscription .error").remove();
					                        }',
									),
									'ecrasement' => true,
							),
					),
			),
			'idDialog' => 'dialogInscription',
	);
	
	/**
	 * Liste des privilèges
	 */
	const IS_AUTHENTICATED_ANONYMOUSLY = 'IS_AUTHENTICATED_ANONYMOUSLY';
	const ROLE_USER = 'ROLE_USER';
	const ROLE_ADMIN = 'ROLE_ADMIN';
	
	public static $aPrivileges = array(
		//self::IS_AUTHENTICATED_ANONYMOUSLY,
		self::ROLE_USER => 0,
		self::ROLE_ADMIN => 1
	);
	
	/*-- DataTable Options --*/
	public static $aDataTableLanguage = array(
			'language' => array(
					'processing' => 'Traitement des données...',
					'lengthMenu' => 'Afficher _MENU_ résultats',
					'zeroRecords' => 'Aucun résultat à afficher',
					"emptyTable" =>	 "Pas de résultat pour les critères demandés.",
					"info" =>         "Affichage des résultats _START_ à _END_ sur _TOTAL_ éléments",
					"infoEmpty" =>  "Pas de résultat pour les critères demandés.",
					"infoFiltered" => "(filtré de _MAX_ résultats au total)",
					"infoPostFix" => "",
					"search" => "Rechercher :",
					"url" => "",
					"paginate" => array(
							"first"=>  "Début",
							"previous" => "Précédent",
							"next" => "Suivant",
							"last" => "Fin"
					),
			),
	);
	
	/**
	 * Debug
	 */
	public static function vardump() {
		if(func_num_args() == 0) {
			throw new \Exception('Aucune variable');
		}
		
		$aArgs = func_get_args();
		
		echo '<pre>';
		
		foreach($aArgs as $arg) {
			var_dump($arg);
		}
		
		echo '</pre>';
	}
}