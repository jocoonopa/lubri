<div class="form-group">
    {!! Form::label('file', '*選擇轉換檔案', ['class' => 'control-label']) !!}
     <input type="text" readonly class="form-control" placeholder="Browse...">
    {!! Form::file('file', ['id'=>'excel', 'class' => 'form-control', 'accept' => 'application/vnd.ms-excel']) !!}

    <p class="help-block">{{'請選擇沒有密碼鎖定的 excel 檔案，並且只保留一個工作表'}}</p>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-raised btn-primary"><i class="glyphicon glyphicon-save"></i>轉換</button>
</div>



