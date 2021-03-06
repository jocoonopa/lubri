<div class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <a href="/" class="navbar-brand">
        @if (Auth::check())
          {{ Auth::user()->username . '@' }}
        @endif
        景華生技
      </a>
      <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <div class="navbar-collapse collapse" id="navbar-main">
      <ul class="nav navbar-nav">
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="themes">報表排程 <span class="caret"></span></a>
          <ul class="dropdown-menu" aria-labelledby="themes">  
            <li class="dropdown-header">Hourly|Much Frequently</li>  
            <li><a href="#">系統設備檢測<span class="badge">每小時</span> </a></li>
            <li><a href="#">康萃特訂單單號+CT<span class="badge">1000~1500/每10分鐘</span></a></li>
            <li class="divider"></li>        
            <li class="dropdown-header">Daily</li>
            <li><a href="{{ route('retail_sales_index') }}">門市營業額分析日報表<span class="badge">0900</span></a></li>
            <li><a href="{{ route('directsale_corp3_trace_index') }}">客經三成效追蹤<span class="badge">0900</span></a></li>
            <li><a href="{{ route('daily_sale_record_index') }}">每日業績<span class="badge">0900</span></a></li>
            <li><a href="{{ route('report_credit_card') }}">訂單刷卡成交<span class="badge">1445</span></a></li>
            <li><a href="{{ route('daily_back_goods_index') }}">每日回貨<span class="badge">1500</span></a></li>
            <li><a href="https://gist.github.com/jocoonopa/691e1dfff67e49b18509" target="_blank">供應處百及FTP檔案<span class="badge">1515</span></a></li>
            <li><a href="{{ route('report_upbrush') }}">補刷訂單<span class="badge">1715</span></a></li>
            <li class="divider"></li>
            <li class="dropdown-header">Weekly</li>
            <li><a href="{{ route('emppurchase_index') }}">員購銷貨單 <span class="badge">每周四</span> </a></a></li>
            <li class="divider"></li>
            <li class="dropdown-header">Monthly</li>
            <li><a href="#">門市月報表<span class="badge">每月初</span></a></li>
            <li><a href="{{ route('conce_index') }}">康思特銷退貨<span class="badge">每月初</span></a></li>
            <li><a href="{{ route('spb_index') }}">每月進銷退<span class="badge">每月初</span></a></li>
            <li><a href="{{ route('promograde_index') }}">促銷模組成效<span class="badge">每月初</span></a></li>
          </ul> 
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="compare">資料檢視或比對 <span class="caret"></span></a>
          <ul class="dropdown-menu" aria-labelledby="compare">
            <!-- <li><a href="{{ route('compare_honeybaby') }}">寵兒名單比對</a></li>  -->           
            <!-- <li><a href="{{ route('compare_financial_strike_balance_index')}}">財務沖帳比對</a></li> -->
           <!--  <li><a href="{{ url('flap/ccs_order_index/salerecord') }}">客經業績總表</a></li>  -->
            <li><a href="{{ url('report/ctilayout') }}">偉特匯入資料下載</a></li>    
            <li><a href="{{ url('viga/que')}}">同步紀錄檢視</a></li>           
            <li><a href="{{ url('flap/ccs_order_index/promote_shipment') }}">促銷出貨撈取</a></li>    
            <li><a href="{{ url('flap/ccs_order_div_index') }}">分寄單查詢</a></li>                  
          </ul> 
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="flap">輔翼修改 <span class="caret"></span></a>
          <ul class="dropdown-menu" aria-labelledby="flap">
            <li><a href="{{ url('flap/pos_member/import_kind') }}">會員匯入</a></li>
            <li><a href="{{ route('pis_goods_fix_cprefix_goods_index') }}">贈品新增BUG處理</a></li> 
            <li><a href="{{ route('pis_goods_copy_to_cometrust_index') }}">複製景華商品為康萃特商品</a></li>              
            <li><a href="{{ route('ccs_orderindex_cancelverify_index') }}">出貨取消覆核</a></li>              
            <li><a href="{{ route('ccs_returngoodsi_cancelverify_index') }}">退貨取消覆核</a></li>
          </ul> 
        </li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="intro">常用資訊<span class="caret"></span></a>  
          <ul class="dropdown-menu" aria-labelledby="intro">
            <li><a href="{{ route('intro_report') }}">報表一覽</a></li>
            <li><a href="{{ route('intro_b') }}">廠商一覽</a></li> 
            <li><a href="{{ url('scrum/todo') }}">SCRUM待辦清單</a></li>   
            <li><a href="{{ url('user') }}">使用者一覽</a></li>  
            <li><a href="{{ url('test/tran_zipcode') }}">郵遞區號轉換</a></li>                                 
          </ul> 
          
        </li>
        <li><a href="{{ url('flap/members') }}">口袋名單</a></li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li><a href="https://github.com/jocoonopa/lubri" target="_blank">{{ $github }}</a></li>
        <li><a href="http://tonyvonhsu.tw/phpbb/index.php" target="_blank">BY 景華資訊處</a></li>
        @if (Auth::check())
            <li><a href="{{ url('auth/logout') }}"><i class="glyphicon glyphicon-log-out"></i> 登出</a></li>
        @else
            <li><a href="{{ url('auth/login') }}"><i class="glyphicon glyphicon-log-in"></i> 登入</a></li>
        @endif
      </ul>
    </div>
  </div>
</div>