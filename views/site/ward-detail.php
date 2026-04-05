<?php
$this->title = 'รายละเอียดการ Admit ราย Ward';

// คำนวณภาพรวมโรงพยาบาล
$totalBeds = array_sum(array_column($wardData, 'bedcount'));
$totalAdmit = array_sum(array_column($wardData, 'current_admit'));
$occupancyRate = $totalBeds > 0 ? ($totalAdmit / $totalBeds) * 100 : 0;
?>

<div class="bg-slate-50 min-h-screen p-6 font-sans fade-up">
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <a href="<?= \yii\helpers\Url::to(['/site/index']) ?>" 
                class="inline-flex items-center gap-3 px-5 py-2.5 bg-white rounded-2xl shadow-sm border border-slate-100 text-slate-600 hover:bg-slate-50 hover:shadow-md hover:text-hos-blue transition-all duration-300 group">
                    <i data-lucide="arrow-left" class="w-5 h-5 group-hover:-translate-x-1 transition-transform"></i>
                    <span class="font-mitr font-bold text-sm tracking-wide">กลับหน้าหลัก</span>
            </a>
            <div>
                <h1 class="font-mitr text-3xl font-black text-slate-800 tracking-tight">ระบบติดตามสถานะเตียง</h1>
                <p class="text-slate-500 italic">ข้อมูล ณ วันที่ <?= date('d/m/Y H:i') ?> น.</p>
            </div>
            <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="text-right">
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Overall Occupancy</p>
                    <p class="text-xl font-black text-hos-blue"><?= number_format($occupancyRate, 2) ?>%</p>
                </div>
                <div class="w-12 h-12 rounded-full border-4 border-slate-100 border-t-hos-blue flex items-center justify-center">
                    <i data-lucide="percent" class="w-5 h-5 text-hos-blue"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 flex items-center gap-5 group hover:shadow-md transition-all">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="users" class="w-8 h-8"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-400">Total Current IPD</p>
                    <h3 class="text-3xl font-black text-slate-800"><?= number_format($totalAdmit) ?> <span class="text-sm font-normal text-slate-400">ราย</span></h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 flex items-center gap-5 group hover:shadow-md transition-all">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="user-plus" class="w-8 h-8"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-400">Admit Today</p>
                    <h3 class="text-3xl font-black text-emerald-600"><?= number_format(array_sum(array_column($wardData, 'admit_today'))) ?></h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100 flex items-center gap-5 group hover:shadow-md transition-all">
                <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="user-minus" class="w-8 h-8"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-400">Discharge Today</p>
                    <h3 class="text-3xl font-black text-amber-600"><?= number_format(array_sum(array_column($wardData, 'discharge_today'))) ?></h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Radar Chart: ใช้กราฟใยแมงมุมเพื่อเปรียบเทียบ Occupancy Rate ของแต่ละ Ward -->
            <div class="lg:col-span-1 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                <h3 class="font-mitr font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <i data-lucide="pie-chart" class="text-hos-blue"></i> อัตราการครองเตียงราย Ward
                </h3>
                <div style="height: 400px; position: relative;">
                    <canvas id="wardOccupancyChart"></canvas>
                </div>
            </div>

            <!-- Radar Chart: ใช้กราฟใยแมงมุมเพื่อเปรียบเทียบ Occupancy Rate ของแต่ละ Ward -->

            <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-50">
                                <th class="pb-4 px-2">Ward</th>
                                <th class="pb-4 px-2 text-center">เตียงทั้งหมด</th>
                                <th class="pb-4 px-2 text-center">ครองเตียง</th>
                                <th class="pb-4 px-2">%</th>
                                <th class="pb-4 px-2 text-center text-emerald-500">Admit</th>
                                <th class="pb-4 px-2 text-center text-amber-500">D/C</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach($wardData as $ward): 
                                $percent = $ward['bedcount'] > 0 ? ($ward['current_admit'] / $ward['bedcount']) * 100 : 0;
                                $barColor = $percent > 90 ? 'bg-red-500' : ($percent > 70 ? 'bg-amber-500' : 'bg-hos-blue');
                            ?>
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="py-4 px-2">
                                    <div class="font-bold text-slate-700"><?= $ward['ward_name'] ?></div>
                                    <div class="text-[10px] text-slate-400 font-mono">CODE: <?= $ward['ward'] ?></div>
                                </td>
                                <td class="py-4 px-2 text-center font-bold text-slate-500"><?= $ward['bedcount'] ?></td>
                                <td class="py-4 px-2 text-center font-black text-slate-800"><?= $ward['current_admit'] ?></td>
                                <td class="py-4 px-2 w-48">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 bg-slate-100 h-2 rounded-full overflow-hidden">
                                            <div class="<?= $percent > 90 ? 'bg-red-600 animate-pulse' : $barColor ?> h-full rounded-full transition-all duration-1000" 
                                                style="width: <?= min($percent, 100) ?>%"></div>
                                        </div>
                                        <span class="text-xs font-bold <?= $percent > 90 ? 'text-red-600 animate-bounce' : 'text-slate-600' ?>">
                                            <?= number_format($percent, 0) ?>%
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-2 text-center font-bold text-emerald-600 bg-emerald-50/30 group-hover:bg-emerald-50"><?= $ward['admit_today'] ?></td>
                                <td class="py-4 px-2 text-center font-bold text-amber-600 bg-amber-50/30 group-hover:bg-amber-50"><?= $ward['discharge_today'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

// ใช้ window.onload หรือ document.ready เพื่อให้แน่ใจว่า Canvas พร้อมใช้งาน
window.addEventListener('load', function() {
    const canvas = document.getElementById('wardOccupancyChart');
    if (!canvas) return; // กัน Error ถ้าหา canvas ไม่เจอ

    const ctx = canvas.getContext('2d');
    
    // เตรียมข้อมูลสำหรับกราฟ ดึงข้อมูลจาก PHP
    const labels = <?= json_encode(array_column($wardData, 'ward_name')) ?>;
    const dataValues = <?= json_encode(array_map(function($w) {
        return $w['bedcount'] > 0 ? round(($w['current_admit'] / $w['bedcount']) * 100, 1) : 0;
    }, $wardData)) ?>;

new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: 'อัตราครองเตียง (%)',
                data: dataValues,
                fill: true,
                backgroundColor: 'rgba(0, 91, 150, 0.2)',
                borderColor: '#005b96',
                borderWidth: 2,
                pointBackgroundColor: '#005b96',
                pointBorderColor: '#fff',
                pointHoverRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    min: 0,
                    max: 100, // ล็อกขอบไว้ที่ 100% ถ้าเกินจะพุ่งทะลุสวยๆ
                    ticks: {
                        stepSize: 20,
                        display: false // ซ่อนตัวเลขสเกลเพื่อให้กราฟดูคลีน
                    },
                    pointLabels: {
                        font: {
                            family: 'Kanit',
                            size: 12
                        }
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ครองเตียง: ' + context.raw + '%';
                        }
                    }
                }
            }
        }
    });
    lucide.createIcons();
});
</script>