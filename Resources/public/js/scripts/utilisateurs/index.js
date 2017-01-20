/**
 * Scripts pour utilisateurs::index
 */
if (typeof Gosyl === 'undefined') {
	Gosyl = {};
}

if(typeof Gosyl.Common === 'undefined') {
	Gosyl.Common = {};
}

Gosyl.Common.GestionUtilisateur = (function($) {
	var dataTableId;

	function init() {
		// Activation des évènements sur les différents boutons
		$(this.dataTableId).on('click', '.suppressionUtilisateur', suppressionUtilisateur);

		$(this.dataTableId).on('click', '.modifUtilisateur', modifUtilisateur);

		$(this.dataTableId).on('click', '.desactiveUtilisateur', desactiveUtilisateur);

		$(this.dataTableId).on('click', '.activeUtilisateur', activeUtilisateur);

		$(this.dataTableId).on('click', '.restaureUtilisateur', restaureUtilisateur);
		
		// datepicker dateNaissance
		Gosyl.Common.loadDatePicker('modification_dateNaissance');
	}

	function modifUtilisateur(e) {
		e.stopPropagation();
		
		// this représente le bouton modifier
		var btn = this;
		
		// Récupération des données de l'utilisateur
		var idUser = $(this).data('id');
		var dataUser = $('#data_' + idUser).data('user');
		
		if(!dataUser.isActualUser) {
			$('#modification_password_first_element').css('display', 'none');
			$($('#modification_password_first_element').get(0).nextElementSibling).css('display', 'none');
			$('#modification_password_second_element').css('display', 'none');
			$($('#modification_password_second_element').get(0).nextElementSibling).css('display', 'none');
		}
		
		// Ouverture du modal
		$('#dialogModifUtilisateur').data('dataUtilisateur', dataUser).dialog('open');
		
		// Evènement sur le bouton 'fermer' de la modale
		$('#modification_btnQuit').click(function() {
			$('#dialogModifUtilisateur').dialog('close');
			Gosyl.Common.GestionUtilisateur.disableActivateFormElement(dataUser, false);
			$('div.error').remove();
			
			$('#modification_password_first_element').css('display', 'block');
			$($('#modification_password_first_element').get(0).nextElementSibling).css('display', 'block');
			$('#modification_password_second_element').css('display', 'block');
			$($('#modification_password_second_element').get(0).nextElementSibling).css('display', 'block');
			
			$('#modification_btnQuit').off('click');
		});
		
		// Evènement sur le bouton d'envoi du formulaire
		$('#modification_sendForm').off('click');
		$('#modification_sendForm').click(function() {
			/*if ($('#modification #password').val() == '') {
				// Pas de changement pour le mot de passe
				$('#modification #password').prop('disabled', true);
				$('#modification #passwordConfirm').prop('disabled', true);
			}*/
			
			// Si aucun choix du privilège
			/*if ($('select#privilege option:selected').val() == -1) {
				var elemPrec = $('#dialogModifUtilisateur #modification_roles_element').get(0).previousElementSibling;
				$(elemPrec).addClass('error').html('Veuillez faire un choix');
				return false;
			}*/
			
			/*if($('#modification_username').val() == dataUser['username']) {
				$('#modification_username').prop('disabled', true);
			}*/
			
			Gosyl.Common.GestionUtilisateur.disableActivateFormElement(dataUser, true);

			// Récupération des données du formulaire
			var data = $('#modification').serializeArray();
						
			if(data[1].name != "gosyl_user_profile[_token]") {
				$.ajax({
					'url' : 'ajax/modifierutilisateur',
					'type' : 'POST',
					'dataType' : 'json',
					'data' : data,
				})
				.then(function(retour) {
					//$($('#dialogModifUtilisateur #modification_roles_element').get(0).previousElementSibling).html('&nbsp;');
					if (retour.error) {
						if (retour.reasons.form != undefined) {
							// Autres erreurs
							$('#modification_username_element').before('<div id="error_form" class="error">' + retour.reasons.form + '<br /><br /></div>');
							Gosyl.Common.GestionUtilisateur.disableActivateFormElement(dataUser, false);
						} else {
							// erreur du formulaire
							var message = '';
							$.each(retour.reasons, function(formElement, oMessage) {
								message = Gosyl.Common.GestionUtilisateur.getMessage(oMessage);
								$($('#modification_' + formElement + '_element').get(0).previousElementSibling).addClass('error').html(message);
							});
						}
					} else {
						// fermeture du modal
						$('#modification_btnQuit').trigger('click');
						
						// rechargement du dataTable
						Gosyl.Common.GestionUtilisateur.reloadDataTable();
						$('#modification_password_first_element').css('display', 'block');
						$($('#modification_password_first_element').get(0).nextElementSibling).css('display', 'block');
						$('#modification_password_second_element').css('display', 'block');
						$($('#modification_password_second_element').get(0).nextElementSibling).css('display', 'block');
					}
				})
				.fail(function() {
					$('#modification_btnQuit').trigger('click');
				});
			} else {
				$('#modification_username_element').before('<div class="error">Aucune modification effectuée<br/></div>');
				Gosyl.Common.GestionUtilisateur.disableActivateFormElement(dataUser, false);
			}
		});
	}
	
	function disableActivateFormElement(dataUser, bool) {
		$.each(dataUser, function(key, value) {
			if(key != 'id') {
				if($('#modification_' + key).length != 0) {
					if($('#modification_' + key).val() == value) {
						$('#modification_' + key).prop('disabled', bool);
					}
				}
			}
			if(key == 'roles') {
				$.each($('#modification_roles option'), function(i, val) {
					if($(val).prop('selected') && $(val).html() == value) {
						$('#modification_roles').prop('disabled', bool);
					}
				});
			}
		});
	}
	
	/**
	 * Rechargement du dataTable
	 */
	function reloadDataTable() {
		$(Gosyl.Common.GestionUtilisateur.dataTableId).DataTable().ajax.reload(function(json) {
			$.each(json.data, function(i, oPersonne) {
				Gosyl.Common.GestionUtilisateur.gereUtilisateur(oPersonne);
				//Gosyl.GestionUtilisateur.init();
			});

		});
	}

	function desactiveUtilisateur(e) {
		e.stopPropagation();
		
		// Récupération des données de l'utilisateur
		var idUser = $(this).data('id');
		
		$.ajax({
			dataType : 'json',
			type : 'POST',
			url : 'ajax/banutilisateur',
			data : {
				'id' : idUser,
				'mode' : 'desactive',
			},
		}).then(function(json) {
			$('#dmdActUtilisateur').dialog('close');

			if (json.error) {
				$('#dialogErrorRestaureUtilisateur').data('dataUtilisateur', oUser).dialog('open');
			} else {
				Gosyl.Common.GestionUtilisateur.reloadDataTable();
			}
		}).fail(function(json) {
			if(json.status != 404) {
				$('#dialogErrorRestaureUtilisateur').data('dataUtilisateur', oUser).dialog('open');
			}
		});
	}

	function activeUtilisateur(e) {
		e.stopPropagation();
		
		// Récupération des données de l'utilisateur
		var idUser = $(this).data('id');
		
		$.ajax({
			dataType : 'json',
			type : 'POST',
			url : 'ajax/banutilisateur',
			data : {
				'id' : idUser,
				'mode' : 'active',
			},
		}).then(function(json) {
			$('#dmdActUtilisateur').dialog('close');

			if (json.error) {
				$('#dialogErrorRestaureUtilisateur').data('dataUtilisateur', oUser).dialog('open');
			} else {
				Gosyl.Common.GestionUtilisateur.reloadDataTable();
			}
		}).fail(function(json) {
			$('#dialogErrorRestaureUtilisateur').data('dataUtilisateur', oUser).dialog('open');
		});
	}

	function restaureUtilisateur(e) {
		e.stopPropagation();
		
		// Récupération des données de l'utilisateur
		var idUser = $(this).data('id');
		
		$.ajax({
			dataType : 'json',
			type : 'POST',
			url : 'ajax/restaurerutilisateur',
			data : {
				'id' : idUser,
				'from' : 'index',
			},
		}).then(function(json) {
			$('#dmdRestUtilisateur').dialog('close');

			if (json.error) {
				$('#dialogErrorRestaureUtilisateur').data('dataUtilisateur', oUser).dialog('open');
			} else {
				Gosyl.Common.GestionUtilisateur.reloadDataTable();
			}
		}).fail(function(json) {
			$('#dialogErrorRestaureUtilisateur').data('dataUtilisateur', oUser).dialog('open');
		});
	}
	
	function suppressionUtilisateur(e) {
		e.stopPropagation();
		
		// Récupération des données de l'utilisateur
		var idUser = $(this).data('id');
		
		$.ajax({
			dataType : 'json',
			type : 'POST',
			url : 'ajax/supprimerutilisateur',
			data : {
				'id' : idUser,
			},
		}).then(function(json) {
			$('#dmdSupprUtilisateur').dialog('close');

			if (json.error) {
				$('#dialogErrorSupprUtilisateur').data('dataUtilisateur', oUser).dialog('open');
			} else {
				Gosyl.Common.GestionUtilisateur.reloadDataTable();
			}
		});
	}
	
	function gereUtilisateur(data) {
		// On barre l'utilisateur supprimé
		var bSupprime = data.bDeleted;
		var bActif = data.isActive;
		var bIsActualUser = data.isActualUser;

		if (bSupprime) {
			$('#data_' + data.id).addClass('inactif removed')
		} else if (!bActif) {
			$('#data_' + data.id).addClass('inactif')
		}

        var contenu = $('<table style="width: 100%">');
		contenu.addClass('tableAction');
		var rangee = $('<tr>');
		
		if(bIsActualUser) {
            //rangee.css('background-color', '#ffb7b7');
		}
		
		var colonne1 = $('<td>');
		if (data.roles != 'ROLE_ADMIN' && !bSupprime) {
			colonne1.html('<img class="suppressionUtilisateur" data-id="' + data.id + '" src="' + Gosyl.Common.basePath + '/css/library/constellation/images/icons/fugue/cross-circle.png" title="Supprimer" />');
		} else {
			colonne1.addClass('removed').html('&nbsp;&nbsp;');
		}
		var colonne2 = $('<td>');
		if (!bSupprime) {
			colonne2.html('<img class="modifUtilisateur" data-id="' + data.id + '" src="' + Gosyl.Common.basePath + '/css/library/constellation/images/icons/fugue/pencil.png" title="Modifier" />');
		} else {
            colonne2.addClass('removed').html('&nbsp;&nbssssp;');
		}
		var colonne3 = $('<td>');
		var sActiveOuNon = 'active';
		var img = '';
		if (data.roles != 'ROLE_ADMIN') {
			if (!bSupprime) {
				if (bActif) {
					sActiveOuNon = 'desactive';
					img = 'user';
				} else {
					sActiveOuNon = 'active';
					img = 'user-red';
				}
				colonne3.html('<img src="' + Gosyl.Common.basePath + '/css/library/constellation/images/icons/fugue/' + img + '.png"  data-id="' + data.id + '" class="' + sActiveOuNon + 'Utilisateur" class="with-tip" title="' + Gosyl.Common.STR_ucwords(sActiveOuNon) + 'r l\'utilisateur">');
			} else {
				colonne3.html('<img src="' + Gosyl.Common.basePath + '/css/library/constellation/images/icons/fugue/user-black.png" class="restaureUtilisateur" data-id="' + data.id + '" class="with-tip" title="Restaurer l\'utilisateur">');
			}
		} else {
			colonne3.addClass('removed').html('&nbsp;&nbsp;');
		}

		rangee.html(colonne1);
		rangee.append(colonne2);
		rangee.append(colonne3);
		contenu.html(rangee);

		return contenu[0].outerHTML;
	}
	
	function getMessage(oMessage) {
		var msgToReturn = '';
		$.each(oMessage, function(i, message) {
			msgToReturn = message;
		});

		return msgToReturn;
	}
	
	function populateModifForm(dataUser) {
		$.each(dataUser, function(idInput, data) {
			if(idInput == 'roles') {
				$('#modification_oldRole').val(data);
			}
			
			if ($('#modification_' + idInput).length) {
				if ($('#modification_' + idInput).get(0).tagName == 'INPUT') {
					$('#modification_' + idInput).val(data);
				} else if ($('#modification_' + idInput).get(0).tagName == 'SELECT') {
					$.each($('#modification_' + idInput + ' option'), function(i, val) {
						if($(val).html() == data) {
							$('#modification_' + idInput + ' option[value="' + $(val).val() + '"]').prop('selected', true);
						}
					});
					//
				}
			}
		});
	}

	return {
		init : init,
		dataTableId : dataTableId,
		reloadDataTable: reloadDataTable,
		gereUtilisateur: gereUtilisateur,
		restaureUtilisateur: restaureUtilisateur,
		activeUtilisateur: activeUtilisateur,
		desactiveUtilisateur: desactiveUtilisateur,
		getMessage: getMessage,
		populateModifForm: populateModifForm,
		disableActivateFormElement: disableActivateFormElement
	};

})(jQuery);

$(document).ready(function () {
    Gosyl.Common.GestionUtilisateur.init();
});