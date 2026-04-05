<?php
/**
 * _patient_list_table_pttype.php
 * 
 * รับข้อมูล $patients จาก DashboardStat::getPTTypePatientList()
 * ซึ่ง GROUP BY main_dep มาแล้ว แต่ละ row = 1 แผนก
 * 
 * โครงสร้าง $patients row:
 *   - pttype       : รหัสสิทธิ
 *   - pttype_name  : ชื่อสิทธิ
 *   - department   : ชื่อแผนก (kskdepartment.department)
 *   - total        : จำนวนผู้ป่วยในแผนกนั้น
 */

// ✅ แก้ไข: ข้อมูลจาก DB GROUP BY main_dep แล้ว ใช้ตรงๆ เลย ไม่ต้อง loop group ใหม่
// ตัด Top 10 แผนกที่มีผู้ป่วยมากสุดสำหรับกราฟ (ข้อมูลมา ORDER BY total DESC แล้ว)
$chartData = !empty($patients) ? array_slice($patients, 0, 100) : [];

// เตรียม Label และ Data สำหรับ Chart.js
$chartLabels = array_column($chartData, 'department');
$chartCounts = array_map('intval', array_column($chartData, 'total'));

// คำนวณยอดรวมทั้งหมด
$grandTotal = array_sum(array_column($patients ?? [], 'total'));
?>

<?php if (!empty($patients)): ?>

    <!-- ===== ส่วนกราฟ ===== -->
    <div class="mb-6 p-4 bg-slate-50 rounded-3xl border border-slate-100">
        <h4 class="font-mitr text-sm font-bold text-slate-700 mb-1 flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-4 h-4 text-indigo-500"></i>
            จำนวนผู้ป่วยสิทธิ
            <span class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-xs">
                <?= htmlspecialchars($patients[0]['pttype'] ?? '') ?>
            </span>
            <span class="text-slate-500 font-normal">
                <?= htmlspecialchars($patients[0]['pttype_name'] ?? '') ?>
            </span>
            แยกตามแผนก
        </h4>
        <p class="text-xs text-slate-400 mb-3 ml-6">
            ยอดรวมทั้งหมด: 
            <span class="font-bold text-indigo-600"><?= number_format($grandTotal) ?> ราย</span>
        </p>
        <div style="height: 260px; position: relative;">
            <canvas id="pttypeDiagChart"></canvas>
        </div>
    </div>

    <!-- ===== ส่วนตาราง ===== -->
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">
                    <th class="py-3 px-3">#</th>
                    <th class="py-3 px-3">แผนก</th>
                    <th class="py-3 px-3">สิทธิการรักษา</th>
                    <th class="py-3 px-3 text-right">จำนวน (ราย)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                <?php foreach ($patients as $i => $pt): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3 px-3 text-slate-400 text-xs"><?= $i + 1 ?></td>
                    <td class="py-3 px-3 font-bold text-slate-700">
                        <?= htmlspecialchars($pt['department'] ?: 'ไม่ระบุแผนก') ?>
                    </td>
                    <td class="py-3 px-3">
                        <span class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded text-xs font-bold mr-1">
                            <?= htmlspecialchars($pt['pttype']) ?>
                        </span>
                        <span class="text-slate-500 text-xs">
                            <?= htmlspecialchars($pt['pttype_name'] ?: 'ไม่ระบุชื่อสิทธิ') ?>
                        </span>
                    </td>
                    <td class="py-3 px-3 text-right">
                        <span class="bg-emerald-50 text-emerald-700 px-3 py-0.5 rounded-full text-xs font-bold">
                            <?= number_format((int)$pt['total']) ?> ราย
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-slate-200 bg-slate-50">
                    <td colspan="3" class="py-3 px-3 font-bold text-slate-600 text-sm">รวมทั้งหมด</td>
                    <td class="py-3 px-3 text-right">
                        <span class="bg-indigo-600 text-white px-3 py-0.5 rounded-full text-xs font-bold">
                            <?= number_format($grandTotal) ?> ราย
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- ===== Script กราฟ ===== -->
    <script>
    (function() {
        // ✅ ใช้ข้อมูล department + total จาก PHP โดยตรง
        var labels = <?= json_encode($chartLabels, JSON_UNESCAPED_UNICODE) ?>;
        var counts = <?= json_encode($chartCounts) ?>;

        // สีไล่เฉดสำหรับแท่งกราฟ
        // Generate สีแบบ HSL วนไปเรื่อยๆ ไม่จำกัดจำนวน
        function generateColors(count) {
            var colors = [];
            for (var i = 0; i < count; i++) {
                var hue = Math.round((i * 360) / count); // กระจายเฉดสีเท่าๆ กัน
                colors.push('hsl(' + hue + ', 70%, 58%)');
            }
            return colors;
        }
        var bgColors = generateColors(labels.length);

        // รอให้ DOM และ Chart.js พร้อมก่อน render
        var maxWait = 20;
        var waited = 0;
        function tryRender() {
            var canvas = document.getElementById('pttypeDiagChart');
            if (!canvas || typeof Chart === 'undefined') {
                if (waited < maxWait) {
                    waited++;
                    setTimeout(tryRender, 200);
                }
                return;
            }

            // ทำลาย instance เก่า (ถ้า modal เปิด-ปิดซ้ำ)
            var existing = Chart.getChart(canvas);
            if (existing) existing.destroy();

            new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'จำนวน (ราย)',
                        data: counts,
                        backgroundColor: bgColors.map(c => c.replace('58%)', '58% / 0.85)')), // opacity
                        borderColor: bgColors,
                        borderWidth: 2,
                        borderRadius: 8,
                        barThickness: 'flex',
                        maxBarThickness: 35,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ' ' + ctx.parsed.y.toLocaleString() + ' ราย';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                callback: function(v) { return v.toLocaleString(); }
                            },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            ticks: {
                                font: { size: 10 },
                                maxRotation: 30,
                                callback: function(val, index) {
                                    // ตัดชื่อแผนกที่ยาวเกินไป
                                    var label = labels[index] || '';
                                    return label.length > 12 ? label.substring(0, 12) + '…' : label;
                                }
                            },
                            grid: { display: false }
                        }
                    }
                }
            });

            // Re-init lucide icons
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        setTimeout(tryRender, 300);
    })();
    </script>

<?php else: ?>
    <div class="text-center py-20 text-slate-400 italic font-mitr">
        <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3 text-slate-300"></i>
        <p>ไม่พบข้อมูลในช่วงเวลาดังกล่าว</p>
    </div>
<?php endif; ?>