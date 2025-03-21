<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Burger;
use Illuminate\Support\Facades\Storage;

class burgerController extends Controller
{
//    public function __construct()
//    {
//        // Protéger toutes les routes avec le middleware 'auth'
//        $this->middleware('auth:sanctum')->except('index', 'show', 'search');
//    }

    // CREATE: Ajouter un nouveau burger (réservé aux administrateurs)
    public function store(Request $request): JsonResponse
    {
        // Vérifier si l'utilisateur est admin
//        if (auth()->user()->role !== 'admin') {
//            return response()->json(['message' => 'Accès non autorisé'], 403);
//        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|numeric',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('burgers', 'public');
            $validatedData['image'] = Storage::url($imagePath);
        }

        $burger = Burger::create($validatedData);
        return response()->json(['message' => 'Burger ajouté avec succès', 'burger' => $burger], 201);
    }

    // READ: Récupérer tous les burgers
    public function index(): JsonResponse
    {
        $burgers = Burger::all();
        return response()->json($burgers, 200);
    }

    // READ: Récupérer un burger spécifique
    public function show($id) : JsonResponse
    {
        $burger = Burger::findOrFail($id);
        return response()->json($burger, 200);
    }

    // UPDATE: Mettre à jour un burger (réservé aux administrateurs)
    public function update(Request $request, $id) : JsonResponse
    {
        // Vérifier si l'utilisateur est admin
//                if (auth()->user()->role !== 'admin') {
//                    return response()->json(['message' => 'Accès non autorisé'], 403);
//                }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'description' => 'nullable|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'sometimes|required|numeric',
        ]);

        $burger = Burger::findOrFail($id);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($burger->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $burger->image));
            }
            $imagePath = $request->file('image')->store('burgers', 'public');
            $validatedData['image'] = Storage::url($imagePath);
        }

        $burger->update($validatedData);

        return response()->json(['message' => 'Burger mis à jour avec succès', 'burger' => $burger], 200);
    }

    // DELETE: Supprimer un burger (réservé aux administrateurs)
    public function destroy($id) : JsonResponse
    {
        // Vérifier si l'utilisateur est admin
//        if (auth()->user()->role !== 'admin') {
//            return response()->json(['message' => 'Accès non autorisé'], 403);
//        }

        $burger = Burger::findOrFail($id);
        
        // Delete image if exists
        if ($burger->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $burger->image));
        }
        
        $burger->delete();

        return response()->json(['message' => 'Burger supprimé avec succès'], 200);
    }
}
