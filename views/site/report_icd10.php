<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'รายงานรายชื่อผู้ป่วยตามรหัสโรค';

// ลงทะเบียน CSS/JS (ดึงจาก CDN เหมือนหน้าหลักของคุณ)
$this->registerCssFile('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css');
$this->registerCssFile('https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css');
$this->registerJsFile('https://code.jquery.com/jquery-3.6.0.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js');
?>

<style>
    .font-mitr { font-family: 'Mitr', sans-serif; }
    .daterangepicker { border-radius: 1.5rem !important; border: none !important; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important; padding: 10px !important; z-index: 1000 !important; }
    .daterangepicker .applyBtn { background-color: #2563eb !important; border: none !important; border-radius: 0.75rem !important; }
    /* สไตล์สำหรับตาราง DataTables ให้เข้ากับ Tailwind */
    .dataTables_wrapper .dataTables_filter input { border-radius: 1rem; border: 1px solid #e2e8f0; padding: 0.5rem 1rem; margin-bottom: 1rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #2563eb !important; color: white !important; border: none !important; border-radius: 0.75rem; }
</style>

<div class="bg-slate-50 min-h-screen p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-mitr text-3xl font-bold text-slate-800">รายชื่อผู้ป่วยตามรหัสโรค OPD</h2>
                <p class="text-slate-500">ค้นหาข้อมูลตามรหัส ICD10: <span class="font-bold text-rose-600"><?= Html::encode($icd10) ?></span></p>
            </div>
            <div class="flex gap-2">
                <button onclick="exportToExcel()" class="flex items-center gap-2 bg-emerald-50 text-emerald-600 px-4 py-2.5 rounded-2xl font-bold text-sm hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Excel
                </button>
            </div>
            
        </div>

        <div class="bg-white p-4 rounded-[2.5rem] shadow-sm border border-slate-100">
            <p class="text-slate-500">ระบุ ICD10 ที่ต้องการค้น เช่น : <span class="text-blue-600">e110-e119 หรือ e110,e119</span></p>
            <form id="search-form" method="get" action="<?= Url::to(['site/report-icd10']) ?>" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <input type="hidden" name="r" value="site/report-icd10">
                <input type="hidden" id="start_date" name="start_date" value="<?= $start_date ?>">
                <input type="hidden" id="end_date" name="end_date" value="<?= $end_date ?>">

                <div class="md:col-span-5">
                    <div class="relative flex items-center">
                        <i data-lucide="search" class="absolute left-4 w-5 h-5 text-slate-400"></i>
                        <input type="text" name="icd10" value="<?= Html::encode($icd10) ?>" 
                               placeholder="รหัส ICD10 (เช่น I60-I69 หรือ I60,I61)"
                               class="w-full pl-12 pr-4 py-4 rounded-2xl border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-700">
                    </div>
                </div>

                <div class="md:col-span-4">
                    <div id="reportrange_display" class="flex items-center gap-3 px-5 py-4 cursor-pointer bg-slate-50 hover:bg-slate-100 rounded-2xl transition-all border border-slate-100 group">
                        <i data-lucide="calendar" class="w-5 h-5 text-blue-600"></i>
                        <span id="date_text" class="font-bold text-slate-700 text-sm">เลือกช่วงเวลา</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 ml-auto"></i>
                    </div>
                </div>

                <div class="md:col-span-3">
                    <button type="submit" class="w-full h-full bg-slate-900 text-white py-4 rounded-2xl font-bold shadow-lg hover:bg-slate-800 hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                        <i data-lucide="refresh-cw" class="w-5 h-5"></i> ประมวลผลรายงาน
                    </button>
                </div>
            </form>
        </div>
        <!-- ผลรวมจำนวนค้นหา -->
        <?php
            $maleCount = 0;
            $femaleCount = 0;
            $ageUnder15 = 0;
            $age15to60 = 0;
            $ageOver60 = 0;

            foreach ($data as $row) {
                // แยกเพศ (สมมติว่าฟิลด์ sex มีค่า '1'=ชาย, '2'=หญิง หรือตามโครงสร้าง HOSxP)
                if ($row['sex'] == '1') $maleCount++;
                else if ($row['sex'] == '2') $femaleCount++;

                // แยกอายุ
                $age = $row['age_y'] ?? 0; // หรือคำนวณจากวันเกิดถ้าใน $data ไม่มี age_y
                if ($age < 15) $ageUnder15++;
                else if ($age <= 60) $age15to60++;
                else $ageOver60++;
            }
        ?>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

            <div class="bg-white p-4 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shrink-0">
                    <i data-lucide="database" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">ทั้งหมด</p>
                    <h3 class="text-xl font-black text-slate-700"><?= number_format(count($data)) ?> <span class="text-xs font-normal">รายการ</span></h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center shrink-0">
                    <i data-lucide="venus-and-mars" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">ชาย / หญิง</p>
                    <h3 class="text-xl font-black text-slate-700">
                        <span class="text-blue-500"><?= $maleCount ?></span> 
                        <span class="text-slate-300 mx-1">/</span> 
                        <span class="text-rose-400"><?= $femaleCount ?></span>
                    </h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shrink-0">
                    <i data-lucide="baby" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider"> < 15 / 15-60 ปี</p>
                    <h3 class="text-xl font-black text-slate-700">
                        <?= $ageUnder15 ?> <span class="text-slate-300 mx-1">/</span> <?= $age15to60 ?>
                    </h3>
                </div>
            </div>

            <div class="bg-white p-4 rounded-[2rem] shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shrink-0">
                    <i data-lucide="heart-pulse" class="w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">สูงอายุ (60+)</p>
                    <h3 class="text-xl font-black text-slate-700"><?= $ageOver60 ?> <span class="text-xs font-normal">ราย</span></h3>
                </div>
            </div>

        </div>
        <!-- กล่องผลรวมจำนวนค้นหา -->
         

        <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden">
            <div class="p-8">
                <div class="overflow-x-auto">
                    <table id="patientTable" class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-slate-400 text-[11px] uppercase tracking-[0.15em] border-b border-slate-100">
                                <th class="pb-4 px-2">วันที่รับบริการ</th>
                                <th class="pb-4 px-2">HN</th>
                                <th class="pb-4 px-2">CID</th>
                                <th class="pb-4 px-2">ชื่อ-นามสกุล</th>
                                <th class="pb-4 px-2">การวินิจฉัย</th>
                                <th class="pb-4 px-2">ที่อยู่ / เบอร์โทร</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-50">
                            <?php foreach ($data as $row): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="py-5 px-2">
                                    <div class="font-bold text-slate-700"><?= date('d/m/Y', strtotime($row['vstdate'])) ?></div>
                                    <div class="text-[10px] text-slate-400">Visit Date</div>
                                </td>
                                <td class="py-5 px-2">
                                    <div class="font-bold text-blue-600"><?= $row['hn'] ?></div>
                                </td>
                                <td class="py-5 px-2">
                                    <div class="text-[10px] text-slate-400 font-mono"><?= $row['cid'] ?></div>
                                </td>
                                <td class="py-5 px-2 font-bold text-slate-700">
                                    <?= $row['pname'].$row['fname'].' '.$row['lname'] ?>
                                </td>
                                <td class="py-5 px-2">
                                    <span class="inline-block bg-indigo-50 text-rose-600 px-3 py-1 rounded-lg text-xs font-black mb-1"><?= $row['icd10'] ?></span>
                                    <div class="text-[11px] text-slate-500 leading-tight max-w-[200px]"><?= $row['diag_name'] ?></div>
                                </td>
                                <td class="py-5 px-2">
                                    <div class="text-xs text-slate-600 leading-relaxed">
                                        <?= "บ้านเลขที่ ".$row['addrpart']." ม.".$row['moopart']." ".$row['full_name'] ?>
                                    </div>
                                    <div class="flex items-center gap-1 text-blue-500 font-bold mt-1">
                                        <i data-lucide="phone" class="w-3 h-3"></i>
                                        <span class="tracking-widest"><?= $row['hometel'] ?: '-' ?></span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    /* ── DateRangePicker Configuration ── */
    // var startStr = '<?= $start_date ?>';
    // var endStr   = '<?= $end_date ?>';
    var startStr = '<?= $start_date ?: date('Y-m-d') ?>';
    var endStr   = '<?= $end_date ?: date('Y-m-d') ?>';
    var start = moment(startStr), end = moment(endStr);

    function updateDateDisplay(s, e) {
        // $('#date_text').text(s.format('D MMM YYYY') + ' - ' + e.format('D MMM YYYY'));  //เปลี่ยนจาก 30 Mar 2026 - 30 Mar 2026 เป็น 30/03/2026
        $('#date_text').text(s.format('DD/MM/YYYY') + ' - ' + e.format('DD/MM/YYYY'));
        //DB Format
        $('#start_date').val(s.format('YYYY-MM-DD'));
        $('#end_date').val(e.format('YYYY-MM-DD'));
    }

    // คำนวณปีงบประมาณอัตโนมัติ
    var fy = moment().month() >= 9 ? moment().year() + 1 : moment().year();
    var ranges = {
        'วันนี้': [moment(), moment()],
        '7 วันล่าสุด': [moment().subtract(6, 'days'), moment()],
        'เดือนนี้': [moment().startOf('month'), moment().endOf('month')],
        'เดือนที่แล้ว': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        'ปีงบฯปัจจุบัน': [
            moment().month() >= 9 ? moment().month(9).date(1) : moment().subtract(1,'year').month(9).date(1),
            moment().month() >= 9 ? moment().add(1,'year').month(8).date(30) : moment().month(8).date(30)
        ]
    };
    // เพิ่มปีงบย้อนหลัง 2-3 ปี
    for(var i=1; i<=3; i++){
        var y = fy - i;
        ranges['ปีงบฯ ' + (y+543)] = [moment().year(y-1).month(9).date(1), moment().year(y).month(8).date(30)];
    }

    $('#reportrange_display').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: ranges,
        alwaysShowCalendars: true,
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: 'ตกลง',
            cancelLabel: 'ยกเลิก',
            customRangeLabel: 'ระบุเอง',
            daysOfWeek: ['อา','จ','อ','พ','พฤ','ศ','ส'],
            monthNames: ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.']
        }
    }, updateDateDisplay);

    updateDateDisplay(start, end);

    /* ── DataTables ── */
    if ($.fn.DataTable) {
        $('#patientTable').DataTable({
            pageLength: 10,
            order: [[0, 'asc']], //เรียงมากไปน้อย นอกจากปรับใน DashboardStat แล้ว
            language: {
                search: "_INPUT_",
                searchPlaceholder: "ค้นหาในตาราง...",
                lengthMenu: "แสดง _MENU_ รายการ",
                info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                paginate: { next: "ถัดไป", previous: "ก่อนหน้า" }
            },
            drawCallback: function() { lucide.createIcons(); }
        });
    }
});

/* ── Export Function ── */
function exportToExcel() {
    var table = document.getElementById("patientTable");
    var wb = XLSX.utils.table_to_book(table, {sheet: "รายชื่อผู้ป่วย"});
    XLSX.writeFile(wb, "Report_ICD10_OPD_<?= $icd10 ?>.xlsx");
}
</script>