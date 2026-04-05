<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\bootstrap5\Html;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$currentRoute = Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;

// ฟังก์ชันเช็คสิทธิ์และสถานะ Active ของเมนู
function menuStyle($route, $currentRoute) {
    $base = "flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 ";
    return $route === $currentRoute 
        ? $base . "bg-blue-600 text-white shadow-lg shadow-blue-200 font-bold active-menu" 
        : $base . "text-slate-500 hover:bg-white hover:text-blue-600 hover:shadow-sm";
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-full">
<head>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- favicon -->
    <link rel="shortcut icon" href="/hos-dashboard/bch_logo_50.png?v=3" type="image/jpeg">
    <link rel="icon" href="/hos-dashboard/bch_logo_50.png?v=3" type="image/jpeg">

    <link rel="shortcut icon" href="<?= Yii::getAlias('@web') ?>/bch_logo_50.png?v=3" type="image/png">
    <link rel="icon" href="<?= Yii::getAlias('@web') ?>/bch_logo_50.png?v=3" type="image/png">
    

    
    
   
    
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 'hos-blue': '#005b96' },
                    fontFamily: { 
                        // กำหนดค่าเริ่มต้นของ Tailwind ให้เป็น Kanit
                        'sans': ['Kanit', 'sans-serif'],
                        'kanit': ['Kanit', 'sans-serif'] 
                    }
                }
            }
        }
    </script>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        /* บังคับใช้ Font Kanit กับทุก Element */
        body, html, * {
            font-family: 'Kanit', sans-serif !important;
        }

        /* ตกแต่งเพิ่มเติม */
        .sidebar-blur { 
            background: rgba(255, 255, 255, 0.8); 
            backdrop-filter: blur(12px); 
            -webkit-backdrop-filter: blur(12px);
        }
       
        .sidebar-blur { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        
    </style>
</head>


<body class="bg-[#f8fafc] h-full flex overflow-hidden">
<?php $this->beginBody() ?>

    <div class="md:hidden fixed top-0 left-0 right-0 bg-white/80 backdrop-blur-md border-b border-slate-100 z-50 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-hos-blue rounded-lg flex items-center justify-center text-white">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
            <span class="text-lg font-black text-slate-800 tracking-tighter">HOSxP<span class="text-hos-blue">DASH</span></span>
        </div>
        <button onclick="document.getElementById('mobile-sidebar').classList.toggle('-translate-x-full')" class="p-2 bg-slate-50 rounded-xl text-slate-600">
            <i data-lucide="menu"></i>
        </button>
    </div>

    <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 z-[60] w-72 sidebar-blur border-r border-slate-100 flex flex-col transition-transform duration-300 -translate-x-full md:translate-x-0 md:static">
        
        <div class="p-8 hidden md:block">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-hos-blue rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-100">
                    <i data-lucide="activity" class="w-6 h-6"></i>
                </div>
                <span class="text-2xl font-black text-slate-800 tracking-tighter">HOSxP<span class="text-hos-blue">DASH</span></span>
            </div>
        </div>

        <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto custom-scrollbar pt-20 md:pt-0">
            <p class="px-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Main Menu</p>
        
            <a href="<?= \yii\helpers\Url::to(['/site/index']) ?>" class="<?= menuStyle('site/index', $currentRoute) ?>">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>HomeDash</span>
            </a>

            <a href="<?= \yii\helpers\Url::to(['/site/ward-detail']) ?>" class="<?= menuStyle('site/ward-detail', $currentRoute) ?>">
                <i data-lucide="bed" class="w-5 h-5"></i>
                <span>Ward Status</span>
            </a>

            <?php if (Yii::$app->user->isGuest): ?>
                <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-400 border border-dashed border-slate-200 hover:border-blue-300 hover:text-blue-500 transition-all">
                    <i data-lucide="lock" class="w-5 h-5"></i>
                    <span class="text-sm">HOSxP Report (Login)</span>
                </a>
            <?php else: ?>
                <a href="<?= \yii\helpers\Url::to(['/site/hos-report']) ?>" class="<?= menuStyle('site/hos-report', $currentRoute) ?>">
                    <i data-lucide="file-bar-chart" class="w-5 h-5"></i>
                    <span>HOSxP Report</span>
                </a>
            <?php endif; ?>

            <?php if (!Yii::$app->user->isGuest): ?>
                <div class="px-6 mb-2 mt-6">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-4 mb-2">Reports</p>
                    <nav class="space-y-1">
                        <a href="<?= \yii\helpers\Url::to(['site/report-icd10']) ?>" class="<?= menuStyle('site/report-icd10', $currentRoute) ?>">
                            <i data-lucide="file-search-2" class="w-5 h-5"></i>
                            <span class="text-sm">รายงานตามรหัสโรค</span>
                        </a>
                        </nav>
                </div>
            <?php endif; ?>

            <p class="px-4 text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-8 mb-4">Utilities</p>
            
            <div class="px-4 py-3 flex items-center gap-3 text-slate-300 grayscale">
                <i data-lucide="badge-dollar-sign" class="w-5 h-5"></i>
                <span class="text-sm font-medium italic">Finance (Soon)</span>
            </div>
        </nav>

        <div class="p-4 mt-auto">
            <div class="bg-white/50 border border-slate-100 rounded-[2rem] p-4 shadow-sm">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <div class="flex items-center gap-3 mb-4 px-2">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-hos-blue text-white flex items-center justify-center font-bold shadow-md">
                            <?= strtoupper(substr(Yii::$app->user->identity->username, 0, 1)) ?>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800 leading-none mb-1"><?= Yii::$app->user->identity->name ?></span>
                            <span class="text-[10px] text-slate-400 uppercase tracking-tighter">Online</span>
                        </div>
                    </div>
                    <?= Html::beginForm(['/site/logout'], 'post')
                        . Html::submitButton(
                            '<i data-lucide="log-out" class="w-4 h-4 mr-2"></i> ออกจากระบบ',
                            ['class' => 'w-full flex items-center justify-center px-4 py-2.5 text-xs font-bold text-rose-500 bg-rose-50 hover:bg-rose-500 hover:text-white rounded-xl transition-all duration-300 shadow-sm shadow-rose-100']
                        )
                        . Html::endForm() ?>
                <?php else: ?>
                    <a href="<?= \yii\helpers\Url::to(['/site/login']) ?>" class="flex items-center justify-center gap-2 bg-hos-blue text-white py-3 rounded-2xl font-bold text-sm shadow-lg shadow-blue-100 hover:scale-[1.02] transition-transform">
                        <i data-lucide="log-in" class="w-4 h-4"></i> เข้าสู่ระบบ
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto custom-scrollbar relative pt-16 md:pt-0">
        <div class="p-6 md:p-10 max-w-7xl mx-auto">
            <?= $content ?>
        </div>
    </main>

    <script>
        lucide.createIcons();
        // ปิด Sidebar อัตโนมัติเมื่อกด Link ในมือถือ
        document.querySelectorAll('#mobile-sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    document.getElementById('mobile-sidebar').classList.add('-translate-x-full');
                }
            });
        });
    </script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>