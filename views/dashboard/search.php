<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">ค้นหาประวัติผู้ป่วยรายบุคคล</h2>
        
        <form method="get" action="<?= \yii\helpers\Url::to(['dashboard/search']) ?>" class="mb-8">
            <div class="flex gap-2">
                <input type="text" name="cid" value="<?= Html::encode($cid) ?>" 
                       placeholder="ระบุเลขบัตรประชาชน 13 หลัก..." 
                       class="flex-1 p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-hos-blue outline-none">
                <button type="submit" class="bg-hos-blue text-white px-6 py-3 rounded-lg hover:bg-blue-800 transition">
                    ค้นหาข้อมูล
                </button>
            </div>
        </form>

        <?php if ($patientData): ?>
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border-t-4 border-hos-blue">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">ชื่อ-นามสกุล</p>
                        <p class="text-lg font-bold"><?= $patientData['fname'] ?> <?= $patientData['lname'] ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">HN</p>
                        <p class="text-lg font-mono text-blue-600"><?= $patientData['hn'] ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-100 text-gray-600 text-sm">
                        <tr>
                            <th class="p-4">วันที่รับบริการ</th>
                            <th class="p-4">รหัสโรค (ICD10)</th>
                            <th class="p-4">ชื่อโรค</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($visitHistory as $visit): ?>
                        <tr class="hover:bg-blue-50 transition">
                            <td class="p-4 text-sm"><?= $visit['vstdate'] ?></td>
                            <td class="p-4 font-bold"><?= $visit['icd10'] ?></td>
                            <td class="p-4 text-sm text-gray-600"><?= $visit['diag_name'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($cid): ?>
            <div class="text-center p-10 bg-white rounded-xl border-2 border-dashed">
                <p class="text-gray-400">ไม่พบข้อมูลผู้ป่วยรายนี้ในระบบ HOSxP</p>
            </div>
        <?php endif; ?>
    </div>
</div>