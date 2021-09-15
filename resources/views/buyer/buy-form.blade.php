 <!-- search -->
<div class="card-body">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <form class="card card-sm">
                <div class="card-body row no-gutters align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-search h4 text-body"></i>
                    </div>
                    <!--end of col-->
                        <div class="col">
                            <input id="search" class="form-control form-control form-control-borderless" type="search" placeholder="Cari Coin">
                        </div>
                       
                       <!-- sort -->
                        <div class="col-auto">
                            <select class="form-control" name="sort">
                                <option>Urutkan</option>
                                <option value="coin">Coin</option>
                                <option value="rate">Kurs</option>
                                <option value="price">Harga</option>
                            </select>
                        </div>

                        <!-- range -->
                        <div class="col-auto">
                            <select class="form-control" name="range">
                                <option value="1">Tertinggi</option>
                                <option value="2">Terendah</option>
                            </select>
                        </div>

                        <div class="col-auto">
                            <button class="btn btn-success" id="btn-src" type="button">Cari</button>
                        </div>
                    <!--end of col-->
                </div>
            </form>
        </div>
        <!--end of col-->
    </div>
</div>

<div id="seller_list" class="card-body table-responsive">
    <!-- buyer table -->
</div>