<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;

uses(RefreshDatabase::class);

beforeEach(function () {
    // nothing for now
});

it('creates a cash withdrawal and decrements the user wallet', function () {
    $admin = User::factory()->create(['role' => 'ADMIN']);
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100000]);

    $this->actingAs($admin)
        ->post(route('admin.withdrawals.store'), [
            'user_id' => $user->id,
            'amount' => 25000,
            'method' => 'CASH'
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('withdrawals', [
        'user_id' => $user->id,
        'amount' => 25000,
        'method' => 'CASH',
        'status' => 'SUCCESS'
    ]);

    $user->refresh();
    expect($user->wallet->balance)->toBe(75000.0);
});

it('requires bank fields when method is TRANSFER', function () {
    $admin = User::factory()->create(['role' => 'ADMIN']);
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 50000]);

    // Missing bank info -> validation fails
    $this->actingAs($admin)
        ->post(route('admin.withdrawals.store'), [
            'user_id' => $user->id,
            'amount' => 10000,
            'method' => 'TRANSFER'
        ])
        ->assertSessionHasErrors(['bank_name', 'account_number']);

    // With bank info -> success
    $this->actingAs($admin)
        ->post(route('admin.withdrawals.store'), [
            'user_id' => $user->id,
            'amount' => 10000,
            'method' => 'TRANSFER',
            'bank_name' => 'BCA',
            'account_number' => '1234567890'
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('withdrawals', [
        'user_id' => $user->id,
        'amount' => 10000,
        'method' => 'TRANSFER'
    ]);
});

it('validates minimum amount', function () {
    $admin = User::factory()->create(['role' => 'ADMIN']);
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 50000]);

    $this->actingAs($admin)
        ->post(route('admin.withdrawals.store'), [
            'user_id' => $user->id,
            'amount' => 500, // less than 1000
            'method' => 'CASH'
        ])
        ->assertSessionHasErrors(['amount']);
});
