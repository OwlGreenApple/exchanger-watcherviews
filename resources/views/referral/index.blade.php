@extends('layouts.app')

@section('content')

<div class="container mb-5 main-cont">
  <div class="row">
    <div class="col-md-12">

      <h2><b>Referral Programs</b></h2>  
    
      <hr>
    </div>
  </div>

  <div class="row justify-content-center">
      <div class="col-md-8">
          <div class="card">
              <div class="card-body text-center">
                <div id="message"><!--  --></div>
                <div class="input-group mb-3">
                  <input id="ref_link" class="form-control" readonly="readonly" value="{{ $referral_link }}" />
                  <div class="input-group-append">
                    <button class="btn btn-primary btn-sm btn-copy">Copy Link</button>
                  </div>
                </div>
                <button id="generate_link" class="btn btn-primary btn-sm">Generate Link</button>
              </div>
          </div>
      </div>
  </div>

  <div class="col-lg-8 justify-content-center mt-3 mx-auto bg-white py-2">
    <h5 class="text-center"><b>Referral List</b></h5>

    <div class="table-responsive">
      <table id="referral_list" class="table table-striped table-bordered">
        <thead align="center">
          <th>No</th>
          <th>User Name</th>
          <th>Joined</th>
         <!--  <th>Status</th> -->
        </thead>
        <tbody>
          @if($user->count() > 0)
            @php $no = 1; @endphp
            @foreach($user as $row)
              <tr>
                <td>{{ $no }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->created_at }}</td>
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
    generate_ref_link();
    let table = $("#referral_list").DataTable({
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ],
      "aaSorting" : [],
    });
  });

  function generate_ref_link()
  { 
    $("#generate_link").click(function(){
      $.ajax({
        type : 'GET',
        url : "{{ url('referral-link') }}",
        dataType: 'json',
        beforeSend: function() {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
        
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          
          if(result.msg == 0)
          {
            $("#ref_link").val(result.link);
            $("#message").html("<div class='alert alert-primary'>Your referral link has been generated, please copy link on below.</div>")
          }
          else
          {
            $("#message").html("<div class='alert alert-danger'>Currently our server is too busy, please try again later.</div>")
          }
        },
        error : function(xhr)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
      });
    });
  }

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
