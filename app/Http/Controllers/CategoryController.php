<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function store(Request $request, Colocation $colocation)
    {
        if (Auth::id() !== $colocation->owner_id) abort(403);

        $request->validate(['name' => 'required|string|max:50']);

        Category::create([
            'name' => $request->name,
            'coloc_id' => $colocation->id,
        ]);

        return back()->with('success', 'Catégorie ajoutée.');
    }

    public function destroy(Colocation $colocation, Category $category)
    {
        if (Auth::id() !== $colocation->owner_id || $category->coloc_id !== $colocation->id) abort(403);

        $category->delete();
        return back()->with('success', 'Catégorie supprimée.');
    }
}