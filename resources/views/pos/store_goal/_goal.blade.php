<tr id="_{{$goal->id}}">                            
    <td>
        <b>{{ $goal->store->sn }}</b>{{ '  ' . $goal->store->name}}
    </td>
    <td>
    <input type="text" data-id="{{$goal->id}}" name="origin_goal" value="{{ $goal->origin_goal }}" />
    </td>
    <td>
    <input type="text" data-id="{{$goal->id}}" name="pl_origin_goal" value="{{ $goal->pl_origin_goal }}" />
    </td>                          
</tr>