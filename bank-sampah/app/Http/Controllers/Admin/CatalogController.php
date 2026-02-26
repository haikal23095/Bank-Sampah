<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WasteCategoryRequest;
use App\Http\Requests\Admin\WasteTypeRequest;
use App\Models\WasteCategory;
use App\Models\WasteType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Cache the default list for performance, search remains dynamic
        $categories = $search
            ? $this->getCatalogData($search)
            : Cache::remember('catalog_data', 3600, fn () => $this->getCatalogData());

        return view('admin.catalog.index', compact('categories', 'search'));
    }

    /**
     * Get Catalog data with optimized queries.
     */
    private function getCatalogData(?string $search = null)
    {
        return WasteCategory::query()
            ->select(['id', 'name', 'description'])
            ->with([
                'wasteTypes' => function ($q) use ($search) {
                    $q->select(['id', 'category_id', 'name', 'price_per_kg', 'unit']);
                    if ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    }
                },
            ])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhereHas('wasteTypes', fn ($twq) => $twq->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('id')
            ->get();
    }

    // Simpan Jenis Sampah
    public function storeType(WasteTypeRequest $request)
    {
        WasteType::query()->create($request->validated());

        Cache::forget('catalog_data');

        return back()->with('success', 'Jenis sampah berhasil ditambahkan!');
    }

    // Perbarui item jenis sampah
    public function updateType(WasteTypeRequest $request, $id)
    {
        $type = WasteType::query()->findOrFail($id);
        $type->update($request->validated());

        Cache::forget('catalog_data');

        return back()->with('success', 'Item berhasil diperbarui!');
    }

    public function storeCategory(WasteCategoryRequest $request)
    {
        WasteCategory::query()->create($request->validated());

        Cache::forget('catalog_data');

        return back()->with('success', 'Kategori Sampah berhasil ditambahkan!');
    }

    public function updateCategory(WasteCategoryRequest $request, $id)
    {
        $category = WasteCategory::query()->findOrFail($id);
        $category->update($request->validated());

        Cache::forget('catalog_data');

        return back()->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroyCategory($id)
    {
        $category = WasteCategory::query()->findOrFail($id);

        // Lebih efisien menggunakan exists() daripada count()
        if ($category->wasteTypes()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki jenis sampah di dalamnya.');
        }

        $category->delete();
        Cache::forget('catalog_data');

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    // Hapus Item
    public function destroyType($id)
    {
        WasteType::destroy($id);
        Cache::forget('catalog_data');

        return back()->with('success', 'Item dihapus.');
    }
}
