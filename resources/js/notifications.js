$(document).ready(function() {
    // Función para obtener los parámetros específicos de la URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    };

    // Obtener los parámetros result y msg de la URL
    var result = getUrlParameter('result');
    var msg = getUrlParameter('msg');

    // Verificar si hay parámetros result y msg en la URL
    if (result && msg) {
        if (result === 'ok') {
            showSuccessNotification(msg);
        } else {
            showErrorNotification(msg);
        }
    }

    // Función para mostrar notificación de éxito
    function showSuccessNotification(msg) {
        $.notify({
            title: '<strong>Éxito</strong>',
            message: "<br>" + msg,
            icon: 'glyphicon glyphicon-ok',
        }, {
            type: 'success',
            position: null,
            element: 'body',
            showProgressbar: false,
            placement: {
                from: 'bottom',
                align: 'right'
            },
            timer: 7500,
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
        });
    }

    // Función para mostrar notificación de error
    function showErrorNotification(msg) {
        $.notify({
            title: '<strong>Error</strong>',
            message: "<br>" + msg,
            icon: 'glyphicon glyphicon-remove',
        }, {
            type: 'danger',
            position: null,
            element: 'body',
            showProgressbar: false,
            placement: {
                from: 'bottom',
                align: 'right'
            },
            timer: 15000,
            onShow: null,
            onShown: null,
            onClose: null,
            onClosed: null,
            icon_type: 'class',
        });
    }
});
