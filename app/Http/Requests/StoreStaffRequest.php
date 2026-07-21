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

            // Descriptor wajah hasil ekstraksi face-api.js (128 angka)
            'face_descriptor' => 'nullable|json',
        ];
    }
}