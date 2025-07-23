<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imunisasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ImunisasiController extends Controller
{
    /**
     * Store a newly created Imunisasi in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'balita_id' => 'required|integer|exists:balita,id',
                'jenis_imunisasi' => [
                    'required','string','max:255',
                    Rule::unique('imunisasi')->where(function ($query) use ($request) {
                        return $query->where('balita_id', $request->balita_id)
                            ->where('jenis_imunisasi', $request->jenis_imunisasi);
                    }),
                ],
                'tanggal_imunisasi' => 'required|date'
            ], [
                'jenis_imunisasi.unique' => 'Jenis Imunisasi : ' . $request->jenis_imunisasi . ' sudah diisi.',
            ]);


            $imunisasi = Imunisasi::create($validated);
            $imunisasi->load('balita');

            return response()->json([
                'success' => true,
                'message' => 'Data imunisasi berhasil ditambahkan',
                'data' => $imunisasi
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
                'message' => 'Gagal menambahkan data imunisasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified Imunisasi.
     */
    public function show($id): JsonResponse
    {
        try {
            $imunisasi = Imunisasi::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data imunisasi berhasil diambil',
                'data' => $imunisasi
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data imunisasi tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data imunisasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified Imunisasi in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $imunisasi = Imunisasi::findOrFail($id);

            $validated = $request->validate([
                'balita_id' => 'sometimes|integer|exists:balita,id',
                'jenis_imunisasi' => [
                    'sometimes','string','max:255',
                    Rule::unique('imunisasi')->where(function ($query) use ($request, $imunisasi) {
                        return $query->where('balita_id', $request->balita_id?? $imunisasi->balita_id)
                            ->where('jenis_imunisasi', $request->jenis_imunisasi);
                    })->ignore($imunisasi->id),
                ],
                'tanggal_imunisasi' => 'sometimes|date'
            ], [
                'jenis_imunisasi.unique' => 'Jenis Imunisasi : ' . $request->jenis_imunisasi . ' sudah ada silahkan hapus '. $request->jenis_imunisasi . ' terlebih dahulu untuk merubah ini.',
            ]);

            $imunisasi->update($validated);
            $imunisasi->load('balita');

            return response()->json([
                'success' => true,
                'message' => 'Data imunisasi berhasil diperbarui',
                'data' => $imunisasi
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data imunisasi tidak ditemukan'
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
                'message' => 'Gagal memperbarui data imunisasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified Imunisasi from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $imunisasi = Imunisasi::findOrFail($id);
            $imunisasi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data imunisasi berhasil dihapus'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data imunisasi tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data imunisasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all Imunisasi data by Balita.
     */
    public function GetImunisasibyBalita($balitaId): JsonResponse
    {
        try {
            $imunisasi = Imunisasi::where('balita_id', $balitaId)->get();

            if ($imunisasi->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data imunisasi tidak ditemukan untuk balita ini'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data imunisasi berhasil diambil',
                'data' => $imunisasi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data imunisasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
