@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-success text-white">
                    Buyer Dispute
                </div>

                <div class="card-body">
                    <div id="err_message"><!--  --></div>
                    <form id="buyer_dispute">
                          <div class="form-group">
                            <label for="email">No Invoice:</label>
                            <div class="form-control">{{ $tr->no }}</div>
                          </div>
                          <div class="form-group">
                            <label for="fl">Upload KTP(Transfer Bank) / Profile (DANA/OVO/GOPAY)</label>
                            <input type="file" class="form-control" name="identity" id="fl">
                          </div>
                          
                          <div class="form-group">
                            <label for="f2">Upload Bukti Transfer</label>
                            <input type="file" class="form-control" name="proof" id="f2">
                          </div>
                          
                          <div class="form-group">
                            <label for="f3">Upload Mutasi</label>
                            <input type="file" class="form-control" name="mutation" id="f3">
                          </div>
                          <div class="form-group">
                            <label for="ct">Komentar</label>
                            <textarea name="comments" class="form-control"></textarea>
                          </div>
                          <button type="submit" class="btn btn-success">Kirim</button>
                        </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var dispute_role = 1;
    var trans_id = "{{ $tr->id }}";
    var url = "{{ url('save-dispute') }}";
    var page_success = "{{ url('page-dispute') }}";
    var err_message = "{{ Lang::get('custom.failed') }}";
</script>
<script src="{{ asset('assets/js/dispute.js') }}" type="text/javascript"></script>
@endsection
