@extends('layouts.app')

@section('content')

<div class="bg-custom">
  <div class="container">
    <div class="col-md-12 py-4">
      <h2 class="mb-0"><b class="mr-2">Program Referral</b><i class="fas fa-user-plus"></i></h2>  
    </div>
  </div>
</div>

<hr>

<div class="container mb-5 main-cont">
  <div class="row justify-content-center">
      <div class="col-md-8">
          <div class="card">
              <div class="card-body text-center">
                <div id="message"><!--  --></div>
                <div class="input-group mb-3">
                  <input id="ref_link" class="form-control" readonly="readonly" value="{{ $referral_link }}" />
                  <div id="append-button" class="input-group-append">
                    <button class="btn btn-primary btn-sm btn-copy">Copy Link</button>
                  </div>
                </div>
              <!--   @if(Auth::user()->referral_link == null)
                  <button id="generate_link" class="btn btn-primary btn-sm">Generate Link</button>
                @endif -->
              </div>
          </div>
      </div>
  </div>

  <div class="col-lg-12 justify-content-center mt-3 mx-auto py-2">
    <h5 class="text-center"><b>Daftar Referral</b></h5>

    <div class="table-responsive">
      <table id="referral_list" class="table table-striped table-bordered">
        <thead align="center">
          <th>No</th>
          <th>Tgl Bergabung</th>
          <th>User Name</th>
          <th>Koin Yang Didapat</th>
        </thead>
        <tbody>
          @if($user->count() > 0)
            @php $no = 1; @endphp
            @foreach($user as $row)
              <tr>
                <td>{{ $no }}</td>
                <td>{{ $row->created_at }}</td>
                <td>{{ explode("-",$row->source)[3] }}</td>
                <td>{{ str_replace(",",".",number_format($row->debit)) }}</td>
              </tr>
              @php $no++; @endphp
            @endforeach
          @endif
        </tbody>
      </table>
  </div>

  </div>
<!--  -->
</div>

<!-- Modal Copy Link -->
<div class="modal fade" id="copy-link" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Copy Link
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        You have copied the link!
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" data-dismiss="modal">
          OK
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">

  $(document).ready(function()
  {
    copyLink();
    let table = $("#referral_list").DataTable({
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ],
      "aaSorting" : [],
    });
  });

  function copyLink(){
    $( "body" ).on("click",".btn-copy",function(e) 
    {
      e.preventDefault();
      e.stopPropagation();

      var link = $("#ref_link").val();

      var tempInput = document.createElement("input");
      tempInput.style = "position: absolute; left: -1000px; top: -1000px";
      tempInput.value = link;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand("copy");
      document.body.removeChild(tempInput);
      $('#copy-link').modal('show');
    });
  }
  
</script>

@endsection
