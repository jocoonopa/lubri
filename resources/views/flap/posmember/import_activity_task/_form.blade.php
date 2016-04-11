<div class="form-group has-warning">
    {!! Form::label(
        App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_CATEGORY, 
        '*任務名稱', 
        ['class' => 'control-label']) 
    !!}

    {!! Form::text(
        'name', 
        NULL,
        ['id'=> 'name', 'class' => 'form-control', 'required' => true, 'placeholder' => '請輸入任務名稱']) 
    !!}

     <p class="help-block">{{'任務名稱必須唯一，不可重複'}}</p>
</div>

@if (NULL === $task->id)
<div class="form-group has-warning">
    {!! Form::label('file', '*選擇匯入檔案', ['class' => 'control-label']) !!}
     <input type="text" readonly class="form-control" placeholder="Browse...">
    {!! Form::file(
        'file', 
        ['id'=>'excel', 'class' => 'form-control', 'accept' => 'application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, .csv']) 
    !!}

    <p class="help-block">{{'請選擇沒有密碼鎖定的 excel 檔案，並且只留下一個工作表'}}</p>
</div>
@endif

<div class="form-group has-warning">
    {!! Form::label(
        App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_CATEGORY, 
        '*會員類別', 
        ['class' => 'control-label']) 
    !!}

    {!! Form::text(
        App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_CATEGORY, 
        $task->category,
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
        $task->distinction,
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
        $task->getInsertFlagString(),
        ['id'=>App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_INSERTFLAG, 'class' => 'form-control']) 
    !!}

    <p class="help-block"><b>11:A 5:B</b>  (旗標 11 設定為A, 旗標 5 設定為 B，使用空白區隔)</p>
</div>

<div class="form-group has-warning">
    {!! Form::label(
        App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_MEMO, 
        '添加備註', 
        ['class' => 'control-label']) 
    !!}

    {!! Form::text(
        App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_MEMO, 
        $task->distinction,
        ['id'=>App\Utility\Chinghwa\Flap\POS_Member\Import\Import::OPTIONS_MEMO, 'class' => 'form-control',  'placeholder' => '請輸入添加備註']) 
    !!}
</div>

<div class="form-group">
    {!! Form::submit(NULL === $task->id ? '匯入' : '確認', ['class' => 'btn btn-raised btn-primary btn-sm']) !!}
</div>