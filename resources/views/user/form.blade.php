{{-- Temporary --}}

@include('user.profileform')

@include ('user.passwordform')

<div class="form-group">
	{!! Form::submit($submitButtonText, ['class' => 'btn btn-raised btn-primary form-control']) !!}
</div>