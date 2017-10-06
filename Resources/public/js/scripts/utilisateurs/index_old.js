/**
 * Scripts pour utilisateurs::index
 */

function decomposeJson(json) {
	$.each(json.data, function(i, user){
		activeBtn(user);
	});
	
	// datepicker dateNaissance
	$('#dateNaissance').datepicker();
}

/**
 * Activation des boutons de gestion d'un utilisateur
 * @param oUser Données de l'utilisateur
 */
function activeBtn(oUser) {
	$('#data_' + oUser.id + ' td').eq(5).css('padding', '0');
	
	$('#suppressionUtilisateur_' + oUser.id).click(function() {
		$('#dmdSupprUtilisateur').data('dataUtilisateur', oUser).dialog('open');
	});
	
	$('#restaureUtilisateur_' + oUser.id).click(function() {
		$('#dmdRestUtilisateur').data('dataUtilisateur', oUser).dialog('open');
	});
	
	$('#activeUtilisateur_' + oUser.id).click(function() {
		$('#dmdActUtilisateur').data('dataUtilisateur', oUser).dialog('open');
	});
	
	$('#desactiveUtilisateur_' + oUser.id).click(function() {
		$('#dmdDesactiveUtilisateur').data('dataUtilisateur', oUser).dialog('open');
	});
	
	$('#modifUtilisateur_' + oUser.id).click(function() {
		$('#dialogModifUtilisateur').data('dataUtilisateur', oUser).dialog('open');
		
		$('#btnQuit').click(function() {
			$('#dialogModifUtilisateur').dialog('close');
			$('#btnQuit').off('click');
		});
		
		$('#sendFormLogin').off('click');
		$('#sendFormLogin').click(function() {
			if($('#modification #password').val() == '') {
				// Pas de changement pour le mot de passe
				$('#modification #password').prop('disabled', true);
				$('#modification #passwordConfirm').prop('disabled', true);
			}
			
			if($('select#privilege option:selected').val() == -1) {
				var elemPrec = $('#modification #privilege_element').get(0).previousElementSibling;
				$(elemPrec).addClass('error').html('Veuillez faire un choix');
				return false;
			}
			
			var data = $('#modification').serializeArray();
			var dataToSend = {};
			
			$.each(data, function(i, val){
				dataToSend[val.name] = val.value;
			});
			
			$.ajax({
				'url': GLOBAL_basePath + '/gosyl/ajax/modifierutilisateur',
				'type': 'POST',
				'dataType': 'json',
				'data': dataToSend,
			}).then(function(retour) {
				$($('#modification #privilege_element').get(0).previousElementSibling).html('&nbsp;');
				if(retour.error) {
					if(retour.reasons.form != undefined) {
						// Autres erreurs
						$('#modification #login_element').before('<div id="error_form" class="formError">' + retour.reasons.form + '<br/></div>');
 					} else {
 						// erreur du formulaire
 						var message = '';
 						$.each(retour.reasons, function(formElement, oMessage) {
 							message = getMessage(oMessage);
 							$($('#modification #' + formElement + '_element').get(0).nextElementSibling).addClass('error').html(message);
 						});
 					}
				} else {
					// fermeture du modal
					$('#dialogModifUtilisateur').data('dataUtilisateur', data).dialog('close');
					
					// rechargement du dataTable
					reloadDataTable();
				}
			});			
		});
	});
}

function gereUtilisateur(data) {
	// On barre l'utilisateur supprimé
	var bSupprime = data.bDeleted;
	var bActif = data.actif;
	
	if(bSupprime) {
		$('#data_' + data.id).addClass('inactif removed')
	} else if(!bActif) {
		$('#data_' + data.id).addClass('inactif')
	}
	
	var contenu = $('<table>');
	contenu.addClass('tableAction');
		var rangee = $('<tr>');
			var colonne1 = $('<td>');
				if(data.PRIVILEGE_DESIGNATION != 'admin' && !bSupprime) {
					colonne1.html('<img id="suppressionUtilisateur_' + data.id + '" src="' + GLOBAL_basePath + '/css/library/constellation/images/icons/fugue/cross-circle.png" title="Supprimer" />');
				} else {
					colonne1.addClass('removed').html('&nbsp;&nbsp;');
				}
			var colonne2 = $('<td>');
				if(!bSupprime) {
					colonne2.html('<img id="modifUtilisateur_' + data.id + '" src="' + GLOBAL_basePath + '/css/library/constellation/images/icons/fugue/pencil.png" title="Modifier" />');
				} else {
					colonne2.addClass('removed').html('&nbsp;&nbsp;');
				}
			var colonne3 = $('<td>');
			var sActiveOuNon = 'active';
			var img = '';
			if(data.PRIVILEGE_DESIGNATION != 'admin') {
				if(!bSupprime) {
					if(bActif) {
						sActiveOuNon = 'desactive';
						img = 'user';
					} else {
						sActiveOuNon = 'active';
						img = 'user-red';
					}
					colonne3.html('<img src="' + GLOBAL_basePath + '/css/library/constellation/images/icons/fugue/' + img + '.png" id="' + sActiveOuNon + 'Utilisateur_' + data.id + '" class="with-tip" title="' + Debride.Common.STR_ucwords(sActiveOuNon) + 'r l\'utilisateur">');
				} else {
					colonne3.html('<img src="' + GLOBAL_basePath + '/css/library/constellation/images/icons/fugue/user-black.png" id="restaureUtilisateur_' + data.id + '" class="with-tip" title="Restaurer l\'utilisateur">');
				}
			} else {
				colonne3.addClass('removed').html('&nbsp;&nbsp;');
			}
			
		rangee.html(colonne1);
		rangee.append(colonne2);
		rangee.append(colonne3);
	contenu.html(rangee);
	
	activeBtn(data.id);
	
	return contenu[0].outerHTML;
}

/**
 * Rechargement du dataTable
 */
function reloadDataTable() {
	$(GLOB_dataTableId).DataTable().ajax.reload(function(json) {
		$.each(json.data, function(i, oPersonne) {
			activeBtn(oPersonne);
			gereUtilisateur(oPersonne);
		});
		
	});
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
		if($('#'+idInput).length) {
			if($('#' + idInput).get(0).tagName == 'INPUT') {
				$('#' + idInput).val(data);
			} else if($('#' + idInput).get(0).tagName == 'SELECT') {
				$('#' + idInput + ' option[value="' + data + '"]').prop('selected', true);
			}
		}
	});
}

function suppressionUtilisateur(oUser) {
	$.ajax({
		dataType: 'json',
		type: 'POST',
		url: GLOBAL_basePath + '/gosyl/ajax/supprimerutilisateur',
		data: {
			'id': oUser.id,
		},
	}).then(function(json) {
		$('#dmdSupprUtilisateur').dialog('close');
		
		if(json.error) {
			$('#dialogErrorSupprUtilisateur').data('dataUtilisateur', oUser).dialog('open');
		} else {
			reloadDataTable();
		}
	});
}

function restoreUser(oUser) {
	$.ajax({
		dataType: 'json',
		type: 'POST',
		url: GLOBAL_basePath + '/gosyl/ajax/restaurerutilisateur',
		data: {
			'id': oUser.id,
			'from': 'index',
		},
	}).then(function(json) {
		$('#dmdRestUtilisateur').dialog('close');
		
		if(json.error) {
			$('#dialogErrorRestaureUtilisateur').data('dataUtilisateur', oUser).dialog('open');
		} else {
			reloadDataTable();
		}
	}).fail(function(json) {
		$('#dialogErrorRestaureUtilisateur').data('dataUtilisateur', oUser).dialog('open');
	});
}

function activeUser(oUser) {
	$.ajax({
		dataType: 'json',
		type: 'POST',
		url: GLOBAL_basePath + '/gosyl/ajax/banutilisateur',
		data: {
			'id': oUser.id,
			'mode': 'active',
		},
	}).then(function(json) {
		$('#dmdActUtilisateur').dialog('close');
		
		if(json.error) {
			$('#dialogErrorRestaureUtilisateur').data('dataUtilisateur', oUser).dialog('open');
		} else {
			reloadDataTable();
		}
	}).fail(function(json) {
		$('#dialogErrorRestaureUtilisateur').data('dataUtilisateur', oUser).dialog('open');
	});
}

function desactiveUser(oUser) {
	$.ajax({
		dataType: 'json',
		type: 'POST',
		url: GLOBAL_basePath + '/gosyl/ajax/banutilisateur',
		data: {
			'id': oUser.id,
			'mode': 'desactive',
		},
	}).then(function(json) {
		$('#dmdDesactiveUtilisateur').dialog('close');
		
		if(json.error) {
			$('#dialogErrorRestaureUtilisateur').data('dataUtilisateur', oUser).dialog('open');
		} else {
			reloadDataTable();
		}
	}).fail(function(json) {
		$('#dialogErrorRestaureUtilisateur').data('dataUtilisateur', oUser).dialog('open');
	});
}