toastr.options = {"positionClass": "toast-bottom-full-width"};

function updateGoal(goalId, attr, val) {
    $.post(
        '/pos/store_goal/' + goalId, 
        {'attr': attr, 'val': val, '_method': 'PUT', '_token': _token}
    ).done(function() {
        toastr.success('更新完成', '', {timeOut: 2000});
    })
    .fail(function() {
        toastr.error( "error" );
    });
}

(function () {
    $('input').keyup(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) || 
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            if (8 === e.keyCode) {
                $(this).val(typeWithComma($(this).val()));
            }
            
            return;
        }

        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

        hasTask = $(this).data('id');

        $(this).val(typeWithComma($(this).val()));
    });

    $('input').blur(function () {
        var str = $(this).val();

        return updateGoal($(this).data('id'), $(this).attr('name'), str.split(',').join(''));
    });

    $('select[name="year"]').change(function () {
        window.location.href = '?year=' + $(this).val();
    });

    $('input').each(function () {
        $(this).val(typeWithComma($(this).val()));
    });
})();