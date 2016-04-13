<div class="list-group-item">
<!--     <div class="row-action-primary">
      <i class="glyphicon glyphicon-cutlery"></i>
    </div> -->
    @if($kind->is_enabled) 
    <div class="row-content">
      <div class="least-content">
        <span class="label label-success">啟用</span>
        </div>
      <h4 class="list-group-item-heading">
        <a href="/flap/pos_member/import_task?kind_id={{$kind->id}}">{{$kind->name}}</a>
      </h4>

      <p class="list-group-item-text">{{'允許部門:  '}}<b>{{ implode(', ', $kind->allow_corps) }}</b></p>
    </div>
    @else
    
    <div class="row-content">
      <div class="least-content">
        <span class="label label-default">停用</span> 
        </div>
      <h4 class="list-group-item-heading">
        {{$kind->name}}
      </h4>

      <p class="list-group-item-text">{{'允許部門:  '}}<b>{{ implode(', ', $kind->allow_corps) }}</b></p>
    </div>

    @endif

</div>