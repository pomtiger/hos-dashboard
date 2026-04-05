<script src="https://unpkg.com/lucide@latest"></script>

<style>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php 
// เตรียมข้อมูลสำหรับเขียนกราฟวงกลม
$chartLabels = [];
$chartData = [];
$chartDepCodes = [];

if (!empty($deptStats)) {
    foreach ($deptStats as $dept) {
        $depName = $dept['dept_name'] ?: 'ไม่ระบุแผนก';
        $depCode = isset($dept['main_dep']) ? $dept['main_dep'] : (isset($dept['depcode']) ? $dept['depcode'] : '');
        
        $chartLabels[] = $depName;
        $chartData[] = (int)$dept['total'];
        $chartDepCodes[] = $depCode;
    }
}
?>

<!-- ส่วนโหลดเวลา หมุนๆ -->
<!-- <div id="loading-screen">
    <div class="relative">
        <div class="w-20 h-20 border-4 border-slate-200 border-t-hos-blue rounded-full animate-spin"></div>
        <i data-lucide="ambulance" class="w-8 h-8 text-hos-blue absolute inset-0 m-auto animate-pulse"></i>
    </div>
    <p class="mt-6 font-mitr font-bold text-slate-600 tracking-wider text-lg">กำลังประมวลผลข้อมูล...</p>
</div> -->
<!-- ส่วนโหลดเวลา -->

<div class="bg-slate-50 min-h-screen p-4 md:p-6 font-sans fade-up">
    <!-- ส่วนหัวหน้าจอและตัวกรองวันที่ -->
    <div class="max-w-7xl mx-auto mb-6">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div class="flex items-center gap-5">
                <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" 
                   class="bg-white p-3 rounded-2xl shadow-sm hover:shadow-md hover:bg-slate-50 transition-all border border-slate-100 text-slate-600">
                    <i data-lucide="arrow-left" class="w-6 h-6"></i>
                </a>
                <div>
                    <h1 class="font-mitr text-3xl font-black text-slate-800 tracking-tight">แยกตามแผนก</h1>
                    <p class="text-slate-500 flex items-center gap-2 mt-1">
                        <i data-lucide="calendar-range" class="w-4 h-4 text-hos-blue"></i>
                        <span class="font-medium"><?= Yii::$app->formatter->asDate($start_date, 'php:d/m/Y') ?></span>
                        <span class="text-slate-300 mx-1">/</span>
                        <span class="font-medium"><?= Yii::$app->formatter->asDate($end_date, 'php:d/m/Y') ?></span>
                    </p>
                </div>
            </div>

            <div class="bg-white p-2 pl-6 rounded-[2rem] shadow-sm border border-slate-100 flex-1 max-w-2xl">
                <form method="get" action="<?= \yii\helpers\Url::to(['site/dept-detail']) ?>" class="flex flex-col sm:flex-row items-center gap-4">
                    <input type="hidden" name="r" value="site/dept-detail">
                    <div class="flex-1 w-full">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 ml-1">เลือกช่วงวันที่ประมวลผล</label>
                        <div class="relative">
                            <i data-lucide="calendar" class="w-4 h-4 absolute left-0 top-1/2 -translate-y-1/2 text-hos-blue"></i>
                            <input type="text" id="date-range" 
                                class="w-full bg-transparent border-none rounded-xl pl-7 pr-4 py-1 text-slate-700 font-bold focus:ring-0 cursor-pointer" 
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

    <!-- กราฟสัดส่วนแยกตามแผนก -->
    <div class="max-w-7xl mx-auto mb-6">
        <div class="bg-white p-6 md:p-8 rounded-[2rem] md:rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-50 relative overflow-hidden">
            <!-- ตกแต่งฉากหลังนิดนึงให้ดูพรีเมียม -->
            <div class="absolute -right-20 -top-20 w-48 md:w-64 h-48 md:h-64 bg-indigo-50 rounded-full opacity-50 blur-3xl"></div>
            
            <div class="flex justify-between items-center mb-4 md:mb-6 relative z-10">
                <div>
                    <h3 class="font-mitr font-bold text-slate-800 text-lg md:text-xl tracking-tight">สัดส่วนผู้ป่วยแยกตามแผนก</h3>
                    <p class="text-slate-400 text-xs md:text-sm mt-1">
                        <i data-lucide="mouse-pointer-click" class="w-3.5 h-3.5 md:w-4 md:h-4 inline-block text-indigo-400"></i>
                        คลิกที่กราฟเพื่อดูรายชื่อ
                    </p>
                </div>
                <div class="bg-indigo-50 p-3 md:p-4 rounded-2xl md:rounded-3xl text-indigo-600 shadow-sm">
                    <i data-lucide="pie-chart" class="w-5 h-5 md:w-7 md:h-7"></i>
                </div>
            </div>
            <div class="relative h-[250px] sm:h-[300px] lg:h-[350px] w-full flex justify-center z-10">
                <canvas id="deptPieChart"></canvas>
            </div>
        </div>
    </div>
    <!-- กราฟสัดส่วนแยกตามแผนก -->
    

    
    <div class="max-w-7xl mx-auto">
        <div class="md:columns-2 lg:columns-3 gap-6 space-y-6">
            
            <div class="masonry-item bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-50">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="font-mitr font-bold text-slate-800 text-lg">Service Point Stats</h3>
                        <p class="text-slate-400 text-xs">ปริมาณผู้ป่วยแยกตามจุดบริการหลัก</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-2xl text-blue-600">
                        <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                    </div>
                </div>

                <div class="space-y-6">
                    <?php if(!empty($deptStats)): ?>
                        <?php foreach($deptStats as $dept): 
                            $maxTotal = $deptStats[0]['total'] > 0 ? $deptStats[0]['total'] : 1;
                            $widthPercent = ($dept['total'] / $maxTotal) * 100;

                            $depCode = isset($dept['main_dep']) ? $dept['main_dep'] : (isset($dept['depcode']) ? $dept['depcode'] : '');
                        ?>
                        <div class="group/item cursor-pointer hover:bg-slate-50 p-2 -mx-2 rounded-2xl transition-all"
                            onclick="viewPatientList('<?= $depCode ?>', '<?= htmlspecialchars($dept['dept_name']) ?>')">
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-sm font-bold text-slate-700 group-hover/item:text-hos-blue transition-colors">
                                    <?= $dept['dept_name'] ?: 'ไม่ระบุแผนก' ?>
                                    <i data-lucide="external-link" class="w-3 h-3 inline-block opacity-0 group-hover/item:opacity-100 ml-1"></i>
                                </span>
                                <div class="text-right">
                                    <span class="text-lg font-black text-slate-800"><?= number_format($dept['total']) ?></span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase ml-1">Case</span>
                                </div>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden shadow-inner">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-full rounded-full transition-all duration-1000 ease-out" style="width: <?= $widthPercent ?>%"></div>
                            </div>
                            <div class="flex gap-4 mt-2.5 px-1">
                                <div class="flex items-center gap-1.5 text-[11px] font-bold text-emerald-600">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                    ในเวลา: <?= number_format($dept['Intime']) ?>
                                </div>
                                <div class="flex items-center gap-1.5 text-[11px] font-bold text-amber-500">
                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                    นอกเวลา: <?= number_format($dept['Outtime']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-16">
                            <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="database" class="w-8 h-8 text-slate-300"></i>
                            </div>
                            <p class="text-slate-400 font-medium">ไม่พบข้อมูลในช่วงที่เลือก</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="masonry-item bg-white p-8 rounded-[2.5rem] shadow-lg shadow-slate-200/30 border border-slate-50">
                 <div class="flex items-center gap-3 mb-6">
                     <div class="w-2 h-8 bg-hos-blue rounded-full"></div>
                     <h3 class="font-bold text-slate-800">นิยามการนับข้อมูล</h3>
                 </div>
                 <div class="space-y-4">
                     <div class="bg-slate-50 p-4 rounded-2xl">
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            นับจำนวน <span class="text-hos-blue font-bold">Visit (VN)</span> ที่มีการลงทะเบียนในช่วงวันที่เลือก โดยคัดกรองจากแผนกหลักที่ผู้ป่วยเข้ารับบริการ
                        </p>
                     </div>
                     <div class="grid grid-cols-1 gap-3">
                         <div class="border border-slate-100 p-4 rounded-2xl">
                            <span class="text-[10px] font-black text-blue-500 uppercase block mb-1 tracking-tighter">Office Hours</span>
                            <span class="text-sm font-bold text-slate-700">07:30 - 16:30 น.</span>
                         </div>
                         <div class="border border-slate-100 p-4 rounded-2xl">
                            <span class="text-[10px] font-black text-amber-500 uppercase block mb-1 tracking-tighter">After Hours</span>
                            <span class="text-sm font-bold text-slate-700">หลัง 16:30 น. เป็นต้นไป</span>
                         </div>
                     </div>
                 </div>
            </div>

            <div class="masonry-item bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl border border-slate-800 text-white relative overflow-hidden group">
                 <div class="relative z-10">
                     <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-lg flex items-center gap-2 text-indigo-300">
                            <i data-lucide="help-circle" class="w-5 h-5"></i>
                            IT Support
                        </h3>
                     </div>
                     <p class="text-sm text-slate-400 font-light leading-relaxed mb-6">
                        ข้อมูลดึงตรงจากฐานข้อมูล <span class="text-white font-medium">HOSxP</span> แบบกึ่งเรียลไทม์ 
                        หากพบตัวเลขที่ไม่ถูกต้อง กรุณาตรวจสอบการลงข้อมูลใบนำทางในระบบ หรือติดต่อศูนย์คอมพิวเตอร์
                     </p>
                     <div class="h-1 w-12 bg-indigo-500 rounded-full group-hover:w-full transition-all duration-700"></div>
                 </div>
                 <i data-lucide="server" class="w-32 h-32 absolute -right-8 -bottom-8 text-slate-800 opacity-50 transform rotate-12"></i>
            </div>
        </div>
    </div>

        <!--Modal ใหม่ -->
        <div id="patientModal" class="custom-modal p-4" onclick="if(event.target == this) closeModal()">
            <div class="bg-white w-full max-w-4xl max-h-[85vh] rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden border border-slate-200/50 transform transition-all">
                <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-slate-50 to-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-hos-blue/10 text-hos-blue rounded-2xl flex items-center justify-center">
                            <i data-lucide="users" class="w-6 h-6"></i>
                        </div>
                    <div>
                    <div class="flex items-center gap-2">
                        <h3 id="modalDeptName" class="font-mitr text-xl font-bold text-slate-800 tracking-tight">ชื่อแผนก</h3>
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
                    <span class="text-[10px] text-slate-400 font-medium italic">* ข้อมูลนี้แสดงผลเฉพาะผู้ป่วยที่มีสถานะเข้ารับบริการแล้ว</span>
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
// ฟังก์ชันสำหรับเปิด Modal และโหลดข้อมูลรายชื่อผู้ป่วย
function viewPatientList(depcode, depname) {
    if(!depcode) return;
    
    // ตั้งค่าหัวข้อ Modal
    $('#modalDeptName').text('แผนก: ' + depname);
    
    // เปิด Modal และล็อกการ Scroll ของหน้าจอหลัก
    $('#patientModal').addClass('active');
    $('body').addClass('modal-open');
    
    // แสดงตัว Loading ขณะรอข้อมูล
    $('#modalContent').html('<div class="flex justify-center py-20"><div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-200 border-t-hos-blue"></div></div>');
    
    // ดึงข้อมูลผ่าน AJAX
    $.get('<?= \yii\helpers\Url::to(['site/get-patient-list']) ?>', {
        depcode: depcode,
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val()
    }, function(data) {
        $('#modalContent').html(data);
        lucide.createIcons(); // โหลด Icon สำหรับข้อมูลที่ดึงมาใหม่
    }).fail(function() {
        $('#modalContent').html('<div class="text-center py-10 text-red-500 font-bold">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>');
    });
}

//**ฟังก์ชันปิด Modal
function closeModal() {
    $('#patientModal').removeClass('active');
    $('body').removeClass('modal-open');
    // ล้างเนื้อหาเป็น Loading สำหรับการเปิดครั้งถัดไป
    $('#modalContent').html('<div class="flex justify-center py-20"><div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-200 border-t-hos-blue"></div></div>');
}

// ปิด Modal


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
    
    // ============================================
    // สร้างกราฟวงกลมโดนัท (Doughnut Chart)
    // ============================================
    const pieLabels = <?= json_encode($chartLabels) ?>;
    const pieData = <?= json_encode($chartData) ?>;
    const pieDepCodes = <?= json_encode($chartDepCodes) ?>;

    function generatePremiumPieColors(count) {
        let colors = [];
        let hoverColors = [];
        for (let i = 0; i < count; i++) {
            const hue = (i * 137.5) % 360; // Golden angle สุ่มสีแบบกลมกลืน
            // ให้แสงเงาดูมีมิติ
            let color = ctx => {
                let p = ctx.chart.ctx;
                let gradient = p.createRadialGradient(0, 0, 0, 0, 0, 400);
                gradient.addColorStop(0, `hsla(${hue}, 80%, 75%, 1)`);
                gradient.addColorStop(1, `hsla(${hue}, 85%, 55%, 1)`);
                return gradient;
            };
            colors.push(`hsla(${hue}, 80%, 65%, 0.85)`);
            hoverColors.push(`hsla(${hue}, 90%, 55%, 1)`);
        }
        return { colors, hoverColors };
    }

    if (document.getElementById('deptPieChart') && pieData.length > 0) {
        const ctxPie = document.getElementById('deptPieChart').getContext('2d');
        const palette = generatePremiumPieColors(pieData.length);
        
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: palette.colors,
                    hoverBackgroundColor: palette.hoverColors,
                    borderColor: '#ffffff', // ขอบสีขาวให้ดูสะอาดตา
                    borderWidth: 3,         // ความหนาขอบเพื่อแยกชิ้นชัดเจน
                    hoverOffset: 12         // ทำให้โดนัทเด้งออกมาเวลามีเมาส์ชี้ เป็นมิติสวยงาม
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%', // เจาะรูตรงกลาง
                layout: {
                    padding: 20 // ให้มีพื้นที่เวลาชิ้นกราฟเด้ง
                },
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { family: 'Mitr', size: 12 },
                            usePointStyle: true, // จุดสีกลมๆ ดูโมเดิร์นกว่าสี่เหลี่ยม
                            padding: 15,
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: { family: 'Mitr', size: 14 },
                        bodyFont: { family: 'Mitr', size: 13 },
                        padding: 14,
                        cornerRadius: 12,
                        boxPadding: 6,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) { label += ' : '; }
                                label += context.parsed.toLocaleString() + ' ราย';
                                
                                // เพิ่ม % ใน tooltip
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = Math.round((context.parsed / total) * 100);
                                label += ' (' + percentage + '%)';
                                
                                return label;
                            }
                        }
                    }
                },
                // ทำให้คลิกที่กราฟแล้วแสดง Modal คล้ายกับที่กดลิตส์ข้างล่าง
                onClick: (e, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const depCode = pieDepCodes[index];
                        const depName = pieLabels[index];
                        viewPatientList(depCode, depName);
                    }
                },
                // เมาส์ชี้ให้เปลี่ยนเป็นรูปนิ้วมือ
                onHover: (e, elements) => {
                    e.native.target.style.cursor = elements.length ? 'pointer' : 'default';
                }
            }
        });
    }
});
</script>