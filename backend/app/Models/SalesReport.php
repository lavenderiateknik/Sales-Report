<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    use HasFactory;
    protected $casts = [
    'nominal_purchase_order' => 'float',
    ];


    // Kolom yang bisa diisi massal
    protected $fillable = [
        'date',
        'check_in',
        'coordinate_check_in',
        'type_customer_id',
        'customer_name',
        'type_project_id',
        'project_name',
        'pic_name',
        'pic_phone',
        'pic_position',
        'type_report_id',
        'report_notes',
        'equipment_needs',
        'items_purchase_order',
        'nominal_purchase_order',
        'check_out',
        'coordinate_check_out',
    ];

    public function typeCustomer()
    {
        return $this->belongsTo(TypeCustomer::class, 'type_customer_id');
    }

    public function typeProject()
    {
        return $this->belongsTo(TypeProject::class, 'type_project_id');
    }

    public function typeReport()
    {
        return $this->belongsTo(TypeReport::class, 'type_report_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
