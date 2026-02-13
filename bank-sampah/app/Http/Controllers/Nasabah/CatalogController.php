<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\WasteCategory;

class CatalogController extends Controller
{
    public function index()
    {
        $categories = WasteCategory::with('wasteTypes')->get();

        return view('nasabah.catalog.index', [
            'categories' => $categories,
        ]);
    }
}
