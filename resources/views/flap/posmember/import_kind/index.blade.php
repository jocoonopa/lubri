@extends('base')

@section('title')選擇會員匯入方式@stop

@section('body')
<div class="bs-docs-section clearfix">
    <div class="row">
        <div class="col-md-12">
            <h3>選擇會員匯入方式</h3><hr>
            
            <div class="list-group">
              @each('flap.posmember.import_kind._listItem', $kinds, 'kind')                            
            </div>       
        </div>
    </div>  
</div>
@stop

