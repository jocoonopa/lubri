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
        <h1>偉特匯入資料下載</h1><hr>
      
        <form action="" method="GET">
            <h4 class="text-primary">瑛聲相關條件(押住 ctrl 可多選)</h4>

            <div class="form-group">
                <label class="control-label" for="eng_emp_codes">瑛聲負責人代號</label>
                
                <select class="form-control js-multi" name="eng_emp_codes[]" multiple="multiple">
                    @foreach ($empCorpGroups as $groupKey => $empCorpGroup)
                        <optgroup label="{{$groupKey}}">
                        @foreach ($empCorpGroup as $emp) 
                            <option value="{{$emp['Code']}}">{{$emp['Code'] . $emp['Name']}}</option>
                        @endforeach     
                        </optgroup>                   
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="control-label" for="eng_campaign_cds">瑛聲活動代號</label>
                
                <select class="form-control js-multi" name="eng_campaign_cds[]" multiple="multiple">
                    @foreach ($campaigns as $campaign)
                        <option value="{{$campaign['CampaignCD']}}">{{$campaign['CampaignCD'] . $campaign['CampaignName']}}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="control-label" for="eng_assign_date">瑛聲指派日期(>=)</label>
                <input class="form-control" name="eng_assign_date" id="assign_date" type="text">

                <p class="help-block hint">{{'>= 選擇的指派日期'}}</p>
            </div>

            <div class="form-group">
                <label class="control-label" for="eng_source_cds">瑛聲客戶代號(SourceCD)</label>
                <select class="js-example-tags form-control" name="eng_source_cds[]" id="eng_source_cds" multiple="multiple">
                    <option value="">請輸入客戶代號</option>
                </select>
            </div>
            
            <h4 class="text-primary">輔翼相關條件(押住 ctrl 可多選)</h4>

            <div class="form-group">
                <label class="control-label" for="flap_emp_codes">輔翼開發人代號</label>

                <select class="form-control js-multi" id="flap_emp_codes" name="flap_emp_codes[]" multiple="multiple">
                    @foreach ($empCorpGroups as $groupKey => $empCorpGroup)
                        <optgroup label="{{$groupKey}}">
                        @foreach ($empCorpGroup as $emp) 
                            <option value="{{$emp['Code']}}">{{$emp['Code'] . $emp['Name']}}</option>
                        @endforeach     
                        </optgroup>                   
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="control-label" for="flap_source_cds">輔翼客戶代號</label>

                <select class="js-example-tags form-control" name="flap_source_cds[]" id="flap_source_cds" multiple="multiple">
                    <option value="">請輸入客戶代號</option>
                </select>
            </div>

            <div class="form-group">
                <input type="hidden" name="redirect" value="">
                <button data-id="flap" type="button" class="submit btn btn-default btn-sm btn-raised"><i class="glyphicon glyphicon-download-alt"></i> 名單匯出</button>

                <a class="btn btn-primary btn-sm btn-raised" href="{{ url('report/ctilayout/campaign') }}"><i class="glyphicon glyphicon-download-alt"></i>瑛聲活動</a>
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

$('.js-multi').select2();

$(".js-example-tags").select2({
  tags: true
});

</script>
@stop