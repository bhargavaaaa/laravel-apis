<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncryptedPdfs extends Model
{
    use HasFactory;

    protected $table = "encrypted_pdfs";
    public $timestamps = false;
    protected $guarded = [];
}
