<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::orderBy('percentage')->get();
        return view('admin.taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('admin.taxes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0',
        ]);

        Tax::create([
            'name' => $request->name,
            'percentage' => $request->percentage,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.taxes.index');
    }

    public function edit(Tax $tax)
    {
        return view('admin.taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0',
        ]);

        $tax->update([
            'name' => $request->name,
            'percentage' => $request->percentage,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.taxes.index');
    }
}
