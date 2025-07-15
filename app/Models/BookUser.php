<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BookUser extends Pivot
{
     use HasFactory;
    protected $table = 'book_users';
    protected $hidden = ['created_at', 'updated_at'];
    protected $appends = ['created_at_jalali', 'updated_at_jalali'];

    protected $fillable = [
        'book_id',
        'user_id',
        'status',
        'added_at',
    ];
    public $timestamps = true;
    public function book()
    {
        return $this->belongsTo(Book::class,'book_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
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
