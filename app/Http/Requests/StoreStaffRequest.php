<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staffId = $this->route('staff') ? $this->route('staff')->id : null;

        return [
            'staff_code' => 'required|string|max:50|unique:outsourcing_staffs,staff_code,' . $staffId,
            'name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'department' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'photo_profile' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'password' => 'nullable|string|min:8',
            'email' => 'nullable|email|max:255',

            // Descriptor wajah hasil ekstraksi face-api.js (128 angka)
            'face_descriptor' => 'nullable|json',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = $this->input('contract_start_date');
            $end = $this->input('contract_end_date');

            if ($start && $end) {
                $startDate = \Carbon\Carbon::parse($start);
                $endDate = \Carbon\Carbon::parse($end);

                $diffInDays = $startDate->diffInDays($endDate);

                if ($diffInDays < 19) { // 19 full days difference means at least 20 calendar days
                    $validator->errors()->add('contract_end_date', 'Masa kontrak payung (outsourcing) minimal adalah 20 hari.');
                }

                if ($diffInDays > 731) { // 2 years is max 731 days
                    $validator->errors()->add('contract_end_date', 'Masa kontrak payung (outsourcing) maksimal adalah 2 tahun.');
                }
            }
        });
    }
}