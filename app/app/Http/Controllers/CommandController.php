<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\Burger;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommandController extends Controller
{
    public function index()
    {
        $commands = Command::with(['user', 'burgers'])->latest()->get();
        
        return Inertia::render('commands/Index', [
            'commands' => $commands
        ]);
    }

    public function getAll()
    {
        $commands = Command::with(['user', 'burgers'])->latest()->get();
        return response()->json($commands);
    }

    public function myCommands()
    {
        $commands = Command::with(['burgers'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
            
        return Inertia::render('commands/MyCommands', [
            'commands' => $commands
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'burgers' => 'required|array',
            'burgers.*.id' => 'required|exists:burgers,id',
            'burgers.*.quantity' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0'
        ]);

        $command = Command::create([
            'user_id' => auth()->id(),
            'total_amount' => $request->total_amount,
            'status' => 'pending'
        ]);

        foreach ($request->burgers as $burger) {
            $command->burgers()->attach($burger['id'], [
                'quantity' => $burger['quantity']
            ]);
        }

        return redirect()->route('commands.my')
            ->with('success', 'Command created successfully');
    }

    public function update(Request $request, Command $command)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $command->update([
            'status' => $request->status
        ]);

        return redirect()->route('commands.index')
            ->with('success', 'Command status updated successfully');
    }

    public function destroy(Command $command)
    {
        $command->delete();
        
        return redirect()->route('commands.index')
            ->with('success', 'Command deleted successfully');
    }
}
