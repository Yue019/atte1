<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rest extends Model
{
    use HasFactory;

    protected $fillable = ['work_id','start_rest','end_rest','duration'];

    public function works()
    {
        return $this->belongsTo(Work::class);
    }

}
