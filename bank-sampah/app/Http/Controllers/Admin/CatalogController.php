<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WasteCategory;
use App\Models\WasteType;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // Ambil kategori beserta itemnya dengan filter pencarian
        $query = WasteCategory::with([
            'wasteTypes' => function ($q) use ($request) {
                if ($request->filled('search')) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                }
            }
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhereHas('wasteTypes', function ($sq) use ($search) {
                        $sq->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $categories = $query->orderBy('id')->get();
        return view('admin.catalog.index', compact('categories'));
    }

    // Simpan Jenis Sampah (Sesuai Modal di Gambar 2)
    public function storeType(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:waste_categories,id',
            'name' => 'required|string|max:255',
            'price_per_kg' => 'required|numeric|min:0',
            'unit' => 'required|string|max:10', // Tambahan input satuan (kg/pcs/dll)
        ]);

        WasteType::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price_per_kg' => $request->price_per_kg,
            'unit' => $request->unit,
        ]);

        return back()->with('success', 'Jenis sampah berhasil ditambahkan!');
    }

    // Hapus Item
    public function destroyType($id)
    {
        WasteType::destroy($id);
        return back()->with('success', 'Item dihapus.');
    }
}
