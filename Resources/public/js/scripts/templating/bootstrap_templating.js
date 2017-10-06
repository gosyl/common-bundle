/**
 * Created by alexandre on 15/03/17.
 */

if(typeof Gosyl === 'undefined') {
    Gosyl = {};
}

if(typeof Gosyl.Common === 'undefined') {
    Gosyl.Common = {};
}

Gosyl.Common.BootstrapTemplating = (function($) {
    var $divColxs1 = $('<div>', {
        'class': 'col-xs-1'
    }),
        $divColxs2 = $('<div>', {
            'class': 'col-xs-2'
        }),
        $divColxs3 = $('<div>', {
            'class': 'col-xs-3'
        }),
        $divColxs4 = $('<div>', {
            'class': 'col-xs-4'
        }),
        $divColxs5 = $('<div>', {
            'class': 'col-xs-5'
        }),
        $divColxs6 = $('<div>', {
            'class': 'col-xs-6'
        }),
        $divColxs7 = $('<div>', {
            'class': 'col-xs-7'
        }),
        $divColxs8 = $('<div>', {
            'class': 'col-xs-8'
        }),
        $divColxs9 = $('<div>', {
            'class': 'col-xs-9'
        }),
        $divColxs10 = $('<div>', {
            'class': 'col-xs-10'
        }),
        $divColxs11 = $('<div>', {
            'class': 'col-xs-11'
        }),
        $divColxs12 = $('<div>', {
            'class': 'col-xs-12'
        }),
        $divColsm1 = $('<div>', {
            'class': 'col-sm-1'
        }),
        $divColsm2 = $('<div>', {
            'class': 'col-sm-2'
        }),
        $divColsm3 = $('<div>', {
            'class': 'col-sm-3'
        }),
        $divColsm4 = $('<div>', {
            'class': 'col-sm-4'
        }),
        $divColsm5 = $('<div>', {
            'class': 'col-sm-5'
        }),
        $divColsm6 = $('<div>', {
            'class': 'col-sm-6'
        }),
        $divColsm7 = $('<div>', {
            'class': 'col-sm-7'
        }),
        $divColsm8 = $('<div>', {
            'class': 'col-sm-8'
        }),
        $divColsm9 = $('<div>', {
            'class': 'col-sm-9'
        }),
        $divColsm10 = $('<div>', {
            'class': 'col-sm-10'
        }),
        $divColsm11 = $('<div>', {
            'class': 'col-sm-11'
        }),
        $divColsm12 = $('<div>', {
            'class': 'col-sm-12'
        }),
        $divColmd1 = $('<div>', {
            'class': 'col-md-1'
        }),
        $divColmd2 = $('<div>', {
            'class': 'col-md-2'
        }),
        $divColmd3 = $('<div>', {
            'class': 'col-md-3'
        }),
        $divColmd4 = $('<div>', {
            'class': 'col-md-4'
        }),
        $divColmd5 = $('<div>', {
            'class': 'col-md-5'
        }),
        $divColmd6 = $('<div>', {
            'class': 'col-md-6'
        }),
        $divColmd7 = $('<div>', {
            'class': 'col-md-7'
        }),
        $divColmd8 = $('<div>', {
            'class': 'col-md-8'
        }),
        $divColmd9 = $('<div>', {
            'class': 'col-md-9'
        }),
        $divColmd10 = $('<div>', {
            'class': 'col-md-10'
        }),
        $divColmd11 = $('<div>', {
            'class': 'col-md-11'
        }),
        $divColmd12 = $('<div>', {
            'class': 'col-md-12'
        }),
        $divCollg1 = $('<div>', {
            'class': 'col-lg-1'
        }),
        $divCollg2 = $('<div>', {
            'class': 'col-lg-2'
        }),
        $divCollg3 = $('<div>', {
            'class': 'col-lg-3'
        }),
        $divCollg4 = $('<div>', {
            'class': 'col-lg-4'
        }),
        $divCollg5 = $('<div>', {
            'class': 'col-lg-5'
        }),
        $divCollg6 = $('<div>', {
            'class': 'col-lg-6'
        }),
        $divCollg7 = $('<div>', {
            'class': 'col-lg-7'
        }),
        $divCollg8 = $('<div>', {
            'class': 'col-lg-8'
        }),
        $divCollg9 = $('<div>', {
            'class': 'col-lg-9'
        }),
        $divCollg10 = $('<div>', {
            'class': 'col-lg-10'
        }),
        $divCollg11 = $('<div>', {
            'class': 'col-lg-11'
        }),
        $divCollg12 = $('<div>', {
            'class': 'col-lg-12'
        }),
        $divContainerfluid = $('<div>', {
            'class': 'container-fluid'
        }),
        $divContainer = $('<div>', {
            'class': 'container'
        }),
        $divRow = $('<div>', {
            'class': 'row'
        });

    function getDiv(type) {
        type = Gosyl.Common.STR_ucwords(type);
        var nomDiv = "$div" + type;

        return eval(nomDiv);
    }

    function init() {

    }

    return {
        init: init,
        getDiv: getDiv
    };
})(jQuery);

jQuery(document).ready(function() {
    Gosyl.Common.BootstrapTemplating.init();
});