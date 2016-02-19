@extends('base')

@section('body')
<div class="bs-docs-section clearfix">
    <div class="row">
        <div class="col-md-12">
            <h1>麗嬰房會員名單匯入 <small><a href="/flap/pos_member/import_task" class="btn btn-raised btn-sm btn-default">
                <i class="glyphicon glyphicon-list"></i>
                回到任務列表
            </a></small></h1><hr>

            @include('common.errormsg')

            {!! Form::open(['url' => 'flap/pos_member/import_task', 'files' => true, 'id' => 'import-task']) !!}
            
            <div class="form-group has-warning">
                {!! Form::label('file', '*選擇匯入檔案', ['class' => 'control-label']) !!}
                 <input type="text" readonly class="form-control" placeholder="Browse...">
                {!! Form::file(
                    'file', 
                    ['id'=>'excel', 'class' => 'form-control', 'accept' => 'application/vnd.ms-excel']) 
                !!}

                <p class="help-block">{{'請選擇沒有密碼鎖定的 .xls 檔案，並且只留下一個工作表'}}</p>
            </div>

            <div class="form-group has-warning">
                {!! Form::label(
                    App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_CATEGORY, 
                    '*會員類別', 
                    ['class' => 'control-label']) 
                !!}

                {!! Form::text(
                    App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_CATEGORY, 
                    NULL,
                    ['id'=>App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_CATEGORY, 'class' => 'form-control', 'required' => true, 'placeholder' => '請輸入會員類別代號']) 
                !!}

                 <p class="help-block">{{'Example: 126'}}</p>
            </div>

            <div class="form-group has-warning">
                {!! Form::label(
                    App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_DISTINCTION, 
                    '*會員區別', 
                    ['class' => 'control-label']) 
                !!}

                {!! Form::text(
                    App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_DISTINCTION, 
                    NULL,
                    ['id'=>App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_DISTINCTION, 'class' => 'form-control',  'required' => true, 'placeholder' => '請輸入會員區別代號']) 
                !!}

                 <p class="help-block">{{'Example: 126-75'}}</p>
            </div>

            <div class="form-group">
                {!! Form::label(
                    App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_INSERTFLAG, 
                    '請參考提示輸入首次匯入旗標', 
                    ['class' => 'control-label']) 
                !!}

                {!! Form::text(
                    App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_INSERTFLAG, 
                    NULL,
                    ['id'=>App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_INSERTFLAG, 'class' => 'form-control']) 
                !!}

                <p class="help-block"><b>11:A 5:B</b>  (旗標 11 設定為A, 旗標 5 設定為 B，使用空白區隔)</p>
            </div>

            <div class="form-group">
                {!! Form::label(
                    App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_UPDATEFLAG, 
                    '請參考提示輸入重覆比對旗標', 
                    ['class' => 'control-label']) 
                !!}

                {!! Form::text(
                    App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_UPDATEFLAG, 
                    NULL,
                    ['id'=>App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_UPDATEFLAG, 'class' => 'form-control']) 
                !!}

                <p class="help-block"><b>12:A 5:B</b>  (旗標 12 設定為 A, 旗標 5 設定為 B，使用空白區隔)</p>
            </div>
            
            <div class="form-group">
                {!! Form::submit('匯入', ['class' => 'btn btn-raised btn-primary btn-sm']) !!}
            </div>

            <div class="form-group">
                <img src="{!! URL::asset('/assets/image/import_flag_map.jpg') !!}" class="img-responsive" alt="月份對應旗標">
            </div>
            {!! Form::close() !!}
        </div>
    </div> 
</div>
@stop

@section('js')
<script src="/assets/js/jquery.blockui.js"></script>
<script src="/assets/js/facade.js"></script>
<script src="/assets/js/importtask.js"></script>
@stop