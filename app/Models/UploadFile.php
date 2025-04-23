<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadFile extends Model
{
    use HasFactory;

    protected $fillable =[
            'upload_session_id',
            'original_name',
            'stored_name',
            'mime_type',
            'size',
            'download_count',
    ];

    public function session()
{
    return $this->belongsTo(UploadSession::class);
}
}
