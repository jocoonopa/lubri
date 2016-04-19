@if ($dtCarbon = \Carbon\Carbon::create(Input::get('year', \Carbon\Carbon::now()->format('Y')), array_get($goals, 0)->month, 1, 0, 0, 0) )
<div class="col-md-6 col-xs-12">
    <div class="panel panel-default">
        <div class="panel-heading">{{$dtCarbon->year}}年{{$dtCarbon->month}}月</div>
        <div class="panel-body">
            <table class="table">
                <thead>
                    <th></th>
                    <th>營業目標</th>
                    <th>PL</th>
                </thead>
                <tbody>
                    @each('pos.store_goal._goal', $goals, 'goal')
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
        @if ($dtCarbon->lte(\Carbon\Carbon::now()))
            <a href="/report/retail_sales/download?date={{ $dtCarbon->format('Y-m-d H:i:s') }}" class="btn btn-primary btn-raised">
                <i class="glyphicon glyphicon-save"></i>業績報表
            </a>
        @endif
        </div> 
    </div>
</div>
@endif
