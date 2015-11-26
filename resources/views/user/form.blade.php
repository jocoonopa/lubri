{{-- Temporary --}}

@include('user.profileform')

{{-- <div class="form-group">
	{!! Form::label('email', '信箱:') !!}
	{!! Form::text('email', null, ['class' => 'form-control']) !!}
</div> --}}
@include ('user.passwordform')

<div class="form-group">
	{!! Form::submit($submitButtonText, ['class' => 'btn btn-primary form-control']) !!}
</div>