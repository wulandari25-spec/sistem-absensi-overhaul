<?php

namespace App\Http\Requests;

use App\Enums\AttendanceMethod;
use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_id' => ['required', 'integer', 'exists:outsourcing_staffs,id'],
            'method' => ['required', Rule::enum(AttendanceMethod::class)],
            'status' => ['required', Rule::enum(AttendanceStatus::class)],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'face_descriptor' => ['required_if:method,face_recognition', 'array'],
            'face_descriptor.*' => ['numeric'],
            'proof_photo' => ['nullable', 'string'],
            'confidence_score' => ['nullable', 'numeric', 'between:0,1'],
            'qr_token' => ['required_if:method,qr_code', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'staff_id.required' => 'ID pegawai diperlukan.',
            'staff_id.exists' => 'Pegawai tidak ditemukan dalam sistem.',
            'latitude.required' => 'Koordinat GPS diperlukan.',
            'longitude.required' => 'Koordinat GPS diperlukan.',
            'face_descriptor.required_if' => 'Data deskriptor wajah diperlukan untuk metode Face Recognition.',
            'qr_token.required_if' => 'Token QR Code diperlukan untuk metode QR.',
        ];
    }
}
