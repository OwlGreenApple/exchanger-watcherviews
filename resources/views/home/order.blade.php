<div class="container mb-5 main-cont">
  <div class="row">
    <div class="col-md-12">
      
      <h5>
        <button type="button" class="btn btn-primary btn-confirm" data-toggle="modal" data-target="#transfer-information" style="font-size: 13px; padding: 5px 8px;">
          Informasi Pembayaran / Transfer
        </button>        
      </h5>
      
    </div>

    <div class="col-md-12">
      <form class="table-responsive">
        <table class="display responsive nowrap w-100" id="data_order">
          <thead>
            <th>{{$lang::get('order.no')}}</th>
            <th>{{$lang::get('order.package')}}</th>
            <th>{{$lang::get('order.price')}}</th>
            <th>{{$lang::get('order.total')}}</th>
            <th>{{$lang::get('order.date')}}</th>
            <th>{{$lang::get('order.date_complete')}}</th>
            <th>{{$lang::get('order.desc')}}</th>
            <th>{{$lang::get('order.proof')}}</th>
            <th>{{$lang::get('order.status')}}</th>
          </thead>
          <tbody></tbody>
        </table>
       <!--  -->
      </form>
    </div>

  </div>
</div>

<!-- Modal Transfer Information -->
<div class="modal fade" id="transfer-information" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          {{$lang::get('order.transfer')}}
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">

          <p class="card-text">
            {{$lang::get('order.step')}}
          </p> 
          <h2>{{ env('NO_REK') }}</h2>
          <p class="card-text">
            {{ env('BANK_NAME') }} <b>Sugiarto Lasjim</b>
          </p>
          <p class="card-text">
            {!! $lang::get('order.step_next') !!}<br> {{$lang::get('order.step_next_1')}} <b>{{ env('EMAIL_ADMIN') }}</b> <br>
            {{$lang::get('order.admin_24')}}
          </p>

      </div>
      <div class="modal-footer" id="foot">
        <button class="btn" data-dismiss="modal">
          Ok
        </button>
      </div>
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

<!-- Modal Confirm payment -->
<div class="modal fade" id="confirm-payment" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          {{$lang::get('order.upload')}}
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form enctype="multipart/form-data" method="POST" action="{{ url('order-confirm-payment') }}">
        <div class="modal-body">
          @csrf
          <input type="hidden" name="id_confirm" id="id_confirm">

          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>{{$lang::get('order.no')}}</b>
            </label>

            <span class="col-md-6 col-12" id="mod-no_order">
            </span>
          </div>

          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>{{$lang::get('order.package')}}</b>
            </label>

            <span class="col-md-6 col-12" id="mod-package">
            </span>
          </div>

          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>{{$lang::get('order.total')}}</b>
            </label>

            <span class="col-md-6 col-12" id="mod-total"></span>
          </div>

          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>{{$lang::get('order.date')}}</b> 
            </label>

            <span class="col-md-6 col-12" id="mod-date"></span>
          </div>

          <div class="form-group">
            <label class="col-md-3 col-12 float-left">
              <b>{{$lang::get('order.upload')}}</b> 
            </label>

            <div class="col-md-6 col-12 float-left">
              <input type="file" name="buktibayar">
            </div>
          </div>
          <div class="clearfix mb-3"></div>
          <div class="form-group">
            <label class="col-md-3 col-12">
              <b>{{$lang::get('order.notes')}}</b> 
            </label>
            <div class="col-md-12 col-12">
              <textarea class="form-control" name="keterangan"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer" id="foot">
          <input type="submit" class="btn btn-primary" id="btn-confirm-ok" value="{{$lang::get('order.confirm')}}">
          <button type="submit" class="btn" data-dismiss="modal">
            {{$lang::get('order.cancel')}}
          </button>
        </div>
      </form>
    </div>
      
  </div>
</div>