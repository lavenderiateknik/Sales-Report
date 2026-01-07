<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Model
{
    protected $fillable = [
        'user_id','month','year',
        'working_days','present_days','absent_days',
        'created_by'
    ];

    protected $casts = [
    'month' => 'integer',
    'year' => 'integer',
    'working_days' => 'integer',
    'present_days' => 'integer',
    'absent_days' => 'integer',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

