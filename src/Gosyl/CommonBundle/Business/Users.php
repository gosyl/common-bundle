<?php
namespace Gosyl\CommonBundle\Business;

use Gosyl\CommonBundle\Entity\ParamUsers;
use Gosyl\CommonBundle\Constantes;
use Gosyl\CommonBundle\Entity\ParamUsersRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;

class Users {
	private $_sUrl = 'common/ajax/listerutilisateur';
	
	private $_cols = array(
			'id' => array(
					'title' => 'id',
					'data' => 'id',
					'visible' => false,
			),
			'username' => array(
					'title' => 'Login',
					'data' => 'username',
			),
			'nom' => array(
					'title' => 'Nom',
					'data' => 'name'
			),
			'prenom' => array(
					'title' => 'Prenom',
					'data' => 'prenom'
			),
			'dateInscription' => array(
					'title' => 'Date d\'inscription',
					'data' => 'dateInscription',
			),
			'dateNaissance' => array(
					'title' => 'Date de naissance',
					'data' => 'dateNaissance',
					'visible' => false,
			),
			'isActive' => array(
					'title' => 'Utilisateur Actif',
					'data' => 'isActive',
					'visible' => false,
			),
			'roles' => array(
					'title' => 'Type d\'utilisateur',
					'data' => 'roles',
			),
			'email' => array(
					'data' => 'email',
					'title' => 'Email',
			),
			'action' => array(
					'title' => 'action',
					'data' => 'null',
					'width' => '109px',
					"orderable" => false,
					'render' => array(
							'render' => 'function(oObject) {
			                    var contenu = Gosyl.Common.GestionUtilisateur.gereUtilisateur(oObject);
			                    return contenu;
			                }',
					),
			),
	);
	
	private $_allUserOptions = array(
			'jQueryUI' => true,
			'paging' => true,
			'autoWidth' => true,
			'stateSave' => true,
			'retrieve' => true,
			'pageLength' => 10,
			'pagingType' => 'full_numbers',
			'dom' => '<"H"RCTlf>t<"F"rpi>',
			'initComplete' => array(
					'function' => '{
		                Gosyl.Common.GestionUtilisateur.dataTableId = $(this).selector;
		                //decomposeJson(json);
		        		Gosyl.Common.GestionUtilisateur.init();
		            }'
			),
			'createdRow' => array(
					'function' => '{
		                $(row).data(\'user\', data);
						$(row).attr(\'id\', \'data_\' + data.id);
		            }',
			),
	);
	
	private $_OneUserOptions = array(
			'jQueryUI' => true,
			'paging' => false,
			'autoWidth' => true,
			'stateSave' => true,
			'retrieve' => true,
			'searching' => false,
			'info' => false,
			//'pageLength' => 10,
			//'pagingType' => 'full_numbers',
			'dom' => '<"H"RCTlf>t<"F"rpi>',
			'initComplete' => array(
					'function' => '{
		                Gosyl.Common.GestionUtilisateur.dataTableId = $(this).selector;
		                //decomposeJson(json);
		        		Gosyl.Common.GestionUtilisateur.init();
		            }'
			),
			'createdRow' => array(
					'function' => '{
		                $(row).data(\'user\', data);
						$(row).attr(\'id\', \'data_\' + data.id);
		            }',
			),
	);
	
	static public $aDialogMsgErreurSuppr = 'Erreur lors de la suppression de l\'utilisateur';
	
	static public $aDialogMsgErreurRest = 'Erreur lors de la restauration de l\'utilisateur';
	
	static public $aDialogOptionsErreur = array(
			'dialogOptions' => array(
					'events' => array(
							'open' => array(
									'data' => array(
											'function' => '{
					                            $("#" + $(this).attr("id") + " p").append("<b> " + $(this).data("dataUtilisateur").login + "</b>");
					                        }',
									),
									'ecrasement' => false,
							),
							'close' => array(
									'data' => array(
											'function' => '{
					                            $("#" + $(this).attr("id") + " p b").remove();
					                        }',
									),
									'ecrasement' => true,
							),
					),
			),
			'idDialog' => 'dialogErrorSupprUtilisateur',
	);
	
	/*-- Modal form ModifUser --*/
	public static $aModalFormModifUser = array(
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
					    	                    Gosyl.Common.GestionUtilisateur.populateModifForm($(event.target).data("dataUtilisateur"));
					    	                    $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").css("display", "none");
					    	                    $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").parent().css("display", "none");
					                        }',
									),
									'ecrasement' => false,
							),
							'close' => array(
									'data' => array(
											'function' => '{
					                            /*var elemPrec = $("#dialogModifUtilisateur #modification_roles_element").get(0).previousElementSibling;
					                            $(elemPrec).html("&nbsp;");*/
					    	                    $.each($("#modification input"), function(i, oInput) {
					                                $(oInput).prop("disabled", false);
					                            });
					    	                    $.each($("#modification .espace"), function(i, oDiv){
					                                $(oDiv).html("&nbsp;").removeClass("error");
					                            });
					                            $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").css("display", "block");
					    	                    $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").parent().css("display", "block");
					                        }',
									),
									'ecrasement' => true,
							),
					),
			),
			'idDialog' => 'dialogModifUtilisateur',
	);
	
	/**
	 * @var Registry
	 */
	protected $_oDoctrine;
	
	/**
	 * @var ParamUsersRepository
	 */
	protected $_oParamUsersRepository;
	
	public function __construct($oDoctrine) {
		$this->_oDoctrine = $oDoctrine;
		//$this->_oParamUsersRepository = new ParamUsersRepository($this->_oDoctrine, 'GosylCommonBundle:ParamUsers');
		$this->_oParamUsersRepository = $this->_oDoctrine->getRepository('GosylCommonBundle:ParamUsers');
	}
	
	public function getAllUserForDataTable() {
		$aResult = array();
		$aResult['options'] = array_merge($this->_allUserOptions, Constantes::$aDataTableLanguage);
		$aResult['cols'] = $this->_cols;
		$aResult['results'] = $this->_sUrl;
		
		return $aResult;
	}
	
	public function getOneUserForDataTable($idUser) {
		$aResult = array();
		$aResult['options'] = array_merge($this->_OneUserOptions, Constantes::$aDataTableLanguage);
		$aResult['cols'] = $this->_cols;
		unset($aResult['cols']['roles']);
		$aResult['results'] = $this->_sUrl . '/' . $idUser;
		
		return $aResult;
	}
	
	public function listerUtilisateursForDataTable($oActualUser, $idUser) {
		//echo "<pre>";var_dump(get_class_methods ($this->_oParamUsersRepository));die('</pre>');
		if(!is_null($idUser)) {
			$aWhere = array('U.id' => array('val' => $idUser, 'type' => 'number', 'bind' => ':idUser'));
		} else {
			$aWhere = null;
		}
		
		$aResultSet = $this->_oParamUsersRepository->getAllForDataTable($aWhere);
		foreach ($aResultSet['data'] as $key => $aValue) {
			$aResultSet['data'][$key]['roles'] = array_key_exists(0, $aValue['roles']) ? $aValue['roles'][0] : 'ROLE_USER';
			$aResultSet['data'][$key]['dateNaissance'] = $aValue['dateNaissance']->format('d/m/Y');
			$aResultSet['data'][$key]['dateInscription'] = $aValue['dateInscription']->format('d/m/Y');
			
			if(!is_null($aValue['dateSuppression'])) {
				$aResultSet['data'][$key]['dateSuppression'] = $aValue['dateSuppression']->format('d/m/Y');
				$aResultSet['data'][$key]['bDeleted'] = true;
			} else {
				$aResultSet['data'][$key]['bDeleted'] = false;
			}
			
			if($oActualUser->getId() == $aValue['id']) {
				$aResultSet['data'][$key]['isActualUser'] = true;
			} else {
				$aResultSet['data'][$key]['isActualUser'] = false;
			}
			
		}
		//var_dump($aResultSet);die;
		return $aResultSet;
	}
	
	public function banUtilisateur($idUser, $sMode) {
		$result = $this->_oParamUsersRepository->banUtilisateur($idUser, $sMode == 'active' ? '1' : '0');
	
		$aVarReturn = array('error' => false, 'result' => $result, 'reasons' => array());
	
		return $aVarReturn;
	}
	
	/**
	 * Suppression d'un utilisateur
	 * @param int $idUser
	 * @return array
	 */
	public function supprimerUtilisateur($idUser) {
		$oDateNow = new \DateTime('now');
		
		$iReturn = $this->_oParamUsersRepository->supprimerUtilisateur($idUser, $oDateNow);
		
		if($iReturn != 1) {
			$aVarReturn = array(
					'error' => true,
					'reasons' => array(
							'msg'=>'Une erreur s\'est produite durant la suppression'
					),
			);
		} else {
			$aVarReturn = array(
					'error' => false,
					'reasons' => null,
					'nbMaj' => $iReturn,
			);
		}
		return $aVarReturn;
	}
	
	public function restaurerUtilisateur($idUser) {
		$iReturn = $this->_oParamUsersRepository->restaureUtilisateur($idUser);
		
		if($iReturn != 1) {
			$aVarReturn = array(
					'error' => true,
					'reasons' => array(
							'msg'=>'Une erreur s\'est produite durant la suppression'
					),
			);
		} else {
			$aVarReturn = array(
					'error' => false,
					'reasons' => null,
					'nbMaj' => $iReturn,
			);
		}
		return $aVarReturn;
	}
	
	public function updateUtilisateur($aData, $sOldRole) {
		if(array_key_exists('dateNaissance', $aData)) {
		    $aData['dateNaissance'] = \DateTime::createFromFormat('d/m/Y H:i:s', $aData['dateNaissance'] . ' 00:00:00');
		}
		
		if(array_key_exists('roles', $aData)) {
			foreach (Constantes::$aPrivileges as $role => $key) {
				if($key == $aData['roles']) {
					$aData['roles'] = serialize(array($role));
					$bIsAdmin = $role == Constantes::ROLE_ADMIN ? true : false ;
				}
			}
			
		} else {
			$bIsAdmin = $sOldRole == Constantes::ROLE_ADMIN ? true : false ;
		}
		
	    $aDataToModify = array();
	    foreach ($aData as $key => $value) {
	    	$aDataToModify['U.' . $key] = $value;
	    }
	    
	    $iNbAdmin = count($this->getAdmin());
	    
	    if($iNbAdmin >= 1 && $sOldRole == Constantes::ROLE_ADMIN && $bIsAdmin) {
	    	// On peut modifier les informations d'un admin
	    	$result = $this->_oParamUsersRepository->modifierUsers($aDataToModify, $aData['id']);
	    	$aVarReturn = array('error' => false, 'result' => $result, 'reasons' => array());
	    } elseif($iNbAdmin == 1 && $sOldRole == Constantes::ROLE_ADMIN && !$bIsAdmin) {
	    	// On ne peut pas changer le privilege d'un admin s'il est le seul
	    	$aVarReturn = array('error' => true, 'reasons' => array('form' => 'Changement de privilège du dernier admin'));
	    } elseif($iNbAdmin > 1 && $sOldRole == Constantes::ROLE_ADMIN && !$bIsAdmin) {
	    	// On peut changer le privilège d'un admin s'il n'est pas le seul
	    	$result = $this->_oParamUsersRepository->modifierUsers($aDataToModify, $aData['id']);
	    	$aVarReturn = array('error' => false, 'result' => $result, 'reasons' => array());
	    } else {
	    	// On peut modifier les autres utilisateurs
	    	$result = $this->_oParamUsersRepository->modifierUsers($aDataToModify, $aData['id']);
	    	$aVarReturn = array('error' => false, 'result' => $result, 'reasons' => array());
	    }
	    
	    return $aVarReturn;
	}
	
	public function updatePassword($PwdEncoded, $idUser) {
		$iReturn = $this->_oParamUsersRepository->updatePassword($PwdEncoded, $idUser);
		
		return $iReturn;
	}
	
	public function getAdmin() {
		$aReturn = $this->_oParamUsersRepository->getAdmin();
		
		return $aReturn;
	}
	
	public function getUserById($id) {
		$aReturn = $this->_oParamUsersRepository->getUserById($id);
		
		return $aReturn;
	}
}