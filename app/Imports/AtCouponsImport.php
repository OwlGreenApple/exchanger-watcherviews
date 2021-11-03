<?php

namespace App\Imports;

use App\Models\Atcoupons;
use Maatwebsite\Excel\Concerns\FromCollection;

class AtCouponsImport implements FromCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection()
    {   
        return Atcoupons::all();
    }
}
