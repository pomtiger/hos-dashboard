<?php
/** @var array $labels */
/** @var array $data */
/** @var string $start_date */
/** @var string $end_date */
/** @var string $depcode */
/** @var string $deptName */
/** @var array $diagStats */
$this->title = 'รายละเอียดการวินิจฉัยโรค - ' . htmlspecialchars($deptName);
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">

<style>
    .font-kanit { font-family: 'Kanit', sans-serif; }
    .bg-hos-blue { background-color: #2563eb; }
    .text-hos-blue { color: #2563eb; }
</style>

<div class="bg-slate-50 min-h-screen p-8 font-kanit">
    <div class="max-w-6xl mx-auto">
        <!-- วันที่ ประมวลผลข้อมูล -->
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 mb-8">
            <form method="get" action="<?= \yii\helpers\Url::to(['/site/clinic-diag-detail']) ?>" id="date-form-diag" class="flex flex-wrap items-end gap-4">
                <!-- ส่ง param แบบ GET -->
                <input type="hidden" name="r" value="site/clinic-diag-detail">
                <input type="hidden" name="depcode" value="<?= htmlspecialchars($depcode) ?>">
        
                <div class="flex-1 min-w-[300px]">
                    <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-widest">ช่วงวันที่เริ่มต้น - สิ้นสุด</label>
                    <div class="relative group">
                        <input type="text" id="reportrange" class="w-full bg-slate-50 border-none rounded-xl px-11 py-3 text-slate-700 font-bold focus:ring-2 focus:ring-indigo-500 cursor-pointer" placeholder="เลือกช่วงวันที่...">
                        <i data-lucide="calendar" class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 group-hover:text-indigo-500 transition-colors"></i>
                        <input type="hidden" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
                        <input type="hidden" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
                    </div>
                </div>

                <button type="submit" class="bg-hos-blue text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i> ประมวลผล
                </button>
            </form>
        </div>
        <!-- วันที่ ประมวลผลข้อมูล -->

        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-4">
                <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="bg-white p-2 rounded-full shadow-sm hover:bg-slate-100 transition">
                    <i data-lucide="arrow-left" class="w-6 h-6 text-slate-600"></i>
                </a>
                <h1 class="text-3xl font-black text-hos-blue">อันดับรหัสโรค (<?= htmlspecialchars($deptName) ?>)</h1>
            </div>

            <!-- EXPORT Buttons -->
            <div class="flex gap-2">
                <button onclick="window.print()" class="flex items-center gap-2 bg-slate-600 text-white px-4 py-2 rounded-xl font-bold shadow-lg hover:bg-slate-700 transition text-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i> Print
                </button>
                <button onclick="exportToExcel()" class="flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-xl font-bold shadow-lg hover:bg-emerald-700 transition text-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Excel
                </button>
            </div>
        </div>

        <!-- กราฟแสดงจำนวนผู้ป่วยตามรหัสโรค  -->
        <div id="pdf-area" class="space-y-8">
            <div class="bg-white rounded-[2rem] shadow-xl p-8 border border-slate-100">
                <h2 class="text-lg font-bold text-slate-700 mb-6 italic"> กราฟแสดงจำนวนผู้ป่วย(<?= htmlspecialchars($deptName) ?>) ตามรหัสโรค 
                    (<?= date('d/m/Y', strtotime($start_date)) ?> ถึง <?= date('d/m/Y', strtotime($end_date)) ?>)
                </h2>
                <div class="relative h-[350px]">
                    <canvas id="diagChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl p-8 border border-slate-100">
                <h2 class="text-lg font-bold text-slate-700 mb-4 italic">
                    ตารางข้อมูล(<?= htmlspecialchars($deptName) ?>)การวินิจฉัย  (<?= date('d/m/Y', strtotime($start_date)) ?> ถึง <?= date('d/m/Y', strtotime($end_date)) ?>)
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
                            <td class="py-4 font-bold text-hos-blue"><?= htmlspecialchars($row['icd10']) ?></td>
                            <td class="py-4 text-slate-500 text-sm"><?= htmlspecialchars($row['diag_name']) ?></td>
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
        <!-- กราฟแสดงจำนวนผู้ป่วยตามรหัสโรค  -->

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script>
function initDashboard() {
    if (typeof $ === 'undefined' || typeof moment === 'undefined') {
        setTimeout(initDashboard, 100);
        return;
    }

    lucide.createIcons();

    var startDateStr = $('#start_date').val() || moment().format('YYYY-MM-DD');
    var endDateStr = $('#end_date').val() || moment().format('YYYY-MM-DD');
    
    var start = moment(startDateStr);
    var end = moment(endDateStr);

    function cb(start, end) {
        $('#reportrange').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#start_date').val(start.format('YYYY-MM-DD'));
        $('#end_date').val(end.format('YYYY-MM-DD'));
    }

    var customRanges = {
        'วันนี้': [moment(), moment()],
        'เมื่อวาน': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        '7 วันล่าสุด': [moment().subtract(6, 'days'), moment()],
        'เดือนนี้': [moment().startOf('month'), moment().endOf('month')],
        'ปีงบประมาณปัจจุบัน': [
            moment().month() >= 9 ? moment().month(9).date(1) : moment().subtract(1, 'year').month(9).date(1),
            moment().month() >= 9 ? moment().add(1, 'year').month(8).date(30) : moment().month(8).date(30)
        ]
    };

    var currentFiscalYear = moment().month() >= 9 ? moment().year() + 1 : moment().year();
    for (var i = 1; i <= 3; i++) {
        var fYear = currentFiscalYear - i;
        var label = 'ปีงบประมาณ ' + (fYear + 543);
        var fStart = moment().year(fYear - 1).month(9).date(1);
        var fEnd = moment().year(fYear).month(8).date(30);
        customRanges[label] = [fStart, fEnd];
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: customRanges, 
        alwaysShowCalendars: true,
        showDropdowns: true, 
        opens: 'right',
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: 'ตกลง',
            cancelLabel: 'ยกเลิก',
            customRangeLabel: 'กำหนดเอง',
            daysOfWeek: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
            monthNames: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.']
        }
    }, cb);

    cb(start, end);

    const ctx = document.getElementById('diagChart');
    if (ctx && typeof Chart !== 'undefined') {
        let chartStatus = Chart.getChart("diagChart"); 
        if (chartStatus != undefined) { chartStatus.destroy(); }

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels ?? []) ?>,
                datasets: [{
                    label: 'จำนวน (ราย)',
                    data: <?= json_encode($data ?? []) ?>,
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    $('#date-form-diag').on('submit', function() {
        $(this).find('button[type="submit"]').prop('disabled', true).html('กำลังประมวลผล...');
    });
}

if (document.readyState === "complete") {
    initDashboard();
} else {
    window.addEventListener("load", initDashboard);
}

function exportToExcel() {
    // 1. เลือกตารางที่ต้องการ export
    var table = document.getElementById("diagTable");
    
    // 2. แปลงตาราง HTML เป็น Worksheet
    var wb = XLSX.utils.table_to_book(table, { sheet: "Diagnostic_Data" });
    
    // 3. ตั้งชื่อไฟล์ (ระบุชื่อแผนกและวันที่เพื่อให้ไฟล์ดูเป็นระเบียบ)
    var deptName = "<?= htmlspecialchars($deptName) ?>";
    var startDate = "<?= $start_date ?>";
    var endDate = "<?= $end_date ?>";
    var fileName = "10_อันดับโรค_" + deptName + "_" + startDate + "_to_" + endDate + ".xlsx";

    // 4. สั่งให้ Browser ดาวน์โหลดไฟล์
    XLSX.writeFile(wb, fileName);
}
</script>
