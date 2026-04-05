<?php
/** @var array $diagStats */
/** @var array $labels */
/** @var array $data */
/** @var string $start_date */
/** @var string $end_date */
/** @var string $type_name */

$this->title = 'รายละเอียดการส่งต่อผู้ป่วย (Refer In)';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<div class="bg-slate-50 min-h-screen p-8 font-sans">
    <div class="max-w-6xl mx-auto">
        
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 mb-8">
            <form method="get" action="<?= \yii\helpers\Url::to(['/site/refer-in-detail']) ?>" class="flex flex-wrap items-end gap-4">
                <input type="hidden" name="r" value="site/refer-in-detail">
                <input type="hidden" name="type" value="<?= Yii::$app->request->get('type') ?>">
                
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-widest">ช่วงวันที่ Refer In</label>
                    <div class="relative">
                        <input type="text" id="date-range" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-slate-700 font-bold focus:ring-2 focus:ring-rose-500" placeholder="เลือกช่วงวันที่...">
                        <input type="hidden" id="start_date" name="start_date" value="<?= $start_date ?>">
                        <input type="hidden" id="end_date" name="end_date" value="<?= $end_date ?>">
                    </div>
                </div>
                <button type="submit" class="bg-rose-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-rose-700 transition">
                    ประมวลผลข้อมูล
                </button>
            </form>
        </div>

        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-4">
                <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="bg-white p-2 rounded-full shadow-sm hover:bg-slate-100 transition border border-slate-100">
                    <i data-lucide="arrow-left" class="w-6 h-6 text-slate-600"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-rose-600">10 อันดับโรค Refer In</h1>
                    <p class="text-slate-400 font-bold text-sm">จุดบริการ: <span class="text-slate-700"><?= $type_name ?></span></p>
                </div>
            </div>
            
            <div class="flex gap-2">
                <button onclick="exportToExcel()" class="flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-xl font-bold shadow-lg hover:bg-emerald-700 transition text-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Excel
                </button>
                <button onclick="exportToPDF()" class="flex items-center gap-2 bg-rose-500 text-white px-4 py-2 rounded-xl font-bold shadow-lg hover:bg-rose-600 transition text-sm">
                    <i data-lucide="file-text" class="w-4 h-4"></i> PDF
                </button>
            </div>
        </div>

        <div id="pdf-area" class="space-y-8">
            <div class="bg-white rounded-[2rem] shadow-xl p-8 border border-slate-100">
                <h2 class="text-lg font-bold text-slate-700 mb-6 italic">
                    ICD10 Refer Stat (<?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?>)
                </h2>
                <div class="relative h-[350px]">
                    <canvas id="referChart"></canvas>
                </div>
            </div>


            <div class="bg-white rounded-[2rem] shadow-xl p-8 border border-slate-100 overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-lg font-bold text-slate-700 mb-6 italic">
                        <!-- <h2 class="text-xl font-black text-slate-800 flex items-center gap-2"> -->
                            <span class="w-2 h-8 bg-rose-500 rounded-full"></span>
                            ตารางรายการโรคที่รับมากที่สุด
                        </h2>
                        <p class="text-slate-400 font-medium text-sm mt-1 ml-4">
                            จุดบริการ: <span class="text-rose-600 font-bold"><?= $type_name ?></span> <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?>
                        </p>
                </div>
                    <div class="bg-slate-50 px-4 py-2 rounded-2xl border border-slate-100">
                        <span class="text-xs text-slate-400 uppercase tracking-widest font-bold">Total Cases</span>
                        <p class="text-xl font-black text-slate-700 leading-none">
                            <?php 
                                $totalAll = array_sum(array_column($diagStats, 'total'));
                                echo number_format($totalAll);
                            ?>
                        </p>
                    </div>
            </div>
    
            <div class="overflow-x-auto">
                <table id="referTable" class="w-full border-separate border-spacing-y-2">
                    <thead>
                        <tr class="text-left uppercase text-[10px] tracking-[0.2em] text-slate-400 font-black">
                            <th class="pb-4 pl-6">ICD10 Code</th>
                            <th class="pb-4">Diagnosis Name</th>
                            <th class="pb-4 text-right pr-6">Frequency</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($diagStats as $index => $row): 
                            // คำนวณ % เพื่อทำแถบความกว้างด้านล่าง
                            $maxTotal = !empty($diagStats) ? $diagStats[0]['total'] : 1;
                            $percent = ($row['total'] / $maxTotal) * 100;
                        ?>
                        <tr class="group hover:translate-x-1 transition-all duration-300">
                            <td class="py-4 pl-6 bg-slate-50 group-hover:bg-rose-50 rounded-l-2xl transition-colors">
                                <span class="inline-block px-3 py-1 bg-white border border-slate-200 text-rose-600 font-bold rounded-lg shadow-sm text-sm group-hover:border-rose-200 group-hover:shadow-rose-100 transition-all">
                                    <?= $row['icd10'] ?>
                                </span>
                            </td>
                        
                            <td class="py-4 bg-slate-50 group-hover:bg-rose-50 transition-colors">
                                <div class="flex flex-col gap-1">
                                    <span class="text-slate-700 font-bold text-sm leading-tight"><?= $row['diag_name'] ?></span>
                                    <div class="w-32 h-1 bg-slate-200 rounded-full overflow-hidden mt-1">
                                        <div class="h-full bg-rose-400 rounded-full transition-all duration-1000" style="width: <?= $percent ?>%"></div>
                                    </div>
                                </div>
                            </td>
                        
                            <td class="py-4 text-right pr-6 bg-slate-50 group-hover:bg-rose-50 rounded-r-2xl transition-colors">
                                <div class="flex flex-col items-end">
                                    <span class="text-lg font-black text-slate-800"><?= number_format($row['total']) ?></span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">ครั้ง</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if(empty($diagStats)): ?>
                        <tr>
                            <td colspan="3" class="py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <i data-lucide="folder-search" class="w-12 h-12 text-slate-200"></i>
                                    <p class="text-slate-400 font-bold">ไม่พบข้อมูลการ Refer In ในช่วงวันที่เลือก</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


         <!-- จบ อันดับสถานพยาบาลที่รับส่งต่อ -->
         <div class="bg-white rounded-[2rem] shadow-xl p-8 border border-slate-100 mt-8 overflow-hidden relative">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-rose-50 rounded-full blur-3xl opacity-50"></div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 relative z-10">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-gradient-to-br from-rose-500 to-rose-600 text-white rounded-2xl shadow-lg shadow-rose-100">
                        <i data-lucide="hospital" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-800 tracking-tight">10 อันดับสถานพยาบาลส่งกลับ</h2>
                        <p class="text-sm text-slate-400 font-medium">
                            Analysis by Service Point: <span class="text-rose-500 font-bold"><?= $type_name ?></span> 
                            <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 relative z-10">
                <div class="lg:col-span-5">
                    <div class="overflow-hidden">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-[10px] uppercase tracking-[0.15em] text-slate-400 font-black border-b border-slate-100">
                                    <th class="pb-4">Destination Hospital</th>
                                    <th class="pb-4 text-right">Referrals</th>
                                </tr>
                            </thead>
                                <tbody class="divide-y divide-slate-50">
                                    <?php foreach ($hospStats as $index => $h): ?>
                                        <tr class="group cursor-default">
                                            <td class="py-4">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-xs font-bold text-slate-300 group-hover:text-rose-400 transition-colors"><?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?></span>
                                                    <span class="text-sm font-semibold text-slate-600 group-hover:text-slate-900 transition-colors"><?= $h['hosp_name'] ?></span>
                                                </div>
                                            </td>
                                            <td class="py-4 text-right">
                                                <span class="text-sm font-black text-slate-700 bg-slate-50 px-3 py-1 rounded-lg group-hover:bg-rose-500 group-hover:text-white transition-all"><?= number_format($h['total']) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                        </table>
                    </div>
                </div>

            <div class="lg:col-span-7 bg-slate-50/50 p-6 rounded-[1.5rem] border border-slate-50">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <span class="w-1 h-1 bg-rose-400 rounded-full"></span>Volume Distribution
                </h3>
            
                <div class="space-y-6">
                    <?php 
                        $maxHosp = !empty($hospStats) ? $hospStats[0]['total'] : 1; 
                            foreach ($hospStats as $h): 
                        $percent = ($h['total'] / $maxHosp) * 100;
                    ?>
                    <div class="group">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-xs font-bold text-slate-600 group-hover:text-rose-600 transition-colors">
                                <?= $h['hosp_name'] ?>
                            </span>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-sm font-black text-slate-800"><?= number_format($h['total']) ?></span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Cases</span>
                                </div>
                        </div>
                        <div class="w-full bg-white h-2 rounded-full shadow-sm overflow-hidden border border-slate-100 p-[1px]">
                            <div class="bg-gradient-to-r from-rose-400 to-rose-500 h-full rounded-full transition-all duration-1000 ease-out shadow-[0_0_8px_rgba(244,63,94,0.3)]" 
                                style="width: <?= $percent ?>%">
                            </div>
                        </div>
                    </div>
                        <?php endforeach; ?>

                        <?php if(empty($hospStats)): ?>
                    <div class="flex flex-col items-center justify-center py-12 opacity-40">
                        <i data-lucide="inbox" class="w-12 h-12 mb-2"></i>
                        <p class="text-sm font-bold">ไม่มีข้อมูล</p>
                    </div>
                        <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

         <!-- จบ อันดับสถานพยาบาลที่รับส่งต่อ -->


        <div class="bg-white rounded-[2rem] shadow-xl p-8 border border-slate-100 mt-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-700 italic">Total Refer In Department/Point</h2>
                </div>
                <span class="text-xs font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full">
                    ทั้งหมด <?= count($allDeptStats) ?> แผนก <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?>
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b border-slate-100 text-[10px] uppercase tracking-widest text-slate-400">
                            <th class="pb-4 pl-4">ชื่อแผนก / จุดบริการ</th>
                            <th class="pb-4 text-right pr-4">จำนวน Refer (ราย)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php 
                        $grandTotal = 0;
                        foreach ($allDeptStats as $dept): 
                            $grandTotal += $dept['total'];
                        ?>
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="py-4 pl-4 text-sm font-medium text-slate-600 group-hover:text-indigo-600">
                                <?= $dept['department_name'] ?>
                            </td>
                            <td class="py-4 text-right pr-4 font-black text-slate-800">
                                <?= number_format($dept['total']) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50">
                            <td class="py-4 pl-4 font-bold text-slate-700">รวมทั้งสิ้น</td>
                            <td class="py-4 text-right pr-4 font-black text-indigo-600 text-lg">
                                <?= number_format($grandTotal) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>



<script>
    // 1. Chart.js Config
    const ctx = document.getElementById('referChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'จำนวน Refer',
                data: <?= json_encode($data) ?>,
                backgroundColor: 'rgba(225, 29, 72, 0.8)', // Rose 600
                borderColor: 'rgb(225, 29, 72)',
                borderWidth: 1,
                borderRadius: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Flatpickr
    flatpickr("#date-range", {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: ["<?= $start_date ?>", "<?= $end_date ?>"],
        locale: { rangeSeparator: ' ถึง ' },
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                document.getElementById('start_date').value = instance.formatDate(selectedDates[0], "Y-m-d");
                document.getElementById('end_date').value = instance.formatDate(selectedDates[1], "Y-m-d");
            }
        }
    });

    // 3. Export
    function exportToExcel() {
        const table = document.getElementById("referTable");
        const wb = XLSX.utils.table_to_book(table, { sheet: "Refer_Data" });
        XLSX.writeFile(wb, `Refer_Report_<?= $type_name ?>_<?= $start_date ?>.xlsx`);
    }

    function exportToPDF() {
        const element = document.getElementById('pdf-area');
        const opt = {
            margin: 0.5,
            filename: `Refer_Report_<?= $start_date ?>.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }

    lucide.createIcons();
</script>