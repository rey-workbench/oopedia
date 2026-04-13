import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    vus: 10,
    duration: '30s',
    thresholds: {
        http_req_failed: ['rate<0.01'],
        http_req_duration: ['p(95)<500'],
    },
};

export default function () {
    const res = http.get('http://127.0.0.1:8000');

    check(res, {
        'status is 200': (r) => r.status === 200,
        // Diubah menjadi case-insensitive agar cocok dengan "OOPedia"
        'contains OOPedia': (r) => r.body.toLowerCase().includes('oopedia'),
    });

    sleep(1);
}
