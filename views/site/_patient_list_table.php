<?php
// 1. ประมวลผลสรุปจำนวน ICD10 จาก $patients
$diagSummary = [];
$chartData = []; // ประกาศตัวแปรไว้ก่อนเพื่อป้องกัน Error กรณีไม่มีข้อมูล
if (!empty($patients)) {
    foreach ($patients as $pt) {
        $code = $pt['pdx'] ?: 'Unknown';
        if (!isset($diagSummary[$code])) {
            $diagSummary[$code] = [
                'code' => $code,
                'name' => $pt['diag_name'] ?: 'ไม่ระบุการวินิจฉัย',
                'count' => 0
            ];
        }
        $diagSummary[$code]['count']++;
    }
    
    // เรียงลำดับจากมากไปน้อย
    uasort($diagSummary, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    
    // ตัดเอาเฉพาะ Top 5 หรือ Top 10 มาทำกราฟ (เพื่อความสวยงาม)
    $chartData = array_slice($diagSummary, 0, 10);
}
?>

<?php if(!empty($patients)): ?>
    <div class="mb-8 p-4 bg-slate-50 rounded-3xl border border-slate-100">
        <h4 class="font-mitr text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-4 h-4 text-hos-blue"></i>
            สรุปกลุ่มโรคที่พบสูงสุด (Top 10 ICD10)
        </h4>
        <div style="height: 250px; position: relative;">
            <canvas id="deptDiagChart"></canvas>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-xs font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                    <th class="py-4 px-2">เวลา</th>
                    <th class="py-4 px-2">HN</th>
                    <!-- <th class="py-4 px-2">ชื่อ-นามสกุล</th> -->
                    <th class="py-4 px-2">วินิจฉัย (ICD10)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                <?php foreach($patients as $pt): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-2 font-medium text-slate-500"><?= substr($pt['vsttime'], 0, 5) ?></td>
                    <td class="py-4 px-2 font-bold text-hos-blue"><?= $pt['hn'] ?></td>
                    <!-- <td class="py-4 px-2 text-slate-700 font-bold"><?= $pt['fullname'] ?></td> -->
                    <td class="py-4 px-2 text-slate-500">
                        <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-xs font-bold mr-2"><?= $pt['pdx'] ?></span>
                        <?= $pt['diag_name'] ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    setTimeout(function() {
        const canvas = document.getElementById('deptDiagChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');
        
        // เตรียมข้อมูลจาก PHP สู่ JS
        const labels = <?= json_encode(array_column($chartData, 'code')) ?>;
        const counts = <?= json_encode(array_column($chartData, 'count')) ?>;
        const fullNames = <?= json_encode(array_column($chartData, 'name')) ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'จำนวนเคส',
                    data: counts,
                    backgroundColor: 'rgba(37, 99, 235, 0.8)', // hos-blue
                    borderRadius: 8,
                    barThickness: 'flex',
                    barThickness: 30,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                    return ' ' + context.parsed.y + ' ราย';
                                },
                                afterLabel: function(context) {
                                    return fullNames[context.dataIndex];
                                }
                        }
                    }
                },
                scales: {
                    // y: { beginAtZero: true, grid: { display: false } },
                    y: { beginAtZero: true, ticks: { stepSize: 1 }},
                    x: { grid: { display: false } }
                }
            }
        });
    }
        
        // รีเฟรช Lucide Icons (ถ้ามีใน Modal)
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 300);
    </script>

<?php else: ?>
    <div class="text-center py-10 text-slate-400 italic">ไม่พบรายชื่อผู้ป่วยในแผนกนี้</div>
<?php endif; ?>