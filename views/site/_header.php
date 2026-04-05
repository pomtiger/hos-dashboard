<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>


<div class="w-full mb-8">
    <div class="bg-white/70 backdrop-blur-md border border-white shadow-sm rounded-[2.5rem] p-4 md:p-6 flex flex-col lg:flex-row justify-between items-center gap-6">
        
        <div class="flex items-center gap-5 w-full lg:w-auto">
            <div class="flex-shrink-0 w-14 h-14 bg-hos-blue rounded-3xl flex items-center justify-center shadow-xl shadow-blue-100 rotate-3 group-hover:rotate-0 transition-transform duration-500">
                <i data-lucide="activity" class="text-white w-8 h-8"></i>
            </div>
            <div>
                <h1 class="font-mitr text-2xl md:text-3xl font-black text-slate-800 tracking-tight leading-none">
                    Buached <span class="text-hos-blue">HOSxP</span> Dashboard
                </h1>
                <div class="flex items-center gap-2 mt-2">
                    <div class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-[0.15em]">Live Intelligence System</p>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap lg:flex-nowrap items-center gap-4 w-full lg:w-auto justify-between lg:justify-end">
            
            <div class="flex items-center gap-4 bg-slate-50/50 px-5 py-2.5 rounded-[1.5rem] border border-slate-100 shadow-inner">
                <div class="w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100">
                    <i data-lucide="user" class="<?= Yii::$app->user->isGuest ? 'text-slate-300' : 'text-hos-blue' ?> w-6 h-6"></i>
                </div>
                <div class="flex flex-col">
                    <?php if (!Yii::$app->user->isGuest): ?>
                        <span class="text-sm font-black text-slate-700 leading-tight">
                            <?= Yii::$app->user->identity->name ?>
                        </span>
                        <span class="text-[10px] text-emerald-500 font-bold uppercase tracking-wider flex items-center gap-1">
                             Authorized Access
                        </span>
                    <?php else: ?>
                        <span class="text-sm font-bold text-slate-400 leading-tight">Guest Mode</span>
                        <a href="<?= Url::to(['/site/login']) ?>" class="text-[10px] text-hos-blue font-bold uppercase hover:underline">Click to Sign In</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center gap-2 bg-slate-900 px-3 py-2 rounded-[1.5rem] shadow-xl shadow-slate-200">
                <label class="flex items-center gap-2 px-3 py-1.5 cursor-pointer group">
                    <input type="checkbox" id="auto-refresh-checkbox" checked 
                           class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-hos-blue focus:ring-offset-slate-900 transition-all cursor-pointer">
                    <span class="text-[11px] font-black text-slate-400 group-hover:text-white transition-colors tracking-widest">AUTO</span>
                </label>

                <div class="w-[1px] h-6 bg-slate-700 mx-1"></div>

                <button onclick="location.reload()" 
                        class="flex items-center gap-2 px-4 py-1.5 text-white hover:text-hos-blue transition-all group">
                    <i data-lucide="refresh-cw" class="w-4 h-4 group-hover:rotate-180 transition-transform duration-700"></i>
                    <span class="text-[11px] font-black tracking-widest">REFRESH</span>
                </button>
                <?php if (!Yii::$app->user->isGuest): ?>
                    <div class="w-[1px] h-6 bg-slate-700 mx-1"></div>
    
                        <button type="button" onclick="confirmLogout()" class="flex items-center p-1.5 text-rose-400 hover:text-rose-500 transition-colors cursor-pointer">
                            <i data-lucide="power" class="w-5 h-5"></i>
                        </button>

                        <form id="logout-form" action="<?= \yii\helpers\Url::to(['/site/logout']) ?>" method="post" style="display: none;">
                            <?= yii\helpers\Html::beginForm(['/site/logout'], 'post') ?>
                            <?= yii\helpers\Html::endForm() ?>
                        </form>
                <?php endif; ?>
                    <!-- ออกจากระบบโดยไม่ถามก่อน -->
                    <!-- <?php if (!Yii::$app->user->isGuest): ?>
                        <div class="w-[1px] h-6 bg-slate-700 mx-1"></div>
                            <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'inline']) ?>
                            <button type="submit" class="flex items-center p-1.5 text-rose-400 hover:text-rose-500 transition-colors">
                                <i data-lucide="power" class="w-5 h-5"></i>
                            </button>
                            <?= Html::endForm() ?>
                        <?php endif; ?> -->
                </div>

        </div>
    </div>
</div>

<!-- <?php if (!Yii::$app->user->isGuest): ?>
    <div class="w-[1px] h-6 bg-slate-700 mx-1"></div>
    
    <button type="button" onclick="confirmLogout()" class="flex items-center p-1.5 text-rose-400 hover:text-rose-500 transition-colors cursor-pointer">
        <i data-lucide="power" class="w-5 h-5"></i>
    </button>

    <form id="logout-form" action="<?= \yii\helpers\Url::to(['/site/logout']) ?>" method="post" style="display: none;">
        <?= yii\helpers\Html::beginForm(['/site/logout'], 'post') ?>
        <?= yii\helpers\Html::endForm() ?>
    </form>
<?php endif; ?> -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmLogout() {
    Swal.fire({
        title: 'ยืนยันการออกจากระบบ?',
        text: "คุณต้องการออกจากระบบ Dashboard ใช่หรือไม่",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48', // สี rose-600
        cancelButtonColor: '#64748b',  // สี slate-500
        confirmButtonText: 'ใช่, ออกจากระบบ',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            popup: 'rounded-[2rem] font-mitr',
            confirmButton: 'rounded-xl px-6 py-2 font-bold',
            cancelButton: 'rounded-xl px-6 py-2 font-bold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // โชว์ Loading ก่อนไป
            Swal.fire({
                title: 'กำลังออกจากระบบ...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            // ส่ง Form Logout
            document.getElementById('logout-form').submit();
        }
    })
}
</script>