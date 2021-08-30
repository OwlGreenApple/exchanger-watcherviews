@extends('layouts.app')

@section('content')
<link href="{{ asset('assets/css/order.css') }}" rel="stylesheet" />

<div class="container mb-5 main-cont" style="">
  <div class="row">
    <div class="col-md-12">
      <h2><b>History Transaction</b></h2>  
    </div>

    <div class="col-md-12">
      <form>
        <table class="table table-bordered w-100" style="font-size : 0.65rem" id="data_transaction">
          <thead>
            <th class="menu-nomobile">
             No
            </th>
            <th class="menu-nomobile">
             {{$lang::get('transaction.no')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('transaction.type')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('transaction.amount')}}
            </th>
            <th class="menu-nomobile">
              {{$lang::get('transaction.created')}}
            </th>
            <th class="header" action="status">
              {{$lang::get('transaction.status')}}
            </th>
          </thead>
          <tbody>
            @if($data->count() > 0)
              @php $no = 1; @endphp
              @foreach($data as $row)
                 <tr>
                   <td>{{ $no++ }}</td>
                   <td>{{ $row->no }}</td>
                   <td class="text-center">
                      @if($row->type == 1)
                        {{ $lang::get('transaction.buy') }}
                      @elseif($row->type == 2)
                        {{ $lang::get('transaction.sell') }}
                      @else
                        {{ $lang::get('transaction.withdraw') }}
                      @endif
                    </td>
                   <td class="text-right">{{ str_replace(",",".",number_format($row->amount)) }}</td>
                   <td>{{ $row->created_at }}</td>
                   <td>
                     @if($row->status == 0 && $row->type == 1)
                        {{ $lang::get('transaction.buy.status') }}
                     @elseif($row->status == 1 && $row->type == 1)
                        {{ $lang::get('transaction.buy.done') }}
                     @elseif($row->status == 0 && $row->type == 2)
                        {{ $lang::get('transaction.sell.status') }}
                     @elseif($row->status == 1 && $row->type == 2)
                        {{ $lang::get('transaction.sell.progress') }}
                     @elseif($row->status == 2 && $row->type == 2)
                        {{ $lang::get('transaction.sell.done') }}
                     @else
                        -
                     @endif
                   </td>
                 </tr>
              @endforeach
            @endif
          </tbody>
        </table>
       <!--  -->
      </form>
    </div>

  </div>
</div>

<!-- Modal Confirm Delete -->
<div class="modal fade" id="confirm-repromote" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <div id="pesan"><!--  --></div>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div id="re-title"><!--  --></div>
        <div>{{$lang::get('order.package')}} : <span id="order-package"></span></div>
        <div>{{$lang::get('order.price')}} : <span id="order-price"></span></div>
        <div>{{$lang::get('order.total')}} : <span id="order-total"></span></div>
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" id="btn-promote" data-dismiss="modal">
          {{$lang::get('order.yes')}}
        </button>
        <button class="btn" data-dismiss="modal">
          {{$lang::get('order.cancel')}}
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">

  $(document).ready(function() {
    load_page();
  });

  function load_page()
  {
    $("#data_transaction").DataTable();
  }

  function formatNumber(num) {
    num = parseInt(num);
    if(isNaN(num) == true)
    {
       return '';
    }
    else
    {
       return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    }
  }

</script>
@endsection