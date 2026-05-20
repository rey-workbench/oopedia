import type { Reporter, TestCase, TestResult, FullResult } from '@playwright/test/reporter';

class CleanReporter implements Reporter {
    private currentSuiteName = '';
    private totalTests = 0;
    private passedTests = 0;
    private failedTests = 0;
    private skippedTests = 0;
    private startTime = 0;

    onBegin() {
        this.startTime = Date.now();
        console.clear();
        console.log(
            `\x1b[1m\x1b[36m┌────────────────────────────────────────────────────────────────────────┐\x1b[0m`
        );
        console.log(
            `\x1b[1m\x1b[36m│                  OOPEDIA ADAPTIVE E-LEARNING SYSTEM                    │\x1b[0m`
        );
        console.log(
            `\x1b[1m\x1b[36m│                     LAPORAN PENGUJIAN OTOMATIS                         │\x1b[0m`
        );
        console.log(
            `\x1b[1m\x1b[36m└────────────────────────────────────────────────────────────────────────┘\x1b[0m`
        );
    }

    onTestBegin(test: TestCase) {
        const parentSuite = test.parent;
        const suiteName = parentSuite && parentSuite.title ? parentSuite.title : '';

        if (
            suiteName &&
            suiteName !== this.currentSuiteName &&
            suiteName !== 'chromium' &&
            !suiteName.endsWith('.ts') &&
            !suiteName.endsWith('.spec.ts')
        ) {
            this.currentSuiteName = suiteName;
            console.log(`\n\x1b[1m\x1b[34m▶ [KATEGORI] ${suiteName.toUpperCase()}\x1b[0m`);
            console.log(
                `\x1b[90m  ├──────────────────────────────────────────────────────────────────────\x1b[0m`
            );
        }
    }

    onTestEnd(test: TestCase, result: TestResult) {
        this.totalTests++;
        const duration = (result.duration / 1000).toFixed(2);

        let statusSymbol = '';
        let statusText = '';
        let colorCode = '';

        if (result.status === 'passed') {
            this.passedTests++;
            statusSymbol = '✓';
            statusText = 'PASSED';
            colorCode = '\x1b[32m'; // Hijau
        } else if (result.status === 'failed') {
            this.failedTests++;
            statusSymbol = '✗';
            statusText = 'FAILED';
            colorCode = '\x1b[31m'; // Merah
        } else {
            this.skippedTests++;
            statusSymbol = '-';
            statusText = 'SKIPPED';
            colorCode = '\x1b[33m'; // Kuning
        }

        // Cetak hasil test dengan format yang sangat bersih
        console.log(
            `  \x1b[90m├─\x1b[0m ${colorCode}[${statusText}]\x1b[0m ${test.title} \x1b[90m(${duration}s)\x1b[0m`
        );

        if (result.status === 'failed' && result.error) {
            console.log(
                `  \x1b[90m│  \x1b[31mError: ${result.error.message?.split('\n')[0]}\x1b[0m`
            );
        }
    }

    onEnd(result: FullResult) {
        const totalDuration = ((Date.now() - this.startTime) / 1000).toFixed(1);
        const successRate =
            this.totalTests > 0 ? ((this.passedTests / this.totalTests) * 100).toFixed(1) : '0.0';

        console.log(
            `\n\x1b[1m\x1b[36m┌────────────────────────────────────────────────────────────────────────┐\x1b[0m`
        );
        console.log(
            `\x1b[1m\x1b[36m│                       RINGKASAN HASIL PENGUJIAN                        │\x1b[0m`
        );
        console.log(
            `\x1b[1m\x1b[36m├────────────────────────────────────────────────────────────────────────┤\x1b[0m`
        );
        console.log(`\x1b[0m│  Total Skenario Uji   : ${String(this.totalTests).padEnd(46)} │`);
        console.log(
            `│  \x1b[32mBerhasil (Passed)    : ${String(this.passedTests).padEnd(46)}\x1b[0m │`
        );
        console.log(
            `│  \x1b[31mGagal (Failed)       : ${String(this.failedTests).padEnd(46)}\x1b[0m │`
        );
        console.log(
            `│  \x1b[33mDilewati (Skipped)   : ${String(this.skippedTests).padEnd(46)}\x1b[0m │`
        );
        console.log(`│  Waktu Eksekusi       : ${(totalDuration + ' detik').padEnd(46)} │`);
        console.log(`│  Tingkat Keberhasilan : ${(successRate + ' %').padEnd(46)} │`);
        console.log(
            `\x1b[1m\x1b[36m├────────────────────────────────────────────────────────────────────────┤\x1b[0m`
        );

        if (result.status === 'passed') {
            console.log(
                `│  \x1b[1m\x1b[32mKESIMPULAN: SEMUA SKENARIO UJI LULUS DENGAN SUKSES (100% SUCCESS)     \x1b[0m │`
            );
        } else {
            console.log(
                `│  \x1b[1m\x1b[31mKESIMPULAN: TERDAPAT SKENARIO UJI YANG GAGAL / ERROR                  \x1b[0m │`
            );
        }
        console.log(
            `\x1b[1m\x1b[36m└────────────────────────────────────────────────────────────────────────┘\x1b[0m\n`
        );
    }
}

export default CleanReporter;
