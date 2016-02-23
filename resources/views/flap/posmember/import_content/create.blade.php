@extends('base')

@section('css')
<link rel="stylesheet" href="/assets/bootstrap-material-datetimepicker-gh-pages/css/bootstrap-material-datetimepicker.css">
@stop

@section('body')
<div class="bs-docs-section clearfix">
    <div class="row">
        <div class="col-md-12">
           <h1>{{ $task->name . '  項目新增'}} 
            <small><a href="/flap/pos_member/import_task/{{$task->id}}/content" class="btn btn-raised btn-sm btn-default">
            <i class="glyphicon glyphicon-circle-arrow-left"></i>{{ '回到任務&nbsp;' . $task->name }}</a></small></h1>
                
            @include('common.errormsg')

           {!! Form::model($content, [
                'method' => 'POST',
                'url' => '/flap/pos_member/import_task/' . $task->id . '/content'
            ]) !!}
                @include('flap.posmember.import_content._form')
           {!! Form::close() !!}
        </div>
    </div>  
</div>
@stop

@section('js')
<script src="http://momentjs.com/downloads/moment-with-locales.min.js"></script>
<script src="/assets/bootstrap-material-datetimepicker-gh-pages/js/bootstrap-material-datetimepicker.js"></script>
<script src="/assets/jQuery-TWzipcode-master/jquery.twzipcode.min.js"></script>
<script src="/assets/js/importcontent.js"></script>
@stop