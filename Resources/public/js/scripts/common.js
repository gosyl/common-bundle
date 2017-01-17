/**
 * Scripts communs à toutes les pages
 */

if(typeof Gosyl === 'undefined') Gosyl = {};

Gosyl.Common = (function() {
	/**
	 * Réglages pour le datepicker
	 */
	var dateMini = new Date('1900', '1' , '1');
	var dateNow = new Date();
	
	/*
	 * réglage pour le sessionStorage
	 */
	var expire = 86400;//24 heures
	
	function init() {
		initSessionStorage();
	}
	
	function getIdItem(item) {
		var aItem = item.split('_');
		
		return aItem[1];
	}
	
	/*********************************************
	 * SessionStorage
	 *********************************************/
	
	/*
	 * Initialisation d'une durée de vie du sessionStorage
	 */
	function initSessionStorage() {
		if(typeof sessionStorage != 'undefined') {
			var dtNow = new Date();
			
			if(!('expire' in sessionStorage)) {
				sessionStorage.setItem('expire', dtNow.getTime());
			} else {
				// On évalue la date d'expiration
				var dtExpire = new Date();
				
				dtExpire.setTime(sessionStorage.getItem('expire'));
				
				// On retranche une durée de vie du cache
				dtNow.setTime(dtNow.getTime() - this.expire);
				
				if(dtExpire.getTime() < dtNow.getTime()) {
					var dtNewExpire = new Date();
					
					clearSessionStorage();
					sessionStorage.setItem('expire', dtNewExpire.getTime());
				}
			}
		}
	}
	
	/**
	 * On efface le sessionStorage
	 */
	function clearSessionStorage(reInit) {
		if(typeof reInit == 'undefined') {
			reInit = false;
		}
		
		sessionStorage.clear();
		if(reInit) {
			initSessionStorage();
		}
	}
	
	/*********************************************
	 * Fonctions date
	**********************************************/
	
	function getFrDateTime(date) {
	    if(typeof date == 'undefined') {
	        date = new Date();
        }
		
		var jour = date.getUTCDate();
		var mois = date.getMonth();
		var annee = date.getFullYear();
		
		var heure = date.getHours();
		var minute = date.getMinutes();
		var seconde = date.getSeconds();
		
		function ajout0(prop) {
			if(prop.toString().length == 1) {
				return '0' + prop;
			}
			return prop;
		}
		
		jour = ajout0(jour);
		mois = ajout0(mois + 1);
		
		return jour + '/' + mois + '/' + annee + ' ' + heure + ':' + minute + ':' + seconde;
	}
	
	/**
	 * Retourne l'objet Date avec en parèmetre un dateTime prédéfini (jj/mm/aaaa hh:MM:ss) 
	 * @param sDateHeure string
	 * @returns {Date}
	 */
	function decomposeDate(sDateHeure) {
		var aDateHeure = sDateHeure.split(' ');
		var sDate = aDateHeure[0];
		var sHeure = aDateHeure[1];
		
		var aDate = sDate.split('/');
		var jour = aDate[0];
		var mois = aDate[1];
		var annee = aDate[2];
		
		var aHeure = sHeure.split(':');
		var heure = aHeure[0];
		var minute = aHeure[1];
		var seconde = aHeure[2];
		
		return new Date(annee, mois, jour, heure, minute, seconde);
	}
	
	/*********************************************
	 * Fonctions chaîne de caractères
	**********************************************/
	
	/**
	 * Met en majuscule la première lettre de chaque mot d'une chaîne de caractères
	 * @param str
	 * @returns string
	 */
	function STR_ucwords(str) {
		str = str.toLowerCase().replace(/^[\u00C0-\u1FFF\u2C00-\uD7FF\w]|\s[\u00C0-\u1FFF\u2C00-\uD7FF\w]/g, function(letter) {
		    return letter.toUpperCase();
		});
		
		return str;
	}
	
	function loadDatePicker(element) {
		$('#' + element).datepicker({
			autoSize: true,
			onClose: function(dateText, instance) {
				$(this).datepicker('setDate', dateText);
			}
		});
	}
	
	function loadingAjax() {
		$('#ajaxLoading').show();
	}
	
	function ajaxLoaded() {
		$('#ajaxLoading').hide();
	}
	
	function getDimensionElement(elem) {
		function internal(elem) {
			if($('#' + elem).length == 0) {
				throw new MonException(elem + ' : is undefined !');
			} else {
				var oElem = $('#' + elem);
				
				var borderLeftWidth = parseInt(oElem.css('border-left-width'));
				var borderRightWidth = parseInt(oElem.css('border-right-width'));
				var paddingLeft = parseInt(oElem.css('padding-left'));
				var paddingRight = parseInt(oElem.css('padding-right'));
				var width = parseInt(oElem.css('width'));
				
				var borderTopWidth  = parseInt(oElem.css('border-top-width'));
				var paddingTop = parseInt(oElem.css('padding-top'));
				var height = parseInt(oElem.css('height'));
				var paddingBottom = parseInt(oElem.css('padding-bottom'));
				var borderBottomWidth = parseInt(oElem.css('border-bottom-width'));
				
				return {
                    x: borderLeftWidth + paddingLeft + width + paddingRight + borderRightWidth,
                    y: borderTopWidth + paddingTop + height + paddingBottom + borderBottomWidth
                };
			}
		}
		var dim = {};
		try {
			dim = internal(elem);
		} catch (e) {
			dim = {
				x: 0,
				y: 0
			}
			
		}
		return dim;
	}
	
	function MonException(message) {
		this.message = message;
		this.name = "MonException";
	}
	
	function getMsgRetourForm(messages) {
		var contenu = "";
		
		$.each(messages, function(i, msg) {
			contenu += msg;
			if(i < messages.length) {
				contenu += '<br />';
			}
		});

		return contenu;
	}
	
	return {
		init: init,
		getIdItem: getIdItem,
		getFrDateTime: getFrDateTime,
		decomposeDate: decomposeDate,
		STR_ucwords: STR_ucwords,
		loadDatePicker: loadDatePicker,
		loadingAjax: loadingAjax,
		ajaxLoaded: ajaxLoaded,
		getDimensionElement: getDimensionElement,
		getMsgRetourForm: getMsgRetourForm,
		dateMini: dateMini,
		dateNow: dateNow,
		clearSessionStorage: clearSessionStorage
	};
})();

$(document).ready(function() {
	$('#ajaxLoading')
	.width($(document).width())
	.height($(document).height());
	
	Gosyl.Common.init();
});

$.datepicker.setDefaults({
	changeMonth: true,
	changeYear: true,
	dateFormat: 'dd/mm/yy',
	dayNames: [ "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" ],
	dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ],
	dayNamesShort: [ "Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam" ],
	firstDay: 1,
	minDate: Gosyl.Common.dateMini,
	maxDate: Gosyl.Common.dateNow,
	monthName: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre' , 'Octobre', 'Novembre', 'Décembre'],
	monthNamesShort: [ "Jan", "Fev", "Mar", "Avr", "Mai", "Jui", "Juil", "Aoû", "Sep", "Oct", "Nov", "Déc" ],
	yearRange: Gosyl.Common.dateMini.getFullYear() + ':' + Gosyl.Common.dateNow.getFullYear(),
	showButtonPanel: true,
	currentText: "Aujourd'hui",
	closeText: "Fermer",
	
});



