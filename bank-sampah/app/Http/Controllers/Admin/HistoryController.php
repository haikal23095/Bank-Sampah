<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    /**
     * Display a listing of transaction history.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Optimized query with specific columns and scoped relations
        $transactions = Transaction::query()
            ->with([
                'nasabah:id,name',
                'details:id,transaction_id,weight,subtotal',
            ])
            ->when($startDate, fn ($q) => $q->whereDate('date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('date', '<=', $endDate))
            ->latest('date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.history.index', compact('transactions'));
    }

    /**
     * Display the specified transaction details.
     */
    public function show($id)
    {
        // Optimized: select only necessary nested relation columns
        $transaction = Transaction::query()
            ->with([
                'nasabah:id,name,email,phone,address',
                'petugas:id,name',
                'details' => function ($q) {
                    $q->select(['id', 'transaction_id', 'waste_type_id', 'weight', 'subtotal'])
                        ->with('wasteType:id,name,unit');
                },
            ])
            ->findOrFail($id);

        return view('admin.history.show', compact('transaction'));
    }
}
