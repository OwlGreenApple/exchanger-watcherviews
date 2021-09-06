<form id="profile">
     <div class="form-group row">
        <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.email_watcherviews') }}</label>

        <div class="col-md-6">
            <input type="text" class="form-control" name="wt_email" />
            <span class="error wt_email"><!--  --></span>
        </div>
    </div> 

    <div class="form-group row">
        <label class="col-md-4 col-form-label text-md-right">{{ $lang::get('custom.pass_watcherviews') }}</label>

        <div class="col-md-6">
            <input type="password" class="form-control" name="wt_pass" />
            <span class="error wt_pass"><!--  --></span>
        </div>
    </div>

    <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-4">
            <button type="submit" class="btn alert-warning">
                {{ $lang::get('custom.connect') }}
            </button>
        </div>
    </div>
</form>