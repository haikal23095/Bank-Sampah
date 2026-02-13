<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WasteCategory;
use App\Models\WasteType;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        // Ambil kategori beserta itemnya dengan filter pencarian
        $query = WasteCategory::with([
            'wasteTypes' => function ($q) use ($request) {
                if ($request->filled('search')) {
                    $q->where('name', 'like', '%'.$request->search.'%');
                }
            },
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhereHas('wasteTypes', function ($sq) use ($search) {
                        $sq->where('name', 'like', '%'.$search.'%');
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

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        WasteCategory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Kategori Sampah berhasil ditambahkan!');
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = WasteCategory::findOrFail($id);
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroyCategory($id)
    {
        $category = WasteCategory::findOrFail($id);

        // Cek jika masih ada item di kategori ini
        if ($category->wasteTypes()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki jenis sampah di dalamnya.');
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    // Hapus Item
    public function destroyType($id)
    {
        WasteType::destroy($id);

        return back()->with('success', 'Item dihapus.');
    }
}
