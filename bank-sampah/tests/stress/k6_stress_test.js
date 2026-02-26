import http from 'k6/http';
import { check, sleep, group } from 'k6';

// Konfigurasi Stress Test
export let options = {
    stages: [
        { duration: '1m', target: 20 }, // Naikkan ke 20 pengguna dalam 1 menit
        { duration: '3m', target: 50 }, // Bertahan di 50 pengguna selama 3 menit
        { duration: '1m', target: 100 }, // Naikkan lagi ke 100 untuk lonjakan akhir
        { duration: '1m', target: 0 },  // Turun perlahan
    ],
    thresholds: {
        http_req_duration: ['p(95)<500'], // 95% request harus di bawah 500ms
        http_req_failed: ['rate<0.01'],    // Kurang dari 1% error rate
    },
};

const BASE_URL = 'https://bank-sampah.firmansyahdev.my.id'; // Ubah sesuai URL lokal kamu

export default function () {
    // Skenario 1: Login (Opsional jika ingin testing login flow)
    // Skenario 2: Simulasi Deposit Sampah oleh Petugas
    group('Deposit Flow', function () {
        let depositPayload = JSON.stringify({
            user_id: 2, // Ganti dengan ID nasabah yang ada di DB
            items: [
                { waste_type_id: 1, weight: 1.5 },
                { waste_type_id: 2, weight: 0.5 }
            ],
            // Catatan: Jika CSRF aktif, kamu perlu mengambil token dulu atau menonaktifkannya untuk testing
        });

        let params = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                // 'X-CSRF-TOKEN': '...' 
            },
        };

        let res = http.post(`${BASE_URL}/api-test/setor`, depositPayload, params);
        if (res.status !== 201 && res.status !== 302) {
            console.log(`STATUS ERROR: ${res.status} | URL: ${res.url}`);
        }
        check(res, {
            'deposit status is 201 or 302': (r) => r.status === 201 || r.status === 302,
        });
    });

    // Skenario 3: Simulasi Penarikan Saldo (Race Condition Test)
    group('Withdrawal Flow', function () {
        let withdrawPayload = JSON.stringify({
            user_id: 2,
            amount: 5000,
            method: 'CASH'
        });

        let res = http.post(`${BASE_URL}/api-test/penarikan`, withdrawPayload, {
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
        });

        check(res, {
            'withdraw status is 200 or 302': (r) => r.status === 200 || r.status === 302,
        });
    });

    sleep(1);
}
