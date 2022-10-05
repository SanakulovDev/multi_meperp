$(document).ready(function () {
    
    $(".form-modal").click(function (e) {
        e.preventDefault();
        $("#modalError").find(".help-block").html('');
        var title = $(this).text();
        $('#modal').find('#modal_head').html(title);
        $("#modal").modal('show')
            .find('#modalContent')
            .load($(this).attr('href'));
    });

    $(document).on('click', '.modalFormSubmit', function () {
        $('#modal').find('form').submit();
    });

    $(document).on('click', '.modalButtonUpdate', function () {
        var title = $('.modalButtonUpdate').attr('title');
        $('#modal').find('#modal_head').html(title);
        $('#modalContent').load($(this).attr('value'));
        $("#modalError").find(".help-block").html('');
        $('#modal').modal('show');
    });

    $(document).on('beforeSubmit', '.modalForm', function () {
        var form = $(this);
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
                $.pjax.reload({container:'#pjaxGrid'});
                $('#modal').modal('hide');
            } else {
                $("#modalError").addClass("has-error");
                $("#modalError").find(".help-block").html(data.errors);
            }
        }).fail(function (error) {
            console.log("server error:" + error);
        });
        return false;
    });

    // delete record
    $(document).on('click', '.modalButtonDelete', function () {
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
