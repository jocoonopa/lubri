@if(Session::has('error'))
    <div class="alert alert-danger" role="alert">
        <span>{!! Session::get('error') !!}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
    </div>
@endif