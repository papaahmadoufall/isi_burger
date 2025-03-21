<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(): Response
    {
        $payments = Payment::all();
        return Inertia::render('payments/Index', [
            'payments' => $payments
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|string|max:255',
        ]);

        Payment::create($validatedData);

        return redirect()->route('payments.index')
            ->with('message', 'Payment method added successfully');
    }

    public function update(Request $request, Payment $payment)
    {
        $validatedData = $request->validate([
            'type' => 'required|string|max:255',
        ]);

        $payment->update($validatedData);

        return redirect()->route('payments.index')
            ->with('message', 'Payment method updated successfully');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index')
            ->with('message', 'Payment method deleted successfully');
    }

    public function getAll(): JsonResponse
    {
        $payments = Payment::all();
        return response()->json($payments);
    }
} 