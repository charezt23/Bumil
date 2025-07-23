<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kematian;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class KematianController extends Controller
{
    /**
     * Get all kematian data.
     */
    public function index(): JsonResponse
    {
        try {
            $kematian = Kematian::with('balita')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data kematian berhasil diambil',
                'data' => $kematian
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kematian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created kematian in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'balita_id' => 'required|integer|exists:balita,id',
                'tanggal_kematian' => 'required|date',
                'penyebab_kematian' => 'required|string'
            ]);

            $kematian = Kematian::create($validated);
            $kematian->load('balita');

            return response()->json([
                'success' => true,
                'message' => 'Data kematian berhasil ditambahkan',
                'data' => $kematian
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data kematian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified kematian.
     */
    public function show($id): JsonResponse
    {
        try {
            $kematian = Kematian::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data kematian berhasil diambil',
                'data' => $kematian
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data kematian tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kematian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified kematian in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $kematian = Kematian::findOrFail($id);

            $validated = $request->validate([
                'balita_id' => 'sometimes|required|integer|exists:balita,id',
                'tanggal_kematian' => 'sometimes|required|date',
                'penyebab_kematian' => 'sometimes|required|string'
            ]);

            $kematian->update($validated);
            $kematian->load('balita');

            return response()->json([
                'success' => true,
                'message' => 'Data kematian berhasil diperbarui',
                'data' => $kematian
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data kematian tidak ditemukan'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data kematian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified kematian from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $kematian = Kematian::findOrFail($id);
            $kematian->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data kematian berhasil dihapus'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data kematian tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data kematian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Kematian by Balita.
     */
    public function getByBalita($balitaId): JsonResponse
    {
        try {
            $kematian = Kematian::where('balita_id', $balitaId)->with('balita')->first();

            if (!$kematian) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data kematian tidak ditemukan untuk balita ini'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data kematian berhasil diambil',
                'data' => $kematian
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kematian',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kematian data by user (bidan) who owns the posyandu.
     */
    public function getKematianByUser($user_id): JsonResponse
    {
        try {
            // Debug logging
            \Log::info("Getting kematian data for user: " . $user_id);

            $kematian = Kematian::whereHas('balita.posyandu', function($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })->with(['balita', 'balita.posyandu'])->get();

            \Log::info("Found " . $kematian->count() . " kematian records for user " . $user_id);
            foreach($kematian as $k) {
                \Log::info("Kematian ID: " . $k->id . ", Balita ID: " . $k->balita_id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data kematian berdasarkan user berhasil diambil',
                'data' => $kematian
            ], 200);
        } catch (\Exception $e) {
            \Log::error("Error getting kematian by user: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kematian berdasarkan user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
