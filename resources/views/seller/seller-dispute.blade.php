@extends('layouts.app')

@section('content')

<div class="page-header">
    <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-store-24-hour"></i>
    </span>Seller Dispute</h3>
</div>

<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div id="err_message"><!--  --></div>
                <form id="buyer_dispute">
                      <div class="form-group">
                        <label for="email">No Invoice:</label>
                        <div class="form-control">{{ $tr->no }}</div>
                      </div>
                      <div class="form-group">
                        <label for="fl">Upload Bukti</label>
                        <input type="file" class="form-control" name="proof" id="fl">
                        <span class="error proof"><!-- error --></span>
                      </div>
                      <div class="form-group">
                        <label for="ct">Komentar</label>
                        <textarea name="comments" class="form-control"></textarea>
                        <span class="error comments"><!-- error --></span>
                      </div>
                      <button type="submit" class="btn bg-gradient-success">Kirim</button>
                    </form>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    var dispute_role = 2;
    var trans_id = "{{ $tr->id }}";
    var url = "{{ url('save-dispute') }}";
    var page_success = "{{ url('page-dispute') }}";
    var err_message = "{{ Lang::get('custom.failed') }}";

</script>
<script src="{{ asset('assets/js/dispute.js') }}" type="text/javascript"></script>
@endsection
