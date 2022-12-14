<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crop extends Model
{
    use HasFactory;

    public function cropCategory()
    {
        return $this->belongsTo('App\Models\CropCategory','crop_category_id','id');
    }


}
