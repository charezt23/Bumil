<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class PosyanduController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $posyandu = Posyandu::with('user')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data posyandu berhasil diambil',
                'data' => $posyandu
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data posyandu',
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
                'user_id' => 'required|integer|exists:users,id',
                'nama_posyandu' => 'required|string|max:255',
                'nama_desa' => 'required|string|max:255'
            ]);

            $posyandu = Posyandu::create($validated);
            $posyandu->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Posyandu berhasil dibuat',
                'data' => $posyandu
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
                'message' => 'Gagal membuat posyandu',
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
            $posyandu = Posyandu::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail posyandu berhasil diambil',
                'data' => $posyandu
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Posyandu tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail posyandu',
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
        $posyandu = Posyandu::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'nama_posyandu' => 'required|string|max:255',
            'nama_desa' => 'required|string|max:255'
        ]);

        if ($posyandu->user_id !== (int) $validated['user_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Anda Tidak Memiliki Izin untuk mengedit ini.'
            ], 403);
        }
        $posyandu->update($validated);


        return response()->json([
            'success' => true,
            'message' => 'Posyandu berhasil diperbarui',
            'data' => $posyandu
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Posyandu tidak ditemukan'
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
            'message' => 'Gagal memperbarui posyandu',
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
            $posyandu = Posyandu::findOrFail($id);
            $posyandu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Posyandu berhasil dihapus'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Posyandu tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus posyandu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get posyandu by user ID.
     */
    public function getByUser($userId): JsonResponse
    {
        try {
            $posyandu = Posyandu::where('user_id', $userId)
                ->withCount('balita')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data posyandu berhasil diambil berdasarkan user',
                'data' => $posyandu
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data posyandu berdasarkan user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search posyandu by name.
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

            $posyandu = Posyandu::with('user')
                ->where('nama_posyandu', 'LIKE', '%' . $query . '%')
                ->orWhere('nama_desa', 'LIKE', '%' . $query . '%')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Hasil pencarian posyandu',
                'data' => $posyandu
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pencarian posyandu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get posyandu with their balita count.
     */
    public function getWithBalitaCount($posyanduId): JsonResponse
    {
        try {
            $posyandu = Posyandu::where('id', $posyanduId)
                ->withCount('balita')
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data posyandu dengan jumlah balita berhasil diambil',
                'data' => $posyandu
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data posyandu dengan jumlah balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
