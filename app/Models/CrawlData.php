<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawlData extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'data',
    ];

}
