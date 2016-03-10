@extends('base')

@section('css')
<link rel="stylesheet" href="/assets/bootstrap-material-datetimepicker-gh-pages/css/bootstrap-material-datetimepicker.css">
@stop

@section('title') 
促銷代號出貨單撈取
@stop

@section('body')
    <div class="row">       
        <div class="col-md-12">     
            <h1>促銷代號出貨單撈取</h1> 

            {!! Form::open(['method' => 'POST', 'action' => ['Flap\CCS_OrderIndex\PromoteShipmentController@export']]) !!}    
                @include('flap.ccsorderindex.promoteshipment.form')
            {!! Form::close() !!}   

            <table class="promotes table table-striped hide">
                <thead>
                    <th>開始時間</th>
                    <th>結束時間</th>
                    <th>活動代號</th>
                    <th></th>
                </thead>
                <tbody></tbody>
            </table>   
        </div>
    </div>

    <span class="hide my-snackbar" data-toggle=snackbar data-content="請求已送出，請稍後片刻!">&nbsp;</span>
@stop

@section('js')
<script src="/assets/js/snackbar.js"></script>
<script src="http://momentjs.com/downloads/moment-with-locales.min.js"></script>
<script src="/assets/bootstrap-material-datetimepicker-gh-pages/js/bootstrap-material-datetimepicker.js"></script>
<script>
moment.locale('zh-tw');
var bmdObg = {
    'time': false, 
    'clearButton': true,
    'cancelText': '取消',
    'okText': '確認',
    'clearText': '清除',
    'format': 'YYYYMMDD'
};

function tableToggle() {
    if (0 === $('table.promotes').find('tbody tr').length) {
        $('table.promotes').addClass('hide');
    } else {
        $('table.promotes').removeClass('hide');
    }
}

function addTr($e) {
    var $form  = $e.closest('form'); 
    var $psa   = $form.find('#promote-start-at');
    var $pea   = $form.find('#promote-end-at');
    var $code  = $form.find('#promote-code');
    var $table = $('table.promotes');
    $table.prepend('<tr class="promote"><td>' + $psa.val() + ' </td><td>' + $pea.val() + '</td><td>' + $code.val() + '</td><td><button type="button" class="promote-remove btn btn-xs btn-warning"><i class="glyphicon glyphicon-remove-sign"></i></td></tr>');

    $('table.promotes').find('.promote-remove').eq(0).click(function () {
        $(this).closest('tr').remove();
        tableToggle();
        submitDisabledToggle();
        refreshPromoteVal();

        return false;
    });
}

function submitDisabledToggle() {
    $('button[type="submit"]').prop('disabled', (0 === $('table.promotes').find('tbody tr').length));
}

function promoteAddToggle() {
    $('button.promote-add').prop('disabled', true);
}

function emptyInput() {
    $('form').find('input').not('[name="_token"]').val('');
}

function refreshPromoteVal() {
    var promote_q = [];
    
    $('table.promotes').find('tr.promote').each(function () {
        var $tr = $(this);
        var obj = {'start_at': $tr.find('td').eq(0).text(), 'end_at': $tr.find('td').eq(1).text(), 'code': $tr.find('td').eq(2).text()};
        promote_q.push(obj);
    });

    $('input[name="promote-q"]').val(JSON.stringify(promote_q));
}

$('#promote-start-at').add($('#promote-end-at')).bootstrapMaterialDatePicker(bmdObg);
$('#promote-code').keypress(function () {
    $('button.promote-add').prop('disabled', (8 > $(this).val().length));
});

$('#promote-code').blur(function () {
    $('button.promote-add').prop('disabled', (8 > $(this).val().length));
});

$('button.promote-add').click(function () {       
    addTr($(this));
    tableToggle();
    submitDisabledToggle();
    promoteAddToggle();
    refreshPromoteVal();
    emptyInput();
});

var options =  {
    style: "toast", // add a custom class to your snackbar
    timeout: 100 // time in milliseconds after the snackbar autohides, 0 is disabled
};

$.snackbar(options);

$('button[type="submit"]').click(function () {
    var $this = $(this);

    setTimeout(function () {
        $('.my-snackbar').snackbar('show');
    }, 1000);

    refreshPromoteVal();
    
    $('form').submit();
    $(this).prop('disabled', true);
    setTimeout(function () {
        $this.prop('disabled', false);
    }, 10000);

    return false;
});
</script>
@stop