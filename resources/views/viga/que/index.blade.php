@extends('base')

@section('title') 
同步排程紀錄
@stop

@section('body')

<div class="row">
    <div class="col-md-12">
        <h1>偉特瑛聲資料同步紀錄<small>{{'每頁' . $limit . '筆'}}</small></h1><hr>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>同步排程</th>
                    <th>狀態</th>
                    <th>開始執行時間</th>
                    <th>完成時間</th>
                    <th>匯出檔案</th>
                </tr>                
            </thead>
            <tbody>
                @foreach($ques as $que)
                <tr>
                    <td>{{ $que->type->hname }}</td>
                    <td>{!! $que->getStatusName() !!}</td>
                    <td>{{ $que->created_at->format('Y-m-d H:i:s')}}</td>
                    <td>{{ $que->getCompletedDateTime() }}</td>
                    <td>{{ $que->dest_file }}</td>
                </tr>                    
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-md-12">
        {!! $ques->render() !!}
    </div>
</div>

@stop