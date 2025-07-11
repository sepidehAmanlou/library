<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
class Order extends Model
{
   use HasFactory;

   protected $hidden = ['created_at', 'updated_at'];
   protected $appends = ['created_at_jalali', 'updated_at_jalali'];

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
        'payment_ref',
    ];

    public function user()
   {
      return $this->belongsTo(User::class,'user_id');
   }

   public function orderItems()
  {
    return $this->hasMany(OrderItem::class, 'order_id');
  }

  public function payments()
  {
     return $this->hasMany(Payment::class,'order_id');
  }

  public function updateTotalPrice()
    {
        $total = $this->orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $this->total_price = $total;
        $this->save();
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
