@extends('base')

@section('title')
偉特匯入資料下載
@stop

@section('css')
<link rel="stylesheet" href="/assets/bootstrap-material-datetimepicker-gh-pages/css/bootstrap-material-datetimepicker.css">
<link href="/assets/select2-4.0.2/dist/css/select2.css" rel="stylesheet" />
@stop

@section('body')

<div class="row">
    <div class="col-md-12">
        <h1>偉特匯入資料下載</h1>
      
        <form action="" method="GET">
            <div class="form-group">
                <label class="control-label" for="source_cd">客戶代號</label>
                <input class="form-control" name="source_cd" id="source_cd" type="text" value="{{Input::get('source_cd', '')}}">

                <p class="help-block hint">{{'N032310, T88420'}}</p>
            </div>

            <div class="form-group">
                <label class="control-label" for="code">專員代號</label>
                <input class="form-control" name="code" id="code" type="text" value="{{Input::get('code', '')}}">

                <p class="help-block hint">{{'20160202,20160203'}}</p>
            </div>

            <div class="form-group">
                <label class="control-label" for="corp">部門</label>
                <select class="form-control" name="corps[]" multiple="multiple">
                    <option value="CH53000">客戶經營一部</option>
                    <option value="CH54000">客戶經營二部</option>
                    <option value="CH54100">客戶經營三部</option>
                </select>
            </div>

            <div class="form-group">
                <label class="control-label" for="assign_date">指派日期(>=)</label>
                <input class="form-control" name="assign_date" id="assign_date" type="text" value="{{Input::get('assign_date', '')}}">

                <p class="help-block hint">{{'>= 選擇的指派日期'}}</p>
            </div>

            <div class="form-group">
                <label class="control-label" for="campaign_cd">活動代號</label>
                <input class="form-control" name="campaign_cd" id="campaign_cd" type="text" value="{{Input::get('campaign_cd', '')}}">

                <p class="help-block hint">{{'OB_6713,OB_6714'}}</p>
            </div>

            <div class="form-group">
                <input type="hidden" name="redirect" value="">
                <button data-id="flap" type="button" class="submit btn btn-default btn-sm btn-raised"><i class="glyphicon glyphicon-download-alt"></i> 輔翼會員</button>
            </div>
        </form>
    </div>
</div>

@stop

@section('js')
<script src="http://momentjs.com/downloads/moment-with-locales.min.js"></script>
<script src="/assets/bootstrap-material-datetimepicker-gh-pages/js/bootstrap-material-datetimepicker.js"></script>
<script src="/assets/select2-4.0.2/dist/js/select2.min.js"></script>
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

$('#assign_date').bootstrapMaterialDatePicker(bmdObg);    

$('.submit').click(function () {
    $('[name="redirect"]').val($(this).data('id'));

    $('form').attr('action', '/report/ctilayout/' + $('[name="redirect"]').val()).submit();

    return false;
});

$('select').select2();

</script>
@stop