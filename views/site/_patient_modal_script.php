<!-- <div id="patientModal" class="custom-modal p-4" onclick="if(event.target == this) closeModal()">
    <div class="bg-white w-full max-w-4xl max-h-[90vh] rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 id="modalDeptName" class="font-mitr text-xl font-bold text-slate-800">รายชื่อผู้ป่วย</h3>
                <p class="text-sm text-slate-400">ประมวลผลตามช่วงวันที่เลือก</p>
            </div>
            <button onclick="closeModal()" class="p-2 hover:bg-white hover:shadow-sm rounded-full transition-all text-slate-400 hover:text-red-500">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div id="modalContent" class="flex-1 overflow-y-auto p-6 custom-scrollbar">
            <div class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-200 border-t-hos-blue"></div>
            </div>
        </div>
    </div>
</div> -->

<!-- new modal -->
<div id="patientModal" class="custom-modal p-4" onclick="if(event.target == this) closeModal()">
    <div class="bg-white w-full max-w-4xl max-h-[85vh] rounded-[2.5rem] shadow-2xl flex flex-col overflow-hidden border border-slate-200/50 transform transition-all">
        
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-hos-blue/10 text-hos-blue rounded-2xl flex items-center justify-center shadow-sm border border-hos-blue/5">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 id="modalDeptName" class="font-mitr text-xl font-bold text-slate-800 tracking-tight">รายชื่อผู้ป่วย</h3>
                        <span class="bg-emerald-100 text-emerald-600 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider animate-pulse">Live</span>
                    </div>
                    <p class="text-[13px] text-slate-400 flex items-center gap-2 mt-1">
                        <i data-lucide="calendar-range" class="w-3.5 h-3.5 text-hos-blue"></i>
                        ประมวลผลวันที่: 
                        <span class="text-slate-700 font-bold bg-white shadow-sm border border-slate-100 px-2.5 py-0.5 rounded-lg ml-1">
                            <?= Yii::$app->formatter->asDate($start_date, 'php:d/m/Y') ?>
                        </span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-700 font-bold bg-white shadow-sm border border-slate-100 px-2.5 py-0.5 rounded-lg">
                            <?= Yii::$app->formatter->asDate($end_date, 'php:d/m/Y') ?>
                        </span>
                    </p>
                </div>
            </div>

            <button onclick="closeModal()" class="group p-2 bg-white hover:bg-red-50 rounded-full transition-all duration-300 border border-slate-100 hover:border-red-100 shadow-sm">
                <i data-lucide="x" class="w-6 h-6 text-slate-400 group-hover:text-red-500 transition-colors"></i>
            </button>
        </div>

        <div id="modalContent" class="flex-1 overflow-y-auto p-8 custom-scrollbar bg-white">
            <div class="flex flex-col justify-center items-center py-24 gap-4">
                <div class="relative flex h-16 w-16">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-hos-blue opacity-10"></span>
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-slate-50 border-t-hos-blue shadow-inner"></div>
                </div>
                <div class="text-center">
                    <p class="text-slate-600 font-bold text-lg font-mitr animate-pulse">กำลังเตรียมข้อมูล...</p>
                    <p class="text-slate-400 text-sm">กรุณารอสักครู่ ระบบกำลังดึงรายชื่อจากฐานข้อมูล</p>
                </div>
            </div>
        </div>

        <div class="px-8 py-4 bg-slate-50/80 border-t border-slate-100 flex justify-between items-center">
            <p class="text-[11px] text-slate-400 font-medium">
                <i data-lucide="info" class="w-3 h-3 inline-block mr-1"></i>
                แสดงผลเฉพาะข้อมูลที่มีการบันทึกสมบูรณ์แล้วเท่านั้น
            </p>
            <button onclick="closeModal()" class="px-5 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-700 transition-all shadow-md">
                ตกลง
            </button>
        </div>
    </div>
</div>
<!-- new modal -->

<script>
function viewPatientList(depcode, depname) {
    $('#modalDeptName').text('แผนก: ' + depname);
    $('#patientModal').addClass('active');
    $('body').addClass('modal-open');
    
    // ดึงข้อมูลผ่าน AJAX (ตรวจสอบว่ามี actionGetPatientList ใน SiteController แล้ว)
    $.get('<?= \yii\helpers\Url::to(['site/get-patient-list']) ?>', {
        depcode: depcode,
        start_date: '<?= $start_date ?? date('Y-m-d') ?>',
        end_date: '<?= $end_date ?? date('Y-m-d') ?>'
    }, function(data) {
        $('#modalContent').html(data);
        if(typeof lucide !== 'undefined') { lucide.createIcons(); }
    }).fail(function() {
        $('#modalContent').html('<div class="text-center py-10 text-red-500 font-bold">ไม่สามารถดึงข้อมูลได้</div>');
    });
}

function closeModal() {
    $('#patientModal').removeClass('active');
    $('body').removeClass('modal-open');
    $('#modalContent').html('<div class="flex justify-center py-20"><div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-200 border-t-hos-blue"></div></div>');
}
</script>

<style>
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
</style>