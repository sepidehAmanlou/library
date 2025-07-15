<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;


class User extends Authenticatable
{
    use HasFactory;

   protected $hidden = ['created_at', 'updated_at'];
   protected $appends = ['created_at_jalali', 'updated_at_jalali'];

    protected $fillable = [
        'user_name',
        'user_category_id',
        'name',
        'email',
        'password',
        'gender',
        'status',
    ];

   protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
];
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    
   public function userCategory()
   {
      return $this->belongsTo(UserCategory::class,'user_category_id');
   }

   public function userTokens()
   {
       return $this->hasMany(UserToken::class,'user_id');
   }
//    user book
   public function books()
   {
       return $this->belongsToMany(Book::class,'book_users')->using(BookUser::class)->withPivot('status','added_at')->withTimestamps();
   }
   
   public function carts()
   {
      return $this->hasMany(Cart::class,'user_id');
   }

   public function orders()
   {
     return $this->hasMany(Order::class,'user_id');
   }
   public function bookUsers()
    {
        return $this->hasMany(BookUser::class, 'user_id');
    }

    public function isAdmin()
   {
    return $this->user_category_id === 1; 
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
