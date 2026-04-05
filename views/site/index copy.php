<script src="https://unpkg.com/lucide@latest"></script>

<style>
    /* ปรับแต่ง Scrollbar สำหรับกล่องแผนก */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    /* สไตล์สำหรับทำให้กล่องในระบบ columns ไม่แยกออกจากกันคนละหน้า */
    .masonry-item {
        break-inside: avoid;
        margin-bottom: 1.5rem;
    }
</style>

<div class="bg-slate-50 min-h-screen p-6 font-sans">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="font-mitr text-2xl font-black text-hos-blue">Buached HOSxP Dashboard</h1>
            <p class="text-slate-500 text-sm">ข้อมูล : โรงพยาบาลบัวเชด อัปเดตอัตโนมัติทุก 60s</p>
        </div>
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" checked class="rounded text-hos-blue"> Auto refresh
            </label>
            <button onclick="location.reload()" class="bg-hos-blue text-white px-6 py-2 rounded-full font-bold shadow-lg shadow-blue-200 hover:scale-105 transition">รีเฟรช</button>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        <!-- Visit All Today -->
        <a href="<?= \yii\helpers\Url::to(['site/dept-detail']) ?>" class="block group h-full">
            <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden h-full">
                <div class="flex justify-between items-start mb-4"><div>
                    <h3 class="font-mitr font-bold text-hos-blue text-sm">Visit All Today</h3>
                    <p class="text-slate-400 text-[10px]">จำนวนผู้มารับบริการทั้งหมด</p>
                </div>
                <div class="bg-blue-50 p-2 rounded-lg text-hos-blue group-hover:bg-hos-blue group-hover:text-white transition-colors">
                    <i data-lucide="users" class="w-5 h-5"></i> 
                </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-black text-slate-800"><?= number_format($opdToday) ?></span>
                    <span class="text-slate-400 font-bold">ราย</span>
                </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="text-[10px] bg-orange-50 text-orange-600 px-2 py-1 rounded-full font-bold">● Admit <?= $ipdNew ?></span>
                        <span class="text-[10px] bg-green-50 text-green-600 px-2 py-1 rounded-full font-bold">● OPD <?= $opdToday_o ?></span>
                        <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-1 rounded-full font-bold">● Authen <?= $opdToday_authen ?></span>
                    </div>
                        <i data-lucide="arrow-right-circle" class="absolute bottom-4 right-6 w-5 h-5 opacity-0 group-hover:opacity-100 transition-opacity text-hos-blue"></i>
            </div>
        </a>
        <!-- Visit All Today -->

        <!-- DIAG (OPD) -->
        <a href="<?= \yii\helpers\Url::to(['site/diag-detail']) ?>" class="block group h-full">
            <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 group-hover:shadow-2xl group-hover:scale-[1.02] transition-all duration-300 h-full">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-mitr font-bold text-green-600 text-sm mb-1">DIAG (OPD) วันนี้</h3>
                        <p class="text-slate-400 text-[10px]">สถานะการลงรหัสวินิจฉัย</p>
                    </div>
                        <div class="bg-green-50 p-2 rounded-lg text-green-600">
                            <i data-lucide="file-check" class="w-5 h-5"></i> 
                        </div>
                </div>
                    <div class="flex items-baseline gap-1 mb-2">
                        <span class="text-4xl font-black text-slate-800"><?= $diagPercent ?></span>
                        <span class="text-slate-400 font-bold text-xl">%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-green-500 h-full" style="width: <?= $diagPercent ?>%"></div>
                    </div>
            </div>
        </a>
        <!-- DIAG (OPD) -->

        

        <!-- REFER OUT-->
        <div class="bg-white p-5 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 hover:shadow-2xl transition-all duration-500 group">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-mitr font-bold text-rose-600 text-sm">REFER OUT</h3>
                    <p class="text-slate-400 text-[10px]">ส่งต่อวันนี้แยกตามแผนก</p>
                </div>
                <div class="bg-rose-50 p-2 rounded-lg text-rose-600">
                    <i data-lucide="external-link" class="w-5 h-5"></i> 
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                <a href="<?= \yii\helpers\Url::to(['site/refer-detail', 'type' => 'OPD']) ?>" class="group/item">
                    <p class="text-[9px] text-slate-400 font-bold">OPD</p>
                    <p class="text-lg font-black text-slate-800"><?= number_format($opdReferToday ?? 0) ?></p>
                    <div class="h-0.5 w-4 bg-green-500 mx-auto rounded-full opacity-0 group-hover/item:opacity-100 transition-opacity"></div>
                </a>
                <a href="<?= \yii\helpers\Url::to(['site/refer-detail', 'type' => 'ER']) ?>" class="group/item">
                    <p class="text-[9px] text-slate-400 font-bold">ER</p>
                    <p class="text-lg font-black text-slate-800"><?= number_format($erReferToday ?? 0) ?></p>
                    <div class="h-0.5 w-4 bg-rose-500 mx-auto rounded-full opacity-0 group-hover/item:opacity-100 transition-opacity"></div>
                </a>
                <a href="<?= \yii\helpers\Url::to(['site/refer-detail', 'type' => 'IPD']) ?>" class="group/item">
                    <p class="text-[9px] text-slate-400 font-bold">IPD</p>
                    <p class="text-lg font-black text-slate-800"><?= number_format($ipdReferToday ?? 0) ?></p>
                    <div class="h-0.5 w-4 bg-blue-500 mx-auto rounded-full opacity-0 group-hover/item:opacity-100 transition-opacity"></div>
                </a>
            </div>

            <div class="pt-3 border-t border-slate-50">
                <h4 class="text-[9px] font-bold text-slate-400 mb-2 uppercase tracking-tight">Top 3 ปลายทาง</h4>
                <div class="space-y-1.5">
                    <?php if(!empty($hospReferToday)): ?>
                        <?php foreach(array_slice($hospReferToday, 0, 3) as $h): ?>
                        <div class="flex justify-between items-center text-[10px]">
                            <span class="text-slate-600 truncate max-w-[100px]"><?= $h['hosp_name'] ?></span>
                            <span class="font-bold text-rose-600"><?= $h['total'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-[10px] text-slate-300 italic text-center">ไม่มีข้อมูลส่งต่อวันนี้</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- REFER OUT-->
        <!-- REFER IN-->
        <div class="bg-white p-5 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 hover:shadow-2xl transition-all duration-500 group">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-mitr font-bold text-rose-600 text-sm">REFER IN</h3>
                    <p class="text-slate-400 text-[10px]">ส่งต่อวันนี้แยกตามแผนก</p>
                </div>
                <div class="bg-rose-50 p-2 rounded-lg text-rose-600">
                    <i data-lucide="external-link" class="w-5 h-5"></i> 
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                <a href="<?= \yii\helpers\Url::to(['site/refer-detail', 'type' => 'OPD']) ?>" class="group/item">
                    <p class="text-[9px] text-slate-400 font-bold">OPD</p>
                    <p class="text-lg font-black text-slate-800"><?= number_format($opdReferToday ?? 0) ?></p>
                    <div class="h-0.5 w-4 bg-green-500 mx-auto rounded-full opacity-0 group-hover/item:opacity-100 transition-opacity"></div>
                </a>
                <a href="<?= \yii\helpers\Url::to(['site/refer-detail', 'type' => 'ER']) ?>" class="group/item">
                    <p class="text-[9px] text-slate-400 font-bold">ER</p>
                    <p class="text-lg font-black text-slate-800"><?= number_format($erReferToday ?? 0) ?></p>
                    <div class="h-0.5 w-4 bg-rose-500 mx-auto rounded-full opacity-0 group-hover/item:opacity-100 transition-opacity"></div>
                </a>
                <a href="<?= \yii\helpers\Url::to(['site/refer-detail', 'type' => 'IPD']) ?>" class="group/item">
                    <p class="text-[9px] text-slate-400 font-bold">IPD</p>
                    <p class="text-lg font-black text-slate-800"><?= number_format($ipdReferToday ?? 0) ?></p>
                    <div class="h-0.5 w-4 bg-blue-500 mx-auto rounded-full opacity-0 group-hover/item:opacity-100 transition-opacity"></div>
                </a>
            </div>

            <div class="pt-3 border-t border-slate-50">
                <h4 class="text-[9px] font-bold text-slate-400 mb-2 uppercase tracking-tight">Top 3 ต้นทาง</h4>
                <div class="space-y-1.5">
                    <?php if(!empty($hospReferToday)): ?>
                        <?php foreach(array_slice($hospReferToday, 0, 3) as $h): ?>
                        <div class="flex justify-between items-center text-[10px]">
                            <span class="text-slate-600 truncate max-w-[100px]"><?= $h['hosp_name'] ?></span>
                            <span class="font-bold text-rose-600"><?= $h['total'] ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-[10px] text-slate-300 italic text-center">ไม่มีข้อมูลส่งต่อวันนี้</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- REFER IN-->
        
   
                
            

            <!-- ER -->
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
                                    วันนี้รวม: <span class="font-black text-slate-700 text-sm"><?= number_format($erData['total'] ?? 0) ?></span> ราย
                                </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <!-- กล่องยาว -->
                    <!-- <div class="col-span-2 flex justify-between items-center bg-slate-50 p-3 rounded-2xl hover:bg-slate-100 transition-colors border border-transparent hover:border-slate-200">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-5 bg-slate-300 rounded-full"></div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase">ฉุกเฉิน</span>
                        </div>
                            <span class="font-black text-slate-700 text-base"><?= number_format($erData['emergency_case'] ?? 0) ?></span>
                    </div> -->
                    <!-- กล่องยาว -->
                    <div class="flex flex-col justify-center bg-rose-50 p-3 rounded-2xl border border-red-100 hover:bg-red-100/50 transition-colors">
                        <span class="text-[10px] font-bold text-rose-500 mb-1 uppercase">ฉุกเฉิน</span>
                        <span class="font-black text-rose-700 text-xl"><?= number_format($erData['emergency_case'] ?? 0) ?></span>
                    </div>

                    <div class="flex flex-col justify-center bg-orange-50 p-3 rounded-2xl border border-red-100 hover:bg-red-100/50 transition-colors">
                        <span class="text-[10px] font-bold text-orange-500 mb-1 uppercase">อุบัติเหตุ</span>
                        <span class="font-black text-orange-700 text-xl"><?= number_format($erData['accident_case'] ?? 0) ?></span>
                    </div>

                    <div class="flex flex-col justify-center bg-green-50 p-3 rounded-2xl border border-blue-100 hover:bg-blue-100/50 transition-colors">
                        <span class="text-[10px] font-bold text-green-500 mb-1 uppercase">เคสทั่วไป</span>
                        <span class="font-black text-green-700 text-xl"><?= number_format($erData['general_case'] ?? 0) ?></span>
                    </div>

                    <div class="flex flex-col justify-center bg-purple-50 p-3 rounded-2xl border border-blue-100 hover:bg-blue-100/50 transition-colors">
                        <span class="text-[10px] font-bold text-purple-500 mb-1 uppercase">UC นอกเขต</span>
                        <span class="font-black text-purple-700 text-xl"><?= number_format($erData['another_province'] ?? 0) ?></span>
                    </div>

                    </div>

                    <div class="mt-auto pt-2">
                        <a href="<?= \yii\helpers\Url::to(['site/er-detail']) ?>" class="w-full bg-slate-900 text-white text-[11px] py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-red-600 transition-all duration-300 group/btn">
                            <span>คลิกดูรายละเอียดทั้งหมด</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
           <!-- ER  -->
           <!-- IPD วันนี้ -->
        <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 group">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-cyan-600 font-bold text-sm">IPD วันนี้</h3>
                    <p class="text-slate-400 text-[10px]">รับใหม่ / จำหน่าย</p>
                </div>
                <div class="bg-cyan-50 p-2 rounded-lg text-cyan-600"><i data-lucide="bed" class="w-5 h-5"></i></div>
            </div>
            <a href="<?= \yii\helpers\Url::to(['/site/ipd-diag-detail']) ?>" class="mt-4 block text-center text-[10px] text-slate-400 hover:text-cyan-600 transition-colors">
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-slate-50 p-2 rounded-2xl text-center border border-slate-100">
                    <p class="text-xl font-black text-slate-800"><?= $ipdNew ?></p>
                    <p class="text-[9px] text-blue-600 font-bold">รับใหม่</p>
                </div>
                <div class="bg-slate-50 p-2 rounded-2xl text-center border border-slate-100">
                    <p class="text-xl font-black text-slate-800"><?= $ipdDischarge ?></p>
                    <p class="text-[9px] text-red-600 font-bold">จำหน่าย</p>
                </div>
            </div>
                DIAG ครบ <?= $ipdDiagPercent ?? 0 ?>% ดูรายละเอียด →
            </a>
                <div class="mt-auto pt-2">
                    <a href="<?= \yii\helpers\Url::to(['site/ward-detail']) ?>" class="w-full bg-slate-900 text-white text-[11px] py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-cyan-600 transition-all duration-300 group/btn">
                        <span>สถานะเตียง Bed Status</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform"></i>
                    </a>
                </div>
        </div>
        <!-- IPD วันนี้ -->

        </div>
    <?= $this->render('_footer') ?>
</div>

<script>
    lucide.createIcons();
</script>