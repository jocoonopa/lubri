<div class="panel panel-default">
    <div class="panel-heading">撈取勾選欄位為空之項目</div>
    <div class="panel-body">
        <span class="label label-info">{{ '過濾結果:&nbsp;' . $count . '位'}} 
            @if(\Input::get('is_exist'))
                <span class="badge">@if('yes' == \Input::get('is_exist')){{ '舊會員'}} @else {{'新會員'}} @endif</span>
            @endif
        </span>
        <form id="import-content-notnull-column" method="GET" class="check-component" action="/flap/pos_member/import_task/{{$task->id}}/content">
            <div class="form-group">
                <div class="checkbox">
                    @foreach (\App\Utility\Chinghwa\Flap\POS_Member\Import\Import::getNullColumns() as $key => $column
                    )
                    <label>
                        <input type="checkbox" value="1" name="{{ $key }}" @if('1' == \Input::get($key)) checked @else @endif>
                        {{ $column }}
                    </label>
                    @endforeach                    
                </div>
                
                @if(\Input::get('is_exist'))
                    <input type="hidden" name="is_exist" value="{{ \Input::get('is_exist') }}">
                @endif
                
                <button type="button" class="pull-left btn btn-raised btn-xs btn-default check-all">
                <i class="glyphicon glyphicon-ok"></i>
                全部勾選</button>
                <button type="button" class="pull-left btn btn-xs btn-default cancel-all">
                <i class="glyphicon glyphicon-remove"></i>
                全部取消</button>

                <button type="submit" class="pull-right btn btn-raised btn-xs btn-primary">
                <i class="glyphicon glyphicon-search"></i>
                搜尋</button>
            </div>                      
        </form>       
    </div>
</div>