<div class="form-group">
    {!! Form::label(
        'name', 
        '*會員姓名', 
        ['class' => 'control-label']) 
    !!}

    {!! Form::text(
        'name', 
        $content->name,
        ['id'=>'name', 'class' => 'form-control', 'required' => true, 'placeholder' => '請輸入會員姓名']) 
    !!}

    <p class="help-block">{{'Example: 王大明'}}</p>
</div>
<div class="form-group">
    {!! Form::label(
        'email', 
        '電子信箱', 
        ['class' => 'control-label']) 
    !!}

    {!! Form::email(
        'email', 
        $content->email,
        ['id'=>'email', 'class' => 'form-control', 'placeholder' => '請輸入會員信箱']) 
    !!}

    <p class="help-block">{{'Example: example@domain.com'}}</p>
</div>
<div class="form-group">
    {!! Form::label(
        'cellphone', 
        '手機', 
        ['class' => 'control-label']) 
    !!}

    {!! Form::text(
        'cellphone', 
        $content->cellphone,
        ['id'=>'cellphone', 'class' => 'form-control', 'placeholder' => '請輸入會員手機']) 
    !!}

    <p class="help-block">{{'Example: 0987654321'}}</p>
</div>
<div class="form-group">
    {!! Form::label(
        'hometel', 
        '住家電話', 
        ['class' => 'control-label']) 
    !!}

    {!! Form::text(
        'hometel', 
        $content->hometel,
        ['id'=>'hometel', 'class' => 'form-control', 'placeholder' => '請輸入會員住家電話']) 
    !!}

    <p class="help-block">{{'Example: 0223897654'}}</p>
</div>
<div class="form-group">
    {!! Form::label(
        'officetel', 
        '公司電話', 
        ['class' => 'control-label']) 
    !!}

    {!! Form::text(
        'officetel', 
        $content->officetel,
        ['id'=>'officetel', 'class' => 'form-control', 'placeholder' => '請輸入會員公司電話']) 
    !!}

    <p class="help-block">{{'Example: 0223897654'}}</p>
</div>

<div class="form-group">
    {!! Form::label(
        'zipcode', 
        '郵遞區號/縣市/區', 
        ['class' => 'control-label']) 
    !!}
    
    <div id="twzipcode">
        <div data-role="county" data-style="form-control" data-value="{{$content->getCityName()}}"></div>
        <div data-role="district" data-style="form-control" data-value="{{$content->getStateName()}}"></div>
        <div data-role="zipcode" data-style="form-control" data-value="{{$content->getZipcode()}}"></div>
    </div>                             

    <p class="help-block">{{'Example: 235'}}</p>
</div>

<div class="form-group">
    {!! Form::label(
        'homeaddress', 
        '住家地址', 
        ['class' => 'control-label']) 
    !!}

    {!! Form::text('homeaddress', $content->homeaddress, ['id'=>'homeaddress', 'class' => 'form-control', 'placeholder' => '請輸入會員住家地址']);
    !!}

    <p class="help-block">{{'Example: 愛國東路一段32號'}}</p>
</div>

<div class="form-group">
    {!! Form::label(
        'birthday', 
        '生日', 
        ['class' => 'control-label']) 
    !!}

    {!! Form::text(
        'birthday', 
        $content->birthday,
        ['id'=>'birthday', 'class' => 'form-control', 'placeholder' => '請選擇會員生日']) 
    !!}
</div>

<div class="form-group">
    {!! Form::label(
        'period_at', 
        '預產期', 
        ['class' => 'control-label']) 
    !!}

    {!! Form::text(
        'period_at', 
        $content->getPeriodAt()->format('Ymd'),
        ['id'=>'period_at', 'class' => 'form-control', 'placeholder' => '請選擇會員預產期']) 
    !!}
</div>

<div class="form-group">
    {!! Form::label(
        'hospital', 
        '生產醫院', 
        ['class' => 'control-label']) 
    !!}

    {!! Form::text(
        'hospital', 
        $content->hospital,
        ['id'=>'hospital', 'class' => 'form-control', 'placeholder' => '請輸入會員生產醫院']) 
    !!}

    <p class="help-block">{{'Example: 臺大醫院'}}</p>
</div> 
<div class="form-group">
    {!! Form::submit('確認', ['class' => 'btn btn-raised btn-primary btn-sm']) !!}
    {!! Form::hidden('pos_member_import_task_id', $task->id) !!}

    <a class="btn btn-raised btn-default btn-sm" href="/flap/pos_member/import_task/{{$task->id}}/content">取消</a>
</div>