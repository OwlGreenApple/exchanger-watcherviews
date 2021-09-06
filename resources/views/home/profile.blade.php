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
        <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.ovo') }}</label>

        <div class="col-md-6">
            <input type="file" class="form-control" name="ovo" />
            <span class="error ovo"><!--  --></span>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.dana') }}</label>

        <div class="col-md-6">
            <input type="file" class="form-control" name="dana" />
            <span class="error dana"><!--  --></span>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.gopay') }}</label>

        <div class="col-md-6">
            <input type="file" class="form-control" name="gopay" />
            <span class="error gopay"><!--  --></span>
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