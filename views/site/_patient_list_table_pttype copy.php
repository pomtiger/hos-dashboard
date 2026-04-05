<?php
// 1. ประมวลผลสรุป (ต้องการสรุปตาม pttype ที่ส่งมาในก้อน $patients)
$summary = [];
$chartData = []; 
if (!empty($patients)) {
    foreach ($patients as $pt) {
        // ใช้ pttype เป็น Key ในการรวมกลุ่ม
        $code = $pt['pttype'] ?: 'Unknown'; 
        if (!isset($summary[$code])) {
            $summary[$code] = [
                'code' => $code,
                'pttype_name' => $pt['pttype_name'] ?: 'ไม่ระบุชื่อสิทธิ',
                'count' => 0
            ];
        }
        $summary[$code]['count']++;
    }
    
    // เรียงลำดับจากมากไปน้อย
    uasort($summary, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    
    // ตัดเอาเฉพาะ Top 10
    $chartData = array_slice($summary, 0, 10);
}
?>

<?php if(!empty($patients)): ?>
    <div class="mb-8 p-4 bg-slate-50 rounded-3xl border border-slate-100">
        <h4 class="font-mitr text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-4 h-4 text-hos-blue"></i>
            สถิติสิทธิการรักษาที่พบแยกแผนก
        </h4>
        <div style="height: 250px; position: relative;">
            <canvas id="pttypeDiagChart"></canvas>
        </div>
    </div>

    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                    <!-- <th class="py-4 px-2">เวลา</th> -->
                    <!-- <th class="py-4 px-2">HN</th> -->
                    <!-- <th class="py-4 px-2">ชื่อ-นามสกุล</th> -->
                    <th class="py-4 px-2">สิทธิการรักษา</th>
                    <th class="py-4 px-2">dept</th>
                    <th class="py-4 px-2">จำนวน/ราย</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                <?php foreach($patients as $pt): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <!-- <td class="py-4 px-2 font-medium text-slate-500"><?= substr($pt['vsttime'], 0, 5) ?> น.</td> -->
                    <!-- <td class="py-4 px-2 font-bold text-indigo-600"><?= $pt['hn'] ?></td> -->
                    <!-- <td class="py-4 px-2 text-slate-700 font-bold"><?= $pt['fname'] . ' ' . $pt['lname'] ?></td> -->
                    <td class="py-4 px-2">
                        <span class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded text-xs font-bold mr-2"><?= $pt['pttype'] ?></span>
                        <span class="text-slate-500 text-xs"><?= $pt['pttype_name'] ?></span>
                    </td>
                    <td class="py-4 px-2 font-bold text-indigo-600"><?= $pt['department'] ?></td>
                    <td class="py-4 px-2 font-bold text-indigo-600"><?= $pt['total'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
    setTimeout(function() {
        const canvas = document.getElementById('pttypeDiagChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            const labels = <?= json_encode(array_column($chartData, 'pttype_name')) ?>;
            const counts = <?= json_encode(array_column($chartData, 'count')) ?>;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'จำนวน (ราย)',
                        data: counts,
                        backgroundColor: '#6366f1',
                        borderRadius: 8,
                        barThickness: 25,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } },
                        x: { ticks: { font: { size: 10 } }, grid: { display: false } }
                    }
                }
            });
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }, 400);
    </script>
<?php else: ?>
    <div class="text-center py-20 text-slate-400 italic font-mitr">ไม่พบข้อมูลรายชื่อผู้ป่วยในช่วงเวลาดังกล่าว</div>
<?php endif; ?>