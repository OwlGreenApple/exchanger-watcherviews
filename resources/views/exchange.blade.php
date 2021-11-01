<div class="container mb-2 mt-2">
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
                <input type="radio" class="form-check-input" id="radio1" name="o_exchange" value="1" checked>{{ Lang::get('transaction.mil_1') }}
              </label>
            </div>
            <div class="form-check">
              <label for="radio2">
                <input type="radio" class="form-check-input" id="radio2" name="o_exchange" value="2">{{ Lang::get('transaction.mil_2') }}
              </label>
            </div>
            <a id="o_exc" class="btn btn-primary mb-3 exc">{{ Lang::get('transaction.exc') }}</a>
            <div class="omn_coupon d-flex">
                <input readonly="readonly" id="omn_coupon" class="form-control" />
                <button type="button" class="btn btn-success btn-sm">Copy</button>
            </div>
            <div class="omn_coupon mt-2"><a target="_blank" rel="noopener noreferrer" href="https://omnilinkz.com/dashboard/checkout/2">Gunakan Kupon</a></div>
        </div>
    </div>

    <!-- ACTIVRESPON -->
    <div class="col-lg-6 card-pricing shadow text-center px-3 mb-4">
        <div style="height:75px;" class="bg-transparent card-header pt-4 border-0">
            <a target="_blank" rel="noopener noreferrer" href="https://activrespon.com"><img style="background-color : #30307c" src="{{ asset('assets/img/activrespon.png') }}" /></a>
            <br/>
        </div>
        <!--  -->
        <div class="card-body pt-0">
            <div class="form-check">
              <label for="radio1">
                <input type="radio" class="form-check-input" id="radio1" name="a_exchange" value="1" checked>{{ Lang::get('transaction.mil_1') }}
              </label>
            </div>
            <div class="form-check">
              <label for="radio2">
                <input type="radio" class="form-check-input" id="radio2" name="a_exchange" value="2">{{ Lang::get('transaction.mil_2') }}
              </label>
            </div>
            <a id="a_exc" class="btn btn-primary mb-3 exc">{{ Lang::get('transaction.exc') }}</a>
            <div class="act_coupon d-flex">
                <input readonly="readonly" id="act_coupon" class="form-control" />
                <button type="button" class="btn btn-success btn-sm">Copy</button>
            </div>
            <div class="act_coupon mt-2"><a target="_blank" rel="noopener noreferrer" href="https://activrespon.com/dashboard/checkout/2">Gunakan Kupon</a></div>
        </div>
    </div>
            
       <!--  -->
    </div>
</div>