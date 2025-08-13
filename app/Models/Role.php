<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['role_code', 'name'];

    // ユーザーのリレーション
    public function users()
    {
        return $this->hasMany(User::class, 'role_code', 'role_code');
    }
}
