@extends('base')

@section('title') 
景華商品複製為康萃特商品
@stop

@section('body')
    <div class="row">
        <div class="col-md-12">
            <h4>將選擇商品複製為康萃特商品</h4><hr>
        </div>
        
        <div class="col-md-12">            
            @include ('common.successmsg')
            @include ('common.errormsg')
            @include ('errors.list')

            {!! Form::open(['method' => 'GET', 'action' => ['Flap\PIS_Goods\CopyToCometrustController@index'], 'id' => 'search']) !!}    
                <div class="form-group label-floating m-b-10">
                    <div class="input-group">
                        <label for="code" class="control-label">請輸入產編，多筆請用空白間隔</label>
                        <input id="code" type="text" name="code" class="form-control">
                        <p class="help-block">{{'Example: A00049 D00060 A00047'}}</p>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit" disabled>
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>
                    </div>
                </div>
            {!! Form::close() !!}
            
            @if (0 < count($goodses))
                {!! Form::open(['method' => 'POST', 'action' => ['Flap\PIS_Goods\CopyToCometrustController@store'], 'id' => 'insert']) !!}    
                    @include('flap.pisgoods.copytocometrust.form', ['goodses' => $goodses])
                {!! Form::close() !!}
            @endif
        </div>
    </div>
@stop

@section('js')
<script>
$('#search').find('button[type="submit"]').click(function () {
    var $formInsert   = $('form#insert');
    var $formSearch   = $('form#search');
    var $searchButton = $formSearch.find('button[type="submit"]');
    var $trs          = $formInsert.find('tbody>tr');
    var $input        = $formSearch.find('input[name="code"]');
    var inputStr      = $input.val();

    if (0 === inputStr.length) {
        bootbox.alert('您沒有輸入任何商品編號!');
        $input.focus();

        return false;
    }

    if (0 < $trs.length) {
        $trs.each(function () {
            inputStr += ',' + $(this).data('code');
        });
    }

    $input.val(inputStr);
    $searchButton.prop('disabled', true);
    $formSearch.submit();
    $searchButton.prop('disabled', false);

    return false;
});

$('#search').find('input[name="code"]').keyup(function () {
    var $searchButton = $('#search').find('button[type="submit"]');

    $searchButton.prop('disabled', (0 === $(this).val().length));
});

$('form#insert').find('input[type="checkbox"]').change(function () {
    var $formInsert = $('form#insert');
    var isDisabled = (0 === $formInsert.find('input[type="checkbox"]:checked').length);

    $formInsert.find('button[type="submit"]').prop('disabled', isDisabled);
}).change();

$('form#insert').find('button[type="submit"]').click(function () {
    var $formInsert = $('form#insert');
    var isEnabled = (0 < $formInsert.find('input[type="checkbox"]:checked').length);

    if (isEnabled) {
        $(this).prop('disabled', true);
        $('form').submit();
    } else {
        bootbox.alert('您沒有勾選任何商品編號!');
    }

    return false;
});

$('.jq-remove').click(function () {
    var $formInsert = $('form#insert');

    $(this).closest('tr').remove();

    if (0 === $formInsert.find('tbody>tr ').length) {
        $formInsert.remove();
    }
});

$('#check-all').click(function () {
    var $formInsert = $('form#insert');

    $formInsert.find('input[type="checkbox"]').prop('checked', true);

    $('form#insert').find('input[type="checkbox"]').first().change();
});

$('#inverse-check-all').click(function () {
    var $formInsert = $('form#insert');

    $formInsert.find('input[type="checkbox"]').prop('checked', false);

    $('form#insert').find('input[type="checkbox"]').first().change();
});

$('form#search').find('input').focus();
</script>
@stop