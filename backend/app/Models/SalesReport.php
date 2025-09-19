<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    use HasFactory;

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

    /**
     * Relasi ke TypeCustomer
     */
    public function typeCustomer()
    {
        return $this->belongsTo(TypeCustomer::class);
    }

    /**
     * Relasi ke TypeProject
     */
    public function typeProject()
    {
        return $this->belongsTo(TypeProject::class);
    }

    /**
     * Relasi ke TypeReport
     */
    public function typeReport()
    {
        return $this->belongsTo(TypeReport::class);
    }

    /**
     * Akses lat/lng sebagai array untuk check-in
     */
    // public function getCheckInCoordinateAttribute()
    // {
    //     if ($this->coordinate_check_in) {
    //         [$lat, $lng] = explode(',', $this->coordinate_check_in);
    //         return [
    //             'lat' => (float)$lat,
    //             'lng' => (float)$lng,
    //         ];
    //     }
    //     return null;
    // }

    /**
     * Akses lat/lng sebagai array untuk check-out
     */
    // public function getCheckOutCoordinateAttribute()
    // {
    //     if ($this->coordinate_check_out) {
    //         [$lat, $lng] = explode(',', $this->coordinate_check_out);
    //         return [
    //             'lat' => (float)$lat,
    //             'lng' => (float)$lng,
    //         ];
    //     }
    //     return null;
    // }
}
