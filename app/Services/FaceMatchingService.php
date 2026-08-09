<?php

namespace App\Services;

use App\Models\OutsourcingStaff;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FaceMatchingService
{
    private string $flaskUrl;

    public function __construct()
    {
        $this->flaskUrl = config('services.flask.url', 'http://127.0.0.1:5001');
    }

    /**
     * Mengirim foto ke Flask API untuk mengenali wajah staff.
     *
     * @param string $photoBase64 Base64 encoded image string
     * @return array
     */
    public function findBestMatch(string $photoBase64): array
    {
        try {
            // Bersihkan base64 prefix if exists (e.g., "data:image/jpeg;base64,")
            $imageContent = $this->decodeBase64($photoBase64);
            if (!$imageContent) {
                return [
                    'staff' => null,
                    'distance' => 1.0,
                    'matched' => false,
                    'message' => 'Format gambar tidak valid.',
                ];
            }

            // Kirim ke Flask via HTTP POST multipart
            $response = Http::attach(
                'foto', $imageContent, 'face.jpg'
            )->post($this->flaskUrl . '/recognize');

            if ($response->failed()) {
                Log::error("Flask face recognition failed: Status " . $response->status() . " | " . $response->body());
                return [
                    'staff' => null,
                    'distance' => 1.0,
                    'matched' => false,
                    'message' => 'Gagal menghubungi server face recognition.',
                ];
            }

            $result = $response->json();

            if (isset($result['status']) && $result['status'] === 'success') {
                $staffCode = $result['staff_code'];
                $confidence = $result['confidence'] ?? 0.0;
                $distance = 1.0 - $confidence;

                $staff = OutsourcingStaff::where('staff_code', $staffCode)->first();

                if ($staff) {
                    return [
                        'staff' => $staff,
                        'distance' => $distance,
                        'matched' => true,
                        'confidence' => $confidence,
                    ];
                }
            }

            return [
                'staff' => null,
                'distance' => 1.0,
                'matched' => false,
                'message' => $result['message'] ?? 'Wajah tidak dikenali.',
            ];

        } catch (\Exception $e) {
            Log::error("Error in FaceMatchingService@findBestMatch: " . $e->getMessage());
            return [
                'staff' => null,
                'distance' => 1.0,
                'matched' => false,
                'message' => 'Terjadi kesalahan sistem saat pencocokan wajah.',
            ];
        }
    }

    /**
     * Mendaftarkan wajah staff baru ke Flask API.
     *
     * @param string $staffCode
     * @param string $photoBase64OrPath Bisa berupa base64 string atau path file local storage
     * @return array
     */
    public function registerFace(string $staffCode, string $photoBase64OrPath): array
    {
        try {
            $imageContent = null;
            if (strpos($photoBase64OrPath, 'data:image') === 0 || base64_decode($photoBase64OrPath, true) !== false) {
                $imageContent = $this->decodeBase64($photoBase64OrPath);
            } else {
                // Asumsi path file lokal di storage
                if (\Illuminate\Support\Facades\Storage::disk('local')->exists($photoBase64OrPath)) {
                    $imageContent = \Illuminate\Support\Facades\Storage::disk('local')->get($photoBase64OrPath);
                }
            }

            if (!$imageContent) {
                return [
                    'success' => false,
                    'message' => 'Gambar tidak ditemukan atau tidak valid.',
                ];
            }

            $response = Http::attach(
                'foto', $imageContent, 'register.jpg'
            )->post($this->flaskUrl . '/register', [
                'staff_code' => $staffCode,
            ]);

            if ($response->failed()) {
                Log::error("Flask register face failed: " . $response->body());
                $errMsg = $response->json('message') ?? 'Gagal mendaftarkan wajah ke Flask server.';
                return [
                    'success' => false,
                    'message' => $errMsg,
                ];
            }

            return [
                'success' => true,
                'message' => $response->json('message') ?? 'Wajah berhasil didaftarkan.',
            ];

        } catch (\Exception $e) {
            Log::error("Error in FaceMatchingService@registerFace: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat registrasi wajah: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Menghapus wajah staff dari Flask API.
     *
     * @param string $staffCode
     * @return array
     */
    public function deleteFace(string $staffCode): array
    {
        try {
            $response = Http::delete($this->flaskUrl . "/register/{$staffCode}");

            if ($response->failed()) {
                Log::error("Flask delete face failed: " . $response->body());
                return [
                    'success' => false,
                    'message' => 'Gagal menghapus data wajah di Flask server.',
                ];
            }

            return [
                'success' => true,
                'message' => 'Data wajah berhasil dihapus.',
            ];

        } catch (\Exception $e) {
            Log::error("Error in FaceMatchingService@deleteFace: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menghapus wajah.',
            ];
        }
    }

    /**
     * Helper untuk decode base64 image string
     */
    private function decodeBase64(string $base64Str): ?string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Str, $typeMatches)) {
            $base64Str = substr($base64Str, strpos($base64Str, ',') + 1);
        }
        $decoded = base64_decode($base64Str);
        return $decoded !== false ? $decoded : null;
    }
}