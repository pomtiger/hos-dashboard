<script src="https://unpkg.com/lucide@latest"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->

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
    /* ซ่อน scrollbar สำหรับ Legend กราฟวงกลมสิทธิ แต่ยังเลื่อนได้ */
    .custom-scrollbar::-webkit-scrollbar {
        width: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
</style>

<div class="bg-slate-50 min-h-screen p-6 font-sans">
    <?= $this->render('_header', [
            'opdToday' => $opdToday, // ส่งตัวแปรที่จำเป็นไปถ้าต้องใช้
            // ...
    ]) ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6 grid-flow-dense">

        <!-- COMBINED VISIT & DIAG -->
        <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 hover:shadow-2xl transition-all duration-500 group md:col-span-2 lg:col-span-2 flex flex-col relative overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 h-full">
            
                <a href="<?= \yii\helpers\Url::to(['/site/dept-detail']) ?>" class="block group/left relative border-b md:border-b-0 md:border-r border-slate-100 pb-6 md:pb-0 md:pr-8 hover:-translate-y-1 transition-transform h-full">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="font-mitr font-bold text-hos-blue text-base uppercase tracking-tight">Visit All Today</h3>
                            <p class="text-slate-400 text-xs">จำนวนผู้มารับบริการรวมทุกแผนก</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-2xl text-hos-blue group-hover/left:bg-hos-blue group-hover/left:text-white transition-all duration-300 shadow-sm">
                            <i data-lucide="users" class="w-6 h-6"></i> 
                        </div>
                    </div>
                
                    <div class="flex items-baseline gap-2 mb-6">
                        <span class="text-6xl font-black text-slate-800 tracking-tighter"><?= number_format($opdToday) ?></span>
                        <span class="text-slate-400 font-bold text-lg">ราย</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <span class="text-[10px] bg-cyan-50 text-cyan-600 px-3 py-1.5 rounded-xl font-bold border border-cyan-100 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-cyan-500 rounded-full"></span> Admit <?= $ipdNew ?>
                        </span>
                        <span class="text-[10px] bg-rose-50 text-rose-600 px-3 py-1.5 rounded-xl font-bold border border-rose-100 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-rose-500 rounded-full"></span> Refer <?= $ReferToday ?>
                        </span>
                        <span class="text-[10px] bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-xl font-bold border border-emerald-100 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> OPD <?= $opdToday_o ?>
                        </span>
                        <span class="text-[10px] bg-blue-50 text-blue-600 px-3 py-1.5 rounded-xl font-bold border border-blue-100 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span> Authen <?= $opdToday_authen ?>
                        </span>
                    </div>
                </a>

                    <div class="flex flex-col justify-between py-2 space-y-6">
                
                        <div class="group/authen">
                            <div class="flex justify-between items-end mb-2">
                                <div>
                                    <h4 class="font-mitr font-bold text-slate-700 text-sm flex items-center gap-2">
                                        <i data-lucide="shield-check" class="w-4 h-4 text-blue-500"></i> Authen (OPD)
                                    </h4>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-blue-600"><?= number_format($authenPercent, 1) ?>%</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden shadow-inner">
                                <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-full rounded-full transition-all duration-1000" style="width: <?= $authenPercent ?>%"></div>
                            </div>
                        </div>

                        <a href="<?= \yii\helpers\Url::to(['/site/diag-detail']) ?>" class="group/diag block hover:bg-slate-50 p-2 -m-2 rounded-2xl transition-colors">
                            <div class="flex justify-between items-end mb-2">
                                <div>
                                    <h4 class="font-mitr font-bold text-slate-700 text-sm flex items-center gap-2">
                                        <i data-lucide="file-check-2" class="w-4 h-4 text-emerald-500"></i> DIAG (OPD)
                                    </h4>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-emerald-600"><?= number_format($diagPercent, 1) ?>%</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden shadow-inner">
                                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-full rounded-full transition-all duration-1000" style="width: <?= $diagPercent ?>%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-2 text-right italic font-medium">คลิกเพื่อดูรายละเอียดรหัสโรค</p>
                        </a>

                    </div>
            </div>
        </div>
        <!-- COMBINED VISIT & DIAG -->
        
        

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
                    <div class="col-span-2 flex justify-between items-center bg-green-50 p-3 rounded-2xl hover:bg-slate-100 transition-colors border border-transparent hover:border-slate-200">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-5 bg-green-300 rounded-full"></div>
                            <span class="text-[11px] font-bold text-green-500 uppercase">เคสทั่วไป</span>
                        </div>
                        <span class="font-black text-green-700 text-base"><?= number_format($erData['general_case'] ?? 0) ?></span>
                    </div> 
                        
                    <!-- กล่องสั้น -->
                    <div class="flex flex-col justify-center bg-rose-50 p-3 rounded-2xl border border-red-100 hover:bg-red-100/50 transition-colors">
                        <span class="text-[10px] font-bold text-rose-500 mb-1 uppercase">ฉุกเฉิน</span>
                        <span class="font-black text-rose-700 text-xl"><?= number_format($erData['emergency_case'] ?? 0) ?></span>
                    </div>

                    <div class="flex flex-col justify-center bg-orange-50 p-3 rounded-2xl border border-red-100 hover:bg-red-100/50 transition-colors">
                        <span class="text-[10px] font-bold text-orange-500 mb-1 uppercase">อุบัติเหตุ</span>
                        <span class="font-black text-orange-700 text-xl"><?= number_format($erData['accident_case'] ?? 0) ?></span>
                    </div>
                </div>

                <div class="mt-auto pt-2">
                    <a href="<?= \yii\helpers\Url::to(['site/er-detail']) ?>" class="w-full bg-red-600 text-white text-[11px] py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-slate-600 transition-all duration-300 group/btn">
                        <span>คลิกดูรายละเอียดทั้งหมด</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- ER  -->

        <!-- IPD วันนี้ -->
         <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 group flex flex-col h-full min-h-[320px]">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-cyan-600 font-mitr font-bold text-base uppercase tracking-tight">IPD วันนี้</h3>
                    <p class="text-slate-400 text-xs">สรุปข้อมูลผู้ป่วยใน</p>
                </div>
                <div class="bg-cyan-50 p-3 rounded-2xl text-cyan-600 group-hover:bg-cyan-600 group-hover:text-white transition-all duration-300">
                    <i data-lucide="bed" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-indigo-50/40 p-4 rounded-[1.8rem] text-center border border-indigo-100/50 group-hover:bg-indigo-50 transition-colors duration-300">
                    <p class="text-3xl font-black text-indigo-900 tracking-tighter"><?= number_format($ipdNew) ?></p>
                    <div class="flex items-center justify-center gap-1 mt-1">
                        <span class="w-1.5 h-1.5 bg-indigo-400 rounded-full"></span>
                        <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-widest">รับใหม่</p>
                    </div>
                </div>

                <div class="bg-rose-50/40 p-4 rounded-[1.8rem] text-center border border-rose-100/50 group-hover:bg-rose-50 transition-colors duration-300">
                    <p class="text-3xl font-black text-rose-900 tracking-tighter"><?= number_format($ipdDischarge) ?></p>
                    <div class="flex items-center justify-center gap-1 mt-1">
                        <span class="w-1.5 h-1.5 bg-rose-400 rounded-full"></span>
                        <p class="text-[10px] text-rose-500 font-bold uppercase tracking-widest">จำหน่าย</p>
                    </div>
                </div>
            </div>

            <a href="<?= \yii\helpers\Url::to(['/site/ipd-diag-detail']) ?>" class="block mb-6 group/diag">
                <div class="flex justify-between items-center mb-1.5 px-1">
                    <span class="text-[11px] font-bold text-slate-500">DIAG ครบถ้วน</span>
                    <span class="text-[11px] font-black text-cyan-600"><?= $ipdDiagPercent ?? 0 ?>%</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-cyan-500 h-full rounded-full transition-all duration-1000 group-hover/diag:bg-cyan-400" style="width: <?= $ipdDiagPercent ?? 0 ?>%"></div>
                </div>
                <p class="text-[10px] text-slate-400 mt-2 text-center group-hover/diag:text-cyan-600 transition-colors">
                    คลิกดูรายละเอียดรหัสโรค <i data-lucide="chevron-right" class="inline w-3 h-3"></i>
                </p>
            </a>

            <div class="mt-auto">
                <a href="<?= \yii\helpers\Url::to(['site/ward-detail']) ?>" 
                    class="w-full bg-cyan-900 text-white text-xs py-3.5 rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-cyan-600 hover:shadow-lg hover:shadow-cyan-200 transition-all duration-300 group/btn">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 opacity-70"></i>
                        <span>สถานะเตียง Bed Status</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
        <!-- IPD วันนี้ -->

         <!-- แยกตามสิทธิ -->
        <div class="col-span-1 md:col-span-2 lg:col-span-2 bg-white p-5 rounded-[2rem] shadow-lg shadow-slate-200/50 border border-slate-100 hover:shadow-xl transition-all duration-300 flex flex-col h-full max-h-[300px]">          
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-mitr font-bold text-slate-700 text-sm flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-gradient-to-b from-indigo-400 to-indigo-600 rounded-full"></span>สิทธิการรักษา
                    </h3>
                </div>
                <a href="<?= \yii\helpers\Url::to(['/site/pttype-detail']) ?>" class="text-[9px] font-bold text-indigo-500 hover:text-rose-700 bg-indigo-50 hover:bg-rose-100 px-2 py-1 rounded-lg transition-colors flex items-center gap-1">
                    <p class="text-indigo-400 text-[10px] ml-3 font-mitr">PTType</p>
                    <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
                    <div class="bg-indigo-50 p-2 rounded-xl text-indigo-600">
                        <i data-lucide="pie-chart" class="w-4 h-4"></i>
                    </div> 
                </a>
            </div>
            <div class="flex items-center gap-6 h-full overflow-hidden">
                <div class="w-1/2 relative h-[180px]">
                    <canvas id="pttypeChart"></canvas>
                </div>
                <div class="w-1/2 space-y-1 overflow-y-auto custom-scrollbar pr-2 max-h-[180px]"> 
                    <?php 
                    $pttypes = \app\models\DashboardStat::getOpdPttypeStats();
                        if (!empty($pttypes)): 
                        foreach ($pttypes as $index => $pt): ?>
                    <div class="flex items-center justify-between text-[10px] font-mitr p-1.5 rounded-lg hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <span class="w-2 h-2 rounded-full shrink-0" id="legend-color-<?= $index ?>"></span>
                            <span class="text-slate-600 truncate" title="<?= $pt['pttype_name'] ?>"><?= $pt['pttype_name'] ?: 'ไม่ระบุ' ?></span>
                        </div>
                        <span class="font-bold text-slate-800 ml-2 whitespace-nowrap"><?= number_format($pt['total']) ?></span>
                    </div>
                    <?php endforeach; else: ?>
                        <p class="text-center text-slate-400 text-[10px] py-10 italic font-mitr">ไม่มีข้อมูล</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- แยกตามสิทธิ -->


        <!-- REFER COMBINED 2 -->
         <div class="bg-white p-5 rounded-[2.5rem] shadow-lg shadow-slate-200/40 border border-slate-100 hover:shadow-xl transition-all duration-500 group md:col-span-2 lg:col-span-2 flex flex-col h-full relative overflow-hidden">
            <div class="flex justify-between items-center mb-4 w-full px-1">
                <div class="flex items-center gap-3">
                    <div class="bg-slate-50 p-2.5 rounded-2xl text-slate-400 group-hover:bg-slate-800 group-hover:text-white transition-all duration-500">
                        <i data-lucide="arrow-left-right" class="w-5 h-5"></i> 
                    </div>
                    <div>
                        <h3 class="font-mitr font-bold text-slate-800 text-sm leading-tight">Referral Today</h3>
                        <p class="text-slate-400 text-[10px]">สรุปการส่งต่อผู้ป่วยวันนี้</p>
                    </div>
                </div>
                <span class="text-[9px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-bold uppercase tracking-tighter">Real-time</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1">
                
                <div class="bg-slate-50/40 rounded-[1.8rem] p-3 border border-slate-100/50 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3 px-1">
                        <div class="flex items-center gap-2">
                            <i data-lucide="external-link" class="w-3.5 h-3.5 text-rose-500"></i>
                            <h4 class="font-bold text-slate-700 text-[11px] uppercase tracking-wider">Refer Out</h4>
                        </div>
                        <a href="<?= \yii\helpers\Url::to(['site/refer-detail']) ?>" class="text-[9px] font-bold text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2 py-1 rounded-lg transition-colors flex items-center gap-1">
                            ดูทั้งหมด <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <a href="<?= \yii\helpers\Url::to(['site/refer-detail', 'type' => 'OPD']) ?>" class="bg-emerald-50/60 border border-emerald-100 rounded-2xl py-2 text-center hover:bg-emerald-500 group/opd transition-all shadow-sm">
                            <p class="text-[9px] text-emerald-600 group-hover/opd:text-emerald-100 font-bold">OPD</p>
                            <p class="text-xl font-black text-emerald-700 group-hover/opd:text-white leading-none"><?= number_format($opdReferToday ?? 0) ?></p>
                        </a>
                        <a href="<?= \yii\helpers\Url::to(['site/refer-detail', 'type' => 'ER']) ?>" class="bg-rose-50/60 border border-rose-100 rounded-2xl py-2 text-center hover:bg-rose-500 group/er transition-all shadow-sm">
                            <p class="text-[9px] text-rose-600 group-hover/er:text-rose-100 font-bold">ER</p>
                            <p class="text-xl font-black text-rose-700 group-hover/er:text-white leading-none"><?= number_format($erReferToday ?? 0) ?></p>
                        </a>
                        <a href="<?= \yii\helpers\Url::to(['site/refer-detail', 'type' => 'IPD']) ?>" class="bg-purple-50/60 border border-purple-100 rounded-2xl py-2 text-center hover:bg-purple-500 group/ipd transition-all shadow-sm">
                            <p class="text-[9px] text-purple-600 group-hover/ipd:text-purple-100 font-bold">IPD</p>
                            <p class="text-xl font-black text-purple-700 group-hover/ipd:text-white leading-none"><?= number_format($ipdReferToday ?? 0) ?></p>
                        </a>
                    </div>

                    <div class="space-y-1">
                        <?php if(!empty($hospReferToday)): ?>
                            <?php foreach(array_slice($hospReferToday, 0, 2) as $h): ?>
                            <div class="flex justify-between text-[10px] bg-white/80 px-2 py-1.5 rounded-xl border border-slate-100 shadow-sm">
                                <span class="text-slate-500 truncate max-w-[100px]"><?= $h['hosp_name'] ?></span>
                                <span class="font-bold text-slate-700"><?= $h['total'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-slate-50/40 rounded-[1.8rem] p-3 border border-slate-100/50 flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3 px-1">
                        <div class="flex items-center gap-2">
                            <i data-lucide="log-in" class="w-3.5 h-3.5 text-indigo-500"></i>
                            <h4 class="font-bold text-slate-700 text-[11px] uppercase tracking-wider">Refer In</h4>
                        </div>
                        <a href="<?= \yii\helpers\Url::to(['site/refer-in-detail']) ?>" class="text-[9px] font-bold text-indigo-500 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded-lg transition-colors flex items-center gap-1">
                            ดูทั้งหมด <i data-lucide="chevron-right" class="w-2.5 h-2.5"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <a href="<?= \yii\helpers\Url::to(['site/refer-in-detail', 'type' => 'OPD']) ?>" class="bg-emerald-50/60 border border-emerald-100 rounded-2xl py-2 text-center hover:bg-emerald-500 group/opdIn transition-all shadow-sm">
                            <p class="text-[9px] text-emerald-600 group-hover/opdIn:text-emerald-100 font-bold">OPD</p>
                            <p class="text-xl font-black text-emerald-700 group-hover/opdIn:text-white leading-none"><?= number_format($opdReferInToday ?? 0) ?></p>
                        </a>
                        <a href="<?= \yii\helpers\Url::to(['site/refer-in-detail', 'type' => 'ER']) ?>" class="bg-rose-50/60 border border-rose-100 rounded-2xl py-2 text-center hover:bg-rose-500 group/erIn transition-all shadow-sm">
                            <p class="text-[9px] text-rose-600 group-hover/erIn:text-rose-100 font-bold">ER</p>
                            <p class="text-xl font-black text-rose-700 group-hover/erIn:text-white leading-none"><?= number_format($erReferInToday ?? 0) ?></p>
                        </a>
                        <a href="<?= \yii\helpers\Url::to(['site/refer-in-detail', 'type' => 'IPD']) ?>" class="bg-purple-50/60 border border-purple-100 rounded-2xl py-2 text-center hover:bg-purple-500 group/ipdIn transition-all shadow-sm">
                            <p class="text-[9px] text-purple-600 group-hover/ipdIn:text-purple-100 font-bold">IPD</p>
                            <p class="text-xl font-black text-purple-700 group-hover/ipdIn:text-white leading-none"><?= number_format($ipdReferInToday ?? 0) ?></p>
                        </a>
                    </div>

                    <div class="space-y-1">
                        <?php if(!empty($hospReferInToday)): ?>
                            <?php foreach(array_slice($hospReferInToday, 0, 2) as $h): ?>
                            <div class="flex justify-between text-[10px] bg-white/80 px-2 py-1.5 rounded-xl border border-slate-100 shadow-sm">
                                <span class="text-slate-500 truncate max-w-[100px]"><?= $h['hosp_name'] ?></span>
                                <span class="font-bold text-slate-700"><?= $h['total'] ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
        <!-- REFER COMBINED 2 -->

        <!-- Other Clinics (Placeholder) -->
        <div class="col-span-1 md:col-span-2 lg:col-span-2 bg-white p-6 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 hover:shadow-2xl transition-all duration-500 h-full flex flex-col">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-mitr font-bold text-slate-700 text-sm">Today Visit</h3>
                    <p class="text-slate-400 text-[10px]">สถิติผู้มารับบริการแยกตามแผนก</p>
                </div>
                <div class="bg-purple-50 p-2 rounded-lg text-purple-600">
                    <i data-lucide="layout-grid" class="w-5 h-5"></i> 
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3 flex-1">
                <!-- ทันตกรรม (Dental) -->
                <a href="<?= \yii\helpers\Url::to(['site/clinic-diag-detail', 'depcode' => '005']) ?>" class="block flex flex-col justify-center bg-teal-50 p-3 rounded-2xl border border-teal-100 hover:bg-teal-100/50 transition-colors group/box">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-[11px] font-bold text-teal-600">ทันตกรรม</span>
                        <i data-lucide="smile" class="w-4 h-4 text-teal-400 group-hover/box:scale-110 transition-transform"></i>
                    </div>
                    <div class="flex items-end gap-1 mt-auto">
                        <span class="font-black text-teal-700 text-2xl leading-none pt-2"><?= number_format($dentalVisit ?? 0) ?></span>
                        <span class="text-[9px] text-teal-500 font-bold mb-0.5">Visit</span>
                    </div>
                </a>

                <!-- คลินิกใจสบาย (Mental Health) -->
                <a href="<?= \yii\helpers\Url::to(['site/clinic-diag-detail', 'depcode' => '014']) ?>" class="block flex flex-col justify-center bg-fuchsia-50 p-3 rounded-2xl border border-fuchsia-100 hover:bg-fuchsia-100/50 transition-colors group/box">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-[11px] font-bold text-fuchsia-600">คลินิกใจสบาย</span>
                        <i data-lucide="heart-handshake" class="w-4 h-4 text-fuchsia-400 group-hover/box:scale-110 transition-transform"></i>
                    </div>
                    <div class="flex items-end gap-1 mt-auto">
                        <span class="font-black text-fuchsia-700 text-2xl leading-none pt-2"><?= number_format($mentalVisit ?? 0) ?></span>
                        <span class="text-[9px] text-fuchsia-500 font-bold mb-0.5">Visit</span>
                    </div>
                </a>

                <!-- แผนไทย (Thai Med) -->
                <a href="<?= \yii\helpers\Url::to(['site/clinic-diag-detail', 'depcode' => '041']) ?>" class="block flex flex-col justify-center bg-amber-50 p-3 rounded-2xl border border-amber-100 hover:bg-amber-100/50 transition-colors group/box">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-[11px] font-bold text-amber-600">แพทย์แผนไทย</span>
                        <i data-lucide="leaf" class="w-4 h-4 text-amber-400 group-hover/box:scale-110 transition-transform"></i>
                    </div>
                    <div class="flex items-end gap-1 mt-auto">
                        <span class="font-black text-amber-700 text-2xl leading-none pt-2"><?= number_format($thaiMedVisit ?? 0) ?></span>
                        <span class="text-[9px] text-amber-500 font-bold mb-0.5">Visit</span>
                    </div>
                </a>

                <!-- กายภาพบำบัด (Physical Therapy) -->
                <a href="<?= \yii\helpers\Url::to(['site/clinic-diag-detail', 'depcode' => '042']) ?>" class="block flex flex-col justify-center bg-indigo-50 p-3 rounded-2xl border border-indigo-100 hover:bg-indigo-100/50 transition-colors group/box">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-[11px] font-bold text-indigo-600">กายภาพบำบัด</span>
                        <i data-lucide="activity" class="w-4 h-4 text-indigo-400 group-hover/box:scale-110 transition-transform"></i>
                    </div>
                    <div class="flex items-end gap-1 mt-auto">
                        <span class="font-black text-indigo-700 text-2xl leading-none pt-2"><?= number_format($ptVisit ?? 0) ?></span>
                        <span class="text-[9px] text-indigo-500 font-bold mb-0.5">Visit</span>
                    </div>
                </a>

            </div>
        </div>
        <!-- Other Clinics (Placeholder) -->

        <!-- LAB + XRay -->
        <div class="col-span-1 md:col-span-2 lg:col-span-2 bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 hover:shadow-2xl transition-all duration-500 flex flex-col h-full">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="font-mitr font-bold text-slate-700 text-lg flex items-center gap-2">
                        <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                        LAB & XRay Services
                    </h3>
                    <p class="text-slate-400 text-xs ml-4">สรุปการสั่งตรวจ Lab และ X-Ray ประจำวันนี้</p>
                </div>
                <div class="bg-indigo-50 p-3 rounded-2xl text-indigo-600 shadow-sm shadow-indigo-100">
                    <i data-lucide="microscope" class="w-6 h-6"></i> 
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 flex-1">
                <!-- LAB-->
                <div class="bg-gradient-to-br from-blue-50/50 to-white p-5 rounded-[2rem] border border-blue-100/50 shadow-sm group hover:border-blue-300 transition-colors">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-[11px] font-black text-blue-600 uppercase tracking-[0.15em]">LAB Services</span>
                        <i data-lucide="test-tube-2" class="w-4 h-4 text-blue-300 group-hover:rotate-12 transition-transform"></i>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div class="bg-white p-3 rounded-2xl shadow-sm border border-blue-50">
                            <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">ใบสั่งสะสม</p>
                            <a href="<?= \yii\helpers\Url::to(['/site/lab-detail']) ?>" class="hover:opacity-80 transition-opacity">
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-black text-slate-800"><?= number_format($labStats['totalOrder']) ?></span>
                                <span class="text-[10px] text-slate-400 font-bold">VN</span>
                                <i data-lucide="external-link" class="w-3 h-3 text-slate-300 ml-1"></i>
                            </div>
                            </a>
                        </div>
                        <div class="bg-indigo-50/30 p-3 rounded-2xl border border-indigo-100/50">
                            <a href="<?= \yii\helpers\Url::to(['/site/lab-detail']) ?>" class="hover:opacity-80 transition-opacity">
                            <p class="text-[10px] text-indigo-400 font-bold uppercase mb-1">รายการ</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-black text-rose-600"><?= number_format($labStats['totalItems']) ?></span>
                                <span class="text-[10px] text-indigo-400 font-bold">T</span>
                                <i data-lucide="external-link" class="w-3 h-3 text-slate-300 ml-1"></i>
                            </div>
                            </a>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <div class="flex-1 bg-white/80 py-2 px-3 rounded-xl border border-blue-50 flex justify-between items-center">
                            <span class="text-[10px] text-slate-500 font-bold">OPD</span>
                            <span class="text-sm font-black text-green-600"><?= number_format($labStats['opd']) ?></span>
                        </div>
                        <div class="flex-1 bg-white/80 py-2 px-3 rounded-xl border border-blue-50 flex justify-between items-center">
                            <span class="text-[10px] text-slate-500 font-bold">IPD</span>
                            <span class="text-sm font-black text-cyan-500"><?= number_format($labStats['ipd']) ?></span>
                        </div>
                    </div>
                </div>
                <!-- LAB-->

                <!-- XRAY-->
                <div class="bg-gradient-to-br from-slate-50/50 to-white p-5 rounded-[2rem] border border-slate-200/50 shadow-sm group hover:border-slate-400 transition-colors">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-[11px] font-black text-slate-600 uppercase tracking-[0.15em]">X-Ray Orders</span>
                        <i data-lucide="scan-line" class="w-4 h-4 text-slate-400 group-hover:animate-pulse transition-transform"></i>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div class="bg-white p-3 rounded-2xl shadow-sm border border-blue-50">
                            <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">จำนวน</p>
                            <a href="<?= \yii\helpers\Url::to(['/site/xray-detail']) ?>" class="hover:opacity-80 transition-opacity">
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-black text-slate-800"><?= number_format($xrayStats['total']) ?></span>
                                <span class="text-[10px] text-slate-400 font-bold">เคส</span>
                                <i data-lucide="external-link" class="w-3 h-3 text-slate-300 ml-1"></i>
                            </div>
                            </a>
                        </div>
                        <div class="bg-indigo-50/30 p-3 rounded-2xl border border-indigo-100/50">
                            <a href="<?= \yii\helpers\Url::to(['/site/xray-detail']) ?>" class="hover:opacity-80 transition-opacity">
                            <p class="text-[10px] text-indigo-400 font-bold uppercase mb-1">รายการ</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-black text-rose-600"><?= number_format($xrayStats['totalOrder']) ?></span>
                                <span class="text-[10px] text-indigo-400 font-bold">X</span>
                                <i data-lucide="external-link" class="w-3 h-3 text-slate-300 ml-1"></i>
                            </div>
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <div class="flex-1 bg-white/80 py-2 px-3 rounded-xl border border-blue-50 flex justify-between items-center">
                            <span class="text-[10px] text-slate-500 font-bold">OPD</span>
                            <span class="text-sm font-black text-green-600"><?= number_format($xrayStats['opd']) ?></span>
                        </div>
                        <div class="flex-1 bg-white/80 py-2 px-3 rounded-xl border border-blue-50 flex justify-between items-center">
                            <span class="text-[10px] text-slate-500 font-bold">IPD</span>
                            <span class="text-sm font-black text-cyan-500"><?= number_format($xrayStats['ipd']) ?></span>
                        </div>
                    </div>
                </div>
                <!-- XRAY-->
            </div>
        </div>
        <!-- LAB + XRay -->

        <!-- // การใช้ยา มูลค่าการใช้ยา -->
        <div class="col-span-1 md:col-span-2 lg:col-span-4 bg-white p-5 rounded-[2rem] shadow-lg shadow-slate-200/50 border border-slate-100 mt-4 hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-center mb-5">
                <div>
                    <h3 class="font-mitr font-bold text-slate-700 text-base flex items-center gap-2">
                        <span class="w-1.5 h-5 bg-gradient-to-b from-emerald-400 to-emerald-600 rounded-full"></span>
                        Top Drug Usage Today
                    </h3>
                    <p class="text-slate-400 text-[11px] ml-4 font-mitr">สรุปอันดับการใช้ยาและมูลค่าประจำวัน</p>
                </div>
                <div class="bg-emerald-50 p-2.5 rounded-2xl text-emerald-600">
                    <i data-lucide="pill" class="w-5 h-5"></i> 
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="bg-slate-50/50 p-4 rounded-[1.5rem] border border-slate-100/50 relative">
                    <h4 class="text-center text-[11px] font-bold text-slate-500 mb-3 font-mitr uppercase tracking-wider">
                        <i data-lucide="package" class="w-3.5 h-3.5 inline-block mr-1"></i> อันดับจำนวน (Unit)
                    </h4>
                    <div class="h-[320px]"> <canvas id="drugQtyChart"></canvas>
                    </div>
                </div>

                <div class="bg-slate-50/50 p-4 rounded-[1.5rem] border border-slate-100/50 relative">
                    <h4 class="text-center text-[11px] font-bold text-slate-500 mb-3 font-mitr uppercase tracking-wider">
                        <i data-lucide="banknote" class="w-3.5 h-3.5 inline-block mr-1"></i> อันดับมูลค่า (Baht)
                    </h4>
                    <div class="h-[320px]"> <canvas id="drugValueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- // การใช้ยา มูลค่าการใช้ยา -->


    </div>
    <?= $this->render('_footer') ?>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
<script>
    lucide.createIcons();

    // ======== Auto Refresh Logic ========
    const refreshCheckbox = document.getElementById('auto-refresh-checkbox');
    const REFRESH_INTERVAL = 60000; // 60 วินาที (60,000 ms)

    // จำค่าการเปิด/ปิด Auto Refresh จาก Local Storage (ค่า default คือ true)
    const isAutoRefresh = localStorage.getItem('autoRefresh') !== 'false';
    refreshCheckbox.checked = isAutoRefresh;

    // บันทึกค่าสถานะใหม่ลง Local Storage ทันทีที่ผู้ใช้กดติ๊กถูก
    refreshCheckbox.addEventListener('change', (e) => {
        localStorage.setItem('autoRefresh', e.target.checked);
    });

    // เริ่มทำงาน Timer หมุนทุกๆ 60 วินาที
    setInterval(() => {
        // ถ้ายอมให้ Auto Refresh อยู่ ก็ทำการโหลดหน้าใหม่
        if (refreshCheckbox.checked) {
            window.location.reload();
        }
    }, REFRESH_INTERVAL);

    // เพื่อให้เวลา Redirect กลับมาที่หน้าแรกแล้วมีป๊อปอัพเด้งขึ้นมาบอกว่า Logout สำเร็จ
    <?php if (Yii::$app->session->hasFlash('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: '<?= Yii::$app->session->getFlash('success') ?>',
        timer: 2000,
        showConfirmButton: false,
        customClass: {
            popup: 'rounded-[2rem] font-mitr'
        }
    });
    <?php endif; ?>

// เตรียมข้อมูลเปิดกราฟ
const drugQtyData = <?= json_encode($drugStats['qty'] ?? []) ?>;
const drugValueData = <?= json_encode($drugStats['value'] ?? []) ?>;

// สุ่มสีพรีเมียม
function generatePremiumColors(count, hueOffset) {
    let colors = [];
    let hoverColors = [];
    for (let i = 0; i < count; i++) {
        const hue = ((i * 15) + hueOffset) % 360; // ปรับให้สีไล่เฉดกันนุ่มนวลขึ้น
        colors.push(`hsla(${hue}, 75%, 60%, 0.85)`);
        hoverColors.push(`hsla(${hue}, 85%, 50%, 1)`);
    }
    return { colors, hoverColors };
}

function createDrugChart(ctxId, labels, data, labelName, tooltipSuffix, hueOffset) {
    const ctx = document.getElementById(ctxId);
    if (!ctx) return;

    const palette = generatePremiumColors(data.length, hueOffset);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: labelName,
                data: data,
                backgroundColor: palette.colors,
                hoverBackgroundColor: palette.hoverColors,
                borderRadius: 8,
                borderWidth: 0,
                barPercentage: 0.6,
                categoryPercentage: 0.8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.95)',
                    titleFont: { family: 'Mitr', size: 14, weight: 'bold' },
                    bodyFont: { family: 'Mitr', size: 13 },
                    padding: 14,
                    cornerRadius: 12,
                    displayColors: true,
                    callbacks: {
                        label: (context) => ` ${context.dataset.label}: ${context.parsed.x.toLocaleString()} ${tooltipSuffix}`
                    }
                }
            },
            scales: {
                x: { 
                    grid: { display: true, color: '#f1f5f9', drawBorder: false },
                    beginAtZero: true,
                    ticks: {
                        font: { family: 'Mitr', size: 10 },
                        color: '#94a3b8',
                        callback: (value) => value.toLocaleString()
                    }
                },
                y: { 
                    grid: { display: false },
                    ticks: { 
                        font: { family: 'Mitr', size: 10 },
                        color: '#475569',
                        autoSkip: false,
                        callback: function(value) {
                            let label = this.getLabelForValue(value);
                            // ถ้าชื่อยายาวเกิน 30 ตัวอักษร ให้ตัดและใส่ ...
                            return label.length > 30 ? label.substr(0, 30) + '...' : label;
                        }
                    } 
                }
            }
        }
    });
}

// เรียกใช้งานเมื่อ DOM โหลดเสร็จ
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons(); // โหลด Icon เพิ่มเติม

    if (drugQtyData.length > 0) {
        createDrugChart('drugQtyChart', drugQtyData.map(i => i.drugname), drugQtyData.map(i => parseFloat(i.total_qty || 0)), 'จำนวนใช้', 'Unit', 200);
    }
    if (drugValueData.length > 0) {
        createDrugChart('drugValueChart', drugValueData.map(i => i.drugname), drugValueData.map(i => parseFloat(i.total_value || 0)), 'มูลค่ารวม', 'บาท', 150);
    }
});
// ปิดกราฟ

// เริ่มกราฟ Doughnut PTTYPE
document.addEventListener('DOMContentLoaded', () => {
    const pttypeData = <?= json_encode($pttypes ?? []) ?>;
    
    if (pttypeData.length > 0) {
        const labels = pttypeData.map(item => item.pttype_name || 'ไม่ระบุ');
        const counts = pttypeData.map(item => parseInt(item.total));
        
        // ชุดสีพรีเมียม (Indigo, Emerald, Amber, Rose, Sky)
        const colors = [
            '#6366f1', // Indigo
            '#10b981', // Emerald
            '#f59e0b', // Amber
            '#f43f5e', // Rose
            '#0ea5e9'  // Sky
        ];

        // วาดกราฟ
        const ctx = document.getElementById('pttypeChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: colors,
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', // ทำให้วงกลมตรงกลางกว้างขึ้น ดูโมเดิร์น
                layout: {
                    padding: 0 // เคลียร์พื้นที่ว่างรอบกราฟให้เต็มพื้นที่ฝั่งซ้าย
                },
                plugins: {
                    legend: { display: false }, // ปิด Legend มาตรฐานเพื่อใช้ HTML Legend แทน
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { family: 'Mitr', size: 12 },
                        bodyFont: { family: 'Mitr', size: 11 },
                        padding: 10, // ลด padding รอบกราฟ
                        cornerRadius: 8,
                        callbacks: {
                            label: (context) => ` ${context.label}: ${context.raw.toLocaleString()} ราย`
                        }
                    }
                }
            }
        });

        // ใส่สีให้จุดสีใน Legend (HTML)
        pttypeData.forEach((_, index) => {
            const el = document.getElementById(`legend-color-${index}`);
            if (el) el.style.backgroundColor = colors[index % colors.length];
        });
    }
});
// ปิดกราฟ Doughnut PTTYPE
</script>
