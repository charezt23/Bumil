<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\kunjungan_balita;
use App\Models\balita;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

 class Kunjungan_BalitaController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'balita_id' => 'required|integer|exists:balita,id',
                'tanggal_kunjungan' => 'required|date',
                'berat_badan' => 'required|numeric|min:0|max:999.99',
                'tinggi_badan' => 'required|numeric|min:0|max:999.99',
                'Status_gizi' => ['required', Rule::in(['N', 'K', 'T'])],
                'rambu_gizi' => ['required', Rule::in(['O', 'N1', 'N2', 'T1', 'T2', 'T3'])]
            ]);

            $kunjungan = kunjungan_balita::create($validated);
            $kunjungan->load('balita.posyandu');

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan balita berhasil dibuat',
                'data' => $kunjungan
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kunjungan balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
                'Status_gizi' => ['required', Rule::in(['N', 'K', 'T'])],
                'rambu_gizi' => ['required', Rule::in(['O', 'N1', 'N2', 'T1', 'T2', 'T3'])]
            ]);

            $kunjungan->update($validated);
            $kunjungan->load('balita.posyandu');

            return response()->json([
                'success' => true,
                'message' => 'Kunjungan balita berhasil diperbarui',
                'data' => $kunjungan
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kunjungan balita tidak ditemukan'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kunjungan balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
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
}
