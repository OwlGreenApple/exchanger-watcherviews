@if(Auth::user()->status == 3)
    <div class="alert alert-danger">{{ Lang::get('auth.suspend-time') }}<b>{{ $date_suspend }}</b></div>
@endif

<div id="err_profile"><!--  --></div>

<form id="profile">
    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nama') }}</label>

        <div class="col-md-6">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" autocomplete="name" autofocus>
            <span class="error name"><!--  --></span>
        </div>
    </div>

    <div class="form-group row mb-5">
        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('No HP Anda') }}</label>

        <div class="col-md-6">
            <div id="phone_number" class="form-control alert-secondary">{{ $user->phone_number }}</div>
            <!--  -->
            <div class="col-md-12 row mt-2">
              <input type="text" id="phone" name="phone" class="form-control"/>
                <span class="error phone"></span>

                <input id="hidden_country_code" type="hidden" class="form-control" name="code_country" />
               <input name="data_country" type="hidden" /> 
            </div>
        </div>
    </div>

     <!-- PASSWORD -->

    <div align="center" class="mb-3 alert alert-secondary"><b>{{ Lang::get('auth.notes') }}</b></div>

    <div class="form-group row">
        <label class="col-md-4 col-form-label text-md-right"> Password Lama</label>

        <div class="col-md-6">
            <input type="password" class="form-control" name="oldpass">
            <span class="error oldpass"><!--  --></span>
        </div>
    </div>

    <div class="form-group row">
        <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>

        <div class="col-md-6">
            <input type="password" class="form-control" name="newpass">
            <span class="error newpass"><!--  --></span>
        </div>
    </div>

    <div class="form-group row">
        <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Konfirmasi Password') }}</label>

        <div class="col-md-6">
            <input type="password" class="form-control" name="confpass">
        </div>
    </div>

    <div class="form-group row mb-3">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-success">
                {{ __('Ubah') }}
            </button>
        </div>
    </div>

    <!-- PAYMENT METHOD -->

    @if(Auth::user()->is_admin == 0)
    <div align="center" class="mb-3 alert alert-secondary"><b>Metode Pembayaran</b></div>

    <span id="crop_save"><!-- message success if crop saved --></span>

     <div class="form-group row">
        <label class="col-md-4 col-form-label text-md-right">&nbsp;</label>

        <div class="col-md-6">
           <button type="button" id="add-payment" class="btn btn-success"><i class="fas fa-plus"></i>&nbsp;Tambah Akun Pembayaran</button>
        </div>
    </div> 

    <!-- DROPDOWN METHOD -->
    <span id="dropdown-payment">
         <div class="form-group row">
            <label class="col-md-4 col-form-label text-md-right"><!--  --></label>

            <div class="col-md-6">
               <select class="form-control" name="mpayment">
                   <option>{{ Lang::get('custom.choose_pay') }}</option>
                   <option method="@if($user->bank_1 == null) bank_1 @else bank_2 @endif" value="bank">{{ Lang::get('transaction.bank') }}</option>
                   <option value="epay">{{ Lang::get('transaction.epay') }}</option>
               </select>
            </div>
        </div>
    </span>

    <!-- BANK -->
    <div id="bank-payment">
        <div class="form-group row">
            <label class="col-md-4 col-form-label text-md-right">{{ Lang::get('custom.bank_name') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" name="bank_name" />
                <span class="error bank_name"><!--  --></span>
            </div>
        </div> 

        <div class="form-group row">
            <label class="col-md-4 col-form-label text-md-right">{{ Lang::get('custom.bank_no') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" name="bank_no" />
                <span class="error bank_no"><!--  --></span>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-md-4 col-form-label text-md-right">{{ Lang::get('custom.bank_customer') }}</label>

            <div class="col-md-6">
                <input type="text" class="form-control" name="bank_customer" />
                <span class="error bank_customer"><!--  --></span><br/>

                <!-- BUTTON SAVE -->
                <button type="button" method="@if($user->bank_1 == null) bank_1 @else bank_2 @endif" id="save-bank" class="btn btn-success btn-sm mt-1">{{ Lang::get('custom.save') }}</button>

                <button id="bank-del" type="button" class="mt-1 btn btn-danger btn-sm delpay">{{ Lang::get('custom.del') }}</button>
            </div>
        </div>
     <!--  -->
    </div> 

    
    <!-- EPAYMENT -->
    <div id="e-payment">
        <!-- upload form -->
        <div class="form-group row">
            <label class="col-md-4 col-form-label text-md-right">&nbsp;</label>

            <div class="col-md-6">
                <input type="text" class="form-control mb-2" placeholder="{{ Lang::get('custom.epay') }}" name="epayname" />
                <span class="error epayname"><!--  --></span>
                <input type="file" class="form-control upload_payment" name="payment" />
            </div>
        </div>
        <!-- end e-payment -->
    </div>

    <!-- LIST OF PAYMENTS -->
    <div class="form-group row">
        <label class="col-md-4 col-form-label text-md-right"><!--  --></label>

        <div id="display_epayment" class="col-md-6">
            <span id="bank_1_method">
                @if($user->bank_1 !== null)
                    <div class="mb-2"><button data-value="bank_1" data-name="{{ $bank_1[0] }}" data-no="{{ $bank_1[1] }}" data-owner="{{ $bank_1[2] }}" type="button" class="btn btn-info b_payment text-capitalize w-100"><span class="text-uppercase">{{ $bank_1[0] }}</span></button></div>
                @endif
            </span>
            <span id="bank_2_method">
                @if($user->bank_2 !== null)
                    <div class="mb-2"><button data-value="bank_2" data-name="{{ $bank_2[0] }}" data-no="{{ $bank_2[1] }}" data-owner="{{ $bank_2[2] }}" type="button" class="btn btn-info text-capitalize b_payment w-100"><span class="text-uppercase">{{ $bank_2[0] }}</span></button></div>
                @endif
            </span>
            <span id="display_ovo">
                @if($user->epayment_1 !== null)
                    <div class="mb-2"><button data-value="epayment_1" type="button" class="btn btn-info delpay text-capitalize w-100">{{ Lang::get('custom.del') }} <span class="text-uppercase">{{ $epayment_1[0] }}</span></button></div>
                @endif
            </span>
            <span id="display_dana">
                @if($user->epayment_2 !== null)
                    <div class="mb-2"><button data-value="epayment_2" type="button" class="btn btn-info delpay text-capitalize w-100">{{ Lang::get('custom.del') }} <span class="text-uppercase">{{ $epayment_2[0] }}</span></button></div>
                @endif
            </span>
            <span id="display_gopay">
                @if($user->epayment_3 !== null)
                    <div class="mb-2"><button data-value="epayment_3" type="button" class="btn btn-info delpay text-capitalize w-100">{{ Lang::get('custom.del') }} <span class="text-uppercase">{{ $epayment_3[0] }}</span></button></div>
                @endif
            </span>
        </div>
    </div>
    @endif
</form>

<!-- Modal Confirm -->
<div class="modal fade" id="confirm_payment_delete" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Apakah anda yakin akan menghapus metode pembayaran ini?
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" id="btn_payment_delete" data-dismiss="modal">
          Ya
        </button>
        <button class="btn" data-dismiss="modal">
          Batal
        </button>
      </div>
    </div>
      
  </div>
</div>

<!-- MODAL FOR CROPPING QR-CODE -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Crop QR-Code Sebelum Upload</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="img-container">
                <div class="row">
                    <div class="col-md-7">
                        <img src="" id="sample_image" />
                    </div>
                    <div class="col-md-5">
                        <div class="preview"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="crop" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-secondary crop_cancel" data-dismiss="modal">Cancel</button>
        </div>
    </div>
</div>
</div>  