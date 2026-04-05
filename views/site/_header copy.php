<?php
use yii\helpers\Url;
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8 bg-white/50 p-6 rounded-[2rem] border border-white shadow-sm backdrop-blur-md">
    
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-hos-blue rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200 rotate-3">
            <i data-lucide="layout-dashboard" class="text-white w-6 h-6 -rotate-3"></i>
        </div>
        <div>
            <h1 class="font-mitr text-2xl font-black text-slate-800 tracking-tight leading-none">
                Buached <span class="text-hos-blue">DASH</span>
            </h1>
            <div class="flex items-center gap-2 mt-1.5">
                <span class="flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <p class="text-slate-500 text-xs font-medium uppercase tracking-wider">อัปเดตอัตโนมัติทุก 60 วินาที</p>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
        
        <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-2xl border border-slate-100 shadow-sm ml-auto md:ml-0">
            <div class="text-right hidden sm:block">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <div class="text-sm font-bold text-slate-800 leading-none mb-1">
                        <?= Yii::$app->user->identity->name ?>
                    </div>
                    <div class="text-[10px] text-emerald-500 font-bold uppercase tracking-widest flex items-center justify-end gap-1">
                        Online
                    </div>
                <?php else: ?>
                    <div class="text-sm font-bold text-slate-400 leading-none mb-1">Guest User</div>
                    <a href="<?= Url::to(['/site/login']) ?>" class="text-[10px] text-hos-blue font-bold uppercase hover:underline">Sign In Here</a>
                <?php endif; ?>
            </div>
            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center border border-slate-200 overflow-hidden">
                <?php if (!Yii::$app->user->isGuest): ?>
                    <i data-lucide="user-check" class="text-hos-blue w-5 h-5"></i>
                <?php else: ?>
                    <i data-lucide="user" class="text-slate-400 w-5 h-5"></i>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex items-center gap-2 bg-slate-900/5 p-1.5 rounded-2xl border border-slate-200/50">
            <label class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-slate-600 cursor-pointer hover:bg-white rounded-xl transition-all">
                <input type="checkbox" id="auto-refresh-checkbox" checked class="w-4 h-4 rounded text-hos-blue border-slate-300 focus:ring-hos-blue transition-all cursor-pointer">
                <span class="hidden lg:inline">AUTO REFRESH</span>
            </label>
            
            <button onclick="location.reload()" 
                class="flex items-center gap-2 bg-white text-hos-blue px-4 py-2 rounded-xl font-bold shadow-sm border border-slate-200 hover:bg-hos-blue hover:text-white hover:border-hos-blue transition-all active:scale-95 group">
                <i data-lucide="refresh-cw" class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500"></i>
                <span>รีเฟรช</span>
            </button>

            <?php if (!Yii::$app->user->isGuest): ?>
                <form action="<?= Url::to(['/site/logout']) ?>" method="post">
                    <?= yii\helpers\Html::beginForm(['/site/logout'], 'post') ?>
                    <button type="submit" class="p-2.5 text-rose-500 hover:bg-rose-50 rounded-xl transition-colors shadow-sm border border-transparent hover:border-rose-100">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                    </button>
                    <?= yii\helpers\Html::endForm() ?>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>