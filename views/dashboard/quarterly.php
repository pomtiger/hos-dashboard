<div class="bg-slate-50 min-h-screen p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold text-slate-800 mb-6 border-l-4 border-hos-blue pl-4">รายงานผู้รับบริการรายไตรมาส (5 ปีย้อนหลัง)</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <p class="text-slate-400 text-sm font-semibold">ไตรมาสล่าสุด</p>
                <p class="text-3xl font-black text-hos-blue"><?= number_format($rawData[0]['total_visit'] ?? 0) ?></p>
                <p class="text-xs text-slate-400 mt-2">ข้อมูลจากฐานข้อมูล HOSxP</p>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-200">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-900 text-white">
                    <tr>
                        <th class="p-5 font-bold uppercase text-sm">ปีงบประมาณ</th>
                        <th class="p-5 font-bold uppercase text-sm text-center">ไตรมาส 1</th>
                        <th class="p-5 font-bold uppercase text-sm text-center">ไตรมาส 2</th>
                        <th class="p-5 font-bold uppercase text-sm text-center">ไตรมาส 3</th>
                        <th class="p-5 font-bold uppercase text-sm text-center">ไตรมาส 4</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php 
                    // จัดกลุ่มข้อมูลใหม่เพื่อแสดงผลในตาราง
                    $displayData = [];
                    foreach ($rawData as $row) {
                        $displayData[$row['fiscal_year']][$row['quarter']] = $row['total_visit'];
                    }

                    foreach ($displayData as $year => $qs): ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-5 font-bold text-slate-700"><?= $year + 543 ?></td>
                        <td class="p-5 text-center text-slate-600"><?= isset($qs['Q1']) ? number_format($qs['Q1']) : '-' ?></td>
                        <td class="p-5 text-center text-slate-600"><?= isset($qs['Q2']) ? number_format($qs['Q2']) : '-' ?></td>
                        <td class="p-5 text-center text-slate-600"><?= isset($qs['Q3']) ? number_format($qs['Q3']) : '-' ?></td>
                        <td class="p-5 text-center text-slate-600"><?= isset($qs['Q4']) ? number_format($qs['Q4']) : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>