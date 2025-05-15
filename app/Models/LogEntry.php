<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class LogEntry extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'log_entries';

    protected $fillable = [
        'trace_id',
        'timestamp',
        'type',
        'log_data',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'log_data' => 'array',
    ];

    public $timestamps = true;
}


