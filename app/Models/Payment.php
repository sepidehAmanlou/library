<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
class Payment extends Model
{
    
   use HasFactory;

   protected $hidden = ['created_at', 'updated_at'];
   protected $appends = ['created_at_jalali', 'updated_at_jalali'];

    protected $fillable = [
        'order_id',
        'amount',
        'status',
        'gateway',
        'ref_number',
        'paid_at'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id');
    }

     public function getCreatedAtJalaliAttribute()
{
    return $this->created_at ? Jalalian::fromCarbon(Carbon::parse($this->created_at))->format('Y/m/d H:i'): null;
}

    public function getUpdatedAtJalaliAttribute()
{
    return $this->updated_at ? Jalalian::fromCarbon(Carbon::parse($this->updated_at))->format('Y/m/d H:i'): null;
}

//    public function getDeletedAtJalaliAttribute()
// {
//     return $this->deleted_at ? Jalalian::fromCarbon(Carbon::parse($this->deleted_at))->format('Y/m/d H:i'): null;
// }
}
