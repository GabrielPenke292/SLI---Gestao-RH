<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Clinic;

class ExamsController extends Controller
{
    public function index()
    {
        return view('exams.index');
    }

    public function clinics()
    {
        return view('exams.clinics');
    }

    /**
     * Buscar todas as clínicas (para DataTable)
     */
    public function getClinicsData(): JsonResponse
    {
        $clinics = Clinic::whereNull('deleted_at')
            ->orderBy('corporate_name', 'asc')
            ->get()
            ->map(function($clinic) {
                return [
                    'clinic_id' => $clinic->clinic_id,
                    'corporate_name' => $clinic->corporate_name,
                    'trade_name' => $clinic->trade_name ?? '-',
                    'cnpj' => $clinic->formatted_cnpj,
                    'email' => $clinic->email ?? '-',
                    'phone' => $clinic->formatted_phone,
                    'city' => $clinic->city ?? '-',
                    'state' => $clinic->state ?? '-',
                    'is_active' => $clinic->is_active,
                    'created_at' => $clinic->created_at?->format('d/m/Y H:i') ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $clinics
        ]);
    }

    /**
     * Criar nova clínica
     */
    public function storeClinic(Request $request): JsonResponse
    {
        $request->validate([
            'corporate_name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'cnpj' => 'required|string|max:18|unique:clinics,cnpj',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'address_complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            $user = Auth::user();
            
            // Limpar CNPJ (remover formatação)
            $cnpj = preg_replace('/\D/', '', $request->input('cnpj'));
            
            // Limpar CEP
            $zipCode = $request->input('zip_code') ? preg_replace('/\D/', '', $request->input('zip_code')) : null;

            $clinic = Clinic::create([
                'corporate_name' => $request->input('corporate_name'),
                'trade_name' => $request->input('trade_name'),
                'cnpj' => $cnpj,
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'address_number' => $request->input('address_number'),
                'address_complement' => $request->input('address_complement'),
                'neighborhood' => $request->input('neighborhood'),
                'city' => $request->input('city'),
                'state' => strtoupper($request->input('state') ?? ''),
                'zip_code' => $zipCode,
                'notes' => $request->input('notes'),
                'is_active' => $request->input('is_active', true),
                'created_at' => now(),
                'created_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Clínica cadastrada com sucesso!',
                'data' => $clinic
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar clínica: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar clínica por ID
     */
    public function getClinic($id): JsonResponse
    {
        $clinic = Clinic::whereNull('deleted_at')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'clinic_id' => $clinic->clinic_id,
                'corporate_name' => $clinic->corporate_name,
                'trade_name' => $clinic->trade_name,
                'cnpj' => $clinic->cnpj,
                'email' => $clinic->email,
                'phone' => $clinic->phone,
                'address' => $clinic->address,
                'address_number' => $clinic->address_number,
                'address_complement' => $clinic->address_complement,
                'neighborhood' => $clinic->neighborhood,
                'city' => $clinic->city,
                'state' => $clinic->state,
                'zip_code' => $clinic->zip_code,
                'notes' => $clinic->notes,
                'is_active' => $clinic->is_active,
            ]
        ]);
    }

    /**
     * Atualizar clínica
     */
    public function updateClinic(Request $request, $id): JsonResponse
    {
        $clinic = Clinic::whereNull('deleted_at')->findOrFail($id);

        $request->validate([
            'corporate_name' => 'required|string|max:255',
            'trade_name' => 'nullable|string|max:255',
            'cnpj' => 'required|string|max:18|unique:clinics,cnpj,' . $id . ',clinic_id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'address_complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            $user = Auth::user();
            
            // Limpar CNPJ (remover formatação)
            $cnpj = preg_replace('/\D/', '', $request->input('cnpj'));
            
            // Limpar CEP
            $zipCode = $request->input('zip_code') ? preg_replace('/\D/', '', $request->input('zip_code')) : null;

            $clinic->update([
                'corporate_name' => $request->input('corporate_name'),
                'trade_name' => $request->input('trade_name'),
                'cnpj' => $cnpj,
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address'),
                'address_number' => $request->input('address_number'),
                'address_complement' => $request->input('address_complement'),
                'neighborhood' => $request->input('neighborhood'),
                'city' => $request->input('city'),
                'state' => strtoupper($request->input('state') ?? ''),
                'zip_code' => $zipCode,
                'notes' => $request->input('notes'),
                'is_active' => $request->input('is_active', true),
                'updated_at' => now(),
                'updated_by' => $user->user_name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Clínica atualizada com sucesso!',
                'data' => $clinic
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar clínica: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Excluir clínica (soft delete)
     */
    public function deleteClinic($id): JsonResponse
    {
        $clinic = Clinic::whereNull('deleted_at')->findOrFail($id);

        try {
            $clinic->delete();

            return response()->json([
                'success' => true,
                'message' => 'Clínica excluída com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir clínica: ' . $e->getMessage()
            ], 500);
        }
    }
}
