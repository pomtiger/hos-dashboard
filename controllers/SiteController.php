<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\DashboardStat;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class ,
                // ระบุเฉพาะ Action ที่ "ต้อง" Login เท่านั้นถึงจะดูได้
                // ส่วน Action อื่นๆ เช่น index, diag-detail จะกลายเป็นสาธารณะทันที
                
                'only' => ['logout', 'report-icd10'],
                'rules' => [
                    [
                        'actions' => ['logout', 'report-icd10'],
                        'allow' => true,
                        'roles' => ['@'], // @ หมายถึงต้อง Login เท่านั้น
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class ,
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
            $opdToday = DashboardStat::getOpdToday();
            $opdToday_o = DashboardStat::getOpdNoAdmitToday();
            $opdToday_authen = DashboardStat::getOpdAuthenToday();
            $authenPercent = ($opdToday_o > 0) ? ($opdToday_authen / $opdToday_o) * 100 : 0;

            $totalVisit = $opdToday ?: 0;
            $diagCount = DashboardStat::getOpdDiagCountToday();
            $diagPercent = ($totalVisit > 0) ? ($diagCount / $totalVisit) * 100 : 0;
            

            $ipdNew = DashboardStat::getIpdNewToday();
            $ipdDischarge = DashboardStat::getIpdDischargeToday();
            $ipdTotal = DashboardStat::getIpdTotalToday();
            $ipdDiagCount = DashboardStat::getIpdDiagCountToday();
            $ipdDiagPercent = ($ipdTotal > 0) ? round(($ipdDiagCount / $ipdTotal) * 100, 2) : 0;

            $ReferToday = DashboardStat::getReferOutToday();
            $opdReferToday = DashboardStat::getOpdReferOutToday();
            $ipdReferToday = DashboardStat::getIpdReferOutToday();
            $erReferToday = DashboardStat::getErReferOutToday();

            $opdReferInToday = DashboardStat::getOpdReferInToday();
            $ipdReferInToday = DashboardStat::getIpdReferInToday();
            $erReferInToday = DashboardStat::getErReferInToday();

            $hospReferToday = DashboardStat::getHospReferOutToday(5);
            $hospReferInToday = DashboardStat::getHospReferInToday(5);

            $deptStats = DashboardStat::getDeptStatsToday();
            $erData = DashboardStat::getErStatsToday();
            $clinicStats = DashboardStat::getClinicStatsToday();

            // รหัสแผนก (main_dep) สำหรับจุดบริการอื่นๆ (แก้ไขรหัสให้ตรงกับ HOSxP ของ รพ. ได้เลยครับ)
            $dentalVisit = DashboardStat::getOpdVisitByDep('005'); // ทันตกรรม (เช็ค/แก้รหัสแผนกให้ตรงของจริง)
            $mentalVisit = DashboardStat::getOpdVisitByDep('014'); // คลินิกใจสบาย
            $thaiMedVisit = DashboardStat::getOpdVisitByDep('041'); // แพทย์แผนไทย
            $ptVisit = DashboardStat::getOpdVisitByDep('042'); // กายภาพบำบัด

            // ข้อมูล LAB และ X-Ray
            $labStats = DashboardStat::getLabStatsToday();
            $xrayStats = DashboardStat::getXrayStatsToday();

            // การใช้ยา มูลค่าการใช้ยา
            $drugStats = DashboardStat::getTopDrugStats();
            // ตามสิทธิ์
            // $pttypeStats = DashboardStat::getOpdPttypeStats();

        }
        catch (\Exception $e) {
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
            'authenPercent' => round($authenPercent, 2),
            'diagPercent' => round($diagPercent, 2),
            'diagCount' => $diagCount,
            'totalVisit' => $totalVisit,
            'ipdNew' => $ipdNew ?: 0,
            'ipdDischarge' => $ipdDischarge ?: 0,
            'ipdTotal' => $ipdTotal ?: 0,
            'ipdDiagCount' => $ipdDiagCount ?: 0,
            'ipdDiagPercent' => $ipdDiagPercent,
            'ReferToday' => $ReferToday ?: 0,
            'opdReferToday' => $opdReferToday ?: 0,
            'ipdReferToday' => $ipdReferToday ?: 0,
            'erReferToday' => $erReferToday ?: 0,
            'hospReferToday' => $hospReferToday,
            'hospReferInToday' => $hospReferInToday ?: 0,
            'opdReferInToday' => $opdReferInToday ?: 0,
            'ipdReferInToday' => $ipdReferInToday ?: 0,
            'erReferInToday' => $erReferInToday ?: 0,
            'deptStats' => $deptStats,
            'erData' => $erData,
            'clinicStats' => $clinicStats,
            'dentalVisit' => $dentalVisit,
            'mentalVisit' => $mentalVisit,
            'thaiMedVisit' => $thaiMedVisit,
            'labStats' => $labStats,
            'xrayStats' => $xrayStats,
            'drugStats' => $drugStats,
            // 'pttypeStats' =>$pttypeStats,
            'ptVisit' => $ptVisit
        ]);

    }

    public function actionDeptDetail($start_date = null, $end_date = null)
    {
        $request = Yii::$app->request;
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));

        $deptStats = DashboardStat::getDeptDetail($start_date, $end_date);

        return $this->render('dept-detail', [
            'deptStats' => $deptStats,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    public function actionGetPatientList($depcode, $start_date, $end_date)
    {
        $patients = DashboardStat::getPatientList($depcode, $start_date, $end_date);
        return $this->renderPartial('_patient_list_table', [
            'patients' => $patients
        ]);
    }

    // ข้อมูลสิทธิการรักษา
    public function actionGetPTTypePatientList($pttype, $start_date, $end_date)
    {
            // เรียก Model
            $patients = DashboardStat::getPTTypePatientList($pttype, $start_date, $end_date);
            return $this->renderPartial('_patient_list_table_pttype', [
            'patients' => $patients
        ]);
    }

    // รหัสโรค
    public function actionDiagDetail($start_date = null, $end_date = null)
    {
        $request = Yii::$app->request;
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));

        // ✅ ตารางดึงทั้งหมด ไม่จำกัด
        $diagStats = DashboardStat::getDiagDetail($start_date, $end_date, 100);
        
        // ✅ กราฟใช้แค่ Top 20
        $top20  = array_slice($diagStats, 0, 20); // Top 20  → กราฟ
        $labels = array_column($top20, 'icd10'); // Top 20  → กราฟ
        $data   = array_map('intval', array_column($top20, 'total'));
        $uniqueDiagCount = DashboardStat::getUniqueDiagCount($start_date, $end_date); // ✅ เพิ่มรหัสโรคที่ต่างกัน

        $labels = [];
        $data = [];
        foreach ($diagStats as $row) {
            $labels[] = $row['icd10'];
            $data[] = (int)$row['total'];
        }

        return $this->render('diag-detail', [
            'diagStats' => $diagStats, // ทั้งหมด → ตาราง
            'uniqueDiagCount' => $uniqueDiagCount,
            'labels' => $labels, // Top 20  → กราฟ
            'data' => $data,     // Top 20  → กราฟ
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }

    public function actionClinicDiagDetail($depcode, $start_date = null, $end_date = null)
    {
        $request = Yii::$app->request;
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));

        $diagStats = DashboardStat::getClinicDiagDetail($depcode, $start_date, $end_date, 20);
        $deptName = DashboardStat::getDepartmentName($depcode);

        $labels = [];
        $data = [];
        foreach ($diagStats as $row) {
            $labels[] = $row['icd10'];
            $data[] = (int)$row['total'];
        }

        return $this->render('clinic-diag-detail', [
            'diagStats' => $diagStats,
            'labels' => $labels,
            'data' => $data,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'depcode' => $depcode,
            'deptName' => $deptName,
        ]);
    }

    public function actionWardDetail()
    {
        $wardData = DashboardStat::getWardDetail();

        return $this->render('ward-detail', [
            'wardData' => $wardData,
        ]);
    }

    public function actionIpdDiagDetail()
    {
        $request = Yii::$app->request;
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));

        $diagStats = DashboardStat::getIpdDiagDetail($start_date, $end_date, 10);

        $labels = [];
        $data = [];
        foreach ($diagStats as $row) {
            $labels[] = $row['icd10'];
            $data[] = (int)$row['total'];
        }

        return $this->render('ipd-diag-detail', [
            'diagStats' => $diagStats,
            'labels' => $labels,
            'data' => $data,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }

    public function actionReferDetail()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', 'OPD');
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));

        $type_names = [
            'OPD' => 'ผู้ป่วยนอก (OPD)',
            'IPD' => 'ผู้ป่วยใน (IPD)',
            'ER' => 'ห้องฉุกเฉิน (ER)'
        ];
        $type_name = isset($type_names[strtoupper($type)]) ? $type_names[strtoupper($type)] : $type;

        $diagStats = DashboardStat::getReferOutDiagDetail($type, $start_date, $end_date, 10);

        $labels = [];
        $data = [];
        foreach ($diagStats as $row) {
            $labels[] = $row['icd10'];
            $data[] = (int)$row['total'];
        }

        $hospStats = DashboardStat::getReferOutHospDetail($type, $start_date, $end_date, 10);
        $allDeptStats = DashboardStat::getAllDeptReferOut($start_date, $end_date);

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

    public function actionReferInDetail()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', 'OPD');
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));

        $type_names = [
            'OPD' => 'ผู้ป่วยนอก (OPD)',
            'IPD' => 'ผู้ป่วยใน (IPD)',
            'ER' => 'ห้องฉุกเฉิน (ER)'
        ];
        $type_name = isset($type_names[strtoupper($type)]) ? $type_names[strtoupper($type)] : $type;

        $diagStats = DashboardStat::getReferInDiagDetail($type, $start_date, $end_date, 10);

        $labels = [];
        $data = [];
        foreach ($diagStats as $row) {
            $labels[] = $row['icd10'];
            $data[] = (int)$row['total'];
        }

        $hospStats = DashboardStat::getReferInHospDetail($type, $start_date, $end_date, 10);
        $allDeptStats = DashboardStat::getAllDeptReferIn($start_date, $end_date);

        return $this->render('refer-in-detail', [
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

    public function actionErDetail($start_date = null, $end_date = null)
    {
        $request = Yii::$app->request;
        $start_date = $request->get('start_date', date('Y-m-d'));
        $end_date = $request->get('end_date', date('Y-m-d'));

        $diagStats = DashboardStat::getErDiagDetail($start_date, $end_date, 10);
        $typeStats = DashboardStat::getErTypeStats($start_date, $end_date);
        $dchStats = DashboardStat::getErDchStats($start_date, $end_date);

        return $this->render('er-detail', [
            'diagStats' => $diagStats,
            'typeStats' => $typeStats,
            'dchStats' => $dchStats,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'er_depcode' => '011' //ER Depart
        ]);
    }
    // LAB
    public function actionLabDetail($start_date = null, $end_date = null)
    {
        $start_date = $start_date ?? date('Y-m-d');
        $end_date = $end_date ?? date('Y-m-d');

        $labStats = DashboardStat::getTopLabOrders($start_date, $end_date);
        // เตรียมข้อมูลสำหรับกราฟ
        $labels = [];
        $data = [];
        foreach ($labStats as $row) {
            $labels[] = $row['lab_name'];
            $data[] = (int)$row['total'];
        }
        return $this->render('lab-detail', [
            'labStats' => $labStats,
            'labels' => $labels,
            'data' => $data,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }
    // XRAY
    public function actionXrayDetail($start_date = null, $end_date = null)
    {
        $start_date = $start_date ?? date('Y-m-d');
        $end_date = $end_date ?? date('Y-m-d');

        $labStats = DashboardStat::getTopXrayOrders($start_date, $end_date);
        // เตรียมข้อมูลสำหรับกราฟ
        $labels = [];
        $data = [];
        foreach ($labStats as $row) {
            $labels[] = $row['xray_items_name'];
            $data[] = (int)$row['total'];
        }
        return $this->render('xray-detail', [
            'labStats' => $labStats,
            'labels' => $labels,
            'data' => $data,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }

    // สิทธิการรักษา pttype-detail
    public function actionPttypeDetail($start_date = null, $end_date = null)
    {
        // รับค่าจาก GET (ถ้ามาจากฟอร์ม)
        $request = Yii::$app->request;
        $start_date = $request->get('start_date', $start_date ?: date('Y-m-d'));
        $end_date = $request->get('end_date', $end_date ?: date('Y-m-d'));

        $pttypeData = DashboardStat::getPttypeStatsByRange($start_date, $end_date); 
        
        // สำคัญ: ถ้าดึงข้อมูลไม่ได้ ให้ส่ง Array ว่างไปแทน null เพื่อป้องกัน Error ใน View
        if (!$pttypeData) {
            $pttypeData = [];
        }

        return $this->render('pttype-detail', [
            'pttypeData' => $pttypeData,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ]);
    }
    //Report Start
    public function actionReportIcd10($icd10 = null, $start_date = null, $end_date = null)
    {
        // ตรวจสอบ Login
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }

        $request = Yii::$app->request;
        $icd10 = $request->get('icd10', 'I60-I69'); // ค่าเริ่มต้น
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        // คำนวณปีงบประมาณ 3 ปีย้อนหลัง
        $y = (date('m') >= 10) ? date('Y') + 1 : date('Y');
        $fiscalYears = [$y, $y-1, $y-2];

        // ถ้ายังไม่ได้เลือกวันที่ ให้ใช้ปีงบประมาณปัจจุบันเป็นค่าเริ่มต้น
        // if (!$start_date) {
        //     $start_date = ($y - 1) . '-10-01';
        //     $end_date = $y . '-09-30';
        // }
        
        /*ถ้ายังไม่ได้เลือกวันที่ (กดเข้ามาหน้าเมนูครั้งแรก) 
         * ให้กำหนดเป็นวันที่ปัจจุบัน (Today)
         */
        if (empty($start_date)) {
            $start_date = date('Y-m-d'); // วันนี้
        }
        if (empty($end_date)) {
            $end_date = date('Y-m-d');   // วันนี้
        }

        $data = DashboardStat::getIcd10Report($icd10, $start_date, $end_date);

        return $this->render('report_icd10', [
            'data' => $data,
            'icd10' => $icd10,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'fiscalYears' => $fiscalYears
        ]);
    }
    //Report Close

    public function actionSearch($cid = null)
    {
        $patientData = null;
        $visitHistory = [];

        if ($cid) {
            $patientData = DashboardStat::getPatientByCid($cid);
            if ($patientData) {
                $visitHistory = DashboardStat::getVisitHistoryByHn($patientData['hn'], 10);
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
        $rawData = DashboardStat::getQuarterlyStats();
        return $this->render('quarterly', ['rawData' => $rawData]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            // แทนที่จะเป็น goHome() ให้ระบุ Path ตรงๆ
            return $this->redirect(['site/index']); 
        }

        $model = new LoginForm();
    
            // ถ้ามีการ Post ข้อมูลมา และ Login สำเร็จ
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // ห้ามใช้ goBack() เพราะ Proxy จะพาหลงทางไปที่ https://site/login
            // ให้ใช้ redirect(['index']) เพื่อให้วิ่งไปที่ /hos-dashboard/site/index
            return $this->redirect(['index']);
            }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    public function actionLogout()
    {
        if (Yii::$app->request->isPost) {
            Yii::$app->user->logout();
            // เพิ่ม Flash Message แจ้งสถานะสำเร็จ
            Yii::$app->session->setFlash('success', 'ออกจากระบบเรียบร้อยแล้ว');
            return $this->redirect(['/site/index']); 
        }
        return $this->redirect(['/site/index']);
    }


    public function beforeAction($action)
    {
        // ปิด CSRF สำหรับ Login และการเรียกผ่าน AJAX จาก Proxy
        if ($action->id == 'login') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
}