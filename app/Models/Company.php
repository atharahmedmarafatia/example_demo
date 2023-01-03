<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'companies';

    protected $guarded = ["id"];

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user', 'company_id','user_id');
    }

    public function country()
    {
        return $this->hasOne(Country::class,'id','country_id');
    }
}
