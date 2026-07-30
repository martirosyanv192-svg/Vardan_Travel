<?php
session_start();

// -------------------------------------------------------------
// 1. ԱԴՄԻՆԻ ԳԱՂՏՆԱԲԱՌԸ
// -------------------------------------------------------------
$ADMIN_PASSWORD = '12345'; 

// Ելք համակարգից (Logout)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['admin_logged_in']);
    header('Location: admin.php');
    exit;
}

// Մուտքի (Login) ստուգում
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login_submit'])) {
    $input_password = $_POST['admin_password'] ?? '';
    if ($input_password === $ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $login_error = '❌ Սխալ գաղտնաբառ: Խնդրում ենք փորձել կրկին:';
    }
}

// ԵԹԵ ԱԴՄԻՆԸ ՄՈՒՏՔ ՉԻ ԳՈՐԾԵԼ
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true):
?>
<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelGo - Ադմին Մուտք</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-slate-900/90 border border-slate-800 p-8 rounded-3xl shadow-2xl backdrop-blur-xl space-y-6 relative overflow-hidden">
        <div class="text-center space-y-2 relative z-10">
            <div class="w-16 h-16 bg-sky-500/10 border border-sky-500/30 rounded-2xl flex items-center justify-center mx-auto text-sky-400 text-2xl shadow-lg shadow-sky-500/10">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <h1 class="text-2xl font-black text-white tracking-wide uppercase">Admin Login</h1>
            <p class="text-xs text-slate-400">Մուտքագրեք ադմինիստրատորի գաղտնաբառը</p>
        </div>

        <?php if (!empty($login_error)): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-3 rounded-xl text-xs font-bold text-center">
                <?php echo $login_error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin.php" class="space-y-4 relative z-10">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Գաղտնաբառ</label>
                <div class="relative">
                    <input type="password" name="admin_password" required placeholder="••••••••" class="w-full bg-slate-950/80 border border-slate-700 focus:border-sky-500 text-white px-4 py-3 rounded-xl outline-none text-sm transition-all pl-10">
                    <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-500 text-sm"></i>
                </div>
            </div>
            <button type="submit" name="admin_login_submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3 rounded-xl text-xs uppercase tracking-wider transition-all shadow-lg shadow-sky-500/25">
                Մուտք Պանել
            </button>
        </form>

        <div class="text-center pt-2 relative z-10">
            <a href="index.php" class="text-xs text-slate-500 hover:text-sky-400 transition-colors">← Վերադառնալ Գլխավոր Էջ</a>
        </div>
    </div>
</body>
</html>
<?php 
exit;
endif;
?>

<!DOCTYPE html>
<html lang="hy">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelGo - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script type="text/javascript">
       (function(){
          emailjs.init("3uKWb1VrH8SEnGTCO");
       })();
    </script>

    <script>
        tailwind.config = { darkMode: 'class' };

        function transliterateArmToEng(text) {
            if (!text || typeof text !== 'string') return "";
            const armToEngMap = {
                'Ա': 'A', 'բ': 'b', 'Բ': 'B', 'գ': 'g', 'Գ': 'G', 'դ': 'd', 'Դ': 'D', 'ե': 'e', 'Ե': 'E',
                'զ': 'z', 'Զ': 'Z', 'է': 'e', 'Է': 'E', 'ը': 'e', 'Ը': 'E', 'թ': 'th', 'Թ': 'Th',
                'ժ': 'zh', 'Ժ': 'Zh', 'ի': 'i', 'Ի': 'I', 'լ': 'l', 'Լ': 'L', 'խ': 'kh', 'Խ': 'Kh',
                'ծ': 'ts', 'Ծ': 'Ts', 'կ': 'k', 'Կ': 'K', 'հ': 'h', 'Հ': 'H', 'ձ': 'dz', 'Ձ': 'Dz',
                'ղ': 'gh', 'Ղ': 'Gh', 'ճ': 'ch', 'Ճ': 'Ch', 'մ': 'm', 'Մ': 'M', 'յ': 'y', 'Յ': 'Y',
                'ն': 'n', 'Ն': 'N', 'շ': 'sh', 'Շ': 'Sh', 'ո': 'o', 'Ո': 'O', 'չ': 'ch', 'Չ': 'Ch',
                'պ': 'p', 'Պ': 'P', 'ջ': 'j', 'Ջ': 'J', 'ռ': 'r', 'Ռ': 'R', 'ս': 's', 'Ս': 'S',
                'վ': 'v', 'Վ': 'V', 'տ': 't', 'Տ': 'T', 'ր': 'r', 'Ր': 'R', 'ց': 'ts', 'Ց': 'Ts',
                'ու': 'u', 'Ու': 'U', 'փ': 'ph', 'Փ': 'Ph', 'ք': 'q', 'Ք': 'Q', 'և': 'ev', 'օ': 'o',
                'Օ': 'O', 'ֆ': 'f', 'Ֆ': 'F', 'ա': 'a'
            };
            return text.split('').map(char => armToEngMap[char] || char).join('');
        }
    </script>
    <style>
        #admin-map { 
            height: 220px;
            width: 100%; 
            border-radius: 0.75rem; 
            border: 2px solid #e2e8f0;
            position: relative;
            z-index: 10;
        }
        .dark #admin-map { border-color: #1e293b; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen transition-colors duration-500">

    <nav class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xl sticky top-0 z-50 border-b dark:border-slate-800 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 flex justify-between h-20 items-center">
            <div class="flex items-center gap-3">
                <span id="admin-header-title" class="text-2xl font-black tracking-wider uppercase italic border-l-4 border-sky-500 pl-3">
                    Admin Control Console
                </span>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="toggleLanguage()" id="lang-btn" class="px-3 py-2 text-xs font-black bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-sky-500 hover:text-white transition-all">EN</button>
                <button onclick="toggleDarkMode()" class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">🌓</button>
                <button onclick="goToSite()" id="back-site-btn" class="bg-slate-900 dark:bg-sky-500 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-lg hover:opacity-90">Դեպի Կայք</button>
                <a href="admin.php?action=logout" class="bg-red-500/10 border border-red-500/30 text-red-500 hover:bg-red-500 hover:text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all">Ելք</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="flex border dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl p-1.5 shadow-sm max-w-md mb-8">
            <button id="tab-tours-btn" onclick="switchTab('tours')" class="flex-1 py-3 text-center text-xs font-black bg-slate-900 dark:bg-sky-500 text-white rounded-xl transition-all">🗺️ Տուրեր</button>
            <button id="tab-bookings-btn" onclick="switchTab('bookings')" class="flex-1 py-3 text-center text-xs font-black text-slate-500 rounded-xl transition-all">📩 Հայտեր</button>
            <button id="tab-users-btn" onclick="switchTab('users')" class="flex-1 py-3 text-center text-xs font-black text-slate-500 rounded-xl transition-all">👥 Գրանցվածներ</button>
        </div>

        <div id="section-tours" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 h-fit space-y-4">
                    <h2 id="add-tour-title" class="text-base font-black border-b dark:border-slate-800 pb-3 flex items-center justify-between gap-2">
                        <span id="form-action-title">➕ Ավելացնել Նոր Տուր</span>
                        <button type="button" id="cancel-edit-btn" onclick="resetTourForm()" class="hidden text-xs text-red-500 hover:underline">Չեղարկել</button>
                    </h2>
                    
                    <form id="tour-form" action="save_tour.php" method="POST" class="space-y-4">
                        <input type="hidden" id="edit-tour-id" name="id">

                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase">Անվանում (Հայերեն)</label>
                                <input type="text" id="title-hy" name="title_hy" required class="w-full rounded-xl border dark:border-slate-800 dark:bg-slate-800 p-2 text-xs mt-1">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase">Title (English)</label>
                                <input type="text" id="title-en" name="title_en" required class="w-full rounded-xl border dark:border-slate-800 dark:bg-slate-800 p-2 text-xs mt-1">
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="button" onclick="findCoordinates()" id="find-btn" class="w-full bg-sky-500 text-white py-2 rounded-xl text-xs font-bold hover:bg-sky-600 transition-all">📍 Գտնել Քարտեզի վրա</button>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-3 border p-2.5 rounded-xl dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase">Տևողություն (Հայերեն)</label>
                                <input type="text" id="duration-hy" name="duration_hy" placeholder="1 օր" required class="w-full rounded-xl border dark:border-slate-800 dark:bg-slate-800 p-2 text-xs mt-1">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase">Duration (English)</label>
                                <input type="text" id="duration-en" name="duration_en" placeholder="1 day" required class="w-full rounded-xl border dark:border-slate-800 dark:bg-slate-800 p-2 text-xs mt-1">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label id="label-price" class="block text-[10px] font-bold text-slate-400 uppercase">Գին (֏)</label>
                                <input type="number" id="price" name="price" required class="w-full rounded-xl border dark:border-slate-800 dark:bg-slate-800 p-2 text-xs">
                            </div>
                            <div>
                                <label id="label-spots" class="block text-[10px] font-bold text-slate-400 uppercase">Ընդհանուր Տեղեր</label>
                                <input type="number" id="max-spots" name="spots" required min="1" placeholder="20" class="w-full border dark:border-slate-800 dark:bg-slate-800 p-2 text-xs rounded-xl">
                            </div>
                        </div>

                        <div class="bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-xl border dark:border-slate-800">
                            <label id="label-datetime" class="block text-[10px] font-bold text-slate-400 uppercase">Մեկնարկ</label>
                            <input type="datetime-local" id="tour-datetime" name="tour_date" required class="w-full border dark:border-slate-700 dark:bg-slate-800 p-2 text-xs rounded-lg mt-1">
                        </div>

                        <div class="w-full block">
                            <div id="admin-map"></div>
                        </div>
                        <input type="hidden" id="lat" name="latitude">
                        <input type="hidden" id="lng" name="longitude">

                        <input type="url" id="image" name="image_url" placeholder="Image URL" required class="w-full rounded-xl border dark:border-slate-800 dark:bg-slate-800 p-2 text-xs">

                        <div class="space-y-2">
                            <textarea id="desc-hy" name="description_hy" rows="2" placeholder="Նկարագրություն (Հայերեն)..." required class="w-full rounded-xl border dark:border-slate-800 dark:bg-slate-800 p-2 text-xs"></textarea>
                            <textarea id="desc-en" name="description_en" rows="2" placeholder="Description (English)..." required class="w-full rounded-xl border dark:border-slate-800 dark:bg-slate-800 p-2 text-xs"></textarea>
                        </div>
                        
                        <button type="submit" id="submit-tour-btn" class="w-full bg-slate-900 dark:bg-sky-500 hover:bg-sky-600 text-white font-extrabold py-3 rounded-xl uppercase text-[10px] tracking-wider transition-all">Հրապարակել</button>
                    </form>
                </div>

                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800 lg:col-span-2">
                    <h2 id="active-tours-title" class="text-base font-black border-b dark:border-slate-800 pb-3 mb-4">📋 Ակտիվ Տուրեր</h2>
                    <div class="overflow-x-auto rounded-xl border dark:border-slate-800">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                            <thead class="bg-slate-50 dark:bg-slate-800/50">
                                <tr class="text-left text-xs font-bold text-slate-400 uppercase">
                                    <th class="p-4 th-name">Անվանում</th>
                                    <th class="p-4 th-start">Մեկնարկ</th>
                                    <th class="p-4 th-status">Հաստատված / Ընդհանուր</th>
                                    <th class="p-4 th-action">Գործողություններ</th>
                                </tr>
                            </thead>
                            <tbody id="admin-tours-table" class="divide-y dark:divide-slate-800"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="section-bookings" class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border dark:border-slate-800 hidden">
            <div class="flex justify-between items-center border-b dark:border-slate-800 pb-3 mb-4">
                <h2 id="bookings-title" class="text-base font-black">📩 Հայտեր</h2>
                <button onclick="clearAllBookings()" class="bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white px-3 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1">
                    🗑️ Մաքրել հին test հայտերը
                </button>
            </div>
            <div class="overflow-x-auto rounded-xl border dark:border-slate-800">
                <table class="min-w-full divide-y dark:divide-slate-800 text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr class="text-left text-xs font-bold text-slate-400 uppercase">
                            <th class="p-4 th-tour">Տուր</th>
                            <th class="p-4 th-client">Հաճախորդ</th>
                            <th class="p-4 th-email">Gmail</th>
                            <th class="p-4 th-state">Կարգավիճակ</th>
                            <th class="p-4 th-actions">Կառավարում</th>
                        </tr>
                    </thead>
                    <tbody id="admin-bookings-table" class="divide-y dark:divide-slate-800"></tbody>
                </table>
            </div>
        </div>

        <div id="section-users" class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border dark:border-slate-800 hidden">
            <div class="flex items-center justify-between border-b dark:border-slate-800 pb-3 mb-4">
                <h2 id="users-title" class="text-base font-black">👥 Գրանցված Օգտատերեր</h2>
                <span id="users-count-badge" class="bg-sky-500/10 text-sky-500 text-xs font-black px-3 py-1 rounded-full">0 Օգտատեր</span>
            </div>
            <div class="overflow-x-auto rounded-xl border dark:border-slate-800">
                <table class="min-w-full divide-y dark:divide-slate-800 text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr class="text-left text-xs font-bold text-slate-400 uppercase">
                            <th class="p-4 th-username">Անուն Ազգանուն</th>
                            <th class="p-4 th-useremail">Էլ․ Փոստ (Email)</th>
                            <th class="p-4 th-userdate">Գրանցման Ամսաթիվ</th>
                        </tr>
                    </thead>
                    <tbody id="admin-users-table" class="divide-y dark:divide-slate-800"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const adminTranslations = {
            hy: {
                headerTitle: "Ադմին Պանել", backSite: "Դեպի Կայք 🌐",
                tabTours: "🗺️ Տուրեր", tabBookings: "📩 Հայտեր", tabUsers: "👥 Գրանցվածներ",
                addTour: "➕ Ավելացնել Նոր Տուր", editTourForm: "📝 Խմբագրել Տուրը", findBtn: "📍 Գտնել Քարտեզի վրա",
                labelPrice: "Գին (֏)", labelDatetime: "Մեկնարկ", labelSpots: "Ընդհանուր Տեղեր",
                submitBtn: "Հրապարակել Տուրը 🚀", saveBtn: "Պահպանել Փոփոխությունները 💾",
                activeTours: "📋 Ակտիվ Տուրեր", thName: "Անվանում", thStart: "Մեկնարկ", thStatus: "Հաստատված / Ընդհանուր", thAction: "Գործողություններ",
                bookingsTitle: "📩 Ստացված Հայտեր", thTour: "Տուր", thClient: "Հաճախորդ", thEmail: "Gmail", thState: "Կարգավիճակ", thActions: "Կառավարում",
                deleteBtn: "Ջնջել", editBtn: "Խմբագրել", approveBtn: "Հաստատել", rejectBtn: "Մերժել",
                statusPending: "⏳ Սպասվում է", statusApproved: "🟢 Հաստատված", statusRejected: "🔴 Մերժված",
                alertDel: "Վստա՞հ եք:", published: "🚀 Հրապարակվեց հաջողությամբ:", updated: "💾 Տուրը հաջողությամբ թարմացվեց:",
                usersTitle: "👥 Գրանցված Օգտատերեր", thUsername: "Անուն Ազգանուն", thUseremail: "Էլ․ Փոստ", thUserdate: "Գրանցման Ամսաթիվ"
            },
            en: {
                headerTitle: "Admin Control Console", backSite: "Go to Site 🌐",
                tabTours: "🗺️ Tours", tabBookings: "📩 Bookings", tabUsers: "👥 Users",
                addTour: "➕ Add New Tour", editTourForm: "📝 Edit Tour", findBtn: "📍 Find on Map",
                labelPrice: "Price (֏)", labelDatetime: "Date/Time", labelSpots: "Total Spots",
                submitBtn: "Publish Tour 🚀", saveBtn: "Save Changes 💾",
                activeTours: "📋 Active Tours", thName: "Name", thStart: "Date", thStatus: "Confirmed / Total", thAction: "Actions",
                bookingsTitle: "📩 Received Requests", thTour: "Tour", thClient: "Client", thEmail: "Gmail", thState: "Status", thActions: "Manage",
                deleteBtn: "Delete", editBtn: "Edit", approveBtn: "Approve", rejectBtn: "Reject",
                statusPending: "⏳ Pending", statusApproved: "🟢 Approved", statusRejected: "🔴 Rejected",
                alertDel: "Are you sure?", published: "🚀 Published successfully!", updated: "💾 Tour updated successfully!",
                usersTitle: "👥 Registered Users", thUsername: "Full Name", thUseremail: "Email", thUserdate: "Registered Date"
            }
        };

        let currentLang = localStorage.getItem('lang') || 'hy';
        let adminMap = null, adminMarker = null;
        let allAdminTours = [];
        let currentLoadedBookings = [];

        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');

        function toggleLanguage() {
            currentLang = currentLang === 'hy' ? 'en' : 'hy';
            localStorage.setItem('lang', currentLang);
            applyAdminTranslations();
            renderAdminTours();
            renderBookings();
            renderRegisteredUsers();
        }

        function goToSite() { window.location.href = 'index.php'; }

        function applyAdminTranslations() {
            const t = adminTranslations[currentLang];
            document.getElementById('lang-btn').innerText = currentLang === 'hy' ? 'EN' : 'AM';
            document.getElementById('admin-header-title').innerText = t.headerTitle;
            document.getElementById('back-site-btn').innerText = t.backSite;
            document.getElementById('tab-tours-btn').innerText = t.tabTours;
            document.getElementById('tab-bookings-btn').innerText = t.tabBookings;
            document.getElementById('tab-users-btn').innerText = t.tabUsers;
            
            const isEditing = document.getElementById('edit-tour-id').value !== "";
            document.getElementById('form-action-title').innerText = isEditing ? t.editTourForm : t.addTour;
            document.getElementById('submit-tour-btn').innerText = isEditing ? t.saveBtn : t.submitBtn;

            document.getElementById('find-btn').innerText = t.findBtn;
            document.getElementById('label-price').innerText = t.labelPrice;
            document.getElementById('label-datetime').innerText = t.labelDatetime;
            document.getElementById('label-spots').innerText = t.labelSpots;
            document.getElementById('active-tours-title').innerText = t.activeTours;
            document.getElementById('bookings-title').innerText = t.bookingsTitle;
            document.getElementById('users-title').innerText = t.usersTitle;
        }

        window.onload = () => {
            applyAdminTranslations();
            renderAdminTours();
            
            adminMap = L.map('admin-map').setView([40.1792, 44.5152], 7);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(adminMap);

            adminMap.on('click', function(e) {
                const { lat, lng } = e.latlng;
                document.getElementById('lat').value = lat;
                document.getElementById('lng').value = lng;
                if (adminMarker) adminMap.removeLayer(adminMarker);
                adminMarker = L.marker([lat, lng]).addTo(adminMap);
            });
            setTimeout(() => { adminMap.invalidateSize(); }, 300);
        };

        function switchTab(tab) {
            const toursBtn = document.getElementById('tab-tours-btn');
            const bookingsBtn = document.getElementById('tab-bookings-btn');
            const usersBtn = document.getElementById('tab-users-btn');

            if(tab === 'tours') {
                document.getElementById('section-tours').classList.remove('hidden');
                document.getElementById('section-bookings').classList.add('hidden');
                document.getElementById('section-users').classList.add('hidden');
                toursBtn.classList.add('bg-slate-900', 'dark:bg-sky-500', 'text-white');
                bookingsBtn.classList.remove('bg-slate-900', 'dark:bg-sky-500', 'text-white');
                usersBtn.classList.remove('bg-slate-900', 'dark:bg-sky-500', 'text-white');
                setTimeout(() => adminMap.invalidateSize(), 200);
                renderAdminTours();
            } else if(tab === 'bookings') {
                document.getElementById('section-tours').classList.add('hidden');
                document.getElementById('section-bookings').classList.remove('hidden');
                document.getElementById('section-users').classList.add('hidden');
                bookingsBtn.classList.add('bg-slate-900', 'dark:bg-sky-500', 'text-white');
                toursBtn.classList.remove('bg-slate-900', 'dark:bg-sky-500', 'text-white');
                usersBtn.classList.remove('bg-slate-900', 'dark:bg-sky-500', 'text-white');
                renderBookings();
            } else if(tab === 'users') {
                document.getElementById('section-tours').classList.add('hidden');
                document.getElementById('section-bookings').classList.add('hidden');
                document.getElementById('section-users').classList.remove('hidden');
                usersBtn.classList.add('bg-slate-900', 'dark:bg-sky-500', 'text-white');
                toursBtn.classList.remove('bg-slate-900', 'dark:bg-sky-500', 'text-white');
                bookingsBtn.classList.remove('bg-slate-900', 'dark:bg-sky-500', 'text-white');
                renderRegisteredUsers();
            }
        }

        function findCoordinates() {
            const query = document.getElementById('title-hy').value || document.getElementById('title-en').value;
            if (!query) { alert('Խնդրում ենք լրացնել անվանումը:'); return; }
            
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                .then(res => res.json()) .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);
                        document.getElementById('lat').value = lat;
                        document.getElementById('lng').value = lon;
                        adminMap.setView([lat, lon], 12);
                        if (adminMarker) adminMap.removeLayer(adminMarker);
                        adminMarker = L.marker([lat, lon]).addTo(adminMap);
                    } else {
                        alert('Վայրը չգտնվեց քարտեզի վրա:');
                    }
                });
        }

        function renderAdminTours() {
            fetch('fetch_tours.php')
                .then(res => res.json())
                .then(data => {
                    const tableBody = document.getElementById('admin-tours-table');
                    tableBody.innerHTML = '';
                    const t = adminTranslations[currentLang];
                    
                    // Ճիշտ կարդում ենք տվյալները՝ անկախ նրանից data.tours է, թե direct array
                    if (Array.isArray(data)) {
                        allAdminTours = data;
                    } else if (data && Array.isArray(data.tours)) {
                        allAdminTours = data.tours;
                    } else {
                        allAdminTours = [];
                    }

                    if (allAdminTours.length === 0) {
                        tableBody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-slate-400">Տուրեր չկան:</td></tr>`;
                        return;
                    }

                    const localBookings = currentLoadedBookings.length > 0 ? currentLoadedBookings : (JSON.parse(localStorage.getItem('admin_bookings_list')) || []);

                    allAdminTours.forEach(tour => {
                        const title = tour['title_' + currentLang] || tour.title_hy || tour.title_en || "Untitled";
                        const dateFormatted = tour.tour_date ? tour.tour_date.replace(' ', ' T ') : '---';
                        const totalSpots = tour.spots || 20;

                        const confirmedCount = localBookings.filter(b => {
                            const bTitle = (b.tour_title || b.title || "").trim().toLowerCase();
                            const currentTourTitle = title.trim().toLowerCase();
                            const isStatusConfirmed = String(b.status).toUpperCase() === 'CONFIRMED' || String(b.status).toUpperCase() === 'APPROVED';
                            return isStatusConfirmed && (bTitle === currentTourTitle || b.tour_id == tour.id);
                        }).length;

                        tableBody.innerHTML += `
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800">
                                <td class="p-4 font-black text-xs md:text-sm max-w-[150px] truncate">${title}</td>
                                <td class="p-4 text-[10px] font-mono text-slate-500">${dateFormatted}</td>
                                <td class="p-4">
                                    <div class="flex flex-col">
                                        <span class="bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 text-xs px-2.5 py-1 rounded-full font-extrabold w-fit">
                                            🟢 ${confirmedCount} / ${totalSpots} տեղ
                                        </span>
                                        <span class="text-[10px] text-slate-400 mt-0.5 ml-1 font-semibold">
                                            (Ազատ՝ ${Math.max(0, totalSpots - confirmedCount)})
                                        </span>
                                    </div>
                                </td>
                                <td class="p-4 space-x-3 whitespace-nowrap">
                                    <button onclick="startEditTour(${tour.id})" class="text-sky-500 font-extrabold text-xs hover:underline uppercase">${t.editBtn}</button>
                                    <button onclick="deleteTour(${tour.id})" class="text-red-500 font-extrabold text-xs hover:underline uppercase">${t.deleteBtn}</button>
                                </td>
                            </tr>
                        `;
                    });
                })
                .catch(err => console.log("Tours fetch error:", err));
        }

        function startEditTour(id) {
            let tour = allAdminTours.find(t => Number(t.id) === Number(id));
            const populateForm = (tData) => {
                document.getElementById('edit-tour-id').value = tData.id;
                document.getElementById('title-hy').value = tData.title_hy || "";
                document.getElementById('title-en').value = tData.title_en || "";
                document.getElementById('price').value = tData.price || 0;
                document.getElementById('duration-hy').value = tData.duration_hy || "";
                document.getElementById('duration-en').value = tData.duration_en || "";
                document.getElementById('tour-datetime').value = tData.tour_date ? tData.tour_date.replace(' ', 'T') : '';
                document.getElementById('max-spots').value = tData.spots || 20;
                document.getElementById('lat').value = tData.latitude || "";
                document.getElementById('lng').value = tData.longitude || "";
                document.getElementById('image').value = tData.image_url || "";
                document.getElementById('desc-hy').value = tData.description_hy || "";
                document.getElementById('desc-en').value = tData.description_en || "";

                if (adminMap && tData.latitude && tData.longitude) {
                    const lat = parseFloat(tData.latitude);
                    const lng = parseFloat(tData.longitude);
                    adminMap.setView([lat, lng], 12);
                    if (adminMarker) adminMap.removeLayer(adminMarker);
                    adminMarker = L.marker([lat, lng]).addTo(adminMap);
                    setTimeout(() => { adminMap.invalidateSize(); }, 250);
                }
                document.getElementById('cancel-edit-btn').classList.remove('hidden');
                applyAdminTranslations();
                document.getElementById('tour-form').scrollIntoView({ behavior: 'smooth' });
            };

            if (tour) {
                populateForm(tour);
            } else {
                fetch(`edit_tour.php?id=${id}`)
                    .then(res => res.json())
                    .then(res => { if (res.success && res.data) populateForm(res.data); });
            }
        }

        function resetTourForm() {
            document.getElementById('edit-tour-id').value = "";
            document.getElementById('tour-form').reset();
            document.getElementById('cancel-edit-btn').classList.add('hidden');
            if (adminMarker) adminMap.removeLayer(adminMarker);
            applyAdminTranslations();
        }

        function deleteTour(id) {
            if(confirm(adminTranslations[currentLang].alertDel)) {
                fetch(`delete_tour.php?id=${id}`)
                    .then(() => {
                        renderAdminTours();
                        if (document.getElementById('edit-tour-id').value == id) {
                            resetTourForm();
                        }
                    });
            }
        }

        function renderBookings() {
            const tableBody = document.getElementById('admin-bookings-table');
            if(!tableBody) return;
            tableBody.innerHTML = '';

            fetch('get_bookings.php')
                .then(res => res.json())
                .then(dbBookings => {
                    if (Array.isArray(dbBookings) && dbBookings.length > 0) {
                        drawBookingsTable(dbBookings);
                    } else {
                        let localBookings = JSON.parse(localStorage.getItem('admin_bookings_list')) || [];
                        drawBookingsTable(localBookings);
                    }
                })
                .catch(() => {
                    let localBookings = JSON.parse(localStorage.getItem('admin_bookings_list')) || [];
                    drawBookingsTable(localBookings);
                });
        }

        function drawBookingsTable(bookings) {
            currentLoadedBookings = bookings;
            const tableBody = document.getElementById('admin-bookings-table');
            tableBody.innerHTML = '';
            const t = adminTranslations[currentLang];

            if (bookings.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-slate-400">Հայտեր չկան:</td></tr>`;
                return;
            }

            bookings.forEach(b => {
                const tourTitle = b.tour_title || b.title || "Տուր";
                const clientName = b.client_name || b.name || "Անանուն";
                const clientEmail = b.client_email || b.email || "-";
                
                let statusBadge = `<span class="text-amber-500 font-bold">${t.statusPending}</span>`;
                if (b.status === 'APPROVED' || b.status === 'CONFIRMED') {
                    statusBadge = `<span class="text-emerald-500 font-bold">${t.statusApproved}</span>`;
                } else if (b.status === 'REJECTED') {
                    statusBadge = `<span class="text-rose-500 font-bold">${t.statusRejected}</span>`;
                }

                tableBody.innerHTML += `
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 border-b dark:border-slate-800">
                        <td class="p-4 font-bold text-xs">${tourTitle}</td>
                        <td class="p-4 text-xs font-semibold">${clientName}</td>
                        <td class="p-4 text-xs font-mono text-slate-500">${clientEmail}</td>
                        <td class="p-4 text-xs">${statusBadge}</td>
                        <td class="p-4 space-x-2">
                            <button onclick="updateBookingStatus(${b.id}, 'APPROVED')" class="px-2.5 py-1 bg-emerald-500/10 text-emerald-500 rounded-lg text-xs font-bold hover:bg-emerald-500 hover:text-white transition-all">${t.approveBtn}</button>
                            <button onclick="updateBookingStatus(${b.id}, 'REJECTED')" class="px-2.5 py-1 bg-rose-500/10 text-rose-500 rounded-lg text-xs font-bold hover:bg-rose-500 hover:text-white transition-all">${t.rejectBtn}</button>
                        </td>
                    </tr>
                `;
            });
        }

        function updateBookingStatus(id, newStatus) {
            fetch('update_booking_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, status: newStatus })
            })
            .then(() => {
                let localBookings = JSON.parse(localStorage.getItem('admin_bookings_list')) || [];
                let booking = currentLoadedBookings.find(b => b.id == id) || localBookings.find(b => b.id == id);

                if (booking) {
                    booking.status = newStatus;
                    let idx = localBookings.findIndex(b => b.id == id);
                    if (idx !== -1) localBookings[idx].status = newStatus;
                    else localBookings.push(booking);
                    
                    localStorage.setItem('admin_bookings_list', JSON.stringify(localBookings));
                    
                    sendEmailNotification(booking, newStatus);
                }

                renderBookings();
                renderAdminTours();
            });
        }

        function sendEmailNotification(booking, status) {
            const clientEmail = booking.client_email || booking.email;
            const clientName = booking.client_name || booking.name || "Հաճախորդ";
            const tourTitle = booking.tour_title || booking.title || "Տուր";

            if (!clientEmail || clientEmail === '-') {
                alert("Հաճախորդի էլ․ փոստը բացակայում է, նամակ չուղարկվեց։");
                return;
            }

            const statusText = status === 'APPROVED' ? 'ՀԱՍՏԱՏՎԱԾ Է 🟢' : 'ՄԵՐԺՎԱԾ Է 🔴';
            const messageText = status === 'APPROVED' 
                ? `Ուրախ ենք տեղեկացնել, որ «${tourTitle}» տուրի Ձեր ամրագրումը հաջողությամբ հաստատվել է։`
                : `Ցավոք, «${tourTitle}» տուրի Ձեր ամրագրման հայտը մերժվել է։`;

            const templateParams = {
                to_name: clientName,
                to_email: clientEmail,
                tour_name: tourTitle,
                booking_status: statusText,
                message: messageText
            };

            emailjs.send("service_5roj4kc", "template_mqpg89r", templateParams)
                .then(function(response) {
                    alert(`Նամակը հաջողությամբ ուղարկվեց ${clientEmail} հասցեին:`);
                }, function(error) {
                    alert("Խափանում նամակն ուղարկելիս։ Ստուգեք EmailJS-ի կարգավորումները։");
                });
        }

        function clearAllBookings() {
            if (confirm("Վստա՞հ եք, որ ուզում եք մաքրել բոլոր հայտերը (Բազայից և LocalStorage-ից):")) {
                fetch('clear_bookings.php')
                    .then(() => {
                        localStorage.removeItem('admin_bookings_list');
                        renderBookings();
                        renderAdminTours();
                    });
            }
        }

        function renderRegisteredUsers() {
            fetch('get_users.php')
                .then(res => res.json())
                .then(users => {
                    if (Array.isArray(users) && users.length > 0) {
                        drawUsersTable(users);
                    } else {
                        let users = JSON.parse(localStorage.getItem('registered_users_list')) || [];
                        drawUsersTable(users);
                    }
                })
                .catch(() => {
                    let users = JSON.parse(localStorage.getItem('registered_users_list')) || [];
                    drawUsersTable(users);
                });
        }

        function drawUsersTable(users) {
            const tableBody = document.getElementById('admin-users-table');
            if (!tableBody) return;
            tableBody.innerHTML = '';
            document.getElementById('users-count-badge').innerText = `${users.length} Օգտատեր`;

            users.forEach(u => {
                const rawName = u.full_name || u.name || u.email || "Օգտատեր";
                const displayName = currentLang === 'en' ? transliterateArmToEng(rawName) : rawName;
                const regDate = u.created_at || u.reg_date || new Date().toLocaleDateString();

                tableBody.innerHTML += `
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 border-b dark:border-slate-800">
                        <td class="p-4 font-black text-xs">${displayName}</td>
                        <td class="p-4 text-xs font-mono text-slate-500">${u.email}</td>
                        <td class="p-4 text-xs text-slate-400 font-bold">${regDate}</td>
                    </tr>
                `;
            });
        }
    </script>
</body>
</html>