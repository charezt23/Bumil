<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PosyanduController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'nama_posyandu' => 'required|string|max:255'
            ]);

            // Buat posyandu baru
            $posyandu = Posyandu::create($validated);

            // Load relasi user
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

    public function show($id)
    {
        try {
            $posyandu = Posyandu::with('user')->find($id);

            if (!$posyandu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Posyandu tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail posyandu berhasil diambil',
                'data' => $posyandu
            ], 200);

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
    public function update(Request $request, $id)
    {
        try {
            $posyandu = Posyandu::find($id);

            if (!$posyandu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Posyandu tidak ditemukan'
                ], 404);
            }

            // Handle JSON input untuk PUT/PATCH
            if (empty($request->all()) && $request->getContent()) {
                $jsonData = json_decode($request->getContent(), true);
                if ($jsonData) {
                    $request->merge($jsonData);
                }
            }

            // Validasi input
            $validated = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'nama_posyandu' => 'required|string|max:255'
            ]);

            // Update posyandu
            $posyandu->update($validated);

            // Load relasi user
            $posyandu->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Posyandu berhasil diupdate',
                'data' => $posyandu
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate posyandu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $posyandu = Posyandu::find($id);

            if (!$posyandu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Posyandu tidak ditemukan'
                ], 404);
            }

            // Simpan data sebelum dihapus untuk response
            $deletedPosyandu = $posyandu->toArray();

            // Hapus posyandu
            $posyandu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Posyandu berhasil dihapus',
                'data' => $deletedPosyandu
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus posyandu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get posyandu by user ID
     */
    public function getByUser($userId)
    {
        try {
            $posyandu = Posyandu::with('user')->where('user_id', $userId)->get();

            if ($posyandu->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data posyandu tidak ditemukan untuk user ini',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data posyandu berdasarkan user berhasil diambil',
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


}
