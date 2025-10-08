<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDatabase extends Model
{
    use HasFactory;

    protected $table = 'customer_database';

    protected $fillable = [
        'project_id','project_type','development_type','project_name','project_detail',
        'project_stage','project_region','project_street_name','local_value',
        'construction_start_date','construction_end_date','floor_area','site_area','storeys',
        'role_on_project','last_update','project_address','project_town','company_website',
        'project_province','post_code','company_name','company_street_name','company_roles',
        'company_town','company_state','company_phone','company_email','contact_first_name',
        'contact_surname','contact_position','contact_landline','contact_email','contact_remark',
        'company_region','mobile','role_status','construction_start_text','construction_end_text',
        'id_branch', "hash" 
    ];

    /**
     * Relasi ke Branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch');
    }
}
