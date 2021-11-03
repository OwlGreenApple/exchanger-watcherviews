<div class="container mb-2 mt-2">
    <h6>Jumlah Coin : <b id="cur_coin">{{ $pc->pricing_format($user->coin) }}</b></h6>
    <div class="pricing card-deck row">
        
    <!-- OMNILINKS -->
    <div class="col-lg-6 card-pricing shadow text-center px-3 mb-4">
        <div style="height:75px;" class="bg-transparent card-header pt-4 border-0">
            <a target="_blank" rel="noopener noreferrer" href="https://omnilinkz.com"><img src="{{ asset('assets/img/omnilinkz-logo.png') }}" /></a>
            <br/>
        </div>
        <!--  -->
        <div class="card-body pt-0">
            <div class="form-check">
              <label for="radio1">
                <input type="radio" class="form-check-input" name="o_exchange" value="1" checked>{{ Lang::get('transaction.mil_1') }}
              </label>
            </div>
            <div class="form-check">
              <label for="radio2">
                <input type="radio" class="form-check-input" name="o_exchange" value="2">{{ Lang::get('transaction.mil_2') }}
              </label>
            </div>
            <a id="o_exc" class="btn btn-primary mb-3 open">{{ Lang::get('transaction.exc') }}</a>
            <span class="omn_coin"><!--  --></span>
            <div class="omn_coupon">
                <div class="d-flex">
                    <input readonly="readonly" id="omn_coupon" class="form-control" />
                    <button data-code="omn_coupon" type="button" class="btn btn-success btn-sm btn-copy">Copy</button>
                </div>
                <span class="display_omn_coupon"><!-- show copy message --></span>
                <div class="mt-3"><a class="btn btn-warning btn-sm text-dark" target="_blank" rel="noopener noreferrer" href="https://omnilinkz.com/dashboard/checkout/2">Gunakan Kupon</a></div>
            </div>
        </div>
    </div>

    <!-- ACTIVRESPON -->
    <div class="col-lg-6 card-pricing shadow text-center px-3 mb-4">
        <div style="height:75px;" class="bg-transparent card-header pt-4 border-0">
            <a target="_blank" rel="noopener noreferrer" href="https://activrespon.com"><img style="background-color : #30307c; padding:6px 10px" src="{{ asset('assets/img/activrespon.png') }}" /></a>
            <br/>
        </div>
        <!--  -->
        <div class="card-body pt-0">
            <div class="form-check">
              <label for="radio3">
                <input type="radio" class="form-check-input" name="a_exchange" value="1" checked>{{ Lang::get('transaction.mil_1') }}
              </label>
            </div>
            <div class="form-check">
              <label for="radio4">
                <input type="radio" class="form-check-input" name="a_exchange" value="2">{{ Lang::get('transaction.mil_2') }}
              </label>
            </div>
            <a id="a_exc" class="btn btn-primary mb-3 open">{{ Lang::get('transaction.exc') }}</a>
            <span class="act_coin"><!--  --></span>
            <div class="act_coupon">
                <div class="d-flex">
                    <input readonly="readonly" id="act_coupon" class="form-control" />
                    <button data-code="act_coupon" type="button" class="btn btn-success btn-sm btn-copy">Copy</button>
                </div>
                <span class="display_act_coupon"><!-- show copy message --></span>
                <div class="mt-3"><a class="btn btn-warning btn-sm text-dark" target="_blank" rel="noopener noreferrer" href="https://activrespon.com/dashboard/checkout/2">Gunakan Kupon</a></div>
            </div>
        </div>
    </div>

    <!-- ACTIVTEMPLATE -->
    <div class="col-lg-6 card-pricing shadow text-center px-3 mb-4">
        <div style="height:75px;" class="bg-transparent card-header pt-4 border-0">
            <a target="_blank" rel="noopener noreferrer" href="https://activtemplate.com/"><img src="{{ asset('assets/img/activtemplate-logo.png') }}" /></a>
            <br/>
        </div>
        <!--  -->
        <div class="card-body pt-0">
            <div class="form-check">
              <label for="radio5">
                <input type="radio" class="form-check-input" name="atm_exchange" value="1" checked>{{ Lang::get('transaction.mil_1') }}
              </label>
            </div>
            <div class="form-check">
              <label for="radio6">
                <input type="radio" class="form-check-input" name="atm_exchange" value="2">{{ Lang::get('transaction.mil_2') }}
              </label>
            </div>
            <a id="atm_exc" class="btn btn-primary mb-3 open">{{ Lang::get('transaction.exc') }}</a>
            <span class="atm_coin"><!--  --></span>
            <div class="atm_coupon">
                <div class="d-flex">
                    <input readonly="readonly" id="atm_coupon" class="form-control" />
                    <button data-code="atm_coupon" type="button" class="btn btn-success btn-sm btn-copy">Copy</button>
                </div>
                <span class="display_atm_coupon"><!-- show copy message --></span>
                <div class="mt-3"><a class="btn btn-warning btn-sm text-dark" target="_blank" rel="noopener noreferrer" href="https://activtemplate.com/ACTMP/paket-super">Gunakan Kupon</a></div>
            </div>
        </div>
    </div>
            
    <!--  -->
    </div>
</div>

<!-- Modal Confirm -->
<div class="modal fade" id="modal_exc" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Apakah anda mau menukar coin?
        </h5>
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary exc" data-dismiss="modal">
          Ya
        </button>
        <button class="btn" data-dismiss="modal">
          Tidak
        </button>
      </div>
    </div>
      
  </div>
</div>