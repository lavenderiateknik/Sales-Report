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

    protected $fillable = [
        'date',
        'check_in',
        'coordinate_check_in',
        'type_customer_id',
        'customer_name',
        'type_project',
        'project_name',
        'pic_name',
        'pic_phone',
        'pic_position',
        'type_report',
        'report_notes',
        'equipment_needs',
        'items_purchase_order',
        'nominal_purchase_order',
        'check_out',
        'coordinate_check_out',
        'picture',
        'type_report_id'
    ];

    public function typeCustomer()
    {
        return $this->belongsTo(TypeCustomer::class, 'type_customer_id');
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
