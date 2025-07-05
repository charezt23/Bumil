<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\balita;
use App\Models\Posyandu;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class BalitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $balita = balita::with('posyandu')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data balita berhasil diambil',
                'data' => $balita
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data balita',
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
                'nama' => 'required|string|max:255',
                'nik' => 'required|string|unique:balita,nik|max:16',
                'nama_ibu' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'alamat' => 'required|string',
                'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
                'posyandu_id' => 'required|exists:posyandu,id',
                'Buku_KIA' => ['required', Rule::in(['ada', 'tidak_ada'])]
            ]);

            $balita = balita::create($validated);
            $balita->load('posyandu');

            return response()->json([
                'success' => true,
                'message' => 'Data balita berhasil ditambahkan',
                'data' => $balita
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
                'message' => 'Gagal menambahkan data balita',
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
            $balita = balita::with('posyandu')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Data balita berhasil diambil',
                'data' => $balita
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data balita tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data balita',
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
            $balita = balita::findOrFail($id);
            
            $validated = $request->validate([
                'nama' => 'sometimes|required|string|max:255',
                'nik' => 'sometimes|required|string|max:16|unique:balita,nik,' . $id,
                'nama_ibu' => 'sometimes|required|string|max:255',
                'tanggal_lahir' => 'sometimes|required|date',
                'alamat' => 'sometimes|required|string',
                'jenis_kelamin' => ['sometimes', 'required', Rule::in(['L', 'P'])],
                'posyandu_id' => 'sometimes|required|exists:posyandu,id',
                'Buku_KIA' => ['sometimes', 'required', Rule::in(['ada', 'tidak_ada'])]
            ]);

            $balita->update($validated);
            $balita->load('posyandu');

            return response()->json([
                'success' => true,
                'message' => 'Data balita berhasil diperbarui',
                'data' => $balita
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data balita tidak ditemukan'
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
                'message' => 'Gagal memperbarui data balita',
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
            $balita = balita::findOrFail($id);
            $balita->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data balita berhasil dihapus'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data balita tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get balita by posyandu ID.
     */
    public function getByPosyandu($posyandu_id): JsonResponse
    {
        try {
            $balita = balita::where('posyandu_id', $posyandu_id)
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data balita berhasil diambil berdasarkan posyandu',
                'data' => $balita
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data balita berdasarkan posyandu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search balita by name or NIK.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q');
            
            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter pencarian (q) diperlukan'
                ], 400);
            }

            $balita = balita::with('posyandu')
                ->where('nama', 'LIKE', '%' . $query . '%')
                ->orWhere('nik', 'LIKE', '%' . $query . '%')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Hasil pencarian balita',
                'data' => $balita
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
