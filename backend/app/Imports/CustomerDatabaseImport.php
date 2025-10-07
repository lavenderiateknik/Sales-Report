<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\CustomerDatabase;
use Carbon\Carbon;

class CustomerDatabaseImport implements ToCollection
{
    protected $branchId;

    public function __construct($branchId)
    {
        $this->branchId = $branchId;
    }

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            Log::error("❌ File kosong / tidak memiliki data");
            return;
        }

        $headerRow = $rows->shift()->toArray();
        $headerRow = array_map(fn($h) => trim((string)$h), $headerRow);

        $normalize = function ($value) {
            $normalized = strtolower($value);
            $normalized = preg_replace('/[^a-z0-9]+/u', '', $normalized);
            return $normalized;
        };

        $headerMap = [];
        foreach ($headerRow as $index => $header) {
            $headerMap[$normalize($header)] = $index;
        }

        Log::info('🧭 Header Map:', $headerMap);

        foreach ($rows as $i => $row) {
            $row = $row->toArray();

            if (count(array_filter($row, fn($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            try {
                // ✅ Perbaikan utama di sini
                $get = function ($key) use ($headerMap, $row, $normalize) {
                    $keyNorm = $normalize($key);
                    if (!array_key_exists($keyNorm, $headerMap)) {
                        return null;
                    }
                    $index = $headerMap[$keyNorm];
                    return $row[$index] ?? null;
                };

                $parseDate = function ($value) {
                    if (!$value) return null;
                    $value = trim((string)$value);
                    try {
                        if (is_numeric($value)) {
                            return Carbon::instance(
                                \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                            );
                        }
                        return Carbon::parse($value);
                    } catch (\Exception $e) {
                        return null;
                    }
                };

                CustomerDatabase::create([
                    'id_branch' => $this->branchId,
                    'project_id' => $get('project id'),
                    'project_type' => $get('project type'),
                    'development_type' => $get('development type'),
                    'project_name' => $get('project name'),
                    'project_detail' => $get('project detail'),
                    'project_stage' => $get('project stage'),
                    'project_region' => $get('project region'),
                    'project_street_name' => $get('project street name'),
                    'local_value' => $get('local value'),
                    'construction_start_date' => $parseDate($get('construction start date (date format)')),
                    'construction_end_date' => $parseDate($get('construction end date (date format)')),
                    'floor_area' => $get('floor area (square meters)'),
                    'site_area' => $get('site area (square meters)'),
                    'storeys' => $get('storeys'),
                    'role_on_project' => $get('role on project'),
                    'last_update' => $parseDate($get('last update')),
                    'project_address' => $get('project address'),
                    'project_town' => $get('project town / suburb'),
                    'company_website' => $get('company website'),
                    'project_province' => $get('project province / state'),
                    'post_code' => $get('post code'),
                    'company_name' => $get('company name'),
                    'company_street_name' => $get('company street name'),
                    'company_roles' => $get('company roles'),
                    'company_town' => $get('company town / suburb'),
                    'company_state' => $get('company state / province'),
                    'company_phone' => $get('company phone'),
                    'company_email' => $get('company email'),
                    'contact_first_name' => $get('contact first name'),
                    'contact_surname' => $get('contact surname'),
                    'contact_position' => $get('contact position'),
                    'contact_landline' => $get('contact landline'),
                    'contact_email' => $get('contact email'),
                    'contact_remark' => $get('contact remark'),
                    'company_region' => $get('company region'),
                    'mobile' => $get('mobile'),
                    'role_status' => $get('role status'),
                    'construction_start_text' => $get('construction start date (original format)'),
                    'construction_end_text' => $get('construction end date (original format)'),
                ]);
            } catch (\Throwable $e) {
                Log::error("❌ Error pada row #" . ($i + 2) . ": " . $e->getMessage(), [
                    'row_data' => $row,
                ]);
            }
        }
    }
}
