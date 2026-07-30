<?php
session_start();
?>
<!DOCTYPE html>
<html lang="hy" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelGo - Premium Luxury Tours</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { 
            darkMode: 'class',
            theme: { 
                extend: { 
                    colors: { 
                        brand: { gold: '#D4AF37', blue: '#0F172A', neonBlue: '#38BDF8' } 
                    } 
                } 
            }
        }
    </script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map { 
            height: 200px;
            width: 100%; 
            border-radius: 1rem; 
            z-index: 10; 
            filter: none !important;
        }
        .leaflet-tile {
            filter: none !important;
        }
        .glass { 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px); 
        }
        .dark .glass { 
            background: rgba(15, 23, 42, 0.9);
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 dark:bg-slate-950 dark:text-slate-100 min-h-screen flex flex-col font-sans transition-colors duration-300">

    <nav class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl sticky top-0 z-50 border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-6 flex justify-between h-20 items-center">
            <a href="index.php" class="flex items-center gap-3">
                <img src="https://i.postimg.cc/02XvYg7m/travel-logo.png" alt="Logo" class="h-10 w-auto dark:invert">
                <div class="flex flex-col">
                    <span class="text-2xl font-black tracking-wider uppercase italic leading-none">Travel<span class="text-sky-500">Go</span></span>
                    <span class="text-[9px] font-bold text-slate-400 tracking-widest uppercase">Premium Luxury Tours</span>
                </div>
            </a>
            <div class="flex items-center gap-4">
                <button onclick="toggleLanguage()" id="lang-btn" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-xs font-black hover:bg-sky-500 hover:text-white transition-all">EN</button>
                <button onclick="toggleDarkMode()" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">🌓</button>
                <div id="nav-auth-buttons" class="flex gap-2"></div>
            </div>
        </div>
    </nav>

    <header class="relative overflow-hidden bg-slate-900 text-white py-14 px-6 border-b border-slate-800">
        <div class="absolute inset-0 bg-gradient-to-r from-sky-950/80 via-slate-900/90 to-slate-950/95 z-0"></div>
        <div class="relative z-10 max-w-7xl mx-auto text-center space-y-3">
            <div class="inline-flex items-center gap-2 px-4 py-1 rounded-full bg-sky-500/10 border border-sky-500/30 text-sky-400 text-xs font-bold uppercase tracking-widest mb-1">
                ✨ Luxury Travel Experience
            </div>
            <h1 id="hero-title" class="text-3xl md:text-5xl font-black uppercase tracking-tight">
                Հայաստանի Տեսարժան Վայրերը
            </h1>
            <p id="hero-subtitle" class="text-slate-400 max-w-2xl mx-auto text-xs md:text-sm font-medium">
                Բացահայտեք Հայաստանի պատմամշակութային կոթողները, լեռներն ու հրաշալիքները բարձրակարգ տուրերի միջոցով
            </p>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-12 flex-grow w-full">

        <div class="max-w-5xl mx-auto glass border border-slate-200 dark:border-slate-800 p-8 rounded-3xl mb-12 grid grid-cols-1 md:grid-cols-2 gap-8 items-center shadow-xl">
            
            <div class="flex flex-col justify-center">
                <label id="lbl-search" class="block text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">🔍 Որոնում</label>
                <input 
                    type="text" 
                    id="search-input" 
                    oninput="filterTours()" 
                    placeholder="Փնտրել տուրը (անվանում, նկարագրություն)..." 
                    class="w-full rounded-2xl border border-slate-300 dark:border-slate-700 bg-white/70 dark:bg-slate-900/70 p-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all text-slate-800 dark:text-slate-100 placeholder-slate-400 shadow-inner"
                />
            </div>

            <div class="flex flex-col justify-center">
                <div class="flex justify-between items-center mb-2">
                    <label id="lbl-max-price" class="text-sm font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">💰 Առավելագույն Գին</label>
                    <span id="price-value" class="text-base font-black text-sky-500 bg-sky-500/10 px-3 py-1 rounded-xl border border-sky-500/20">25,000,000 ֏</span>
                </div>
                <input 
                    type="range" 
                    id="price-range" 
                    min="0" 
                    max="25000000" 
                    step="5000" 
                    value="25000000" 
                    oninput="updatePriceLabel(this.value); filterTours();" 
                    class="w-full accent-sky-500 cursor-pointer h-3 bg-slate-200 dark:bg-slate-800 rounded-lg"
                />
            </div>

        </div>

        <div id="tours-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"></div>
    </main>

    <div id="tour-modal" class="fixed inset-0 bg-slate-950/75 z-50 flex items-center justify-center p-4 hidden backdrop-blur-md transition-all duration-300">
        <div class="bg-[#d5dbe0] dark:bg-slate-900 rounded-[2rem] max-w-lg w-full overflow-hidden shadow-2xl max-h-[92vh] flex flex-col border border-slate-300 dark:border-slate-800 text-slate-800 dark:text-slate-100">
            
            <div class="relative h-56 w-full shrink-0">
                <img id="modal-img" src="" alt="Tour Image" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                <h3 id="modal-title" class="absolute bottom-4 left-5 text-2xl font-black text-white tracking-wide drop-shadow-md"></h3>
            </div>

            <div class="p-5 space-y-4 overflow-y-auto flex-1">
                <div class="bg-white dark:bg-slate-800/90 rounded-2xl p-3.5 shadow-sm flex items-center justify-between border border-slate-200/60 dark:border-slate-700">
                    <div class="w-1/2 pr-3">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider flex items-center gap-1">
                            🚨 ՄԵԿՆԱՐԿ
                        </span>
                        <p id="modal-date" class="text-xs font-black text-sky-500 mt-0.5">---</p>
                    </div>
                    <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-700"></div>
                    <div class="w-1/2 pl-4">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider flex items-center gap-1">
                            ⌛ ՏԵՒՈՂՈՒԹՅՈՒՆ
                        </span>
                        <p id="modal-duration" class="text-xs font-black text-slate-700 dark:text-slate-200 mt-0.5">---</p>
                    </div>
                </div>

                <div class="flex items-center gap-1 text-xs font-bold text-emerald-500">
                    <span id="lbl-distance">📍 Հեռավորությունը Ձեզնից` </span>
                    <span id="user-distance"></span>
                </div>

                <p id="modal-desc" class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium px-1"></p>

                <div class="rounded-2xl overflow-hidden border border-slate-300 dark:border-slate-700 shadow-sm">
                    <div id="map"></div>
                </div>

                <div class="space-y-2.5 pt-1">
                    <input type="text" id="client-name" placeholder="Ձեր Անուն Ազգանունը" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 placeholder-slate-400 font-medium focus:ring-2 focus:ring-sky-500 outline-none shadow-sm">
                    <input type="email" id="client-email" placeholder="Ձեր Gmail հասցեն (նամակի համար)" class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 placeholder-slate-400 font-medium focus:ring-2 focus:ring-sky-500 outline-none shadow-sm">
                </div>
            </div>

            <div class="px-5 py-4 bg-slate-200/60 dark:bg-slate-900/80 border-t border-slate-300/80 dark:border-slate-800 flex items-center justify-between shrink-0">
                <div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">ԱՐԺԵՔԸ</span>
                    <span id="modal-price" class="text-2xl font-black text-sky-500">0 ֏</span>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="closeModal()" id="btn-close-modal" class="px-5 py-2.5 bg-slate-300/80 hover:bg-slate-400/80 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-bold transition-all">Փակել</button>
                    <button onclick="submitBooking()" id="btn-submit-booking" class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl text-xs font-black uppercase shadow-lg shadow-sky-500/30 transition-all">Ամրագրել Հիմա</button>
                </div>
            </div>

        </div>
    </div>

<script>
const translations = {
    hy: { 
        lblDistancePrefix: "📍 Հեռավորությունը Ձեզնից` ",
        lblCalc: "Հաշվարկվում է...",
        lblFromYerevan: "(Երևանից)",
        lblNoGps: "GPS հասանելի չէ",
        heroTitle: "Հայաստանի Տեսարժան Վայրերը", 
        heroSubtitle: "Բացահայտեք Հայաստանի պատմամշակութային կոթողները, լեռներն ու հրաշալիքները բարձրակարգ տուրերի միջոցով",
        navReg: "Գրանցվել", 
        navLogin: "Մուտք", 
        logout: "Ելք", 
        more: "Ավելին", 
        alertFields: "Խնդրում ենք լրացնել բոլոր դաշտերը:", 
        alertEmailInvalid: "❌ Խնդրում ենք մուտքագրել ճիշտ Email հասցե (օրինակ՝ name@gmail.com):",
        alertBook: "🎉 Հայտը ուղարկվեց (Սպասման մեջ):", 
        lblName: "Ձեր Անուն Ազգանունը", 
        lblEmail: "Ձեր Email-ը", 
        placeholderName: "Անուն Ազգանուն",
        placeholderEmail: "example@mail.com",
        lblPrice: "Արժեքը",
        btnClose: "Փակել", 
        btnBook: "Ամրագրել",
        lblSearch: "🔍 Որոնում",
        placeholderSearch: "Փնտրել տուրը (անվանում, նկարագրություն)...",
        lblMaxPrice: "💰 Առավելագույն Գին"
    },
    en: { 
        lblDistancePrefix: "📍 Distance from you: ",
        lblCalc: "Calculating...",
        lblFromYerevan: "(from Yerevan)",
        lblNoGps: "GPS unavailable",
        heroTitle: "Armenian Sights & Tours", 
        heroSubtitle: "Discover Armenia's historical monuments, mountains and wonders with premium tours",
        navReg: "Register", 
        navLogin: "Login", 
        logout: "Logout", 
        more: "More Info", 
        alertFields: "Please fill in all fields:", 
        alertEmailInvalid: "❌ Please enter a valid Email address (e.g., name@gmail.com):",
        alertBook: "🎉 Booking sent successfully (Pending):", 
        lblName: "Your Full Name", 
        lblEmail: "Your Email Address", 
        placeholderName: "Full Name",
        placeholderEmail: "example@mail.com",
        lblPrice: "Price",
        btnClose: "Close", 
        btnBook: "Book Now",
        lblSearch: "🔍 Search",
        placeholderSearch: "Search tour (title, description)...",
        lblMaxPrice: "💰 Max Price"
    }
};

let currentLang = localStorage.getItem('lang') || 'hy';
let loadedTours = [], selectedTour = null, map = null;
let currentDistVal = null;
let currentDistType = '';
let currentUser = JSON.parse(localStorage.getItem('active_user')) || null;

function toggleDarkMode() {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}
if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');

function toggleLanguage() {
    currentLang = currentLang === 'hy' ? 'en' : 'hy';
    localStorage.setItem('lang', currentLang);
    applyTranslations();
    filterTours();
}

function applyTranslations() {
    const t = translations[currentLang];
    if (document.getElementById('lang-btn')) document.getElementById('lang-btn').innerText = currentLang === 'hy' ? 'EN' : 'AM';
    if (document.getElementById('hero-title')) document.getElementById('hero-title').innerText = t.heroTitle;
    if (document.getElementById('hero-subtitle')) document.getElementById('hero-subtitle').innerText = t.heroSubtitle;

    if (document.getElementById('lbl-search')) document.getElementById('lbl-search').innerText = t.lblSearch;
    if (document.getElementById('search-input')) document.getElementById('search-input').placeholder = t.placeholderSearch;
    if (document.getElementById('lbl-max-price')) document.getElementById('lbl-max-price').innerText = t.lblMaxPrice;

    if (document.getElementById('lbl-name')) document.getElementById('lbl-name').innerText = t.lblName;
    if (document.getElementById('lbl-email')) document.getElementById('lbl-email').innerText = t.lblEmail;
    if (document.getElementById('client-name')) document.getElementById('client-name').placeholder = t.placeholderName;
    if (document.getElementById('client-email')) document.getElementById('client-email').placeholder = t.placeholderEmail;

    const distanceLabel = document.getElementById('lbl-distance');
    if (distanceLabel) {
        distanceLabel.innerText = t.lblDistancePrefix;
    }

    if (document.getElementById('btn-close-modal')) document.getElementById('btn-close-modal').innerText = t.btnClose;
    if (document.getElementById('btn-submit-booking')) document.getElementById('btn-submit-booking').innerText = t.btnBook;

    if (selectedTour) {
        const title = currentLang === 'hy' ?
            (selectedTour.title_hy || selectedTour.title_en || selectedTour.title) : (selectedTour.title_en || selectedTour.title_hy || selectedTour.title);
        const duration = currentLang === 'hy' ?
            (selectedTour.duration_hy || selectedTour.duration_en || '1 օր') : (selectedTour.duration_en || selectedTour.duration_hy || '1 day');
        const desc = currentLang === 'hy' ?
            (selectedTour.description_hy || selectedTour.description_en || selectedTour.description || selectedTour.desc || 'Նկարագրություն առկա չէ։') : (selectedTour.description_en || selectedTour.description_hy || selectedTour.description || selectedTour.desc || 'No description available.');

        if (document.getElementById('modal-title')) document.getElementById('modal-title').innerText = title;
        if (document.getElementById('modal-duration')) document.getElementById('modal-duration').innerText = duration;
        if (document.getElementById('modal-desc')) document.getElementById('modal-desc').innerText = desc;
    }

    updateDistanceUI();
    updateAuthUI();
}

function updateDistanceUI() {
    const distElem = document.getElementById('user-distance');
    const labelElem = document.getElementById('lbl-distance');
    const t = translations[currentLang];

    if (labelElem) {
        labelElem.innerText = t.lblDistancePrefix ||
            (currentLang === 'hy' ? '📍 Հեռավորությունը Ձեզնից` ' : '📍 Distance from you: ');
    }

    if (!distElem) return;

    const unit = currentLang === 'hy' ? ' կմ' : ' km';
    if (currentDistType === 'calc') {
        distElem.innerText = t.lblCalc;
    } else if (currentDistType === 'gps') {
        distElem.innerText = `${currentDistVal}${unit}`;
    } else if (currentDistType === 'yerevan') {
        distElem.innerText = `~${currentDistVal}${unit} ${t.lblFromYerevan}`;
    } else if (currentDistType === 'nogps') {
        distElem.innerText = t.lblNoGps;
    }
}

function updateAuthUI() {
    const authContainer = document.getElementById('nav-auth-buttons');
    if (!authContainer) return;
    const t = translations[currentLang];

    if (currentUser && currentUser.full_name) {
        authContainer.innerHTML = `
            <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-800 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700">
                <span class="text-xs font-black text-sky-500">👤 ${currentUser.full_name}</span>
                <button onclick="logout()" class="text-xs text-red-500 font-bold ml-2 uppercase hover:underline">${t.logout}</button>
            </div>`;
    } else {
        authContainer.innerHTML = `
            <a href="Reg.php" class="bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 px-4 py-2.5 rounded-xl text-xs font-bold uppercase transition-all flex items-center">${t.navReg}</a>
            <a href="login.php" class="bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase shadow-lg shadow-sky-500/20 transition-all flex items-center">${t.navLogin}</a>`;
    }
}

function loadTours() {
    fetch('fetch_tours.php')
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => { 
            // Ուղղված մաս — fetch_tours.php-ը վերադարձնում է զանգված
            loadedTours = Array.isArray(data) ? data : (data.tours || []); 
            filterTours(); 
        })
        .catch(err => {
            console.error('Տուրերը չբեռնվեցին:', err);
            filterTours();
        });
}

function updatePriceLabel(val) {
    const formatted = Number(val).toLocaleString('hy-AM');
    document.getElementById('price-value').innerText = `${formatted} ֏`;
}

function filterTours() {
    const searchVal = (document.getElementById('search-input')?.value || "").toLowerCase().trim();
    const maxPrice = Number(document.getElementById('price-range')?.value || 25000000);

    const filtered = loadedTours.filter(tour => {
        const matchesPrice = Number(tour.price || 0) <= maxPrice;

        const titleHy = (tour.title_hy || tour.title || "").toLowerCase();
        const titleEn = (tour.title_en || tour.title || "").toLowerCase();
        const descHy = (tour.description_hy || tour.description || tour.desc || "").toLowerCase();
        const descEn = (tour.description_en || tour.description || tour.desc || "").toLowerCase();

        const matchesSearch = searchVal === "" ||
            titleHy.includes(searchVal) ||
            titleEn.includes(searchVal) ||
            descHy.includes(searchVal) ||
            descEn.includes(searchVal);

        return matchesPrice && matchesSearch;
    });

    renderTours(filtered);
}

function renderTours(toursToRender = loadedTours) {
    const container = document.getElementById('tours-container');
    if (!container) return;
    container.innerHTML = '';
    const t = translations[currentLang];

    if (toursToRender.length === 0) {
        container.innerHTML = `
            <div class="col-span-full text-center py-12 text-slate-400 font-bold">
                ❌ Տուրեր չեն գտնվել
            </div>`;
        return;
    }

    toursToRender.forEach(tour => {
        const title = currentLang === 'hy' 
            ? (tour.title_hy || tour.title_en || tour.title || 'Տուր') 
            : (tour.title_en || tour.title_hy || tour.title || 'Tour');

        const desc = currentLang === 'hy' 
            ? (tour.description_hy || tour.description_en || tour.description || tour.desc || 'Նկարագրություն առկա չէ') 
            : (tour.description_en || tour.description_hy || tour.description || tour.desc || 'No description available');

        container.innerHTML += `
            <div class="glass border border-slate-200 dark:border-slate-800 p-5 rounded-3xl space-y-4 flex flex-col justify-between shadow-lg hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300">
                <div class="space-y-3">
                    <div class="relative h-52 w-full overflow-hidden rounded-2xl">
                        <img src="${tour.image_url || 'https://via.placeholder.com/400'}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500">
                    </div>

                    <h3 class="font-black text-xl text-slate-900 dark:text-white line-clamp-1">
                        ${title}
                    </h3>

                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal line-clamp-3">
                        ${desc}
                    </p>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-slate-100 dark:border-slate-800 mt-auto">
                    <span class="text-xl font-black text-sky-500">${Number(tour.price).toLocaleString()} ֏</span>
                    <button onclick="openModal(${tour.id})" class="bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase shadow-md shadow-sky-500/20 transition-all">
                        ${t.more}
                    </button>
                </div>
            </div>`;
    });
}

function calculateDistanceInKm(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return (R * c).toFixed(1);
}

function openModal(id) {
    selectedTour = loadedTours.find(t => Number(t.id) === Number(id));
    if (!selectedTour) return;

    applyTranslations();

    if (document.getElementById('modal-img')) document.getElementById('modal-img').src = selectedTour.image_url || 'https://via.placeholder.com/600';
    if (document.getElementById('modal-date')) document.getElementById('modal-date').innerText = selectedTour.tour_date || '2026-08-15 07:30';
    if (document.getElementById('modal-price')) document.getElementById('modal-price').innerText = `${Number(selectedTour.price).toLocaleString()} ֏`;

    currentDistType = 'calc';
    updateDistanceUI();

    if (currentUser) {
        if (document.getElementById('client-name')) document.getElementById('client-name').value = currentUser.full_name || '';
        if (document.getElementById('client-email')) document.getElementById('client-email').value = currentUser.email || '';
    }

    document.getElementById('tour-modal').classList.remove('hidden');
    const destLat = parseFloat(selectedTour.latitude) || 39.3800;
    const destLng = parseFloat(selectedTour.longitude) || 46.2500;

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                currentDistVal = calculateDistanceInKm(userLat, userLng, destLat, destLng);
                currentDistType = 'gps';
                updateDistanceUI();
            },
            () => {
                const defaultYerevanLat = 40.1792;
                const defaultYerevanLng = 44.5152;
                currentDistVal = calculateDistanceInKm(defaultYerevanLat, defaultYerevanLng, destLat, destLng);
                currentDistType = 'yerevan';
                updateDistanceUI();
            }
        );
    } else {
        currentDistType = 'nogps';
        updateDistanceUI();
    }

    setTimeout(() => {
        if (map) {
            map.remove();
        }

        const title = currentLang === 'hy' ? (selectedTour.title_hy || selectedTour.title_en || selectedTour.title) : (selectedTour.title_en || selectedTour.title_hy || selectedTour.title);

        map = L.map('map', {
            zoomControl: true,
            attributionControl: false
        }).setView([destLat, destLng], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        L.marker([destLat, destLng]).addTo(map).bindPopup(title).openPopup();

        map.invalidateSize();
    }, 250);
}

function closeModal() { 
    document.getElementById('tour-modal').classList.add('hidden');
    selectedTour = null;
}

function logout() {
    localStorage.removeItem('active_user');
    currentUser = null;
    updateAuthUI();
}

function submitBooking() {
    const nameInput = document.getElementById('client-name').value.trim();
    const emailInput = document.getElementById('client-email').value.trim();
    const t = translations[currentLang];

    if (!nameInput || !emailInput) {
        alert(t.alertFields);
        return;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailInput)) {
        alert(t.alertEmailInvalid);
        document.getElementById('client-email').focus();
        return;
    }

    let finalClientName = nameInput;
    if (currentUser && currentUser.full_name) {
        finalClientName = currentUser.full_name;
    }

    const tourTitle = selectedTour ? (currentLang === 'hy' ? (selectedTour.title_hy || selectedTour.title_en || selectedTour.title) : (selectedTour.title_en || selectedTour.title_hy || selectedTour.title)) : "Տուր";

    const newBooking = {
        id: Date.now(),
        client_name: finalClientName,
        client_email: emailInput,
        tour_title: tourTitle,
        price: selectedTour ? selectedTour.price : "0",
        created_at: new Date().toLocaleString(),
        status: "PENDING"
    };

    const existingBookings = JSON.parse(localStorage.getItem('admin_bookings_list')) || [];
    existingBookings.push(newBooking);
    localStorage.setItem('admin_bookings_list', JSON.stringify(existingBookings));

    fetch('book_tour.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            tour_id: selectedTour ? selectedTour.id : 1,
            client_name: finalClientName,
            client_email: emailInput,
            status: 'PENDING'
        })
    }).catch(err => console.log("DB Sync:", err));

    alert(t.alertBook);
    closeModal();
}

applyTranslations();
loadTours();
</script>
</body>
</html>