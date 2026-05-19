<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

#[Fillable(['user_id', 'token', 'email', 'status', 'expires_at'])]

#[Table('verifies')]
#[Table(timestamps: true)]
class Verify extends Model
{
const UPDATED_AT = null;

    protected $casts = [
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];

 public function user()
    {
        return $this->belongsTo(User::class);
    }
}
