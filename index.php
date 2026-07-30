<?php
// Տվյալների բազայի միացում Railway-ի համար
$host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$port = getenv('MYSQLPORT') ?: '3306';
$db   = getenv('MYSQLDATABASE') ?: 'railway';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: 'PRgbbhmvEJxNPSxgUUQYrOjzqdxJRwNq';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (\PDOException $e) {
    // Եթե կապը խափանվի
    $pdo = null;
}
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelGo - Explore Armenia</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        #map { height: 350px; width: 100%; border-radius: 1rem; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors duration-500">

    <nav class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md sticky top-0 z-50 border-b dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <span class="text-2xl font-black tracking-wider uppercase text-sky-500 italic">TravelGo</span>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="toggleLanguage()" id="lang-btn" class="px-3 py-1.5 text-xs font-black bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-sky-500 hover:text-white transition-all">EN</button>
                <button onclick="toggleDarkMode()" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">🌓</button>
                <a href="admin.php" class="bg-slate-900 dark:bg-sky-500 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-lg hover:opacity-90">Ադմին ⚙️</a>
            </div>
        </div>
    </nav>

    <header class="relative py-20 px-6 text-center bg-gradient-to-b from-sky-500/10 to-transparent">
        <div class="max-w-3xl mx-auto space-y-4">
            <h1 id="hero-title" class="text-4xl md:text-6xl font-black tracking-tight">Բացահայտիր Հայաստանը մեզ հետ</h1>
            <p id="hero-subtitle" class="text-sm md:text-base text-slate-500 dark:text-slate-400">Ընտրեք ձեր երազանքի տուրը և ամրագրեք վայրկյանների ընթացքում:</p>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-12">
        <h2 id="section-tours-title" class="text-2xl font-black mb-8 border-l-4 border-sky-500 pl-3">Առաջարկվող Տուրեր</h2>
        <div id="tours-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            </div>
    </main>

    <div id="booking-modal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 border dark:border-slate-800 p-6 rounded-3xl max-w-lg w-full space-y-4 shadow-2xl relative">
            <button onclick="closeModal()" class="absolute right-4 top-4 text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            <h3 id="modal-title" class="text-lg font-black">Ամրագրել Տուրը</h3>
            
            <div id="modal-tour-details" class="text-xs text-slate-500 dark:text-slate-400 space-y-1"></div>
            <div id="map"></div>

            <form id="booking-form" onsubmit="submitBooking(event)" class="space-y-3 pt-2">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1" id="label-name">Անուն Ազգանուն</label>
                    <input type="text" id="client-name" required class="w-full bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 p-2.5 rounded-xl text-xs outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-slate-400 mb-1" id="label-email">Էլ․ Փոստ (Gmail)</label>
                    <input type="email" id="client-email" required class="w-full bg-slate-50 dark:bg-slate-800 border dark:border-slate-700 p-2.5 rounded-xl text-xs outline-none">
                </div>
                <button type="submit" id="modal-submit-btn" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 rounded-xl text-xs uppercase tracking-wider transition-all shadow-lg shadow-sky-500/25">Հաստատել Ամրագրումը</button>
            </form>
        </div>
    </div>

    <script>
        const translations = {
            hy: {
                heroTitle: "Բացահայտիր Հայաստանը մեզ հետ",
                heroSubtitle: "Ընտրեք ձեր երազանքի տուրը և ամրագրեք վայրկյանների ընթացքում:",
                toursTitle: "Առաջարկվող Տուրեր",
                detailsBtn: "Դիտել Մանրամասները",
                bookBtn: "Ամրագրել Հիմա",
                modalTitle: "Ամրագրել Տուրը",
                labelName: "Անուն Ազգանուն",
                labelEmail: "Էլ․ Փոստ (Gmail)",
                submitBooking: "Հաստատել Ամրագրումը",
                alertBook: "🎉 Հայտը հաջողությամբ ուղարկվեց:"
            },
            en: {
                heroTitle: "Explore Armenia with Us",
                heroSubtitle: "Choose your dream tour and book in seconds.",
                toursTitle: "Featured Tours",
                detailsBtn: "View Details",
                bookBtn: "Book Now",
                modalTitle: "Book Tour",
                labelName: "Full Name",
                labelEmail: "Email Address",
                submitBooking: "Confirm Booking",
                alertBook: "🎉 Booking request sent successfully!"
            }
        };

        let currentLang = localStorage.getItem('lang') || 'hy';
        let allTours = [];
        let selectedTour = null;
        let map = null, marker = null;

        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');

        function toggleLanguage() {
            currentLang = currentLang === 'hy' ? 'en' : 'hy';
            localStorage.setItem('lang', currentLang);
            applyTranslations();
            renderTours();
        }

        function applyTranslations() {
            const t = translations[currentLang];
            document.getElementById('lang-btn').innerText = currentLang === 'hy' ? 'EN' : 'AM';
            document.getElementById('hero-title').innerText = t.heroTitle;
            document.getElementById('hero-subtitle').innerText = t.heroSubtitle;
            document.getElementById('section-tours-title').innerText = t.toursTitle;
            document.getElementById('modal-title').innerText = t.modalTitle;
            document.getElementById('label-name').innerText = t.labelName;
            document.getElementById('label-email').innerText = t.labelEmail;
            document.getElementById('modal-submit-btn').innerText = t.submitBooking;
        }

        function loadTours() {
            fetch('fetch_tours.php')
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    allTours = Array.isArray(data) ? data : [];
                    renderTours();
                })
                .catch(err => {
                    console.error('Տուրերը չբեռնվեցին:', err);
                    const grid = document.getElementById('tours-grid');
                    if (grid) {
                        grid.innerHTML = `<div class="col-span-3 text-center text-red-500 py-16 text-lg font-bold">✕ Տուրեր չեն գտնվել</div>`;
                    }
                });
        }

        function renderTours() {
            const grid = document.getElementById('tours-grid');
            grid.innerHTML = '';
            const t = translations[currentLang];

            if (allTours.length === 0) {
                grid.innerHTML = `<p class="text-slate-400 col-span-3 text-center">Տուրեր դեռ առկա չեն:</p>`;
                return;
            }

            allTours.forEach(tour => {
                const title = tour['title_' + currentLang] || tour.title_hy || "Տուր";
                const desc = tour['description_' + currentLang] || tour.description_hy || "";
                const duration = tour['duration_' + currentLang] || tour.duration_hy || "1 օր";

                grid.innerHTML += `
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-xl transition-all flex flex-col justify-between">
                        <div>
                            <img src="${tour.image_url}" alt="${title}" class="w-full h-48 object-cover">
                            <div class="p-6 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-black uppercase bg-sky-500/10 text-sky-500 px-3 py-1 rounded-full">${duration}</span>
                                    <span class="font-black text-sky-600 dark:text-sky-400">${tour.price} ֏</span>
                                </div>
                                <h3 class="text-lg font-black">${title}</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">${desc}</p>
                            </div>
                        </div>
                        <div class="p-6 pt-0">
                            <button onclick="openModal(${tour.id})" class="w-full bg-slate-900 dark:bg-sky-500 text-white py-3 rounded-xl text-xs font-bold uppercase tracking-wider hover:opacity-90 transition-all">${t.bookBtn}</button>
                        </div>
                    </div>
                `;
            });
        }

        function openModal(id) {
            selectedTour = allTours.find(t => t.id == id);
            if (!selectedTour) return;

            const title = selectedTour['title_' + currentLang] || selectedTour.title_hy;
            document.getElementById('modal-tour-details').innerHTML = `<b>${title}</b> - ${selectedTour.price} ֏`;
            document.getElementById('booking-modal').classList.remove('hidden');

            setTimeout(() => {
                if (!map) {
                    map = L.map('map').setView([selectedTour.latitude || 40.1792, selectedTour.longitude || 44.5152], 10);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                } else {
                    map.invalidateSize();
                    map.setView([selectedTour.latitude || 40.1792, selectedTour.longitude || 44.5152], 10);
                }
                if (marker) map.removeLayer(marker);
                if (selectedTour.latitude && selectedTour.longitude) {
                    marker = L.marker([selectedTour.latitude, selectedTour.longitude]).addTo(map);
                }
            }, 200);
        }

        function closeModal() {
            document.getElementById('booking-modal').classList.add('hidden');
        }

        function submitBooking(e) {
            e.preventDefault();
            const name = document.getElementById('client-name').value;
            const email = document.getElementById('client-email').value;
            const t = translations[currentLang];

            const newBooking = {
                id: Date.now(),
                tour_id: selectedTour ? selectedTour.id : 1,
                tour_title: selectedTour ? (selectedTour['title_' + currentLang] || selectedTour.title_hy) : 'Tour',
                client_name: name,
                client_email: email,
                status: "PENDING",
                created_at: new Date().toLocaleString()
            };

            let existing = JSON.parse(localStorage.getItem('admin_bookings_list')) || [];
            existing.push(newBooking);
            localStorage.setItem('admin_bookings_list', JSON.stringify(existing));

            // Also send to backend
            fetch('book_tour.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(newBooking)
            }).catch(() => {});

            alert(t.alertBook);
            closeModal();
            document.getElementById('booking-form').reset();
        }

        window.onload = () => {
            applyTranslations();
            loadTours();
        };
    </script>
</body>
</html>