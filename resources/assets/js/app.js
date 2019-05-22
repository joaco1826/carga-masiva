
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

$(document).ready(function () {
    $('#frmExcel').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        var formdata = false;
        if (window.FormData){
            formdata = new FormData(form[0]);
        }
        $.ajax({
            async:true,
            type: 'POST',
            processData: false,
            cache: false,
            contentType : false,
            url: '/excel',
            dataType: 'json',
            data: formdata ? formdata : form.serialize(),
            statusCode: {
                200: function(data) {
                    swal({
                            title: "Bien hecho",
                            text: data.message,
                            type: "success"
                        },
                        function(isConfirm){
                            if (isConfirm) {
                                location.reload()
                            }
                        });
                },
                422: function (data) {
                    $.each(data.responseJSON.errors, function (key, text) {
                        swal(key, text, 'info');
                        return false;
                    });
                },
                500: function () {
                    swal('¡Ups!', 'Error interno del servidor', 'error')
                }
            }
        });
    });
});
