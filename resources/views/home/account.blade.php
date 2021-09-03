@extends('layouts.app')

@section('content')
<div class="container">
    <h2><b>Setelan Akun</b></h2>  
    <div class="row justify-content-center mt-3">
        <!-- LEFT TAB -->
        <div class="col-md-4">
             <div class="card">
                <div class="card-body bg-white text-black-50 border-bottom"><b>Setelan</b></div>
                <div class="card-header bg-white border-bottom">
                    <a>Billing</a>
                </div>
                <div class="card-header bg-white border-bottom">
                    <a>Billing</a>
                </div>
            </div>
        </div>
        <!-- RIGHT TAB -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body bg-white text-black-50 border-bottom"><b>{{ Lang::get('transaction.sell.title') }}</b></div>

                <div id="msg"><!-- message --></div>

                <div class="card-body">
                    <form id="profile">
                    
                        <div class="float-right">{{ Lang::get('transaction.total.coin') }}&nbsp;:&nbsp;<b></b></div>
                        <div class="clearfix mb-2"><!--  --></div>

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ Lang::get('transaction.sell') }}</label>

                            <div class="col-md-6">
                                 <input type="number" class="form-control" min="100000" name="tr_coin" />
                                <span class="error tr_coin"><!--  --></span>
                            </div>
                        </div>

                         <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ Lang::get('transaction.fee') }}&nbsp;(x%)</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="tr_product" />
                                <span class="error tr_product"><!--  --></span>
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">{{ Lang::get('transaction.total') }}&nbsp;{{ Lang::get('custom.currency') }}</label>

                            <div class="col-md-6">
                                <div id="coin" class="form-control"></div>
                               <span class="error wallet"><!--  --></span>
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">&nbsp;</label>

                            <div class="col-md-6">
                                {{ Lang::get('transaction.min') }}
                            </div>
                        </div> 

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-warning">
                                    {{ Lang::get('transaction.sell.act') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- end col -->
        </div> 
    </div>
    <!-- end justify -->
</div>

<script type="text/javascript">
/**/
</script>
@endsection
