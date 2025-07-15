<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class Book extends Model
{
    use HasFactory;
    protected $hidden = ['created_at', 'updated_at'];
    protected $appends = ['created_at_jalali', 'updated_at_jalali'];

    protected $fillable = [
        'title',
        'author',
        'description',
        'category_id',
    ];

    public function bookCategory()
    {
        return $this->belongsTo(BookCategory::class,'category_id');
    }
// user books
    public function users()
    {
        return $this->belongsToMany(User::class,'book_users')->using(BookUser::class)->withPivot('status','added_at')->withTimestamps();
    }
    
    public function carts()
    {
        return $this->hasMany(Cart::class,'book_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class,'book_id');
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
