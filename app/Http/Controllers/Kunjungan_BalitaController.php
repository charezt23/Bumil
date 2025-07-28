<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\kunjungan_balita;
use App\Models\balita;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use GuzzleHttp\Client;
use Carbon\Carbon;

class Kunjungan_BalitaController extends Controller
{
    /**
     * Get all kunjungan balita data.
     */
    public function index(): JsonResponse
    {
        try {
            $kunjungan = kunjungan_balita::with('balita.posyandu')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data kunjungan balita berhasil diambil',
                'data' => $kunjungan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kunjungan balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created kunjungan in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'balita_id' => 'required|integer|exists:balita,id',
                'tanggal_kunjungan' => 'required|date',
                'berat_badan' => 'required|numeric|min:0|max:999.99',
                'tinggi_badan' => 'required|numeric|min:0|max:999.99',
            ]);

            // 2. Ambil data balita terkait dari database
            $balita = Balita::findOrFail($validated['balita_id']);

            // 3. Inisialisasi Guzzle HTTP Client
            $client = new Client();
            // URL API Python Anda yang berjalan di Docker
            $pythonApiUrl = 'http://localhost:5000/calculate-zscore';

            $JK = $balita->jenis_kelamin === 'L' ? 'male' : 'female';

            $dataToApi = [
                'weight' => $validated['berat_badan'],
                'length_height' => $validated['tinggi_badan'],
                'birth_date' => Carbon::parse($balita->tanggal_lahir)->format('Y-m-d'),
                'measurement_date' => Carbon::parse($validated['tanggal_kunjungan'])->format('Y-m-d'),
                'gender' => $JK,
                'indicator' => 'BB/PB'
            ];

            // 5. Panggil API Python
            $response = $client->post($pythonApiUrl, [
                'json' => $dataToApi
            ]);

            $apiResult = json_decode($response->getBody()->getContents(), true);

            if (!isset($apiResult['z_score']) || !isset($apiResult['status'])) {
                throw new \Exception("API Python mengembalikan format data yang tidak valid.");
            }

            $calculatedStatusGizi = 'N';

            switch ($apiResult['status']) {
                case 'Gizi Buruk':
                    $calculatedStatusGizi = 'GB';
                    break;
                case 'Gizi Kurang':
                    $calculatedStatusGizi = 'GK';
                    break;
                case 'Gizi Lebih':
                    $calculatedStatusGizi = 'GL';
                    break;
                case 'Obesitas':
                    $calculatedStatusGizi = 'OB';
                    break;
                case 'Berisiko Gizi Lebih':
                    $calculatedStatusGizi = 'RGL';
                    break;
                case 'Gizi Baik':
                    $calculatedStatusGizi = 'N';
                    break;
                default:
                    $calculatedStatusGizi = 'OTHER';
                    break;
            }
            $lastKunjungan = kunjungan_balita::where('balita_id', $balita->id)
                ->orderBy('tanggal_kunjungan', 'desc')
                ->first();
            $rambugizi = 'O';

            if ($lastKunjungan) {
                $tanggalKunjungan = Carbon::parse($lastKunjungan->tanggal_kunjungan);
                $selisihBulan = $tanggalKunjungan->diffInMonths(Carbon::parse($validated['tanggal_kunjungan']));

                if ($selisihBulan == 1) {
                    $zscore_now = $apiResult['z_score'];
                    $zscore_last = $lastKunjungan->z_score;

                    $delta = $zscore_now - $zscore_last;

                    if ($delta > 0.2) {
                        $rambugizi = 'N1'; // Tumbuh kejar
                    } elseif ($delta >= -0.2 && $delta <= 0.2) {
                        $rambugizi = 'N2'; // Tumbuh normal (sejajar)
                    } elseif ($delta < -0.2 && $delta >= -0.5) {
                        $rambugizi = 'T1'; // Pertumbuhan lambat
                    } elseif (abs($delta) < 0.01) {
                        $rambugizi = 'T2'; // Datar / stagnan
                    } elseif ($delta < -0.5) {
                        $rambugizi = 'T3'; // Penurunan tajam
                    } else {
                        $rambugizi = 'T1'; // fallback
                    }
                } else {
                    $rambugizi = 'O';
                }
            }


            $kunjungan = kunjungan_balita::create(array_merge($validated, [
                'Status_gizi' => $calculatedStatusGizi,
                'rambu_gizi' => $rambugizi,
                'z_score' => $apiResult['z_score']
            ]));


            return response()->json([
                'success' => true,
                'message' => 'Kunjungan balita berhasil diperbarui dengan perhitungan status gizi otomatis.',
                'data' => $kunjungan,
                'calculated_status_from_api' => $apiResult
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan balita tidak ditemukan'
            ], 404);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorMessage = $e->getMessage();
            if ($e->hasResponse()) {
                $errorDetails = json_decode($e->getResponse()->getBody()->getContents(), true);
                $errorMessage = $errorDetails['error'] ?? $errorMessage;
            }
            return response()->json([
                'success' => false,
                'message' => 'Error dari layanan perhitungan gizi (API Python): ' . $errorMessage
            ], $e->getResponse()->getStatusCode());
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke layanan perhitungan gizi atau ada masalah di sana: ' . $e->getMessage()
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan tidak terduga',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified kunjungan.
     */
    public function show($id): JsonResponse
    {
        try {
            $kunjungan = kunjungan_balita::with('balita.posyandu')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail kunjungan balita berhasil diambil',
                'data' => $kunjungan
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan balita tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail kunjungan balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified kunjungan in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $kunjungan = kunjungan_balita::findOrFail($id);

            $validated = $request->validate([
                'balita_id' => 'required|integer|exists:balita,id',
                'tanggal_kunjungan' => 'required|date',
                'berat_badan' => 'required|numeric|min:0|max:999.99',
                'tinggi_badan' => 'required|numeric|min:0|max:999.99',
                'rambu_gizi' => ['required', Rule::in(['O', 'N1', 'N2', 'T1', 'T2', 'T3'])]
            ]);

            // 2. Ambil data balita terkait dari database
            $balita = Balita::findOrFail($validated['balita_id']);

            // 3. Inisialisasi Guzzle HTTP Client
            $client = new Client();
            // URL API Python Anda yang berjalan di Docker
            $pythonApiUrl = 'http://localhost:5000/calculate-zscore';

            $JK = $balita->jenis_kelamin === 'L' ? 'male' : 'female';

            $dataToApi = [
                'weight' => $validated['berat_badan'],
                'length_height' =>  $validated['tinggi_badan'],
                'birth_date' => Carbon::parse($balita->tanggal_lahir)->format('Y-m-d'),
                'measurement_date' => Carbon::parse($validated['tanggal_kunjungan'])->format('Y-m-d'),
                'gender' => $JK,
                'indicator' => 'BB/PB'
            ];

            // 5. Panggil API Python
            $response = $client->post($pythonApiUrl, [
                'json' => $dataToApi
            ]);

            $apiResult = json_decode($response->getBody()->getContents(), true);

            if (!isset($apiResult['z_score']) || !isset($apiResult['status'])) {
                throw new \Exception("API Python mengembalikan format data yang tidak valid.");
            }

            $calculatedStatusGizi = 'N';

            switch ($apiResult['status']) {
                case 'Gizi Buruk':
                    $calculatedStatusGizi = 'GB';
                    break;
                case 'Gizi Kurang':
                    $calculatedStatusGizi = 'GK';
                    break;
                case 'Gizi Lebih':
                    $calculatedStatusGizi = 'GL';
                    break;
                case 'Obesitas':
                    $calculatedStatusGizi = 'OB';
                    break;
                case 'Berisiko Gizi Lebih':
                    $calculatedStatusGizi = 'RGL';
                    break;
                case 'Gizi Baik':
                case 'Normal':
                    $calculatedStatusGizi = 'N';
                    break;
                default:
                    $calculatedStatusGizi = 'OTHER';
                    break;
            }


            $kunjungan->update(array_merge($validated, [
                'Status_gizi' => $calculatedStatusGizi,
                'z_score' => $apiResult['z_score']
            ]));
            $kunjungan->load('balita.posyandu');

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan balita berhasil diperbarui dengan perhitungan status gizi otomatis.',
                'data' => $kunjungan,
                'calculated_status_from_api' => $apiResult
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan balita tidak ditemukan'
            ], 404);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorMessage = $e->getMessage();
            if ($e->hasResponse()) {
                $errorDetails = json_decode($e->getResponse()->getBody()->getContents(), true);
                $errorMessage = $errorDetails['error'] ?? $errorMessage;
            }
            return response()->json([
                'success' => false,
                'message' => 'Error dari layanan perhitungan gizi (API Python): ' . $errorMessage
            ], $e->getResponse()->getStatusCode());
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke layanan perhitungan gizi atau ada masalah di sana: ' . $e->getMessage()
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan tidak terduga',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified kunjungan from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $kunjungan = kunjungan_balita::findOrFail($id);
            $kunjungan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan balita berhasil dihapus'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan balita tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kunjungan balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kunjungan by balita ID.
     */
    public function getByBalita($balitaId): JsonResponse
    {
        try {
            // Hanya ambil data kunjungan tanpa relasi untuk menghindari duplikasi data
            $kunjungan = kunjungan_balita::where('balita_id', $balitaId)
                ->orderBy('tanggal_kunjungan', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data kunjungan berhasil diambil berdasarkan balita',
                'data' => $kunjungan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kunjungan berdasarkan balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getallZcore($balitaId): JsonResponse
    {
        try {
            $balita = Balita::findOrFail($balitaId);
            $tanggalLahir = Carbon::parse($balita->tanggal_lahir);

            $kunjungan = kunjungan_balita::where('balita_id', $balitaId)
                ->orderBy('tanggal_kunjungan', 'asc')
                ->get();

            if ($kunjungan->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada kunjungan ditemukan untuk balita ini'
                ], 404);
            }

            $data = $kunjungan->map(function ($item) use ($tanggalLahir) {
                $tanggalKunjungan = Carbon::parse($item->tanggal_kunjungan);
                $usiaDiff = $tanggalKunjungan->diff($tanggalLahir);
                $usiaBulan = ($usiaDiff->y * 12) + $usiaDiff->m;

                if ($tanggalKunjungan->day < $tanggalLahir->day) {
                    $usiaBulan -= 1;
                }

                return [
                    'usia' => $usiaBulan,
                    'z_score' => $item->z_score,
                    'status_gizi' => $item->Status_gizi
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Z-Score berhasil diambil',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil Z-Score',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
