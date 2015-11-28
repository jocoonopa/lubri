@if(Session::has('success'))
    <div class="alert alert-success" role="alert">
        <span>{!! Session::get('success') !!}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
    </div>
@endif