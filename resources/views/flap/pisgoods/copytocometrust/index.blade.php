@extends('base')

@section('title') 
景華商品複製為康萃特商品
@stop

@section('body')
    <div class="row">
        <div class="col-md-12">
            <h2>將指定景華商品複製為康萃特商品</h2><hr>
        </div>
        
        <div class="col-md-12">
            <h4>請輸入商品產編搜尋(輸入空白間隔可多筆查詢)</h4>
            
            @include ('common.successmsg')
            @include ('common.errormsg')
            @include ('errors.list')

            {!! Form::open(['method' => 'GET', 'action' => ['Flap\PIS_Goods\CopyToCometrustController@index'], 'id' => 'search']) !!}    
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" name="code" class="form-control" placeholder="Search for...">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">
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

    return false;
});

$('form#insert').find('input[type="checkbox"]').change(function () {
    var $formInsert = $('form#insert');
    var isDisabled = (0 === $formInsert.find('input[type="checkbox"]:checked').length);

    $formInsert.find('button[type="submit"]').prop('disabled', isDisabled);
}).change();

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