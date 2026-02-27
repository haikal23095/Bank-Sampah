<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepositRequest;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WasteType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    /**
     * Show form for waste deposit.
     */
    public function create()
    {
        // Optimized: only nasabah with specific columns and wallet balance
        $nasabahs = User::query()
            ->select(['id', 'name'])
            ->where('role', 'NASABAH')
            ->with(['wallet:id,user_id,balance'])
            ->orderBy('name')
            ->get();

        // Optimized: select only necessary columns for the dropdown
        $wasteTypes = WasteType::query()
            ->select(['id', 'name', 'price_per_kg', 'unit'])
            ->orderBy('name')
            ->get();

        return view('admin.deposits.create', compact('nasabahs', 'wasteTypes'));
    }

    /**
     * Process waste deposit transaction.
     */
    public function store(DepositRequest $request)
    {
        $userId = $request->user_id;
        $items = $request->items;

        try {
            DB::beginTransaction();

            // Load all necessary waste types in one query to avoid N+1 inside loop
            $wasteTypeIds = collect($items)->pluck('waste_type_id')->unique();
            $wasteTypes = WasteType::query()
                ->whereIn('id', $wasteTypeIds)
                ->get()
                ->keyBy('id');

            // 1. Create Transaction Header
            $transaction = Transaction::query()->create([
                'user_id' => $userId,
                'staff_id' => Auth::id(),
                'date' => now()->toDateString(),
            ]);

            $totalAmount = 0;
            $detailsData = [];

            // 2. Map and prepare details for bulk insert or sequential (sequential for detail model events if any)
            foreach ($items as $item) {
                $wasteType = $wasteTypes->get($item['waste_type_id']);

                if (! $wasteType) {
                    continue;
                }

                $subtotal = $wasteType->price_per_kg * $item['weight'];
                $totalAmount += $subtotal;

                $detailsData[] = [
                    'transaction_id' => $transaction->id,
                    'waste_type_id' => $wasteType->id,
                    'weight' => $item['weight'],
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 3. Bulk Insert details for better performance
            TransactionDetail::query()->insert($detailsData);

            // 4. Update or Create Wallet with DB Lock
            $wallet = Wallet::query()->firstOrCreate(
                ['user_id' => $userId],
                ['balance' => 0]
            );

            // Use lockForUpdate if it was already created, but for increment it's generally safe
            // if handled within transaction. For max safety:
            $wallet = Wallet::query()->where('user_id', $userId)->lockForUpdate()->first();
            $wallet->increment('balance', $totalAmount);

            DB::commit();

            return redirect()->route('admin.deposits.create')
                ->with('success', 'Setoran berhasil! Saldo nasabah bertambah Rp '.number_format($totalAmount, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Gagal memproses setoran: '.$e->getMessage())->withInput();
        }
    }
}
