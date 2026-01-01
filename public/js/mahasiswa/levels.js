document.addEventListener('DOMContentLoaded', function () {
    // Fungsi untuk menggambar jalur peta
    function drawTreasureMap() {
        const svg = document.querySelector('.level-paths');
        const levelItems = document.querySelectorAll('.level-item');
        const svgNS = "http://www.w3.org/2000/svg";

        if (!svg) return;

        // Hapus semua path yang ada
        while (svg.firstChild) {
            svg.removeChild(svg.firstChild);
        }

        // Dapatkan posisi setiap level
        const positions = [];
        levelItems.forEach(item => {
            const rect = item.getBoundingClientRect();
            const svgRect = svg.getBoundingClientRect();

            // Hanya tambahkan item yang bukan trophy
            if (!item.classList.contains('trophy')) {
                positions.push({
                    x: rect.left + rect.width / 2 - svgRect.left,
                    y: rect.top + rect.height / 2 - svgRect.top,
                    status: item.classList.contains('completed') ? 'completed' :
                        item.classList.contains('unlocked') ? 'unlocked' : 'locked',
                    level: parseInt(item.getAttribute('data-level') || '0'),
                    questionId: item.getAttribute('data-question-id'),
                    position: item.closest('.level-row').classList.contains('center') ? 'center' :
                        item.closest('.level-row').classList.contains('left') ? 'left' : 'right'
                });
            } else {
                // Tambahkan trophy sebagai level terakhir
                positions.push({
                    x: rect.left + rect.width / 2 - svgRect.left,
                    y: rect.top + rect.height / 2 - svgRect.top,
                    status: item.classList.contains('completed') ? 'completed' : 'locked',
                    level: 'trophy',
                    position: 'center' // Trophy selalu di tengah
                });
            }
        });

        // Gambar jalur untuk semua level
        for (let i = 0; i < positions.length - 1; i++) {
            const start = positions[i];
            const end = positions[i + 1];

            // Jika level saat ini dan level berikutnya berada di tengah, gunakan jalur lurus vertikal
            if (start.position === 'center' && end.position === 'center') {
                // Jalur vertikal lurus tanpa lengkungan
                createStraightVerticalPath(svg, start.x, start.y + 60, end.x, end.y - 60,
                    start.status, end.status, end.status === 'completed');
            }
            // Jika level saat ini di tengah dan berikutnya di kiri
            else if (i % 3 === 0) { // Dari center ke left
                const padding = 40; // Padding untuk belokan tumpul

                // Jalur vertikal dari start ke belokan pertama
                createDotPath(svg, start.x, start.y + 60, start.x, start.y + 150 - padding,
                    start.status, end.status, end.status === 'completed');

                // Belokan tumpul pertama (dari vertikal ke horizontal)
                createCurvedCorner(svg, start.x, start.y + 150 - padding, start.x - padding, start.y + 150, 'bottom-right',
                    start.status, end.status, end.status === 'completed');

                // Jalur horizontal
                createDotPath(svg, start.x - padding, start.y + 150, end.x + padding, start.y + 150,
                    start.status, end.status, end.status === 'completed');

                // Belokan tumpul kedua (dari horizontal ke vertikal)
                createCurvedCorner(svg, end.x + padding, start.y + 150, end.x, start.y + 150 + padding, 'top-left',
                    start.status, end.status, end.status === 'completed');

                // Jalur vertikal ke end
                createDotPath(svg, end.x, start.y + 150 + padding, end.x, end.y - 60,
                    start.status, end.status, end.status === 'completed');

            } else if (i % 3 === 1) { // Dari left ke right
                const padding = 40; // Padding untuk belokan tumpul

                // Jalur vertikal dari start ke belokan pertama
                createDotPath(svg, start.x, start.y + 60, start.x, start.y + 150 - padding,
                    start.status, end.status, end.status === 'completed');

                // Belokan tumpul pertama (dari vertikal ke horizontal)
                createCurvedCorner(svg, start.x, start.y + 150 - padding, start.x + padding, start.y + 150, 'bottom-right',
                    start.status, end.status, end.status === 'completed');

                // Jalur horizontal
                createDotPath(svg, start.x + padding, start.y + 150, end.x - padding, start.y + 150,
                    start.status, end.status, end.status === 'completed');

                // Belokan tumpul kedua (dari horizontal ke vertikal)
                createCurvedCorner(svg, end.x - padding, start.y + 150, end.x, start.y + 150 + padding, 'top-left',
                    start.status, end.status, end.status === 'completed');

                // Jalur vertikal ke end
                createDotPath(svg, end.x, start.y + 150 + padding, end.x, end.y - 60,
                    start.status, end.status, end.status === 'completed');

            } else if (i % 3 === 2) { // Dari right ke center
                const padding = 40; // Padding untuk belokan tumpul

                // Jalur vertikal dari start ke belokan pertama
                createDotPath(svg, start.x, start.y + 60, start.x, start.y + 150 - padding,
                    start.status, end.status, end.status === 'completed');

                // Belokan tumpul pertama (dari vertikal ke horizontal)
                createCurvedCorner(svg, start.x, start.y + 150 - padding, start.x - padding, start.y + 150, 'bottom-left',
                    start.status, end.status, end.status === 'completed');

                // Jalur horizontal
                createDotPath(svg, start.x - padding, start.y + 150, end.x + padding, start.y + 150,
                    start.status, end.status, end.status === 'completed');

                // Belokan tumpul kedua (dari horizontal ke vertikal)
                createCurvedCorner(svg, end.x + padding, start.y + 150, end.x, start.y + 150 + padding, 'top-right',
                    start.status, end.status, end.status === 'completed');

                // Jalur vertikal ke end
                createDotPath(svg, end.x, start.y + 150 + padding, end.x, end.y - 60,
                    start.status, end.status, end.status === 'completed');
            }
        }

        // Tambahkan jalur ke trophy jika ada
        if (positions.length >= 2) {
            const lastLevel = positions[positions.length - 2];
            const trophy = positions[positions.length - 1];

            if (trophy.level === 'trophy') {
                // Cek apakah semua soal sudah selesai
                const allCompleted = lastLevel.status === 'completed';

                // Jika level terakhir di tengah, buat jalur langsung ke bawah tanpa lengkungan
                if (lastLevel.position === 'center') {
                    createStraightVerticalPath(svg, lastLevel.x, lastLevel.y + 60, trophy.x, trophy.y - 60,
                        lastLevel.status, trophy.status, allCompleted);
                }
                else {
                    // Kode untuk jalur yang tidak di tengah (tetap seperti sebelumnya)
                    // ...
                }
            }
        }
    }

    // Buat fungsi baru khusus untuk jalur vertikal yang benar-benar lurus
    function createStraightVerticalPath(svg, x1, y1, x2, y2, startStatus, endStatus, allCompleted) {
        const svgNS = "http://www.w3.org/2000/svg";
        const isCompleted = startStatus === 'completed' && (endStatus === 'completed' || endStatus === 'unlocked');

        // Pastikan x koordinatnya sama untuk jalur lurus
        const x = x1; // Atau bisa juga x2, karena keduanya seharusnya sama untuk jalur vertikal

        // Hitung jarak dan jumlah titik
        const distance = Math.abs(y2 - y1);
        const dotCount = Math.floor(distance / 20); // Titik setiap 20px

        for (let i = 0; i <= dotCount; i++) {
            // Posisi titik (pastikan x tetap sama untuk jalur lurus)
            const ratio = i / dotCount;
            const y = y1 + (y2 - y1) * ratio;

            // Buat titik
            const dot = document.createElementNS(svgNS, "circle");
            dot.setAttribute("cx", x);
            dot.setAttribute("cy", y);

            // Tentukan warna dan ukuran berdasarkan status
            if (isCompleted) {
                // Gunakan warna emas hanya jika semua soal selesai
                if (allCompleted && endStatus === 'completed') {
                    dot.setAttribute("r", "4");
                    dot.setAttribute("fill", "#FFD700");
                    dot.setAttribute("class", "map-dot trophy-dot");
                } else {
                    // Gunakan warna hijau untuk soal yang sudah dikerjakan
                    dot.setAttribute("r", "4");
                    dot.setAttribute("fill", "#4CAF50");
                    dot.setAttribute("class", "map-dot completed-dot");
                }
            } else {
                // Titik abu-abu untuk soal yang belum dikerjakan
                dot.setAttribute("r", "3");
                dot.setAttribute("fill", "#adb5bd");
                dot.setAttribute("class", "map-dot locked-dot");
            }

            svg.appendChild(dot);
        }
    }

    // Fungsi untuk membuat belokan tumpul dengan titik-titik
    function createCurvedCorner(svg, x1, y1, x2, y2, cornerType, startStatus, endStatus, allCompleted) {
        const svgNS = "http://www.w3.org/2000/svg";
        const isCompleted = startStatus === 'completed' && (endStatus === 'completed' || endStatus === 'unlocked');

        // Tentukan titik kontrol untuk kurva berdasarkan jenis belokan
        let cx, cy;

        // Perbaikan titik kontrol berdasarkan jenis belokan yang spesifik
        if (cornerType === 'top-right') {
            cx = x2;
            cy = y1;
        } else if (cornerType === 'top-left') {
            cx = x2;
            cy = y1;
        } else if (cornerType === 'bottom-right') {
            cx = x1;
            cy = y2;
        } else if (cornerType === 'bottom-left') {
            cx = x1;
            cy = y2;
        } else {
            cx = (x1 + x2) / 2;
            cy = (y1 + y2) / 2;
        }

        // Buat titik-titik sepanjang kurva
        const steps = 15;
        for (let i = 0; i <= steps; i++) {
            const t = i / steps;
            const x = Math.pow(1 - t, 2) * x1 + 2 * (1 - t) * t * cx + Math.pow(t, 2) * x2;
            const y = Math.pow(1 - t, 2) * y1 + 2 * (1 - t) * t * cy + Math.pow(t, 2) * y2;

            const dot = document.createElementNS(svgNS, "circle");
            dot.setAttribute("cx", x);
            dot.setAttribute("cy", y);

            if (isCompleted) {
                if (allCompleted && endStatus === 'completed') {
                    dot.setAttribute("r", "4");
                    dot.setAttribute("fill", "#FFD700");
                    dot.setAttribute("class", "map-dot trophy-dot");
                } else {
                    dot.setAttribute("r", "4");
                    dot.setAttribute("fill", "#4CAF50");
                    dot.setAttribute("class", "map-dot completed-dot");
                }
            } else {
                dot.setAttribute("r", "3");
                dot.setAttribute("fill", "#adb5bd");
                dot.setAttribute("class", "map-dot locked-dot");
            }

            svg.appendChild(dot);
        }
    }

    // Fungsi untuk membuat jalur titik-titik
    function createDotPath(svg, x1, y1, x2, y2, startStatus, endStatus, allCompleted) {
        const svgNS = "http://www.w3.org/2000/svg";
        const isCompleted = startStatus === 'completed' && (endStatus === 'completed' || endStatus === 'unlocked');

        // Hitung jarak dan jumlah titik
        const distance = Math.sqrt(Math.pow(x2 - x1, 2) + Math.pow(y2 - y1, 2));
        const dotCount = Math.floor(distance / 20); // Titik setiap 20px

        for (let i = 0; i <= dotCount; i++) {
            // Posisi titik
            const ratio = i / dotCount;
            const x = x1 + (x2 - x1) * ratio;
            const y = y1 + (y2 - y1) * ratio;

            // Buat titik
            const dot = document.createElementNS(svgNS, "circle");
            dot.setAttribute("cx", x);
            dot.setAttribute("cy", y);

            // Tentukan warna dan ukuran berdasarkan status
            if (isCompleted) {
                // Gunakan warna emas hanya jika semua soal selesai
                if (allCompleted && endStatus === 'completed') {
                    dot.setAttribute("r", "4");
                    dot.setAttribute("fill", "#FFD700");
                    dot.setAttribute("class", "map-dot trophy-dot");
                } else {
                    // Gunakan warna hijau untuk soal yang sudah dikerjakan
                    dot.setAttribute("r", "4");
                    dot.setAttribute("fill", "#4CAF50");
                    dot.setAttribute("class", "map-dot completed-dot");
                }
            } else {
                // Titik abu-abu untuk soal yang belum dikerjakan
                dot.setAttribute("r", "3");
                dot.setAttribute("fill", "#adb5bd");
                dot.setAttribute("class", "map-dot locked-dot");
            }

            svg.appendChild(dot);
        }
    }

    // Panggil fungsi saat halaman dimuat
    drawTreasureMap();

    // Panggil ulang saat ukuran window berubah
    window.addEventListener('resize', drawTreasureMap);

    // Cek parameter URL untuk scroll
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('scroll') === 'true') {
        // Cari level yang unlocked
        const unlockedLevel = document.querySelector('.unlocked');

        if (unlockedLevel) {
            // Scroll ke elemen dengan delay
            setTimeout(() => {
                unlockedLevel.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Tambahkan efek highlight
                unlockedLevel.style.transition = 'all 0.3s ease';
                unlockedLevel.style.boxShadow = '0 0 20px rgba(33, 150, 243, 0.8)';

                setTimeout(() => {
                    unlockedLevel.style.boxShadow = '';
                }, 2000);
            }, 500);

            // Hapus parameter scroll dari URL
            const newUrl = window.location.pathname +
                window.location.search.replace('scroll=true', '').replace('?&', '?').replace('&&', '&');
            window.history.replaceState({}, '', newUrl);
        }
    }

    // Handle navigation for guest users
    const container = document.querySelector('.level-container');
    const isGuest = container ? container.getAttribute('data-is-guest') === 'true' : false;

    if (isGuest) {
        // For guest users, check local storage for completed questions
        const questionCompleted = localStorage.getItem('questionCompleted');

        if (questionCompleted === 'true') {
            // Clear the flag
            localStorage.removeItem('questionCompleted');

            // Check if scroll=true is in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('scroll') === 'true') {
                // Force redraw of map by calling drawTreasureMap again
                setTimeout(function () {
                    drawTreasureMap();
                }, 100);
            }
        }
    }
});
