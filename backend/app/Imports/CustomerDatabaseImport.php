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

        // Ambil header baris pertama
        $headerRow = $rows->shift()->toArray();
        $headerRow = array_map(fn($h) => trim((string)$h), $headerRow);

        // Normalisasi header
        $normalize = fn($v) => preg_replace('/[^a-z0-9]+/u', '', strtolower($v));
        $headerMap = [];
        foreach ($headerRow as $index => $header) {
            $headerMap[$normalize($header)] = $index;
        }

        Log::info('🧭 Header Map:', $headerMap);

        $countInserted = 0;
        $countUpdated = 0;
        $countSkipped = 0;

        foreach ($rows as $i => $row) {
            $row = $row->toArray();
            if (count(array_filter($row, fn($v) => $v !== null && $v !== '')) === 0) continue;

            try {
                $get = function ($key) use ($headerMap, $row, $normalize) {
                    $keyNorm = $normalize($key);
                    if (!array_key_exists($keyNorm, $headerMap)) return null;
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
                            )->format('Y-m-d');
                        }
                        return Carbon::parse($value)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                };

                // Ambil semua kolom
                $data = [
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
                ];

                // Hash untuk deteksi identik (semua isi baris)
                $values = array_map(fn($v) => preg_replace('/\s+/', ' ', strtolower(trim((string)$v))), $row);
                $hash = md5(implode('|', $values));
                $data['hash'] = $hash;

                // 🔑 Cari berdasarkan kombinasi unik baru
                $existing = CustomerDatabase::where('project_id', $data['project_id'])
                    ->where('project_name', $data['project_name'])
                    ->where('company_name', $data['company_name'])
                    ->where('role_on_project', $data['role_on_project'])
                    ->first();

                if (!$existing) {
                    // INSERT baru
                    CustomerDatabase::create($data);
                    $countInserted++;
                    Log::info("✅ Insert baru di row #" . ($i + 2));
                } else {
                    if ($existing->hash === $hash) {
                        // SKIP karena identik
                        $countSkipped++;
                        Log::info("⚠️ Skip (identik) di row #" . ($i + 2));
                    } else {
                        // Kolom yang perlu diperbarui bila berbeda
                        $fieldsToCheck = [
                            'project_stage',
                            'role_on_project',
                            'company_website',
                            'company_name',
                            'company_street_name',
                            'company_roles',
                            'company_town',
                            'company_phone',
                            'company_email',
                            'contact_first_name',
                            'contact_surname',
                            'contact_position',
                            'contact_landline',
                            'contact_email',
                        ];

                        $updateData = [];
                        foreach ($fieldsToCheck as $field) {
                            if ($existing->$field !== $data[$field]) {
                                $updateData[$field] = $data[$field];
                            }
                        }

                        if (!empty($updateData)) {
                            $updateData['hash'] = $hash;
                            $existing->update($updateData);
                            $countUpdated++;
                            Log::info("📝 Update di row #" . ($i + 2), $updateData);
                        } else {
                            $countSkipped++;
                            Log::info("⚠️ Skip (tidak ada perubahan signifikan) di row #" . ($i + 2));
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error("❌ Error pada row #" . ($i + 2) . ": " . $e->getMessage(), [
                    'row_data' => $row,
                ]);
            }
        }

        Log::info("📊 Import selesai — Inserted: {$countInserted}, Updated: {$countUpdated}, Skipped: {$countSkipped}");
    }
}
