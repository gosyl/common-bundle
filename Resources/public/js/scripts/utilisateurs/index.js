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
        var $dataTable = $('#' + this.dataTableId);
		// Activation des évènements sur les différents boutons
        $dataTable.on('click', '.suppressionUtilisateur', suppressionUtilisateur);

        $dataTable.on('click', '.modifUtilisateur', modifUtilisateur);

        $dataTable.on('click', '.desactiveUtilisateur', desactiveUtilisateur);

        $dataTable.on('click', '.activeUtilisateur', activeUtilisateur);

        $dataTable.on('click', '.restaureUtilisateur', restaureUtilisateur);

        $('#btnCancel').on('click', resetForm);

        $('#modalModifUser').on('hidden.bs.modal', resetModal);

		// datepicker dateNaissance
		Gosyl.Common.loadDatePicker('modification_dateNaissance');
	}

    function resetModal(e) {
        e.stopPropagation();

        $.each($('.formError'), function (i, elem) {
            $(elem).html('');
        });
        $('#modification').get(0).reset();
    }

    function resetForm(e) {
        e.stopPropagation();

    }

	function modifUtilisateur(e) {
		e.stopPropagation();
        var $btn = $(this);
        var idUser = $btn.data('id');
        var dataUser = $('#data_' + idUser).data('user');
        var $modal = $('#modalModifUser');
        var $modificationUsername = $('#username');
        var $modificationPassword = $('#password');
        var $modificationSendForm = $('#sendForm');
        var $modificationBtnQuit = $('#btnQuit');

        populateModifForm(dataUser);

        $modal.modal('show');

		if(!dataUser.isActualUser) {
            $.each($('input[type="password"]'), function (i, elem) {
                $(elem).prop('disabled', true).prop('readonly', true);
            });
        } else {
            $('#formRoles').hide();
        }

        // Evènement sur le bouton "Fermer"
        $modal.on('hidden.bs.modal', function (e) {
            e.stopPropagation();
            Gosyl.Common.GestionUtilisateur.disableActivateFormElement(dataUser, false);
            $('div.error').remove();
            $.each($('input[type="password"]'), function (i, elem) {
                $(elem).prop('disabled', false).prop('readonly', false);
            });
            $('#formRoles').show();
        });

        // Evènement lors de l'envoie du formulaire
        $modificationSendForm.off('click');
        $modificationSendForm.on('click', function (e) {
            var message;
            e.stopPropagation();

            Gosyl.Common.GestionUtilisateur.disableActivateFormElement(dataUser, true);

            // Récupération des données du formulaire
            var data = $('#modification').serializeArray();

            if (data[1].name != "_token") {
                $.ajax({
                    'url': Gosyl.Common.commonPath + 'ajax/modifierutilisateur',
                    'type': 'POST',
                    'dataType': 'json',
                    'data': data
                })
                    .then(function (retour) {
                        //$($('#dialogModifUtilisateur #modification_roles_element').get(0).previousElementSibling).html('&nbsp;');
                        if (retour.error) {
                            if (typeof retour.noResult != 'undefined') {
                                if (retour.noResult) {
                                    $modal.modal('hide');
                                    $modal.on('hidden.bs.modal', showModalNoChange);
                                    Gosyl.Common.GestionUtilisateur.disableActivateFormElement(dataUser, false);
                                    $.each($('input[type="password"]'), function (i, elem) {
                                        $(elem).prop('disabled', false);
                                    });
                                    return;
                                }
                            }

                            // erreur du formulaire
                            var message;
                            $.each(retour.reasons, function (formElement, oMessage) {
                                if (formElement == 'form') {
                                    $('#formErreurs').html(oMessage);
                                } else {
                                    $('#' + formElement).parent().find('div.formError').html(oMessage);
                                    Gosyl.Common.GestionUtilisateur.disableActivateFormElement(dataUser, false);
                                }
                            });

                        } else {
                            // fermeture du modal
                            $modal.modal('hide');

                            // rechargement du dataTable
                            Gosyl.Common.GestionUtilisateur.reloadDataTable();
                            $.each($('input[type="password"]'), function (i, elem) {
                                $(elem).prop('disabled', false);
                            });
                        }
                    })
                    .fail(function () {
                        $modificationBtnQuit.trigger('click');
                    });
            } else {
                $modal.modal('hide');
                $modal.on('hidden.bs.modal', showModalNoChange);
                Gosyl.Common.GestionUtilisateur.disableActivateFormElement(dataUser, false);
                $.each($('input[type="password"]'), function (i, elem) {
                    $(elem).prop('disabled', false);
                });
            }
        });
	}

    function showModalNoChange(e) {
        e.stopPropagation();
        var $modal = $('#modalNoChange');
        $modal.modal('show');
    }

	function disableActivateFormElement(dataUser, bool) {
        var $modif
		$.each(dataUser, function(key, value) {
            $modif = $('#' + key);
			if(key != 'id') {
                if ($modif.length != 0) {
                    if ($modif.val() == value) {
                        $modif.prop('disabled', bool);
					}
				}
			}
			if(key == 'roles') {
                $.each($('#roles option'), function (i, val) {
					if($(val).prop('selected') && $(val).html() == value) {
                        $('#roles').prop('disabled', bool);
					}
				});
			}
		});
        $.each($('input[type="password"]'), function (i, elem) {
            if ($(elem).val() === '' && bool) {
                $(elem).prop('disabled', bool);
            }
        });
	}

	/**
	 * Rechargement du dataTable
	 */
	function reloadDataTable() {
        $('#' + Gosyl.Common.GestionUtilisateur.dataTableId).DataTable().ajax.reload(function (json) {
			$.each(json.data, function(i, oPersonne) {
                $('#data_' + oPersonne.id).data('user', oPersonne);
                Gosyl.Common.GestionUtilisateur.gereUtilisateur(oPersonne);
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
        /**
         * @todo Lors d'une modification recharger le formulaire
         */
		// On barre l'utilisateur supprimé
		var bSupprime = data.bDeleted;
		var bActif = data.isActive;
		var bIsActualUser = data.isActualUser;

		if (bSupprime) {
			$('#data_' + data.id).addClass('inactif removed')
		} else if (!bActif) {
			$('#data_' + data.id).addClass('inactif')
		}

        var contenu = $('<div class="container-fluid">');
        //contenu.addClass('tableAction');
        var rangee = $('<div class="row">');

        /*
		if(bIsActualUser) {
            //rangee.css('background-color', '#ffb7b7');
		}
         */


		if (data.roles != 'ROLE_ADMIN' && !bSupprime) {
            var colonne1 = $('<div class="col-xs-4">');
            colonne1.html('<span class="suppressionUtilisateur fa fa-trash" data-id="' + data.id + '" title="Supprimer" style="font-size: 1.5em;"></span>');
		}

        if (!bSupprime) {
            var colonne2 = $('<div class="col-xs-4">');
            colonne2.html('<span class="modifUtilisateur fa fa-pencil" data-id="' + data.id + '" title="Modifier" style="font-size: 1.5em;"></span>');
		}

        if (data.roles != 'ROLE_ADMIN' && !bIsActualUser) {
            var colonne3 = $('<div class="col-xs-4">');
            var sActiveOuNon = 'active';
            var color = 'black';
			if (!bSupprime) {
				if (bActif) {
					sActiveOuNon = 'desactive';
				} else {
					sActiveOuNon = 'active';
                    color = 'red';
				}
                colonne3.html('<span data-id="' + data.id + '" class="' + sActiveOuNon + 'Utilisateur fa fa-user with-tip" title="' + Gosyl.Common.STR_ucwords(sActiveOuNon) + 'r l\'utilisateur" style="color: ' + color + '; font-size: 1.5em;"></span>');
			} else {
                colonne3.html('<span class="restaureUtilisateur fa fa-user-o with-tip" data-id="' + data.id + '" title="Restaurer l\'utilisateur" style="font-size: 1.5em;"></span>');
			}
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
            msgToReturn += '<br />' + message;
		});

		return msgToReturn;
	}

    function populateModifForm(dataUser) {
		$.each(dataUser, function(idInput, data) {
			if(idInput == 'roles') {
                $('#oldRole').val(data);
			}
            var $idInput = $('#' + idInput);
            if ($idInput.length) {
                if ($idInput.get(0).tagName == 'INPUT') {
                    $idInput.val(data);
                } else if ($idInput.get(0).tagName == 'SELECT') {
                    $.each($('#' + idInput + ' option'), function (i, val) {
						if($(val).html() == data) {
                            $('#' + idInput + ' option[value="' + $(val).val() + '"]').prop('selected', true);
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