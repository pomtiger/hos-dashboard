<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\helpers\Url;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);

$currentRoute = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;

/**
 * ฟังก์ชันกำหนด Style ของเมนู
 */
function menuStyle($route, $currentRoute) {
    $base = "flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 group ";
    return $route === $currentRoute 
        ? $base . "bg-blue-600 text-white shadow-lg shadow-blue-200 font-bold active-menu" 
        : $base . "text-slate-500 hover:bg-white hover:text-blue-600 hover:shadow-sm";
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-full">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <title><?= Html::encode($this->title) ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="shortcut icon" href="<?= Url::to('@web/bch_logo_50.png?v=3') ?>" type="image/png">
    <link rel="icon" href="<?= Url::to('@web/bch_logo_50.png?v=3') ?>" type="image/png">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 'hos-blue': '#005b96' },
                    fontFamily: { 'sans': ['Kanit', 'sans-serif'] }
                }
            }
        }
    </script>

    <?php $this->head() ?>

    <style>
        /* บังคับ Font Kanit และปรับ Smooth Scrolling */
        body {
            font-family: 'Kanit', sans-serif !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .sidebar-blur { 
            background: rgba(255, 255, 255, 0.85); 
            backdrop-filter: blur(15px); 
            -webkit-backdrop-filter: blur(15px);
        }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        /* Animation เล็กน้อยตอนเปลี่ยนหน้า */
        .fade-in { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>

<body class="bg-[#f8fafc] h-full flex overflow-hidden">
<?php $this->beginBody() ?>

    <div class="md:hidden fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md border-b border-slate-100 z-50 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-hos-blue rounded-lg flex items-center justify-center text-white">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
            <span class="text-lg font-black text-slate-800 tracking-tighter uppercase">HOSxP<span class="text-hos-blue">Dash</span></span>
        </div>
        <button onclick="toggleSidebar()" class="p-2 bg-slate-50 rounded-xl text-slate-600 active:scale-90 transition-transform">
            <i data-lucide="menu"></i>
        </button>
    </div>

    <aside id="main-sidebar" class="fixed inset-y-0 left-0 z-[60] w-72 sidebar-blur border-r border-slate-100 flex flex-col transition-transform duration-300 -translate-x-full md:translate-x-0 md:static">
        
        <div class="p-8 hidden md:block">
            <a href="<?= Url::to(['/site/index']) ?>" class="flex items-center gap-3 group">
                <div class="w-11 h-11 bg-hos-blue rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-100 group-hover:rotate-12 transition-transform duration-300">
                    <i data-lucide="activity" class="w-6 h-6"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-2xl font-black text-slate-800 tracking-tighter leading-none">HOSxP<span class="text-hos-blue">DASH</span></span>
                    <span class="text-[10px] text-slate-400 font-bold tracking-[0.2em] uppercase mt-1">Hospital Insight</span>
                </div>
            </a>
        </div>

        <nav class="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar pt-24 md:pt-0">
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.25em] mb-3 mt-4">Main Dashboard</p>
        
            <a href="<?= Url::to(['/site/index']) ?>" class="<?= menuStyle('site/index', $currentRoute) ?>">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>HOME Dash</span>
            </a>

            <a href="<?= Url::to(['/site/ward-detail']) ?>" class="<?= menuStyle('site/ward-detail', $currentRoute) ?>">
                <i data-lucide="bed" class="w-5 h-5"></i>
                <span>WardStatus (IPD)</span>
            </a>

            <?php if (Yii::$app->user->isGuest): ?>
                <div class="px-2 pt-4">
                    <a href="<?= Url::to(['/site/login']) ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-400 border border-dashed border-slate-200 hover:border-blue-300 hover:text-blue-500 transition-all text-sm group">
                        <i data-lucide="lock" class="w-4 h-4 group-hover:animate-bounce"></i>
                        <span>HOSxP-Report (Login)</span>
                    </a>
                </div>
            
            <?php else: ?>
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.25em] mb-3 mt-6">Medical Records</p>

            <div class="relative">
                <button onclick="toggleSubMenu('submenu-report')" 
                        class="w-full flex items-center justify-between px-4 py-3 rounded-2xl transition-all duration-300 text-slate-500 hover:bg-white hover:text-blue-600 hover:shadow-sm group">
                    <div class="flex items-center gap-3">
                        <i data-lucide="file-bar-chart" class="w-5 h-5"></i>
                        <span class="font-medium">HOSxP-Report</span>
                    </div>
                    <i data-lucide="chevron-down" id="arrow-submenu-report" class="w-4 h-4 transition-transform duration-300"></i>
                </button>

                <div id="submenu-report" class="hidden overflow-hidden transition-all duration-300 pl-4 mt-1 space-y-1">
                    <a href="<?= Url::to(['site/report-icd10']) ?>" 
                        class="<?= menuStyle('site/report-icd10', $currentRoute) ?> !py-2 !text-sm">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>ค้นหาตามรหัสโรค OPD</span>
                    </a>
                    <a href="<?= Url::to(['site/report-icd10-ipd']) ?>" 
                        class="<?= menuStyle('site/report-icd10-ipd', $currentRoute) ?> !py-2 !text-sm">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>ค้นหาตามรหัสโรค IPD</span>
                    </a>
                    
                    <!-- <a href="<?= Url::to(['/site/hos-report']) ?>"  -->
                    <a href="#" 
                        class="<?= menuStyle('site/hos-report', $currentRoute) ?> !py-2 !text-sm">
                        <i data-lucide="pie-chart" class="w-4 h-4"></i>
                        <span>คลังรายงานสถิติ</span>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.25em] mt-8 mb-3">System Utilities</p>
            <div class="px-4 py-3 flex items-center gap-3 text-slate-300 select-none">
                <i data-lucide="badge-dollar-sign" class="w-5 h-5"></i>
                <span class="text-sm italic">ระบบบัญชี (Coming Soon)</span>
            </div>
        </nav>

        <div class="p-4 mt-auto">
            <div class="bg-white/60 border border-slate-100 rounded-[2rem] p-4 shadow-sm backdrop-blur-sm">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <div class="flex items-center gap-3 mb-4 px-1">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-hos-blue text-white flex items-center justify-center font-bold shadow-md shadow-blue-100 uppercase">
                                <?= strtoupper(substr(Yii::$app->user->identity->username, 0, 1)) ?>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="text-sm font-bold text-slate-800 truncate"><?= Yii::$app->user->identity->name ?></span>
                            <span class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter">ผู้ดูแลระบบ</span>
                        </div>
                    </div>
                    
                    <?= Html::beginForm(['/site/logout'], 'post') ?>
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 text-xs font-bold text-rose-500 bg-rose-50 hover:bg-rose-500 hover:text-white rounded-xl transition-all duration-300 shadow-sm shadow-rose-50 group">
                            <i data-lucide="log-out" class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform"></i> ออกจากระบบ
                        </button>
                    <?= Html::endForm() ?>
                <?php else: ?>
                    <a href="<?= Url::to(['/site/login']) ?>" class="flex items-center justify-center gap-2 bg-hos-blue text-white py-3 rounded-2xl font-bold text-sm shadow-lg shadow-blue-200 hover:scale-[1.02] active:scale-95 transition-all">
                        <i data-lucide="log-in" class="w-4 h-4"></i> เข้าสู่ระบบ
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto custom-scrollbar relative pt-16 md:pt-0">
        <div id="sidebar-overlay" onclick="toggleSidebar()" class="hidden fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-[55] md:hidden"></div>
        
        <div class="p-6 md:p-10 max-w-7xl mx-auto fade-in">
            <?= $content ?>
        </div>
    </main>

    <script>
        lucide.createIcons();
        // Sub Manu
        function toggleSubMenu(id) {
            const submenu = document.getElementById(id);
            const arrow = document.getElementById('arrow-' + id);
            
            if (submenu.classList.contains('hidden')) {
                // เปิดเมนู
                submenu.classList.remove('hidden');
                submenu.classList.add('block');
                if(arrow) arrow.classList.add('rotate-180');
            } else {
                // ปิดเมนู
                submenu.classList.add('hidden');
                submenu.classList.remove('block');
                if(arrow) arrow.classList.remove('rotate-180');
            }
        }

        // เช็คสถานะตอนโหลดหน้า: ถ้าอยู่ที่หน้าในเมนูย่อย ให้เปิดคลี่ไว้อัตโนมัติ
        document.addEventListener('DOMContentLoaded', function() {
            const currentRoute = '<?= $currentRoute ?>';
            const reportRoutes = ['site/report-icd10', 'site/hos-report'];
            
            if (reportRoutes.includes(currentRoute)) {
                toggleSubMenu('submenu-report');
            }
            
            // lucide.createIcons();
        });

        // Mobile Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('main-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Close sidebar when clicking a link (mobile only)
        document.querySelectorAll('#main-sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) toggleSidebar();
            });
        });
    </script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>