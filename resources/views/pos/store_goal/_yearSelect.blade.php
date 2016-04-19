<select class="form-control" name="year" id="year">
    @for ($i = -2; $i <= 2; $i ++)
        <option value="{{ Input::get('year', \Carbon\Carbon::now()->format('Y')) + $i }}" @if (0 === $i) selected @endif>
            {{ Input::get('year', \Carbon\Carbon::now()->format('Y')) + $i }}
        </option>
    @endfor
</select> 