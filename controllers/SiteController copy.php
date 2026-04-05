<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                // ระบุเฉพาะ Action ที่ "ต้อง" Login เท่านั้นถึงจะดูได้
                // ส่วน Action อื่นๆ เช่น index, diag-detail จะกลายเป็นสาธารณะทันที
                'only' => ['logout'], 
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'], // @ หมายถึงต้อง Login เท่านั้น
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => ['class' => 'yii\web\ErrorAction'],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        try {
            // 1. OPD วันนี้ (นับจำนวน Visit - VN)
            $opdToday = Yii::$app->db->createCommand("SELECT COUNT(DISTINCT vn) FROM ovst WHERE vstdate = CURDATE()")->queryScalar();
            
            // OPD noAdmit วันนี้
            $opdToday_o = Yii::$app->db->createCommand("SELECT COUNT(DISTINCT vn) FROM ovst WHERE vstdate = CURDATE() AND (an IS NULL OR an ='')")->queryScalar();

            // OPD Authen วันนี้
            $opdToday_authen = Yii::$app->db->createCommand("SELECT COUNT(DISTINCT vp.vn) FROM visit_pttype vp LEFT JOIN ovst ov ON ov.vn=vp.vn WHERE ov.vstdate = CURDATE() AND vp.auth_code IS NOT NULL AND vp.auth_code <> ''")->queryScalar();

            // 2. ข้อมูล DIAG (OPD)
            $totalVisit = $opdToday ?: 0;
            $diagCount = Yii::$app->db->createCommand("SELECT COUNT(DISTINCT vn) FROM ovstdiag WHERE vstdate = CURDATE()")->queryScalar() ?: 0;
            $diagPercent = ($totalVisit > 0) ? ($diagCount / $totalVisit) * 100 : 0;

            // 3. IPD วันนี้ (รับใหม่ / จำหน่าย)
            $ipdNew = Yii::$app->db->createCommand("SELECT COUNT(DISTINCT an) FROM ipt WHERE regdate = CURDATE()")->queryScalar();
            $ipdDischarge = Yii::$app->db->createCommand("SELECT COUNT(DISTINCT an) FROM ipt WHERE dchdate = CURDATE()")->queryScalar();
            $ipdTotal = Yii::$app->db->createCommand("SELECT COUNT(DISTINCT an) FROM ipt WHERE regdate = CURDATE()")->queryScalar();
            $ipdDiagCount = Yii::$app->db->createCommand("SELECT COUNT(DISTINCT an) FROM an_stat WHERE (regdate = CURDATE() AND pdx IS NOT NULL)")->queryScalar();
            $ipdDiagPercent = ($ipdTotal > 0) ? round(($ipdDiagCount / $ipdTotal) * 100, 2) : 0;

            // 1. OPD Refer (ส่งจากหน้านอกปกติ)
            $opdReferToday = Yii::$app->db->createCommand("SELECT COUNT(vn) FROM referout WHERE refer_point='OPD' AND refer_date = CURDATE()")->queryScalar();
            // 2. IPD Refer (ส่งจากตึกผู้ป่วยใน)
            $ipdReferToday = Yii::$app->db->createCommand("SELECT COUNT(vn) FROM referout WHERE refer_point='IPD' AND refer_date = CURDATE()")->queryScalar();
            // 3. ER Refer (กรองเฉพาะจุดบริการ ER - สมมติว่ารหัสแผนก ER คือ '010')
            $erReferToday = Yii::$app->db->createCommand("SELECT COUNT(vn) FROM referout WHERE refer_point='ER' AND refer_date = CURDATE()")->queryScalar();
            // 4. สรุปรายคลินิก (สำหรับกราฟหน้าแรก)
            $hospReferSql = "
                SELECT h.name AS hosp_name, COUNT(*) AS total
                FROM referout r
                LEFT JOIN hospcode h ON h.hospcode = r.refer_hospcode
                WHERE r.refer_date = CURDATE()
                GROUP BY r.refer_hospcode
                ORDER BY total DESC
                LIMIT 5
            "; 
            $hospReferToday = Yii::$app->db->createCommand($hospReferSql)->queryAll();

            //ในเวลา นอกเวลา 
            $deptSql = "SELECT 
                    d.department as dept_name, 
                    COUNT(v.vn) as total,
                    SUM(CASE WHEN v.vsttime BETWEEN '07:30:00' AND '16:30:00' THEN 1 ELSE 0 END) as Intime,
                    SUM(CASE WHEN v.vsttime > '16:30:00' THEN 1 ELSE 0 END) as Outtime
                FROM ovst v 
                LEFT JOIN kskdepartment d ON d.depcode = v.main_dep 
                WHERE v.vstdate = CURDATE() 
                GROUP BY v.main_dep 
                ORDER BY total DESC";
    
            $deptStats = Yii::$app->db->createCommand($deptSql)->queryAll();

            // Query สำหรับ ER Card
            $erSql = "SELECT 
                        COUNT(*) as total,
                        SUM(IF(er_pt_type in('1'), 1, 0)) as emergency_case,
                        SUM(IF(er_pt_type in('2','5'), 1, 0)) as accident_case,
                        SUM(IF(er_pt_type IS NULL OR er_pt_type = '' OR er_pt_type in('3','4'), 1, 0)) as general_case,
                        SUM(IF(er_pt_type in('6'), 1, 0)) as uc_another_province
                    FROM er_regist e
                    LEFT JOIN ovst o ON o.vn = e.vn
                    WHERE e.vstdate = CURDATE()
                    ";
            $erData = Yii::$app->db->createCommand($erSql)->queryOne();
            


            $clinicStats = Yii::$app->db->createCommand("
                SELECT c.name as clinic_name, COUNT(v.vn) as total 
                FROM ovst v 
                INNER JOIN clinic c ON v.main_dep = c.depcode 
                WHERE v.vstdate = CURDATE() 
                GROUP BY v.main_dep 
                ORDER BY total DESC
                LIMIT 5
            ")->queryAll();

        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            // กำหนดค่าเริ่มต้นถ้า Error
            $opdToday = $opdToday_o = $opdToday_authen = $diagCount = $totalVisit = $ipdNew = $ipdDischarge = 0;
            $diagPercent = 0;
            $clinicStats = [];
        }

        return $this->render('index', [
            'opdToday' => $opdToday ?: 0,
            'opdToday_o' => $opdToday_o ?: 0,
            'opdToday_authen' => $opdToday_authen ?: 0,
            'diagPercent' => round($diagPercent, 2),
            'diagCount' => $diagCount,
            'totalVisit' => $totalVisit,
            'ipdNew' => $ipdNew ?: 0,
            'ipdDischarge' => $ipdDischarge ?: 0,
            'ipdTotal' =>$ipdTotal ?: 0,
            'ipdDiagCount' => $ipdDiagCount ?: 0,
            'ipdDiagPercent' => $ipdDiagPercent,
            'opdReferToday' => $opdReferToday ?: 0,
            'ipdReferToday' => $ipdReferToday ?: 0,
            'erReferToday' => $erReferToday ?: 0,
            'hospReferToday' => $hospReferToday,
            'deptStats' => $deptStats,
            'erData' => $erData,
            'clinicStats' => $clinicStats
        ]);
        
    }

    public function actionDeptDetail($start_date = null, $end_date = null)
    {
        // Query นับจำนวนผู้ป่วยแยกตามแผนกหลัก (main_dep) ในวันนี้
        // SQL ที่ดึงข้อมูลแยกแผนก พร้อมแบ่ง In-time (07:30-16:30) และ Out-time
        // รับค่าจาก GET ถ้าไม่มีให้ใช้วันที่ปัจจุบัน
        $request = Yii::$app->request;
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));
        $sql = "
            SELECT 
                v.main_dep,
                d.department as dept_name, 
                COUNT(v.vn) as total,
                SUM(CASE WHEN v.vsttime BETWEEN '07:30:00' AND '16:30:00' THEN 1 ELSE 0 END) as Intime,
                SUM(CASE WHEN v.vsttime > '16:30:00' THEN 1 ELSE 0 END) as Outtime
        FROM ovst v 
        LEFT JOIN kskdepartment d ON d.depcode = v.main_dep 
        WHERE v.vstdate BETWEEN :start AND :end
        GROUP BY v.main_dep 
        ORDER BY total DESC
        "; 
        
        $deptStats = Yii::$app->db->createCommand($sql)
        ->bindValue(':start', $start_date)
        ->bindValue(':end', $end_date)
        ->queryAll();

        return $this->render('dept-detail', [
            'deptStats' => $deptStats,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
        
    }

    public function actionGetPatientList($depcode, $start_date, $end_date)
    {
        $sql = "
            SELECT 
                v.vstdate, v.vsttime, v.hn, 
                CONCAT(p.pname, p.fname, ' ', p.lname) as fullname,
                vn.pdx, i.name as diag_name
            FROM ovst v
            LEFT JOIN vn_stat vn ON v.vn = vn.vn
            LEFT JOIN patient p ON p.hn = v.hn
            LEFT JOIN icd101 i ON i.code = vn.pdx
            WHERE v.main_dep = :depcode 
            AND v.vstdate BETWEEN :start AND :end
            ORDER BY v.vsttime DESC
        ";
        
        $patients = Yii::$app->db->createCommand($sql)
            ->bindValue(':depcode', $depcode)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();

        // ส่งคืนเป็นตาราง HTML
        return $this->renderPartial('_patient_list_table', [
            'patients' => $patients
        ]);
    }

    public function actionDiagDetail($start_date = null, $end_date = null)
    {
        // กำหนดค่าเริ่มต้นถ้าไม่ได้เลือกวันที่
        // $start_date = $start_date ?: date('Y-m-d');
        // $end_date = $end_date ?: date('Y-m-d');
        // ดึงค่าจาก GET Request ถ้าไม่มีให้ใช้ค่าจาก Parameter ถ้าไม่มีอีกให้ใช้วันที่ปัจจุบัน
        $request = Yii::$app->request;
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));

        $sql = "
            SELECT i.pdx AS icd10 ,d.name AS diag_name,d.tname,COUNT(*) AS total
            FROM vn_stat i
            LEFT OUTER JOIN icd101 d ON d.code = i.pdx
            WHERE i.vstdate BETWEEN :start AND :end AND i.pdx <> ''
            and i.pdx not like 'z%'
            GROUP BY i.pdx
            ORDER BY COUNT(*) DESC
            LIMIT 10
        ";

        $diagStats = Yii::$app->db->createCommand($sql)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();
        
        // ... เตรียม $labels และ $data ...
        $labels = [];
        $data = [];
        foreach ($diagStats as $row) {
            $labels[] = $row['icd10'];
            $data[] = (int)$row['total'];
        }

        return $this->render('diag-detail', [
            'diagStats' => $diagStats,
            'labels' => $labels,
            'data' => $data,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }

    // WARD
    public function actionWardDetail()
    {
        $sql = "
            SELECT w.ward, w.name as ward_name, w.bedcount,
                (SELECT COUNT(*) FROM ipt WHERE dchstts IS NULL AND ward = w.ward) AS current_admit,
                (SELECT COUNT(*) FROM ipt WHERE DATE(regdate) = CURDATE() AND ward = w.ward) AS admit_today,
                (SELECT COUNT(*) FROM ipt WHERE DATE(dchdate) = CURDATE() AND ward = w.ward) AS discharge_today
            FROM ward w
            WHERE w.ward_active='Y'
            ORDER BY current_admit DESC
        ";
        
        $wardData = Yii::$app->db->createCommand($sql)->queryAll();
        
        return $this->render('ward-detail', [
            'wardData' => $wardData,
        ]);
    }

    public function actionIpdDiagDetail()
    {
        $request = Yii::$app->request;
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));

        // SQL สำหรับ 10 อันดับโรค IPD
        $sql = "
            SELECT i.pdx AS icd10, d.name AS diag_name, d.tname, COUNT(*) AS total
            FROM an_stat i
            LEFT OUTER JOIN icd101 d ON d.code = i.pdx
            WHERE i.regdate BETWEEN :start AND :end 
            AND i.pdx IS NOT NULL AND i.pdx <> ''
            AND i.pdx NOT LIKE 'z%'
            GROUP BY i.pdx
            ORDER BY total DESC
            LIMIT 10
        ";

        $diagStats = Yii::$app->db->createCommand($sql)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();

        $labels = [];
        $data = [];
        foreach ($diagStats as $row) {
            $labels[] = $row['icd10'];
            $data[] = (int)$row['total'];
        }

        // ใช้ view ตัวใหม่ หรือจะสร้างไฟล์ใหม่ชื่อ ipd-diag-detail.php (ก๊อปจากอันเก่ามาแก้)
        return $this->render('ipd-diag-detail', [
            'diagStats' => $diagStats,
            'labels' => $labels,
            'data' => $data,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }

    // สำหรับหน้า REFER
    public function actionReferDetail()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', 'OPD'); // รับค่า type เป็น OPD, IPD, ER
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));

        // ชื่อภาษาไทยสำหรับแสดงผลหน้าเว็บ
        $type_names = [
            'OPD' => 'ผู้ป่วยนอก (OPD)',
            'IPD' => 'ผู้ป่วยใน (IPD)',
            'ER' => 'ห้องฉุกเฉิน (ER)'
        ];
        $type_name = isset($type_names[strtoupper($type)]) ? $type_names[strtoupper($type)] : $type;

        // SQL ดึง 10 อันดับโรค โดยกรองจาก refer_point ตรงๆ 
        $sql = "
            SELECT r.pdx AS icd10, d.name AS diag_name, COUNT(*) AS total
            FROM referout r
            LEFT JOIN icd101 d ON d.code = r.pdx
            WHERE r.refer_point = :point 
            AND r.refer_date BETWEEN :start AND :end 
            AND r.pdx IS NOT NULL AND r.pdx <> ''
            GROUP BY r.pdx
            ORDER BY total DESC
            LIMIT 10
        ";

        $diagStats = Yii::$app->db->createCommand($sql)
            ->bindValue(':point', $type)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();

        $labels = [];
        $data = [];
        foreach ($diagStats as $row) {
            $labels[] = $row['icd10'];
            $data[] = (int)$row['total'];
        }

        // 2. SQL ดึง 10 อันดับสถานพยาบาล (ตามประเภทที่เลือก)
        $hospSql = "
            SELECT h.name AS hosp_name, COUNT(*) AS total
            FROM referout r
            LEFT JOIN hospcode h ON h.hospcode = r.refer_hospcode
            WHERE r.refer_point = :point 
            AND r.refer_date BETWEEN :start AND :end
            GROUP BY r.refer_hospcode
            ORDER BY total DESC
            LIMIT 10
        ";
        $hospStats = Yii::$app->db->createCommand($hospSql)
            ->bindValue(':point', $type)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();

        // ข้อมูลรวมทุกจุดบริการ (All Departments) ---
        $allDeptSql = "
            SELECT 
                refer_point as department_name, 
                COUNT(*) as total
            FROM referout
            WHERE refer_date BETWEEN :start AND :end
            AND refer_point IS NOT NULL AND refer_point <> ''
            GROUP BY refer_point
            ORDER BY total DESC
        ";
        $allDeptStats = Yii::$app->db->createCommand($allDeptSql)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();

        return $this->render('refer-detail', [
            'diagStats' => $diagStats,
            'labels' => $labels,
            'data' => $data,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'hospStats' => $hospStats,
            'type_name' => $type_name,
            'allDeptStats' => $allDeptStats 
        ]);
    }

    //  ของ ER โดยเฉพาะ
    public function actionErDetail($start_date = null, $end_date = null)
    {
        $request = Yii::$app->request;
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));

        // 1. ดึงสถิติกลุ่มโรค 10 อันดับแรกของ ER
        $sqlDiag = "
            SELECT i.pdx AS icd10, d.name AS diag_name, COUNT(*) AS total
            FROM er_regist e
            LEFT JOIN vn_stat i ON i.vn = e.vn
            LEFT JOIN icd101 d ON d.code = i.pdx
            WHERE e.vstdate BETWEEN :start AND :end 
            AND i.pdx IS NOT NULL AND i.pdx <> ''
            GROUP BY i.pdx
            ORDER BY total DESC
            LIMIT 10
        ";
        $diagStats = Yii::$app->db->createCommand($sqlDiag)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();

        // 2. ดึงข้อมูลแยกประเภท อุบัติเหตุ vs ทั่วไป สำหรับกราฟวงกลม
        $sqlType = "
            SELECT 
                SUM(IF(er_pt_type in('1'), 1, 0)) as emergency_case,
                SUM(IF(er_pt_type in('2','5'), 1, 0)) as accident_case,
                SUM(IF(er_pt_type IS NULL OR er_pt_type = '' OR er_pt_type in('3','4'), 1, 0)) as general_case,
                SUM(IF(er_pt_type in('6'), 1, 0)) as uc_another_province
            FROM er_regist
            WHERE vstdate BETWEEN :start AND :end
        ";
        $typeStats = Yii::$app->db->createCommand($sqlType)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryOne();

        return $this->render('er-detail', [
            'diagStats' => $diagStats,
            'typeStats' => $typeStats,
            'start_date' => $start_date,
            'end_date' => $end_date,
            // ส่งรหัสแผนก ER ไปด้วย ('011') เพื่อใช้กับ Modal รายชื่อ
            'er_depcode' => '011' 
        ]);
    }


    public function actionSearch($cid = null)
    {
        $patientData = null;
        $visitHistory = [];

        if ($cid) {
            $patientData = Yii::$app->db->createCommand("
                SELECT hn, fname, lname, birthday, sex, cid 
                FROM patient 
                WHERE cid = :cid", [':cid' => $cid])->queryOne();

            if ($patientData) {
                $visitHistory = Yii::$app->db->createCommand("
                    SELECT v.vstdate, v.vn, i.pdx as icd10, d.name as diag_name
                    FROM vn_stat v
                    LEFT JOIN icd101 d ON d.code = v.pdx
                    WHERE v.hn = :hn
                    ORDER BY v.vstdate DESC
                    LIMIT 10", [':hn' => $patientData['hn']])->queryAll();
            }
        }

        return $this->render('search', [
            'patientData' => $patientData,
            'visitHistory' => $visitHistory,
            'cid' => $cid
        ]);
    }

    public function actionQuarterly()
    {
        $sql = "
            SELECT 
                CASE WHEN MONTH(vstdate) >= 10 THEN YEAR(vstdate) + 1 ELSE YEAR(vstdate) END AS fiscal_year,
                CASE 
                    WHEN MONTH(vstdate) IN (10,11,12) THEN 'Q1'
                    WHEN MONTH(vstdate) IN (1,2,3) THEN 'Q2'
                    WHEN MONTH(vstdate) IN (4,5,6) THEN 'Q3'
                    WHEN MONTH(vstdate) IN (7,8,9) THEN 'Q4'
                END AS quarter,
                COUNT(vn) as total_visit
            FROM ovst
            WHERE vstdate >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR)
            GROUP BY fiscal_year, quarter
            ORDER BY fiscal_year DESC, quarter DESC
        ";
        $rawData = Yii::$app->db->createCommand($sql)->queryAll();
        return $this->render('quarterly', ['rawData' => $rawData]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) { return $this->goHome(); }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) { return $this->goBack(); }
        $model->password = '';
        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}