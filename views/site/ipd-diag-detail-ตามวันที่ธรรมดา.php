<?php
/** @var array $labels */
/** @var array $data */
/** @var string $start_date */
/** @var string $end_date */
$this->title = 'รายละเอียดการวินิจฉัยโรค IPD';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="bg-slate-50 min-h-screen p-8 font-kanit">
    <div class="max-w-6xl mx-auto">
        
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 mb-8">
            <form method="get" action="<?= \yii\helpers\Url::to(['/site/ipd-diag-detail']) ?>" class="flex flex-wrap items-end gap-4">
                <input type="hidden" name="r" value="site/ipd-diag-detail">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-widest">ช่วงวันที่เริ่มต้น - สิ้นสุด</label>
                    <div class="relative">
                        <input type="text" id="date-range" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-slate-700 font-bold focus:ring-2 focus:ring-hos-blue" placeholder="เลือกช่วงวันที่...">
                        <input type="hidden" id="start_date" name="start_date" value="<?= $start_date ?>">
                        <input type="hidden" id="end_date" name="end_date" value="<?= $end_date ?>">
                    </div>
                </div>
                <button type="submit" class="bg-hos-blue text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-blue-700 transition">
                    ประมวลผลข้อมูล
                </button>
            </form>
        </div>

        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-4">
                <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="bg-white p-2 rounded-full shadow-sm hover:bg-slate-100 transition">
                    <i data-lucide="arrow-left" class="w-6 h-6 text-slate-600"></i>
                </a>
                <h1 class="text-3xl font-black text-hos-blue">10 อันดับรหัสโรค IPD</h1>
            </div>
            
            <div class="flex gap-2">
                <button onclick="exportToExcel()" class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-xl font-bold shadow-lg hover:bg-green-700 transition text-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Excel
                </button>
                <button onclick="exportToPDF()" class="flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-xl font-bold shadow-lg hover:bg-red-700 transition text-sm">
                    <i data-lucide="file-text" class="w-4 h-4"></i> PDF
                </button>
            </div>
        </div>

        <div id="pdf-area" class="space-y-8">
            <div class="bg-white rounded-[2rem] shadow-xl p-8 border border-slate-100">
                <!-- <h2 class="text-lg font-bold text-slate-700 mb-6 italic">กราฟแสดงจำนวนผู้ป่วยตามรหัสโรค (<?= $start_date ?> ถึง <?= $end_date ?>)</h2> -->
                <h2 class="text-lg font-bold text-slate-700 mb-6 italic">
                    กราฟแสดงจำนวนผู้ป่วยตามรหัสโรค 
                    (<?= date('d/m/Y', strtotime($start_date)) ?> ถึง <?= date('d/m/Y', strtotime($end_date)) ?>)
                </h2>
                <div class="relative h-[350px]">
                    <canvas id="diagChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl p-8 border border-slate-100">
                <h2 class="text-lg font-bold text-slate-700 mb-4 italic">
                    ตารางข้อมูลการวินิจฉัย (<?= date('d/m/Y', strtotime($start_date)) ?> ถึง <?= date('d/m/Y', strtotime($end_date)) ?>)
                </h2>
                <table id="diagTable" class="w-full">
                    <thead>
                        <tr class="text-left border-b border-slate-100 uppercase text-xs tracking-widest text-slate-400">
                            <th class="pb-4">ICD10</th>
                            <th class="pb-4">ชื่อโรค</th>
                            <th class="pb-4 text-right">จำนวน (ราย)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($diagStats as $row): ?>
                        <tr>
                            <td class="py-4 font-bold text-hos-blue"><?= $row['icd10'] ?></td>
                            <td class="py-4 text-slate-500 text-sm"><?= $row['diag_name'] ?></td>
                            <td class="py-4 text-right font-black text-slate-800"><?= number_format($row['total']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($diagStats)): ?>
                        <tr>
                            <td colspan="3" class="py-10 text-center text-slate-400">ไม่พบข้อมูลในช่วงวันที่เลือก</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Initialize Chart.js
    const ctx = document.getElementById('diagChart').getContext('2d');
    const labels = <?= json_encode($labels) ?>;
    const dataValues = <?= json_encode($data) ?>;
    
    // สุ่มสี
    const backgroundColors = dataValues.map(() => {
        const r = Math.floor(Math.random() * 200);
        const g = Math.floor(Math.random() * 200);
        const b = Math.floor(Math.random() * 200);
        return `rgba(${r}, ${g}, ${b}, 0.8)`;
    });

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'จำนวน (ราย)',
                data: dataValues,
                backgroundColor: backgroundColors,
                borderRadius: 12
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

    // 2. Initialize Flatpickr
    flatpickr("#date-range", {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: ["<?= $start_date ?>", "<?= $end_date ?>"],
        locale: { rangeSeparator: ' ถึง ' },
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                // ต้องมั่นใจว่า id start_date และ end_date มีอยู่จริงในฟอร์ม
                document.getElementById('start_date').value = instance.formatDate(selectedDates[0], "Y-m-d");
                document.getElementById('end_date').value = instance.formatDate(selectedDates[1], "Y-m-d");
            }
        }
    });

    // 3. Export Functions
    function exportToExcel() {
        const table = document.getElementById("diagTable");
        const wb = XLSX.utils.table_to_book(table, { sheet: "Data" });
        XLSX.writeFile(wb, `Diag_IPD_Report_<?= $start_date ?>_<?= $end_date ?>.xlsx`);
    }

    function exportToPDF() {
        const element = document.getElementById('pdf-area');
        const opt = {
            margin: 0.5,
            // filename: `Report_<?= $start_date ?>.pdf`,
            filename: `Report_IPD_<?= date('d-m-Y', strtotime($start_date)) ?>.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }

    lucide.createIcons();
</script>