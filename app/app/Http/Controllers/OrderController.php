<?php

namespace App\Http\Controllers;

use App\Models\Burger;
use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'burger_id' => 'required|exists:burgers,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $burger = Burger::findOrFail($request->burger_id);

        // Check if there's enough stock
        if ($burger->stock < $request->quantity) {
            return response()->json(['message' => 'Stock insuffisant'], 400);
        }

        // Create a new order
        $order = Command::create([
            'user_id' => Auth::id(),
            'statut' => 'pending',
            'total' => $burger->price * $request->quantity,
        ]);

        // Attach the burger to the order with quantity
        $order->burgers()->attach($burger->id, ['quantite' => $request->quantity]);

        // Update the burger stock
        $burger->update([
            'stock' => $burger->stock - $request->quantity
        ]);

        return response()->json([
            'message' => 'Commande créée avec succès',
            'order' => $order
        ], 201);
    }
} 