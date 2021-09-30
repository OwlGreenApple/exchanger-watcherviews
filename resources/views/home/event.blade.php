@extends('layouts.app')

@section('content')

<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white mr-2">
      <i class="mdi mdi-cart-outline"></i>
    </span> Halaman Event </h3>
</div>

<div class="row justify-content-center">
    <div class="card py-3">
      <div class="col-md-9">
        <div class="form-group">
          <label><b>Judul</b></label>
          <div class="form-control">{{ $row->event_name }}</div>
        </div> 

        <div class="form-group">
          <label><b>Isi</b></label>
          <p class="border px-2 py-2">{!! $row->message !!}</p>
        </div>
      <!-- end col -->
      </div>
    <!-- end card-->
    </div>
</div>
@endsection