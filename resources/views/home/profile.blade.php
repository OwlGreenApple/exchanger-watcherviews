<div id="err_profile"><!--  --></div>

<form id="profile">
    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nama') }}</label>

        <div class="col-md-6">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" autocomplete="name" autofocus>
            <span class="error name"><!--  --></span>
        </div>
    </div>

    <div class="form-group row">
        <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('No HP Anda') }}</label>

        <div class="col-md-6">
            <div id="phone_number" class="form-control alert-success">{{ $user->phone_number }}</div>
            <!--  -->
            <div class="col-md-12 row mt-2">
              <input type="text" id="phone" name="phone" class="form-control"/>
                <span class="error phone"></span>

                <input id="hidden_country_code" type="hidden" class="form-control" name="code_country" />
               <input name="data_country" type="hidden" /> 
            </div>
        </div>
    </div>

    <hr/>

    <div align="center" class="mb-3"><b>Metode Pembayaran</b></div>

     <div class="form-group row">
        <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.bank_name') }}</label>

        <div class="col-md-6">
            <input type="text" value="{{ $user->bank_name }}" class="form-control" name="bank_name" />
            <span class="error bank_name"><!--  --></span>
        </div>
    </div> 

    <div class="form-group row">
        <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.bank_no') }}</label>

        <div class="col-md-6">
            <input type="text" value="{{ $user->bank_no }}" class="form-control" name="bank_no" />
            <span class="error bank_no"><!--  --></span>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.epayment') }}</label>

        <div class="col-md-6">
           <select class="form-control" name="epayment">
               <option value="ovo">{{ $lang::get('custom.ovo') }}</option>
               <option value="dana">{{ $lang::get('custom.dana') }}</option>
               <option value="gopay">{{ $lang::get('custom.gopay') }}</option>
           </select>
        </div>
    </div>

    <!-- upload form -->
    <div class="form-group row">
        <label class="col-md-4 col-form-label text-md-right">&nbsp;</label>

        <div class="col-md-6">
            <input type="file" class="form-control upload_payment" name="payment" />
            <span class="error payment"><!--  --></span>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-md-4 col-form-label text-md-right"><!--  --></label>

        <div id="display_epayment" class="col-md-6">
            <span id="display_ovo">
                @if($user->ovo !== null)
                    <div class="mb-2"><button data-value="ovo" type="button" class="btn btn-danger epay">Hapus OVO</button></div>
                @endif
            </span>
            <span id="display_dana">
                @if($user->dana !== null)
                    <div class="mb-2"><button data-value="dana" type="button" class="btn btn-danger epay">Hapus DANA</button></div>
                @endif
            </span>
            <span id="display_gopay">
                @if($user->gopay !== null)
                    <div class="mb-2"><button data-value="gopay" type="button" class="btn btn-danger epay">Hapus GOPAY</button></div>
                @endif
            </span>
        </div>
    </div>

    <hr/>

    <div align="center" class="mb-3"><b>{{ $lang::get('auth.notes') }}</b></div>

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

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn btn-success">
                {{ __('Ubah') }}
            </button>
        </div>
    </div>
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
            <button type="button" id="crop" class="btn btn-primary">Crop</button>
            <button type="button" class="btn btn-secondary crop_cancel" data-dismiss="modal">Cancel</button>
        </div>
    </div>
</div>
</div>  