<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $customers = User::query()
            ->select(['id', 'name', 'email', 'phone', 'role', 'address', 'join_date'])
            ->whereIn('role', ['NASABAH', 'ADMIN', 'PETUGAS'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers', 'search'));
    }

    public function store(UserRequest $request)
    {
        DB::transaction(function () use ($request) {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => strtoupper($request->role),
                'address' => $request->address,
                'join_date' => now(),
            ]);
        });

        return redirect()->route('admin.customers.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->safe()->except(['password', 'role']);
        $data['role'] = strtoupper($request->role);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.customers.index')->with('success', 'Informasi user berhasil diperbarui!');
    }

    public function destroy($id)
    {
        User::destroy($id);

        return back()->with('success', 'Data user berhasil dihapus.');
    }
}
