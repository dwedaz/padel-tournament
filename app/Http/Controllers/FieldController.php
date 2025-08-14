<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index()
    {
        $fields = Field::all();
        return view('fields.index', compact('fields'));
    }

    public function create()
    {
        // Check if we already have 5 fields
        $fieldCount = Field::count();
        if ($fieldCount >= 5) {
            return redirect()->route('fields.index')
                ->with('error', 'Maximum 5 fields allowed. Please delete a field first.');
        }

        return view('fields.create');
    }

    public function store(Request $request)
    {
        // Check if we already have 5 fields
        $fieldCount = Field::count();
        if ($fieldCount >= 5) {
            return redirect()->route('fields.index')
                ->with('error', 'Maximum 5 fields allowed. Cannot create more fields.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:fields,name',
        ]);

        Field::create([
            'name' => $request->name,
        ]);

        return redirect()->route('fields.index')
            ->with('success', 'Field created successfully.');
    }

    public function show(Field $field)
    {
        return view('fields.show', compact('field'));
    }

    public function edit(Field $field)
    {
        return view('fields.edit', compact('field'));
    }

    public function update(Request $request, Field $field)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fields,name,' . $field->id,
        ]);

        $field->update([
            'name' => $request->name,
        ]);

        return redirect()->route('fields.index')
            ->with('success', 'Field updated successfully.');
    }

    public function destroy(Field $field)
    {
        // Check if field has active games
        $activeGamesCount = $field->games()->whereIn('status', ['pending', 'playing'])->count();
        
        if ($activeGamesCount > 0) {
            return redirect()->route('fields.index')
                ->with('error', 'Cannot delete field with active games. Please finish or remove games first.');
        }

        $field->delete();

        return redirect()->route('fields.index')
            ->with('success', 'Field deleted successfully.');
    }
}
