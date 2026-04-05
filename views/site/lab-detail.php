<?php
/** @var array $labels */
/** @var array $data */
/** @var string $start_date */
/** @var string $end_date */
/** @var array $labStats */
$this->title = 'รายละเอียดการสั่งตรวจ LAB';
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<div class="bg-slate-50 min-h-screen p-8 font-kanit">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 mb-8">
            <form method="get" action="<?= \yii\helpers\Url::to(['/site/lab-detail']) ?>" class="flex flex-wrap items-end gap-4">
                <input type="hidden" name="r" value="site/lab-detail">
                <div class="flex-1 min-w-[300px]">
                    <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-widest">ช่วงวันที่</label>
                    <input type="text" id="reportrange" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-slate-700 font-bold">
                    <input type="hidden" id="start_date" name="start_date" value="<?= $start_date ?>">
                    <input type="hidden" id="end_date" name="end_date" value="<?= $end_date ?>">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold">ประมวลผล</button>
            </form>
        </div>

        <div class="flex items-center gap-4">
            <a href="<?= \yii\helpers\Url::to(['/site/index']) ?>" class="bg-white p-2 rounded-full shadow-sm hover:bg-slate-100 transition">
                <i data-lucide="arrow-left" class="w-6 h-6 text-slate-600"></i>
            </a>
            <h1 class="text-3xl font-black text-blue-600"> LAB</h1>
        </div>

        <div class="grid grid-cols-1 gap-8">
            <div class="bg-white rounded-[2rem] shadow-xl p-8">
                <canvas id="labChart" height="300"></canvas>
            </div>

            <div class="bg-white rounded-[2rem] shadow-xl p-8">
                <div class="flex justify-between mb-4">
                    <h2 class="text-lg font-bold">LAB Data</h2>
                    <button onclick="exportExcel()" class="bg-emerald-500 text-white px-4 py-2 rounded-lg text-sm">Export Excel</button>
                </div>
                <table id="labTable" class="w-full">
                    <thead>
                        <tr class="text-left text-slate-400 text-xs uppercase tracking-widest border-b">
                            <!-- <th class="pb-4">Code</th> -->
                            <th class="pb-4">รายการ LAB</th>
                            <th class="pb-4 text-right">จำนวน (ครั้ง)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($labStats as $row): ?>
                        <tr class="border-b border-slate-50">
                            <!-- <td class="py-4 font-bold text-blue-600"><?= $row['lab_items_code'] ?></td> -->
                            <td class="py-4 text-slate-600"><?= $row['lab_name'] ?></td>
                            <td class="py-4 text-right font-black"><?= number_format($row['total']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() { // ใช้ jQuery wrapper เพื่อความชัวร์

    // 1. จัดการวันที่ (DateRangePicker)
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

    // คำนวณปีงบประมาณย้อนหลัง 3 ปี
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



// 1. ฟังก์ชันสำหรับสร้างชุดสีสุ่ม (สุ่มตามจำนวนข้อมูลที่มี)
function generateRandomColors(count) {
    const colors = [
        'rgba(59, 130, 246, 0.8)',  // Blue
        'rgba(16, 185, 129, 0.8)',  // Emerald
        'rgba(245, 158, 11, 0.8)',  // Amber
        'rgba(239, 68, 68, 0.8)',   // Rose
        'rgba(139, 92, 246, 0.8)',  // Violet
        'rgba(20, 184, 166, 0.8)',  // Teal
        'rgba(244, 63, 94, 0.8)',   // Pink
        'rgba(71, 85, 105, 0.8)',   // Slate
        'rgba(217, 70, 239, 0.8)',  // Fuchsia
        'rgba(99, 102, 241, 0.8)'   // Indigo
    ];
    
    // ถ้าข้อมูลเยอะกว่าสีที่มี ให้ทำการสุ่มใหม่หรือวนลูปสี
    let result = [];
    for (let i = 0; i < count; i++) {
        result.push(colors[i % colors.length]);
    }
    return result;
}

// 2. เรียกใช้งาน Chart
const labData = <?= json_encode($data ?? []) ?>;
    const labLabels = <?= json_encode($labels ?? []) ?>;
    
    if (labData.length > 0) {
        new Chart(document.getElementById('labChart'), {
            type: 'bar',
            data: {
                labels: labLabels,
                datasets: [{
                    label: 'จำนวนการสั่งตรวจ (ครั้ง)',
                    data: labData,
                    backgroundColor: generateRandomColors(labData.length),
                    borderRadius: 12
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }
});

function exportExcel() {
    var table = document.getElementById("labTable");
    var wb = XLSX.utils.table_to_book(table);
    XLSX.writeFile(wb, "Top_Lab_Orders.xlsx");
}


</script>