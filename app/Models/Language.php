<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class Language extends Model
{
    use HasFactory;
    protected $casts = [
    'primary' => 'boolean',
    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $appends = ['created_at_jalali', 'updated_at_jalali'];

    protected $fillable = [
        'title',
        'code',
        'code_flag',
        'status',
        'primary'
    ];

    public function bookCategories()
    {
        return $this->hasMany(BookCategory::class,'language_id');
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
