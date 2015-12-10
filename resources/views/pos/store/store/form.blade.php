{{-- Temporary --}}
<div class="form-group">
	{!! Form::label('sn', '代號:') !!}
	{!! Form::text('sn', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
	{!! Form::label('name', '名稱:') !!}
	{!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
	{!! Form::label('is_active', '經營中:') !!}
	{!! Form::checkbox('is_active', true, true) !!}
</div>

<div class="form-group">
	{!! Form::select('store_area', $areas, null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
	{!! Form::submit($submitButtonText, ['class' => 'btn btn-primary form-control']) !!}
</div>