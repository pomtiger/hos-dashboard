<?php
/** @var array $labels      — Top 20 สำหรับกราฟ */
/** @var array $data        — Top 20 สำหรับกราฟ */
/** @var array $diagStats   — ทั้งหมด สำหรับตาราง */
/** @var int   $uniqueDiagCount */
/** @var string $start_date */
/** @var string $end_date */
$this->title = 'รายละเอียดการวินิจฉัยโรค OPD';

$totalPatients = array_sum($data ?? []);
$topDiag = !empty($diagStats) ? $diagStats[0] : null;
$maxVal  = !empty($diagStats) ? max(array_column($diagStats, 'total')) : 1;
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Mitr:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;700&display=swap" rel="stylesheet">

<style>
:root {
    --blue-deep:   #0f172a;  --blue-mid:    #1e3a8a;
    --blue-bright: #3b82f6;  --blue-light:  #dbeafe;
    --accent:      #06b6d4;  --accent2:     #8b5cf6;
    --success:     #10b981;  --warn:        #f59e0b;
    --bg:          #f0f4ff;  --card:        #ffffff;
    --text-main:   #0f172a;  --text-muted:  #64748b;
    --border:      #e2e8f0;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body { background: var(--bg); }

.dd-wrap {
    font-family: 'Mitr', 'Sarabun', sans-serif;
    background: var(--bg); min-height: 100vh;
    padding: 2rem 1.5rem 4rem; position: relative;
}
.dd-wrap::before {
    content: ''; position: fixed; top: -120px; right: -120px;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 70%);
    border-radius: 50%; pointer-events: none; z-index: 0;
}
.dd-wrap::after {
    content: ''; position: fixed; bottom: -80px; left: -80px;
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(6,182,212,0.10) 0%, transparent 70%);
    border-radius: 50%; pointer-events: none; z-index: 0;
}
.dd-inner { max-width: 1100px; margin: 0 auto; position: relative; z-index: 1; }

.topbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:2rem; flex-wrap:wrap; gap:1rem; }
.topbar-left { display:flex; align-items:center; gap:1rem; }
.back-btn {
    width:44px; height:44px; background:var(--card); border:1px solid var(--border);
    border-radius:14px; display:flex; align-items:center; justify-content:center;
    text-decoration:none; color:var(--text-muted); transition:all .2s;
    box-shadow:0 2px 8px rgba(0,0,0,.06);
}
.back-btn:hover { background:var(--blue-mid); color:white; border-color:var(--blue-mid); transform:translateX(-2px); }
.page-title { font-size:1.6rem; font-weight:700; color:var(--blue-mid); letter-spacing:-.02em; line-height:1.1; }
.page-subtitle { font-size:.75rem; font-weight:400; color:var(--text-muted); margin-top:2px; }
.export-btns { display:flex; gap:.5rem; }
.btn-export {
    display:flex; align-items:center; gap:6px; padding:.5rem 1.1rem;
    border:none; border-radius:12px; font-family:'Mitr',sans-serif;
    font-size:.78rem; font-weight:600; cursor:pointer; transition:all .2s;
    box-shadow:0 2px 8px rgba(0,0,0,.1);
}
.btn-export.excel { background:linear-gradient(135deg,#16a34a,#22c55e); color:white; }
.btn-export.excel:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(22,163,74,.35); }
.btn-export.pdf   { background:linear-gradient(135deg,#dc2626,#f87171); color:white; }
.btn-export.pdf:hover   { transform:translateY(-2px); box-shadow:0 6px 16px rgba(220,38,38,.35); }

.filter-card {
    background:var(--card); border:1px solid var(--border); border-radius:24px;
    padding:1.2rem 1.8rem; margin-bottom:2rem;
    display:flex; align-items:flex-end; gap:1rem; flex-wrap:wrap;
    box-shadow:0 4px 20px rgba(0,0,0,.05);
}
.filter-label { font-size:.68rem; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.1em; margin-bottom:6px; }
.filter-input-wrap { position:relative; flex:1; min-width:280px; }
.filter-input-wrap i[data-lucide] { position:absolute; left:14px; top:50%; transform:translateY(-50%); width:16px; height:16px; color:var(--blue-bright); pointer-events:none; }
.filter-input {
    width:100%; padding:.75rem 1rem .75rem 2.6rem;
    background:var(--bg); border:1.5px solid var(--border); border-radius:14px;
    font-family:'Mitr',sans-serif; font-size:.9rem; font-weight:600;
    color:var(--text-main); cursor:pointer; transition:border-color .2s; outline:none;
}
.filter-input:focus { border-color:var(--blue-bright); }
.btn-submit {
    display:flex; align-items:center; gap:8px; padding:.75rem 2rem;
    background:linear-gradient(135deg,var(--blue-mid),var(--blue-bright));
    color:white; border:none; border-radius:14px;
    font-family:'Mitr',sans-serif; font-size:.9rem; font-weight:600;
    cursor:pointer; transition:all .25s;
    box-shadow:0 4px 14px rgba(59,130,246,.4); white-space:nowrap;
}
.btn-submit:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(59,130,246,.5); }

.stat-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:2rem; }
.stat-pill {
    background:var(--card); border:1px solid var(--border); border-radius:20px;
    padding:1.2rem 1.4rem; display:flex; align-items:center; gap:1rem;
    box-shadow:0 2px 12px rgba(0,0,0,.05); transition:transform .2s,box-shadow .2s;
}
.stat-pill:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.1); }
.stat-pill-icon { width:46px; height:46px; border-radius:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.stat-pill-icon.blue   { background:#dbeafe; color:var(--blue-bright); }
.stat-pill-icon.cyan   { background:#cffafe; color:var(--accent); }
.stat-pill-icon.purple { background:#ede9fe; color:var(--accent2); }
.stat-pill-icon.green  { background:#d1fae5; color:var(--success); }
.stat-pill-val { font-size:1.5rem; font-weight:700; color:var(--text-main); line-height:1; }
.stat-pill-lbl { font-size:.7rem; color:var(--text-muted); margin-top:3px; }

.chart-card {
    background:var(--card); border:1px solid var(--border); border-radius:28px;
    padding:2rem; margin-bottom:2rem; box-shadow:0 4px 24px rgba(0,0,0,.06);
}
.card-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:.5rem; }
.card-title { font-size:1rem; font-weight:700; color:var(--text-main); display:flex; align-items:center; gap:8px; }
.card-title i[data-lucide] { width:18px; height:18px; color:var(--blue-bright); }
.card-date-badge { font-size:.7rem; font-weight:500; color:var(--text-muted); background:var(--bg); border:1px solid var(--border); padding:4px 12px; border-radius:999px; }
.chart-area { position:relative; height:380px; }

.table-card { background:var(--card); border:1px solid var(--border); border-radius:28px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.06); }
.table-card-header { padding:1.5rem 2rem; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
.table-body-wrap { padding:1.5rem 2rem; }

/* ── DataTables override ── */
table.diag-table { width:100% !important; border-collapse:collapse; font-family:'Mitr',sans-serif; }
table.diag-table thead th {
    background:var(--bg) !important;
    padding:.85rem 1rem;
    font-size:.68rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.1em;
    color:var(--text-muted); text-align:left;
    border-bottom:1px solid var(--border) !important; border-top:none !important;
}
table.diag-table td { padding:.85rem 1rem; border-bottom:1px solid #f8fafc !important; vertical-align:middle; }
table.diag-table tbody tr:hover { background:#f8faff; }
table.diag-table tbody tr:last-child td { border-bottom:none !important; }

.dataTables_wrapper .dataTables_filter input {
    font-family:'Mitr',sans-serif; font-size:.85rem;
    border:1.5px solid var(--border); border-radius:10px;
    padding:6px 12px; outline:none; margin-left:8px;
}
.dataTables_wrapper .dataTables_filter input:focus { border-color:var(--blue-bright); }
.dataTables_wrapper .dataTables_length select {
    font-family:'Mitr',sans-serif; font-size:.85rem;
    border:1.5px solid var(--border); border-radius:10px; padding:4px 8px; outline:none;
}
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_length { font-family:'Mitr',sans-serif; font-size:.8rem; color:var(--text-muted); }
.dataTables_wrapper .dataTables_paginate .paginate_button {
    font-family:'Mitr',sans-serif !important; font-size:.8rem !important;
    border-radius:8px !important; padding:4px 10px !important; border:1px solid transparent !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background:var(--blue-bright) !important; color:white !important; border-color:var(--blue-bright) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background:var(--blue-light) !important; color:var(--blue-mid) !important; border-color:var(--blue-light) !important;
}

.rank-badge { width:26px; height:26px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:700; background:var(--bg); color:var(--text-muted); }
.rank-badge.gold   { background:#fef3c7; color:#d97706; }
.rank-badge.silver { background:#f1f5f9; color:#475569; }
.rank-badge.bronze { background:#fef0e7; color:#c2621a; }
.icd-code { display:inline-block; background:var(--blue-light); color:var(--blue-mid); padding:3px 10px; border-radius:8px; font-size:.8rem; font-weight:700; font-family:'Sarabun',monospace; letter-spacing:.03em; }
.diag-name { font-size:.85rem; color:var(--text-muted); }
.bar-mini-wrap { display:flex; align-items:center; gap:10px; justify-content:flex-end; }
.bar-mini { height:6px; border-radius:999px; background:linear-gradient(90deg,var(--blue-bright),var(--accent)); }
.total-num { font-size:.95rem; font-weight:700; color:var(--text-main); white-space:nowrap; }

.empty-state { text-align:center; padding:4rem 2rem; color:var(--text-muted); }
.empty-state i[data-lucide] { width:48px; height:48px; opacity:.3; display:block; margin:0 auto 1rem; }

@keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
.anim { animation:fadeUp .45s ease both; }
.anim-d1 { animation-delay:.05s; } .anim-d2 { animation-delay:.1s; }
.anim-d3 { animation-delay:.15s; } .anim-d4 { animation-delay:.2s; }

@media (max-width:640px) {
    .topbar { flex-direction:column; align-items:flex-start; }
    .page-title { font-size:1.2rem; }
    .stat-row { grid-template-columns:1fr 1fr; }
}
</style>

<div class="dd-wrap">
<div class="dd-inner">

    <!-- Top Bar -->
    <div class="topbar anim">
        <div class="topbar-left">
            <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="back-btn">
                <i data-lucide="arrow-left" style="width:20px;height:20px;"></i>
            </a>
            <div>
                <div class="page-title">อันดับรหัสโรค OPD</div>
                <div class="page-subtitle">สถิติการวินิจฉัยผู้ป่วยนอก · HOSxP Dashboard</div>
            </div>
        </div>
        <div class="export-btns">
            <button onclick="exportToExcel()" class="btn-export excel">
                <i data-lucide="file-spreadsheet" style="width:15px;height:15px;"></i> Excel
            </button>
            <button onclick="exportToPDF()" class="btn-export pdf">
                <i data-lucide="file-text" style="width:15px;height:15px;"></i> PDF
            </button>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-card anim anim-d1">
        <form method="get" action="<?= \yii\helpers\Url::to(['/site/diag-detail']) ?>" id="date-form-diag"
              style="display:flex;align-items:flex-end;gap:1rem;flex-wrap:wrap;width:100%;">
            <input type="hidden" name="r" value="site/diag-detail">
            <div style="flex:1;min-width:260px;">
                <div class="filter-label">ช่วงวันที่ประมวลผล</div>
                <div class="filter-input-wrap">
                    <i data-lucide="calendar-range"></i>
                    <input type="text" id="reportrange" class="filter-input" placeholder="เลือกช่วงวันที่..." readonly>
                    <input type="hidden" id="start_date" name="start_date" value="<?= $start_date ?>">
                    <input type="hidden" id="end_date"   name="end_date"   value="<?= $end_date ?>">
                </div>
            </div>
            <button type="submit" class="btn-submit">
                <i data-lucide="search" style="width:16px;height:16px;"></i> ประมวลผล
            </button>
        </form>
    </div>

    <!-- Stat Pills -->
    <div class="stat-row">
        <div class="stat-pill anim anim-d1">
            <div class="stat-pill-icon blue"><i data-lucide="users" style="width:22px;height:22px;"></i></div>
            <div>
                <div class="stat-pill-val"><?= number_format($totalPatients) ?></div>
                <div class="stat-pill-lbl">ผู้ป่วยรวม Top 20 (ราย)</div>
            </div>
        </div>
        <div class="stat-pill anim anim-d2">
            <div class="stat-pill-icon cyan"><i data-lucide="stethoscope" style="width:22px;height:22px;"></i></div>
            <div>
                <div class="stat-pill-val"><?= number_format($uniqueDiagCount ?? 0) ?></div>
                <div class="stat-pill-lbl">รหัสโรคไม่ซ้ำทั้งหมด</div>
            </div>
        </div>
        <div class="stat-pill anim anim-d3">
            <div class="stat-pill-icon purple"><i data-lucide="trophy" style="width:22px;height:22px;"></i></div>
            <div>
                <div class="stat-pill-val" style="font-size:1.1rem;"><?= $topDiag ? htmlspecialchars($topDiag['icd10']) : '-' ?></div>
                <div class="stat-pill-lbl">โรคที่พบมากสุด</div>
            </div>
        </div>
        <div class="stat-pill anim anim-d4">
            <div class="stat-pill-icon green"><i data-lucide="calendar-check" style="width:22px;height:22px;"></i></div>
            <div>
                <div class="stat-pill-val" style="font-size:1rem;"><?= date('d/m/Y', strtotime($start_date)) ?></div>
                <div class="stat-pill-lbl">ถึง <?= date('d/m/Y', strtotime($end_date)) ?></div>
            </div>
        </div>
    </div>

    <div id="pdf-area">

    <!-- Chart Top 20 -->
    <div class="chart-card anim anim-d2">
        <div class="card-header">
            <div class="card-title">
                <i data-lucide="bar-chart-3"></i>
                20 อันดับแรก — จำนวนผู้ป่วยแยกตามรหัสโรค
            </div>
            <span class="card-date-badge">
                <?= date('d/m/Y', strtotime($start_date)) ?> – <?= date('d/m/Y', strtotime($end_date)) ?>
            </span>
        </div>
        <div class="chart-area">
            <canvas id="diagChart"></canvas>
        </div>
    </div>

    <!-- Table ทั้งหมด + DataTables -->
    <div class="table-card anim anim-d3">
        <div class="table-card-header">
            <div class="card-title">
                <i data-lucide="table-2"></i>
                ตารางข้อมูลการวินิจฉัยทั้งหมด
                <span style="font-size:.75rem;font-weight:400;color:var(--text-muted);">
                    (<?= number_format(count($diagStats)) ?> รหัสโรค)
                </span>
            </div>
            <span class="card-date-badge">
                รวม <?= number_format(array_sum(array_column($diagStats, 'total'))) ?> ราย
            </span>
        </div>
        <div class="table-body-wrap">
            <table class="diag-table" id="diagTable">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>รหัส ICD-10</th>
                        <th>ชื่อโรค / การวินิจฉัย</th>
                        <th style="text-align:right; min-width:160px;">จำนวน (ราย)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($diagStats)): ?>
                        <?php foreach ($diagStats as $i => $row):
                            $rank = $i + 1;
                            $rankClass = $rank === 1 ? 'gold' : ($rank === 2 ? 'silver' : ($rank === 3 ? 'bronze' : ''));
                            $pct = $maxVal > 0 ? round(($row['total'] / $maxVal) * 100) : 0;
                        ?>
                        <tr>
                            <td data-order="<?= $rank ?>">
                                <span class="rank-badge <?= $rankClass ?>"><?= $rank ?></span>
                            </td>
                            <td><?= htmlspecialchars($row['icd10']) ?></td>
                            <td><span class="diag-name"><?= htmlspecialchars($row['diag_name'] ?? '-') ?></span></td>
                            <td data-order="<?= $row['total'] ?>">
                                <div class="bar-mini-wrap">
                                    <div style="width:80px;">
                                        <div class="bar-mini" style="width:<?= $pct ?>%;height:6px;"></div>
                                    </div>
                                    <span class="total-num"><?= number_format($row['total']) ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i data-lucide="inbox"></i>
                                    <p>ไม่พบข้อมูลในช่วงวันที่เลือก</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    </div><!-- /pdf-area -->
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
function initDashboard() {
    if (typeof $ === 'undefined' || typeof moment === 'undefined') {
        setTimeout(initDashboard, 100); return;
    }

    lucide.createIcons();

    /* ── DateRangePicker ── */
    var startStr = $('#start_date').val() || moment().format('YYYY-MM-DD');
    var endStr   = $('#end_date').val()   || moment().format('YYYY-MM-DD');
    var start = moment(startStr), end = moment(endStr);

    function cb(s, e) {
        $('#reportrange').val(s.format('DD/MM/YYYY') + ' – ' + e.format('DD/MM/YYYY'));
        $('#start_date').val(s.format('YYYY-MM-DD'));
        $('#end_date').val(e.format('YYYY-MM-DD'));
    }

    var ranges = {
        'วันนี้':         [moment(), moment()],
        'เมื่อวาน':       [moment().subtract(1,'days'), moment().subtract(1,'days')],
        '7 วันล่าสุด':    [moment().subtract(6,'days'), moment()],
        'เดือนนี้':       [moment().startOf('month'), moment().endOf('month')],
        'ปีงบฯ ปัจจุบัน': [
            moment().month() >= 9 ? moment().month(9).date(1) : moment().subtract(1,'year').month(9).date(1),
            moment().month() >= 9 ? moment().add(1,'year').month(8).date(30) : moment().month(8).date(30)
        ]
    };
    var fy = moment().month() >= 9 ? moment().year() + 1 : moment().year();
    for (var i = 1; i <= 3; i++) {
        var y = fy - i;
        ranges['ปีงบฯ ' + (y + 543)] = [
            moment().year(y-1).month(9).date(1),
            moment().year(y).month(8).date(30)
        ];
    }

    $('#reportrange').daterangepicker({
        startDate: start, endDate: end, ranges: ranges,
        alwaysShowCalendars: true, showDropdowns: true, opens: 'right',
        locale: {
            format: 'DD/MM/YYYY', applyLabel: 'ตกลง', cancelLabel: 'ยกเลิก',
            customRangeLabel: 'กำหนดเอง',
            daysOfWeek: ['อา','จ','อ','พ','พฤ','ศ','ส'],
            monthNames: ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.']
        }
    }, cb);
    cb(start, end);

    /* ── Chart (Top 20 — รับมาจาก Controller แล้ว LIMIT 20) ── */
    var chartLabels = <?= json_encode($labels ?? [], JSON_UNESCAPED_UNICODE) ?>;
    var chartData   = <?= json_encode($data   ?? []) ?>;
    var n = chartLabels.length;

    function genColors(count, l) {
        return Array.from({length: count}, function(_, i) {
            return 'hsl(' + Math.round((i * 360) / count) + ',70%,' + (l||58) + '%)';
        });
    }

    var ctx = document.getElementById('diagChart');
    if (ctx && typeof Chart !== 'undefined') {
        var old = Chart.getChart(ctx);
        if (old) old.destroy();

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'จำนวน (ราย)',
                    data: chartData,
                    backgroundColor: genColors(n, 58).map(function(c) { return c.replace('58%)', '58%/0.82)'); }),
                    borderColor: genColors(n, 46),
                    borderWidth: 2,
                    borderRadius: 10,
                    borderSkipped: false,
                    maxBarThickness: 44,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Mitr', size: 13 },
                        bodyFont:  { family: 'Mitr', size: 12 },
                        padding: 12, cornerRadius: 10,
                        callbacks: {
                            label: function(c) { return '  ' + c.parsed.y.toLocaleString() + ' ราย'; }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9', lineWidth: 1 },
                        ticks: { font: { family: 'Mitr', size: 11 }, callback: function(v) { return v.toLocaleString(); } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Mitr', size: 10 }, maxRotation: 35,
                            callback: function(val, i) {
                                var l = chartLabels[i] || '';
                                return l.length > 10 ? l.substring(0, 10) + '…' : l;
                            }
                        }
                    }
                },
                animation: { duration: 900, easing: 'easeOutQuart' }
            }
        });
    }

    /* ── DataTables (ตารางทั้งหมด แบ่งหน้า ค้นหาได้) ── */
    if ($.fn.DataTable && $('#diagTable').length) {
        $('#diagTable').DataTable({
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'ทั้งหมด']],
            order: [[3, 'desc']],   // sort จำนวนมาก→น้อย (ใช้ data-order)
            columnDefs: [
                { orderable: false, targets: [0] },  // คอลัมน์ # ไม่ sort
            ],
            language: {
                search:        'ค้นหา:',
                lengthMenu:    'แสดง _MENU_ รายการ/หน้า',
                info:          'แสดง _START_–_END_ จาก _TOTAL_ รายการ',
                infoEmpty:     'ไม่พบข้อมูล',
                infoFiltered:  '(กรองจากทั้งหมด _MAX_ รายการ)',
                paginate:      { previous: '‹ ก่อน', next: 'ถัดไป ›' },
                zeroRecords:   'ไม่พบข้อมูลที่ค้นหา',
                emptyTable:    'ไม่มีข้อมูลในตาราง',
            },
            drawCallback: function() {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    /* ── Form Submit ── */
    $('#date-form-diag').on('submit', function() {
        $(this).find('button[type="submit"]')
            .prop('disabled', true)
            .html('<span style="display:flex;align-items:center;gap:6px;"><span style="width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:white;border-radius:50%;animation:spin .6s linear infinite;display:inline-block;"></span>กำลังโหลด...</span>');
    });
}

if (document.readyState === 'complete') { initDashboard(); }
else { window.addEventListener('load', initDashboard); }

/* ── Export Excel (ดึงทุก row จาก DataTables ไม่ใช่แค่หน้าปัจจุบัน) ── */
function exportToExcel() {
    var wb   = XLSX.utils.book_new();
    var rows = [['#', 'ICD-10', 'ชื่อโรค', 'จำนวน (ราย)']];
    $('#diagTable').DataTable().rows().every(function() {
        var cells = $(this.node()).find('td');
        rows.push([
            $(cells[0]).text().trim(),
            $(cells[1]).text().trim(),
            $(cells[2]).text().trim(),
            parseInt($(cells[3]).find('.total-num').text().replace(/,/g,'')) || 0
        ]);
    });
    var ws = XLSX.utils.aoa_to_sheet(rows);
    XLSX.utils.book_append_sheet(wb, ws, 'DiagStats');
    XLSX.writeFile(wb, 'diag-report-<?= $start_date ?>_<?= $end_date ?>.xlsx');
}

function exportToPDF() {
    html2pdf().set({
        margin: .5, filename: 'diag-report-<?= $start_date ?>_<?= $end_date ?>.pdf',
        image: { type: 'jpeg', quality: .95 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    }).from(document.getElementById('pdf-area')).save();
}

document.head.appendChild(Object.assign(document.createElement('style'), {
    textContent: '@keyframes spin { to { transform: rotate(360deg); } }'
}));
</script>