<?php
$this->title = 'รายละเอียดแผนกฉุกเฉิน (ER)';
// ลงทะเบียน jQuery ของ Yii2 (เพื่อให้แน่ใจว่ามีใช้แน่นอน)
\yii\web\JqueryAsset::register($this);
?>

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

    /* Modal Styles */
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

    /* 3. Loading Overlay */
    #loading-screen {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(5px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    /* 4. Smooth Animations */
    .fade-up {
        animation: fadeUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        opacity: 0;
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* 5. Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


<div id="loading-screen">
    <div class="relative">
        <div class="w-20 h-20 border-4 border-slate-200 border-t-hos-blue rounded-full animate-spin"></div>
        <i data-lucide="ambulance" class="w-8 h-8 text-hos-blue absolute inset-0 m-auto animate-pulse"></i>
    </div>
    <p class="mt-6 font-mitr font-bold text-slate-600 tracking-wider text-lg">กำลังประมวลผลข้อมูล ER...</p>
</div>

<div class="bg-slate-50 min-h-screen p-6 font-sans fade-up">
    <div class="max-w-7xl mx-auto mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" 
                    class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 text-slate-600 hover:text-hos-blue transition-colors">
                    <i data-lucide="arrow-left" class="w-6 h-6"></i>
                </a>
                <div>
                    <h1 class="font-mitr text-3xl font-black text-slate-800">ER Dashboard</h1>
                    <p class="text-slate-500">ข้อมูลระหว่าง <span class="text-hos-blue font-bold"><?= date('d/m/Y', strtotime($start_date)) ?></span> ถึง <span class="text-hos-blue font-bold"><?= date('d/m/Y', strtotime($end_date)) ?></span></p>
                </div>
            </div>
            <!-- ส่วนเลือกวันที่่ -->
            <div class="bg-white p-3 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col md:flex-row items-stretch md:items-center gap-3">
                <div id="reportrange" class="flex-1 flex items-center gap-4 px-5 py-3 cursor-pointer hover:bg-slate-50 rounded-2xl transition-all border border-slate-100 group">
                     <i data-lucide="calendar" class="w-6 h-6 text-hos-blue group-hover:scale-110 transition-transform"></i>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">เลือกช่วงเวลา</span>
                        <input type="text" id="date-range" class="bg-transparent border-none p-0 focus:ring-0 font-bold text-slate-700 cursor-pointer w-full md:w-48 text-base" readonly>
                    </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-300 ml-auto"></i>
                </div>
    
                <form id="date-form-er" method="get" action="<?= \yii\helpers\Url::to(['site/er-detail']) ?>" class="flex items-center">
                    <input type="hidden" name="r" value="site/er-detail">
                    <input type="hidden" id="start_date" name="start_date" value="<?= $start_date ?>">
                    <input type="hidden" id="end_date" name="end_date" value="<?= $end_date ?>">
        
                    <button type="submit" class="w-full md:w-auto bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold shadow-lg hover:bg-slate-800 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center gap-2 group">
                        <i data-lucide="refresh-cw" class="w-5 h-5 group-hover:rotate-180 transition-transform duration-700"></i>
                        ประมวลผล
                    </button>
                </form>
            </div>
             <!-- ส่วนเลือกวันที่่ -->
        </div>
    </div>
   
    
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-50 transition-all hover:shadow-2xl">
            <h3 class="font-mitr font-bold text-xl text-slate-800 mb-8 flex items-center gap-2">
                <i data-lucide="pie-chart" class="w-5 h-5 text-red-500"></i>
                สัดส่วนประเภทผู้ป่วย
            </h3>
            <div class="relative">
                <canvas id="erTypeChart" height="300"></canvas>
            </div>
            <br>
            <!-- สถานะการจำหน่าย (ER Discharge) -->
            <!-- <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-50 transition-all hover:shadow-2xl mt-8"> -->
                <h3 class="font-mitr font-bold text-xl text-slate-800 mb-6 flex items-center gap-2">
                    <i data-lucide="log-out" class="w-5 h-5 text-purple-500"></i>ER Discharge Status
                </h3>
                <div class="space-y-4">
                    <?php if(!empty($dchStats)): ?>
                    <?php 
                        $maxDch = $dchStats[0]['total']; 
                        foreach($dchStats as $dch): 
                        $percent = ($maxDch > 0) ? ($dch['total'] / $maxDch) * 100 : 0;

                        // --- กำหนดสีตาม ID สถานะ (1-5) ---
                        // หมายเหตุ: $dch['er_dch_type'] ตัวแรกใน SQL ของคุณคือ ID
                        switch ($dch['dch_id']) {
                            case '1': // กลับบ้าน
                                $c = ['bar' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'bg' => 'bg-emerald-50'];
                                break;
                            case '2': // ส่งต่อสถานพยาบาลอื่น
                                $c = ['bar' => 'bg-amber-500', 'text' => 'text-amber-600', 'bg' => 'bg-amber-50'];
                                break;
                            case '3': // Admitted
                                $c = ['bar' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'bg' => 'bg-indigo-50'];
                                break;
                            case '4': // เสียชีวิต
                                $c = ['bar' => 'bg-slate-700', 'text' => 'text-slate-700', 'bg' => 'bg-slate-100'];
                                break;
                            case '5': // รับไว้สังเกตุอาการ
                                $c = ['bar' => 'bg-cyan-400', 'text' => 'text-cyan-600', 'bg' => 'bg-cyan-50'];
                                break;
                            default: // อื่นๆ หรือ ไม่ระบุ
                                $c = ['bar' => 'bg-slate-400', 'text' => 'text-slate-500', 'bg' => 'bg-slate-50'];
                        }
                    ?>
                    <div class="p-4 rounded-2xl border border-slate-100 hover:shadow-md transition-all group <?= $c['bg'] ?>/50 hover:bg-white">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full <?= $c['bar'] ?>"></div>
                                <span class="text-sm font-bold text-slate-600 group-hover:<?= $c['text'] ?> transition-colors">
                                    <?= $dch['er_dch_type_name'] ?? $dch['er_dch_type'] ?> 
                                    <?php // หมายเหตุ: ควรตั้ง Alias ใน SQL ให้ชื่อเป็น er_dch_type_name เพื่อไม่ให้ทับกับ ID ?>
                                </span>
                            </div>
                            <span class="text-xs font-black <?= $c['text'] ?> bg-white px-2 py-1 rounded-lg shadow-sm border border-slate-100">
                                <?= number_format($dch['total']) ?> <span class="text-[10px] font-medium opacity-70">ราย</span>
                            </span>
                        </div>
                
                        <div class="w-full bg-slate-200/50 h-2 rounded-full overflow-hidden">
                            <div class="<?= $c['bar'] ?> h-full rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(0,0,0,0.1)]" style="width: <?= $percent ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="text-center py-10">
                        <i data-lucide="inbox" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-slate-400 text-sm">ไม่พบข้อมูลการจำหน่าย</p>
                    </div>
                    <?php endif; ?>
                </div>
            <!-- </div> -->
            <!-- สถานะการจำหน่าย (ER Discharge) -->
        </div>
        

        <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-50">
            <div class="flex justify-between items-center mb-8">
                <h3 class="font-mitr font-bold text-xl text-slate-800 flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-hos-blue"></i>
                    10 อันดับการวินิจฉัย (ER)
                </h3>
                <button onclick="viewPatientList('011', 'แผนกฉุกเฉิน')" class="bg-hos-blue/10 text-hos-blue px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-hos-blue hover:text-white transition-all flex items-center gap-2 group">
                    <i data-lucide="users" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                    Detail
                </button>
            </div>
            
            <div class="space-y-6">
                <?php if(!empty($diagStats)): ?>
                    <?php foreach($diagStats as $row): ?>
                        <div class="group cursor-default">
                            <div class="flex items-center gap-5">
                                <div class="w-16 font-mono font-black text-hos-blue bg-blue-50 py-1 rounded-lg text-center group-hover:bg-hos-blue group-hover:text-white transition-colors">
                                    <?= $row['icd10'] ?>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="text-slate-600 font-medium truncate max-w-xs md:max-w-md"><?= $row['diag_name'] ?></span>
                                        <span class="font-black text-slate-800"><?= number_format($row['total']) ?> ราย</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                        <div class="bg-gradient-to-r from-hos-blue to-blue-400 h-full rounded-full transition-all duration-1000" 
                                             style="width: <?= ($diagStats[0]['total'] > 0) ? ($row['total'] / $diagStats[0]['total']) * 100 : 0 ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-20">
                        <i data-lucide="search-x" class="w-12 h-12 text-slate-300 mx-auto mb-4"></i>
                        <p class="text-slate-400 italic">ไม่พบข้อมูลในช่วงวันที่เลือก</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->render('_patient_modal_script', [
    'start_date' => $start_date,
    'end_date' => $end_date
]) ?>

<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>

<script>
$(function() {
    lucide.createIcons();

    // 1. Loading Interaction
    // $('#date-form-er').on('submit', function() {
    //     $('#loading-screen').css('display', 'flex').hide().fadeIn(300);
    //     $(this).find('button[type="submit"]').addClass('opacity-50 cursor-not-allowed').html('<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>');
    // });
    // 1. Loading Interaction แบบหน่วงเวลา
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

    // 2. DateRangePicker Logic
    var start = moment($('#start_date').val());
    var end = moment($('#end_date').val());

    function cb(start, end) {
        $('#date-range').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#start_date').val(start.format('YYYY-MM-DD'));
        $('#end_date').val(end.format('YYYY-MM-DD'));
    }

    // --- ส่วนที่เพิ่ม: คำนวณปีงบประมาณย้อนหลัง 5 ปี ---
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

    // วนลูปสร้างปีงบประมาณย้อนหลัง 5 ปี
    var currentYear = moment().month() >= 9 ? moment().year() + 1 : moment().year(); // ปีงบประมาณปัจจุบัน (ค.ศ.)
    for (var i = 1; i <= 3; i++) {
        var fiscalYear = currentYear - i;
        var label = 'ปีงบประมาณ ' + (fiscalYear + 543); // แปลงเป็น พ.ศ.
        
        // วันที่เริ่มต้น: 1 ต.ค. ของปี (fiscalYear - 1)
        // วันที่สิ้นสุด: 30 ก.ย. ของปี fiscalYear
        var startDate = moment().year(fiscalYear - 1).month(9).date(1);
        var endDate = moment().year(fiscalYear).month(8).date(30);
        
        customRanges[label] = [startDate, endDate];
    }
    // -------------------------------------------
    // เรียกใช้งาน DateRangePicker <div id="reportrange" class="flex items-c บรรทัดประมาณ 97 เด้อ
    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: customRanges, // ใช้ Object ที่เราคำนวณไว้
        showDropdowns: true,  // เพิ่ม dropdown ให้เลือกปี/เดือนได้ง่ายขึ้นในปฏิทิน
        alwaysShowCalendars: true,
        opens: 'left',
        buttonClasses: 'px-4 py-2 rounded-xl font-bold transition-all',
        applyButtonClasses: 'bg-hos-blue text-white',
        cancelButtonClasses: 'bg-slate-100 text-slate-600',
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: 'ตกลง',
            cancelLabel: 'ยกเลิก',
            customRangeLabel: 'กำหนดเอง',
            daysOfWeek: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
            monthNames: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
        }
    }, cb);

    cb(start, end);

    // 3. Smooth Chart.js Configuration
    const ctx = document.getElementById('erTypeChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['ฉุกเฉิน', 'อุบัติเหตุ', 'เคสทั่วไป', 'UC นอกเขตต่างจังหวัด'],
            datasets: [{
                data: [
                    <?= (int)($typeStats['emergency_case'] ?? 0) ?>, 
                    <?= (int)($typeStats['accident_case'] ?? 0) ?>, 
                    <?= (int)($typeStats['general_case'] ?? 0) ?>, 
                    <?= (int)($typeStats['another_province'] ?? 0) ?>
                ],
                backgroundColor: ['#ef4444', '#F59C27', '#10b981', '#7927F5'],
                borderWidth: 8,
                borderColor: '#ffffff',
                hoverOffset: 20
            }]
        },
        options: { 
            cutout: '75%', 
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 2000,
                easing: 'easeOutElastic'
            },
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: {
                        padding: 30,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { family: 'Mitr', size: 13, weight: '500' },
                        color: '#64748b'
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 15,
                    titleFont: { family: 'Mitr', size: 14 },
                    bodyFont: { family: 'Mitr', size: 13 },
                    cornerRadius: 12,
                    displayColors: true
                }
            } 
        }
    });
});
</script>