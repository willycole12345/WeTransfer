<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadSession extends Model
{
    use HasFactory;
    protected $fillable = [
                'token',
                'expires_in',
                'expires_at',
                'email_to_notify',
                'download_password'
    ];

    public function files()
{
    return $this->hasMany(UploadFile::class);
}
}
