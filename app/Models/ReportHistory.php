<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'format',
        'filename',
        'path',
        'row_count',
        'total_revenue',
        'generated_by',
    ];

    protected $casts = [
        'row_count' => 'integer',
        'total_revenue' => 'decimal:2',
    ];
}
