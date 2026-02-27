<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\WasteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CatalogController extends Controller
{
    /**
     * Display the waste catalog for customers.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Cache the default catalog for 1 hour, search remains dynamic
        $categories = $search
            ? $this->getCatalogData($search)
            : Cache::remember('catalog_data', 3600, fn () => $this->getCatalogData());

        return view('nasabah.catalog.index', compact('categories', 'search'));
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
}
