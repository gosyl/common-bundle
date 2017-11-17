/**
 * 
 */
if(typeof Gosyl === 'undefined') {
	Gosyl = {};
}

if(typeof Gosyl.Common === 'undefined') {
	Gosyl.Common = {};
}

Gosyl.Common.Login = (function($) {
    /*var optionDialogConnexion = {
			autoOpen: true,
			resizable: false,
			width: "auto",
			buttons: null,
			closeOnEscape: false,
			dialogClass: "noClose",
			draggable: false,
			modal: true,
			title: "",
			open: function(event, ui) {
				$(".ui-widget-overlay").attr("style","z-index: 100; background: none repeat scroll 0 0 #000000; height: 100%; left: 0; position: fixed; top: 0; width: 100%; opacity: 0.5;");
				$(".ui-dialog").css("z-index","101");
				$(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").css("display", "none");
				$(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").parent().css("display", "none");
	            
				if($(".ui-dialog .ui-dialog-titlebar .ui-dialog-title img[src=\"" + Gosyl.Common.basePath + "/images/info.png\"]").length == 0) {
					$(".ui-dialog .ui-dialog-titlebar .ui-dialog-title img").remove();
	                $(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").prepend("<img src=\"" + Gosyl.Common.basePath + "/images/info.png\"> ");
	            }
			},
			close: function(event, ui) {
				$(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").css("display", "block");
				$(".ui-dialog .ui-dialog-titlebar .ui-dialog-title").parent().css("display", "block");
	        }, 
     };*/
	
	function init() {
        /*$('#dialogConnexion').dialog(optionDialogConnexion);
		
		while(!$('#dialogConnexion').dialog('isOpen')) {
			$('#btnConnexionUser').trigger('click');
		}*/
		
		btnInscriptionFromLogin = function() {
			document.location = Gosyl.Common.commonPath + 'register';
		};
		
		/*$('#btnClose').click(function(e) {
			e.preventDefault();
			
			$('#dialogConnexion').dialog('close');
         });*/
        $('#myModal').modal('show');
	}
	
	return {
		init: init
	};
})(jQuery);

       

$(document).ready(function() {
	Gosyl.Common.Login.init();
});