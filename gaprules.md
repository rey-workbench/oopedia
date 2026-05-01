# Gap Analysis — Adaptive Rule Engine v2.1.0

> **Tanggal:** 2026-04-29  
> **Sumber:** `AdaptiveEngineService.php`, `PerformanceService.php`, `PedagogicalConstants.php`, `FactConstants.php`

---

## 1. Rule Coverage Gap (Kombinasi Fakta Tanpa Rule)

### GAP-01: Akurasi <40% + Tren Stabil + Bantuan ≤3x → R14 (Fallthrough)

| Input    | Nilai         |
| -------- | ------------- |
| Accuracy | < 40% (G01)   |
| Trend    | stable (G06)  |
| Hints    | 0-3 (G20/G09) |

**Masalah:** R01 butuh `trend=down + hints>3`, R02 butuh `trend=down`, R03 butuh `hints>3`. Kombinasi ini menembus semua filter krisis dan jatuh ke R14 "Normal Learning".

**Impact:** Mahasiswa dalam krisis akurasi didiagnosis "Normal" — tidak mendapat intervensi apapun.

**Rekomendasi:** Tambah catch-all rule untuk `accuracy < 40%` di akhir blok krisis.

---

### GAP-02: Akurasi 40-60% + Respons Cepat → R14 (Fallthrough)

| Input    | Nilai        |
| -------- | ------------ |
| Accuracy | 40-60% (G02) |
| Speed    | fast (G11)   |
| Hints    | any          |

**Masalah:** R04 butuh `speed=slow`, R05 butuh `speed=normal + hints 2-3`. Tidak ada rule untuk mahasiswa struggling dengan respons cepat.

**Impact:** Mungkin menandakan tebakan (guessing) — mahasiswa menjawab cepat tapi salah. Tidak terdeteksi.

---

### GAP-03: Akurasi 40-60% + Respons Normal + Bantuan 0-1x → R14 (Fallthrough)

| Input    | Nilai        |
| -------- | ------------ |
| Accuracy | 40-60% (G02) |
| Speed    | normal (G13) |
| Hints    | 0 atau 1     |

**Masalah:** R05 membutuhkan `hints >= 2`. Mahasiswa yang berjuang sendiri tanpa hint (mandiri tapi salah) tidak ter-cover.

---

### GAP-04: Akurasi 60-70% + Tren Naik/Turun → R14 (Fallthrough)

| Input    | Nilai        |
| -------- | ------------ |
| Accuracy | 60-70% (G03) |
| Trend    | up atau down |

**Masalah:** R06 hanya menangkap `trend=stable`. Mahasiswa di zona "sedang" dengan tren turun seharusnya dapat intervensi dini, bukan R14.

---

### GAP-05: Akurasi 70-80% (Dead Zone) → Selalu R14

| Input    | Nilai  |
| -------- | ------ |
| Accuracy | 70-80% |

**Masalah:** Tidak ada rule yang men-cover rentang 70-80%. Blok STRUGGLING berhenti di 70%, blok OPTIMAL dimulai di >80%. Mahasiswa di zona ini selalu mendapat "Progres Normal" tanpa arahan apapun.

**Impact:** Rentang ini adalah zona transisi penting — mahasiswa hampir optimal, tapi bisa menurun atau naik. Tidak ada rule yang mendorong mereka.

---

### GAP-06: Akurasi >80% + Tren Stabil + Speed Normal → R14 (Fallthrough)

| Input    | Nilai        |
| -------- | ------------ |
| Accuracy | > 80% (G04)  |
| Trend    | stable (G06) |
| Speed    | normal (G13) |
| Streak   | < 3          |
| Stagnant | < 3          |

**Masalah:** R07/R08 butuh `trend=up`, R09 butuh `speed=fast`, R12/R13 butuh `stagnant>=3`. Mahasiswa optimal yang konsisten (stable, normal speed) jatuh ke R14.

---

## 2. Hint System Gaps

### GAP-07: `hints` Dibaca dari `current_session` — Reset Setiap 5 Soal

**Sumber:** `AdaptiveEngineService.php:25`

```php
$hints = (int) ($session['hints'] ?? 0);
```

**Masalah:** `current_session.hints` direset ke 0 setiap 5 soal (session buffer logic). Setelah reset:

- R01/R03 butuh `hints > 3` — **tidak mungkin tercapai** dalam 5 soal jika hint awal = 3
- R15 butuh `hints === 0` — **trivially true** setelah reset

**Impact:** Rule R01, R03, R10, R11 (semua yang butuh `hints > 3`) **hampir unreachable** secara praktis.

---

### GAP-08: Tidak Ada Mekanisme Replenish Hint

**Masalah:** `hints_available` hanya berkurang (`max(0, $hintsAvailable - 1)`), tidak pernah ditambah kembali. Setelah 3x pakai hint, mahasiswa permanen kehilangan hint selamanya.

**Impact:** Fitur hint menjadi one-time use. Tidak ada reward loop (misal: benar 5 berturut-turut → hint +1).

---

## 3. Accuracy System Gaps

### GAP-09: Akurasi Awal 0% untuk Mahasiswa Baru → Langsung Krisis

**Masalah:** Default accuracy = 0.0 (`StudentStateSchema::defaults()`). Soal pertama yang salah → `0/1 = 0%` → engine langsung trigger KRISIS.

**Impact:** Mahasiswa baru yang belum punya riwayat langsung didiagnosis krisis sebelum ada data cukup.

**Rekomendasi:** Tambahkan minimum sample size (misal `total_answered >= 3`) sebelum engine aktif mendiagnosis.

---

### GAP-10: Session History Default [0, 0, 0] → Trend = Stable (Palsu)

**Masalah:** Default `session_history = [0.0, 0.0, 0.0]`. Sesi pertama dengan akurasi 60% akan menghitung:

```
delta1 = 0 - 0 = 0 (stable)
delta2 = 60 - 0 = 60 (up)
```

Tapi `calculateTrend` butuh **kedua delta > margin** → hasil = `stable`. Trend "up" yang seharusnya tidak terdeteksi.

---

## 4. Rule Ordering & Shadowing

### GAP-11: R10/R11 Dishadow oleh R01-R03

**Masalah:** R10 butuh `hints > 3 + accuracy < 50`, R11 butuh `hints > 3 + accuracy > 60`. Tapi R01 dan R03 sudah menangkap `accuracy < 40 + hints > 3` lebih dulu. Hanya celah `accuracy 40-50%` yang bisa reach R10.

**R11 practically unreachable:** R03 menangkap `accuracy < 40 + hints > 3` dulu, dan untuk accuracy > 60 + hints > 3, R03 tidak ter-trigger (acc > 40), tapi juga tidak ada rule lain yang menangkap — sehingga R11 hanya tercapai jika accuracy > 60 DAN trend up DAN hints > 3. Ini sangat niche.

---

### GAP-12: R12 vs R13 — Overlapping Conditions

**Masalah:**

- R12: `accuracy > 80% + stagnant >= 3 + streak >= 5`
- R13: `accuracy > 80% + fast + stagnant >= 3`

Jika mahasiswa punya `accuracy > 80% + stagnant >= 3 + streak >= 5 + speed = fast` → R12 menang (karena urutan). R13 hanya tercapai jika streak < 5. Ini mungkin disengaja, tapi tidak terdokumentasi.

---

## 5. R15 Certification — Terlalu Ketat

### GAP-13: Kondisi R15 Gabungan Sangat Restrictive

```php
$level === 'Ahli' && $isConsistentHigh && $streak >= 7 && $hints === 0
```

| Kondisi               | Threshold    | Realistis?                     |
| --------------------- | ------------ | ------------------------------ |
| Level Ahli            | —            | ⚠️ Butuh XP tinggi             |
| 3 sesi terakhir > 85% | Per 5 soal   | ⚠️ 15 soal terakhir harus >85% |
| Streak ≥ 7 hari       | Daily login  | ⚠️ 7 hari berturut-turut       |
| hints = 0 (sesi ini)  | Session hint | 🐛 Trivial setelah reset       |

**Impact:** Gabungan 4 kondisi ini sangat sulit tercapai secara realistis, terutama streak 7 hari berturut + 3 sesi konsisten.

---

## 6. Structural Issues

### GAP-14: Accuracy Fact Mapping — Gap di 70-80%

```php
if ($accuracy < 40)        → G01
elseif ($accuracy <= 60)   → G02
elseif ($accuracy <= 70)   → G03
elseif ($accuracy > 85)    → G17
elseif ($accuracy > 80)    → G04
```

**Masalah:** Akurasi 70.01% - 80.00% tidak mendapat fact code manapun. Tidak ada G-code untuk rentang ini.

---

### GAP-15: Level Hanya Mengenali "Ahli" — Level Lain Diabaikan

```php
if ($level === 'Ahli') → G19
```

**Masalah:** Level Pemula, Menengah, dan Mahir tidak menghasilkan fact code. Engine tidak bisa membedakan perilaku untuk level berbeda selain Ahli.

---

### GAP-16: `BASELINE_TIME` Key Mismatch

```php
public const array BASELINE_TIME = [
    'beginner' => 20,
    'medium'   => 40,
    'hard'     => 60,
];
```

**Masalah:** Key `beginner` tidak cocok dengan `QuestionDifficulty::Easy->value` (yang mungkin `easy`). Jika difficulty value = `easy`, baseline fallback ke 30 (default), bukan 20.

---

### GAP-17: `hints_used` (Global) Tidak Pernah Di-update

**Masalah:** Schema mendefinisikan `HINTS_USED` tapi `PerformanceService::updateAfterAnswer()` tidak pernah mengupdate field ini. Hanya `current_session.hints` dan `hints_available` yang diupdate.

**Impact:** Tidak ada tracking kumulatif penggunaan hint. Field ini menjadi dead column.

---

## Ringkasan Gap Matrix

| ID     | Severity    | Kategori      | Status                                       |
| ------ | ----------- | ------------- | -------------------------------------------- |
| GAP-01 | 🔴 Critical | Rule Coverage | Akurasi krisis lolos ke R14                  |
| GAP-02 | 🟡 Medium   | Rule Coverage | Guessing behavior tidak terdeteksi           |
| GAP-03 | 🟡 Medium   | Rule Coverage | Mandiri-gagal tidak ter-cover                |
| GAP-04 | 🟡 Medium   | Rule Coverage | Tren di zona 60-70 diabaikan                 |
| GAP-05 | 🔴 Critical | Rule Coverage | Dead zone 70-80% = selalu R14                |
| GAP-06 | 🟡 Medium   | Rule Coverage | Optimal konsisten tanpa arahan               |
| GAP-07 | 🔴 Critical | Hint System   | Session reset membuat hint rules unreachable |
| GAP-08 | 🟠 High     | Hint System   | Hint tidak pernah diisi ulang                |
| GAP-09 | 🟠 High     | Accuracy      | Cold-start langsung krisis                   |
| GAP-10 | 🟡 Medium   | Accuracy      | History default menghasilkan tren palsu      |
| GAP-11 | 🟡 Medium   | Rule Ordering | R10/R11 dishadow R01-R03                     |
| GAP-12 | ⚪ Low      | Rule Ordering | R12/R13 overlap                              |
| GAP-13 | 🟠 High     | Certification | R15 terlalu ketat                            |
| GAP-14 | 🔴 Critical | Fact Mapping  | 70-80% tanpa G-code                          |
| GAP-15 | 🟡 Medium   | Fact Mapping  | Level lain diabaikan                         |
| GAP-16 | 🟡 Medium   | Constants     | Key mismatch baseline time                   |
| GAP-17 | 🟡 Medium   | Dead Code     | `hints_used` tidak pernah diupdate           |
