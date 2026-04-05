<div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group h-full transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
    <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-50 rounded-full opacity-50 group-hover:scale-125 transition-transform duration-700"></div>
    
    <div class="relative h-full flex flex-col">
        <div class="flex justify-between items-start mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-red-200 group-hover:rotate-12 transition-transform">
                    <i data-lucide="ambulance" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="font-mitr font-bold text-red-600 text-base">แผนกฉุกเฉิน (ER)</h3>
                    <p class="text-slate-400 text-[11px] flex items-center gap-1">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                        รวมวันนี้: <span class="font-black text-slate-700 text-sm"><?= number_format($erData['total'] ?? 0) ?></span> ราย
                    </p>
                </div>
            </div>
        </div>

        <a href="<?= \yii\helpers\Url::to(['site/er-detail']) ?>" class="block space-y-3 mb-4">
            <div class="flex justify-between items-center bg-slate-50 p-3 rounded-2xl hover:bg-slate-100 transition-colors">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-6 bg-slate-300 rounded-full"></div>
                    <span class="text-[12px] font-bold text-slate-500">เคสทั่วไป</span>
                </div>
                <span class="font-black text-slate-700 text-base"><?= number_format($erData['general_case'] ?? 0) ?></span>
            </div>

            <div class="flex justify-between items-center bg-red-50 p-3 rounded-2xl border border-red-100 hover:bg-red-100/50 transition-colors">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-6 bg-red-400 rounded-full"></div>
                    <span class="text-[12px] font-bold text-red-600">อุบัติเหตุ</span>
                </div>
                <span class="font-black text-red-700 text-base"><?= number_format($erData['accident_case'] ?? 0) ?></span>
            </div>

            <div class="flex justify-between items-center bg-blue-50 p-3 rounded-2xl border border-blue-100 hover:bg-blue-100/50 transition-colors">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-6 bg-blue-400 rounded-full"></div>
                    <span class="text-[12px] font-bold text-blue-600">UC ต่างจังหวัด</span>
                </div>
                <span class="font-black text-blue-700 text-base"><?= number_format($erData['another_province'] ?? 0) ?></span>
            </div>
        </a>

        <div class="mt-auto pt-2">
            <a href="<?= \yii\helpers\Url::to(['site/er-detail']) ?>" class="w-full bg-slate-900 text-white text-xs py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-red-600 transition-all duration-300">
                <span>ดูรายละเอียดทั้งหมด</span>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</div>