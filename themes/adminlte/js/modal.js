$(document).ready(function () {
    function ensureModalLoader() {
        if ($('#modalLoading').length) {
            return $('#modalLoading');
        }

        $('#modal .modal-body').css('position', 'relative').append(
            '<div id="modalLoading" style="display:none; position:absolute; inset:0; background:rgba(255,255,255,0.75); z-index:1051;">' +
                '<div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); text-align:center;">' +
                    '<i class="fa fa-spinner fa-spin" style="font-size:28px; color:#3c8dbc;"></i>' +
                    '<div style="margin-top:8px; font-size:13px; color:#555;">Loading...</div>' +
                '</div>' +
            '</div>'
        );

        return $('#modalLoading');
    }

    function setModalLoading(isLoading) {
        var $loader = ensureModalLoader();
        var $submit = $('#modal .modalFormSubmit');

        if (isLoading) {
            $("#modalError").removeClass("has-error").find(".help-block").html('');
            $loader.show();
            $submit.prop('disabled', true);
        } else {
            $loader.hide();
            $submit.prop('disabled', false);
        }
    }

    function reloadAfterSuccess() {
        if ($.pjax && $('#pjaxGrid').length) {
            $.pjax.reload({container:'#pjaxGrid'});
            $('#modal').modal('hide');
            setModalLoading(false);
            return;
        }

        window.location.reload();
    }
    
    $(".form-modal").click(function (e) {
        e.preventDefault();
        $("#modalError").find(".help-block").html('');
        var title = $(this).text();
        $('#modal').find('#modal_head').html(title);
        setModalLoading(true);
        $("#modal").modal('show')
            .find('#modalContent')
            .load($(this).attr('href'), function () {
                setModalLoading(false);
            });
    });

    $(document).on('click', '.modalFormSubmit', function () {
        $('#modal').find('form').submit();
    });

    $(document).on('click', '.modalButtonUpdate', function () {
        var title = $('.modalButtonUpdate').attr('title');
        $('#modal').find('#modal_head').html(title);
        setModalLoading(true);
        $('#modalContent').load($(this).attr('value'), function () {
            setModalLoading(false);
        });
        $("#modalError").find(".help-block").html('');
        $('#modal').modal('show');
    });

    $(document).on('beforeSubmit', '.modalForm', function () {
        var form = $(this);
        setModalLoading(true);
        $.post(
            form.attr("action"),
            form.serialize()
        ).done(function (result) {
            let data;
            try {
                data = jQuery.parseJSON(result);
            } catch (e) {
                data = result;
            }

            if (data.status == 1) {
                form.trigger("reset");
                reloadAfterSuccess();
            } else {
                setModalLoading(false);
                $("#modalError").addClass("has-error");
                $("#modalError").find(".help-block").html(data.errors);
            }
        }).fail(function (error) {
            setModalLoading(false);
            console.log("server error:" + error);
        });
        return false;
    });

    // delete record
    $(document).on('click', '.modalButtonDelete', function (e) {

			e.preventDefault();
			console.log($(this).data('href'));
        $("#modalDelete")
            .find(".modalFormDelete")
            .data('href', $(this).data('href'))
            .data('grid', $(this).data('grid'));

        $('#modalDelete').modal('show');
    });

    $(document).on('click', '.modalFormDelete', function () {
        var deleteUrl= $(this).data('href');
        var pjaxGrid = $(this).data('grid');
        $.ajax({
            url: deleteUrl,
            type: 'post',
            error: function (xhr, status, error) {
                alert('ERROR: ' + xhr.responseText);
            }
        }).done(function (data) {
            $('#modalDelete').modal('hide');
            $.pjax.reload({container: '#'+pjaxGrid});
        });

    });
});
