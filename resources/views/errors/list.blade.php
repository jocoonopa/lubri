@if ($errors->any())
	<div class="alert alert-danger" role="alert">
        @foreach ($errors->all() as $error)<span>{{ $error . '   ' }}</span>@endforeach

        <button type="button" class="close pull-right" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
	</div>
@endif