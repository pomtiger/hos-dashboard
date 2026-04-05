<?php
/** @var array $pttypeData ข้อมูลสิทธิการรักษา */
/** @var string $start_date */
/** @var string $end_date */
$this->title = 'รายละเอียดผู้รับบริการแยกตามสิทธิ';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    .font-mitr { font-family: 'Mitr', sans-serif; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

    /* สไตล์สำหรับ Masonry Layout */
    .masonry-item { break-inside: avoid; margin-bottom: 1.5rem; }
    
    /* ปรับแต่งฟอนต์ Mitr สำหรับหัวข้อ */
    .font-mitr { font-family: 'Mitr', sans-serif; }
    
    /* ปรับแต่ง DateRangePicker ให้เข้ากับ Theme */
    .daterangepicker { 
        border-radius: 1.5rem !important; 
        border: none !important; 
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
        padding: 10px !important;
        z-index: 110 !important;
    }
    .daterangepicker .applyBtn { background-color: #2563eb !important; border: none !important; border-radius: 0.75rem !important; }

    /* Modal Styles 1*/
    .modal-open { overflow: hidden; }
    .custom-modal { 
        display: none; 
        position: fixed; 
        z-index: 100; 
        inset: 0; 
        background: rgba(15, 23, 42, 0.6); 
        backdrop-filter: blur(4px); 
    }
    .custom-modal.active { display: flex; align-items: center; justify-content: center; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; } 
    /* Modal Styles 1*/
    
</style>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- Chart.js สำหรับวาดกราฟวงกลม -->
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->

<div class="bg-slate-50 min-h-screen p-6 font-mitr">
    <div class="max-w-7xl mx-auto">
        <!-- ส่วนหัวหน้าจอและตัวกรองวันที่ -->
        <div class="max-w-7xl mx-auto mb-6">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <div class="flex items-center gap-5">
                    <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" 
                        class="bg-white p-3 rounded-2xl shadow-sm hover:shadow-md hover:bg-slate-50 transition-all border border-slate-100 text-slate-600">
                        <i data-lucide="arrow-left" class="w-6 h-6"></i>
                    </a>
                    <div>
                        <h1 class="font-mitr text-3xl font-black text-slate-800 tracking-tight">สถิติแยกตามสิทธิการรักษา</h1>
                        <p class="text-slate-500 flex items-center gap-2 mt-1">สัดส่วนจำนวนผู้ป่วยตามสิทธิ
                            <i data-lucide="calendar-range" class="w-4 h-4 text-hos-blue"></i>
                        </p>
                    
                        <span class="font-medium"><?= Yii::$app->formatter->asDate($start_date, 'php:d/m/Y') ?></span>
                        <span class="text-slate-300 mx-1">/</span>
                        <span class="font-medium"><?= Yii::$app->formatter->asDate($end_date, 'php:d/m/Y') ?></span>
                    </div>
                </div>

                <div class="bg-white p-2 pl-6 rounded-[2rem] shadow-sm border border-slate-100 flex-1 max-w-2xl">
                    <form method="get" action="<?= \yii\helpers\Url::to(['/site/pttype-detail']) ?>" class="flex flex-col sm:flex-row items-center gap-4">
                        <input type="hidden" name="r" value="/site/pttype-detail">
                        <div class="flex-1 w-full">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">เลือกช่วงวันที่ประมวลผล</label>
                            <div class="relative">
                                <i data-lucide="calendar" class="w-4 h-4 absolute left-0 top-1/2 -translate-y-1/2 text-hos-blue"></i>
                                <input type="text" id="date-range" class="w-full bg-transparent border-none rounded-xl pl-7 pr-4 py-1 text-slate-700 font-bold focus:ring-0 cursor-pointer" 
                                    placeholder="เลือกวันที่..." readonly>
                                <input type="hidden" id="start_date" name="start_date" value="<?= $start_date ?>">
                                <input type="hidden" id="end_date" name="end_date" value="<?= $end_date ?>">
                            </div>
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-slate-900 text-white px-8 py-4 rounded-[1.5rem] font-bold shadow-lg hover:bg-slate-800 transition-all flex items-center justify-center gap-2 group">
                            <i data-lucide="refresh-cw" class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500"></i>
                            ประมวลผล
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- ส่วนหัวหน้าจอและตัวกรองวันที่ -->
        
        

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100">
                <h3 class="text-slate-700 font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="pie-chart" class="text-indigo-500"></i> สัดส่วนการใช้สิทธิ (%)
                </h3>
                <div class="h-[350px]">
                    <canvas id="pttypePieChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100">
                <h3 class="text-slate-700 font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="bar-chart-3" class="text-emerald-500"></i> จำนวนผู้ป่วยแยกตามสิทธิ (ราย)
                </h3>
                <div class="h-[350px]">
                    <canvas id="pttypeBarChart"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50">
                <h3 class="text-slate-700 font-bold">ตารางสรุปข้อมูลสิทธิการรักษา</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-500 text-sm uppercase">
                        <tr>
                            <th class="px-8 py-4 font-bold">ชื่อสิทธิการรักษา</th>
                            <th class="px-8 py-4 font-bold text-center">จำนวนผู้รับบริการ</th>
                            <!-- <th class="px-8 py-4 font-bold text-center">Detail</th> -->
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($pttypeData as $row): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-8 py-4 font-bold text-slate-700"><?= $row['pttype_name'] ?></td>
                            <td class="px-8 py-4 text-center">
                                <button onclick="viewPatientByPttype('<?= $row['pttype'] ?>', '<?= $row['pttype_name'] ?>')" 
                                    class="group hover:scale-105 transition-transform">
                                    <span class="bg-indigo-50 text-indigo-600 px-4 py-1 rounded-full font-bold group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                        <?= number_format($row['total']) ?> ราย
                                    </span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!--Modal ใหม่ -->
        <div id="pttypeModal" class="custom-modal p-4" onclick="if(event.target == this) closeModal()">
            <div class="bg-white w-full max-w-4xl max-h-[85vh] rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden border border-slate-200/50 transform transition-all">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-slate-50 to-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-hos-blue/10 text-hos-blue rounded-2xl flex items-center justify-center">
                            <i data-lucide="users" class="w-6 h-6"></i>
                        </div>
                    <div>
                    <div class="flex items-center gap-2">
                        <h3 id="modalPTtypeName" class="font-mitr text-xl font-bold text-slate-800 tracking-tight">ชื่อสิทธิการรักษา</h3>
                        <span class="bg-emerald-100 text-emerald-600 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Live Data</span>
                    </div>
                        <p class="text-sm text-slate-400 flex items-center gap-2 mt-0.5">
                            <i data-lucide="calendar-range" class="w-3.5 h-3.5 text-slate-300"></i>ช่วงวันที่: 
                            <span class="text-slate-600 font-semibold italic bg-slate-100 px-2 rounded-md"><?= Yii::$app->formatter->asDate($start_date, 'php:d/m/Y') ?></span>
                            <span class="text-slate-300">/</span>
                            <span class="text-slate-600 font-semibold italic bg-slate-100 px-2 rounded-md"><?= Yii::$app->formatter->asDate($end_date, 'php:d/m/Y') ?></span>
                        </p>
                    </div>
                </div>
                    <button onclick="closeModal()" class="group p-2 bg-slate-50 hover:bg-red-50 rounded-full transition-all duration-300 border border-slate-100 hover:border-red-100">
                        <i data-lucide="x" class="w-6 h-6 text-slate-400 group-hover:text-red-500 transition-colors"></i>
                    </button>
                </div>

                <div id="modalContent" class="flex-1 overflow-y-auto p-8 custom-scrollbar bg-white">
                    <div class="flex flex-col justify-center items-center py-24 gap-4">
                        <div class="relative flex h-12 w-12">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-hos-blue opacity-20"></span>
                            <div class="animate-spin rounded-full h-12 w-12 border-4 border-slate-100 border-t-hos-blue shadow-inner"></div>
                        </div>
                        <div class="text-center">
                            <p class="text-slate-500 font-bold animate-pulse">กำลังดึงข้อมูลรายชื่อ...</p>
                            <p class="text-slate-400 text-xs">กรุณารอสักครู่ ข้อมูลกำลังประมวลผล</p>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-4 bg-slate-50 border-t border-slate-100 flex justify-end items-center gap-3">
                    <span class="text-[10px] text-slate-400 font-medium italic">* ข้อมูลนี้แสดงผลเฉพาะที่มีสถานะเข้ารับบริการแล้ว</span>
                    <button onclick="closeModal()" class="px-6 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-100 transition-colors">
                        ปิดหน้าต่าง
                    </button>
                </div>
            </div>
        </div>
        <!--Modal ใหม่ -->
    </div>
    <?= $this->render('_footer') ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
    // ป้องกัน Error กรณีไม่มีข้อมูล
    const pttypeData = <?= json_encode($pttypeData ?? []) ?>;
    
    if (pttypeData.length === 0) {
        console.warn("ไม่มีข้อมูลสิทธิการรักษาในช่วงวันที่เลือก");
        return; 
    }
    const pttypeLabels = pttypeData.map(item => item.pttype_name || 'ไม่ระบุสิทธิ');
    const pttypeCounts = pttypeData.map(item => parseInt(item.total) || 0);

    // const pttypeLabels = <?= json_encode(array_column($pttypeData, 'pttype_name')) ?>;
    // const pttypeCounts = <?= json_encode(array_column($pttypeData, 'total')) ?>;
    
    const colors = ['#6366f1', '#10b981', '#f59e0b', '#f43f5e', '#0ea5e9', '#8b5cf6', '#ec4899'];

    // 1. กราฟวงกลม
    new Chart(document.getElementById('pttypePieChart'), {
        type: 'doughnut',
        data: {
            labels: pttypeLabels,
            datasets: [{
                data: pttypeCounts,
                backgroundColor: colors,
                borderWidth: 0
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right', labels: { font: { family: 'Mitr' } } } }
        }
    });

    // 2. กราฟแท่ง
    new Chart(document.getElementById('pttypeBarChart'), {
        type: 'bar',
        data: {
            labels: pttypeLabels,
            datasets: [{
                label: 'จำนวนราย',
                data: pttypeCounts,
                backgroundColor: colors,
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
});

// ฟังก์ชันสำหรับเปิด Modal และโหลดข้อมูลรายชื่อผู้ป่วย
function viewPatientByPttype(pttype, pttype_name) {
    if(!pttype) return;
    
    $('#modalPTtypeName').text(pttype_name);
    $('#pttypeModal').addClass('active');
    $('body').addClass('modal-open');
    
    $('#modalContent').html('<div class="flex justify-center py-20"><div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-200 border-t-indigo-600"></div></div>');
    
    // แก้ไข URL ให้ตรงกับ Action ใน SiteController
    $.get('<?= \yii\helpers\Url::to(['site/get-p-t-type-patient-list']) ?>', { 
        // หมายเหตุ: Yii2 จะแปลง camelCase เป็น get-p-t-type-patient-list หรือเขียนเต็มๆ ว่า getPTTypePatientList
        pttype: pttype,
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val()
    }, function(data) {
        $('#modalContent').html(data);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }).fail(function() {
        $('#modalContent').html('<div class="text-center py-10 text-red-500 font-bold">ไม่สามารถโหลดข้อมูลได้ (Check Action Name)</div>');
    });
}
// ฟังก์ชันปิด Modal
function closeModal() {
    $('#pttypeModal').removeClass('active');
    $('body').removeClass('modal-open');
}

// เพิ่ม Event Listener สำหรับปุ่ม ESC เพื่อปิด Modal
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
});

$(function() {
    // โหลด Lucide Icons เบื้องต้น
    lucide.createIcons();

    // 1. Loading Interaction Loading Interaction แบบหน่วงเวลา
    $('#date-form-er').on('submit', function(e) {
        // หยุดการส่งฟอร์มไว้ก่อน (ยังไม่ให้โหลดหน้าใหม่ทันที)
        e.preventDefault(); 
        
        var form = this;

        // แสดงหน้าจอ Loading พร้อมเอฟเฟกต์ค่อยๆ ปรากฏ
        $('#loading-screen').css('display', 'flex').hide().fadeIn(300);
        
        // เปลี่ยนปุ่มเป็นสถานะกำลังโหลด
        $(this).find('button[type="submit"]')
            .addClass('opacity-50 cursor-not-allowed')
            .html('<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>');

        // หน่วงเวลาไว้ตามที่ต้องการ (เช่น 1500ms = 1.5 วินาที)
        setTimeout(function() {
            form.submit(); // ส่งฟอร์มจริงหลังจากหน่วงเวลาเสร็จ
        }, 600); // <--- ปรับตัวเลขตรงนี้ (1000 = 1 วินาที)
    });
    
    // lส่วนเริ่มวันที่ตั้งค่าวันที่เริ่มต้นจากค่าใน Hidden Input
    var start = moment($('#start_date').val());
    var end = moment($('#end_date').val());

    // Callback Function สำหรับอัปเดตหน้าจอเมื่อเปลี่ยนวันที่
    function cb(start, end) {
        $('#date-range').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#start_date').val(start.format('YYYY-MM-DD'));
        $('#end_date').val(end.format('YYYY-MM-DD'));
    }

    /**
     * คำนวณปีงบประมาณ (1 ต.ค. - 30 ก.ย.)
     * ปีงบประมาณไทย = ปี ค.ศ. ปัจจุบัน + 543 (หากเดือน >= ตุลาคม ให้บวกเพิ่มอีก 1)
     */
    var customRanges = {
        'วันนี้': [moment(), moment()],
        'เมื่อวาน': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        '7 วันล่าสุด': [moment().subtract(6, 'days'), moment()],
        '30 วันล่าสุด': [moment().subtract(29, 'days'), moment()],
        'เดือนนี้': [moment().startOf('month'), moment().endOf('month')],
        'เดือนที่แล้ว': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        'ปีงบประมาณปัจจุบัน': [
            moment().month() >= 9 ? moment().month(9).date(1) : moment().subtract(1, 'year').month(9).date(1),
            moment().month() >= 9 ? moment().add(1, 'year').month(8).date(30) : moment().month(8).date(30)
        ]
    };

    // ลูปสร้างเมนูปีงบประมาณย้อนหลัง 5 ปี
    var currentFY = moment().month() >= 9 ? moment().year() + 1 : moment().year(); 
    for (var i = 1; i <= 3; i++) {
        var fYear = currentFY - i;
        var label = 'ปีงบประมาณ ' + (fYear + 543);
        
        // วันที่เริ่มต้น: 1 ต.ค. ของปี (fYear - 1) | วันที่สิ้นสุด: 30 ก.ย. ของปี fYear
        var startDate = moment().year(fYear - 1).month(9).date(1);
        var endDate = moment().year(fYear).month(8).date(30);
        
        customRanges[label] = [startDate, endDate];
    }

    // เรียกใช้งาน DateRangePicker <input type="text" id="date-range" บรรทัดประมาณ 70 เด้อ
    $('#date-range').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: customRanges,
        showDropdowns: true,       // แสดง Dropdown เลือกเดือน/ปี ในปฏิทิน
        alwaysShowCalendars: true, // แสดงปฏิทินคู่กับเมนูทางลัดเสมอ
        opens: 'left',
        buttonClasses: 'px-4 py-2 rounded-xl font-bold transition-all',
        applyButtonClasses: 'bg-hos-blue text-white',
        cancelButtonClasses: 'bg-slate-100 text-slate-600',
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: 'ตกลง',
            cancelLabel: 'ยกเลิก',
            customRangeLabel: 'กำหนดช่วงเอง',
            daysOfWeek: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
            monthNames: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
        }
    }, cb);

    // รันครั้งแรกเพื่อให้ค่าใน Input ถูกต้อง
    cb(start, end);
        
});
</script>