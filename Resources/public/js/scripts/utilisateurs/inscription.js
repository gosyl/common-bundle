/**
 * scripts pour Application\Connexion\Inscription
 */

if(typeof Gosyl === 'undefined') {
	Gosyl = {};
}

if(typeof Gosyl.Common === 'undefined') {
	Gosyl.Common = {};
}

Gosyl.Common.Inscription = (function($) {
	function init() {
        loadDatePicker('inscription_dateNaissance');
    }

    function loadDatePicker(input) {
        $('#' + input).datepicker({
            format: 'dd/mm/yyyy',
            endDate: '0d',
            clearBtn: true,
            todayBtn: 'linked',
            language: 'fr',
            autoclose: true
		});
	};
	
	return {
		init: init
	};
}) (jQuery);



$(document).ready(function () {
	Gosyl.Common.Inscription.init();
});