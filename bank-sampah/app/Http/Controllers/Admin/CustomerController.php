<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['nasabah', 'admin']);

        // Fitur Search
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }

        $customers = $query->latest()->get();
        return view('admin.customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'unique:users,email', 'regex:/^[\w\.-]+@[\w\.-]+\.[a-zA-Z]{2,7}$/'],
            'password' => 'required|min:6',
            'phone' => 'required|numeric|digits_between:10,12',
            'role' => 'required|in:admin,nasabah', // Validasi role
            'address' => 'nullable|string',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.regex' => 'Format email tidak valid (harus mengandung domain yang benar, contoh: .com).',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal :min karakter.',
            'phone.required' => 'No. Telepon wajib diisi.',
            'phone.numeric' => 'No. Telepon harus berupa angka.',
            'phone.digits_between' => 'No. Telepon harus antara 10 sampai 12 digit.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => strtoupper($request->role),
            'address' => $request->address,
            'join_date' => now(),
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Nasabah berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'unique:users,email,' . $id, 'regex:/^[\w\.-]+@[\w\.-]+\.[a-zA-Z]{2,7}$/'],
            'password' => 'nullable|min:6',
            'phone' => 'required|numeric|digits_between:10,12',
            'role' => 'required|in:admin,nasabah',
            'address' => 'nullable|string',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'email.regex' => 'Format email tidak valid (harus mengandung domain yang benar, contoh: .com).',
            'password.min' => 'Password minimal :min karakter.',
            'phone.required' => 'No. Telepon wajib diisi.',
            'phone.numeric' => 'No. Telepon harus berupa angka.',
            'phone.digits_between' => 'No. Telepon harus antara 10 sampai 12 digit.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => strtoupper($request->role),
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.customers.index')->with('success', 'Informasi nasabah berhasil diperbarui!');
    }

    public function destroy($id)
    {
        User::destroy($id);
        return redirect()->back()->with('success', 'Data nasabah dihapus.');
    }
}
