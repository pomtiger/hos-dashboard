<?php

namespace app\models;

use Yii;
use yii\base\Model;

class DashboardStat extends Model
{
    // --- Index Dashboard ---

    public static function getOpdToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(DISTINCT vn) FROM ovst WHERE vstdate = CURDATE()")->queryScalar();
    }

    public static function getOpdNoAdmitToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(DISTINCT vn) FROM ovst WHERE vstdate = CURDATE() AND (an IS NULL OR an ='')")->queryScalar();
    }

    public static function getOpdAuthenToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(DISTINCT vp.vn) FROM visit_pttype vp LEFT JOIN ovst ov ON ov.vn=vp.vn WHERE ov.vstdate = CURDATE() AND vp.auth_code IS NOT NULL AND vp.auth_code <> ''")->queryScalar();
    }

    public static function getOpdDiagCountToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(DISTINCT vn) FROM ovstdiag WHERE vstdate = CURDATE()")->queryScalar() ?: 0;
    }

    public static function getIpdNewToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(DISTINCT an) FROM ipt WHERE regdate = CURDATE()")->queryScalar();
    }

    public static function getIpdDischargeToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(DISTINCT an) FROM ipt WHERE dchdate = CURDATE()")->queryScalar();
    }

    public static function getIpdTotalToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(DISTINCT an) FROM ipt WHERE regdate = CURDATE()")->queryScalar();
    }

    public static function getIpdDiagCountToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(DISTINCT an) FROM an_stat WHERE (regdate = CURDATE() AND pdx IS NOT NULL)")->queryScalar();
    }

    public static function getReferOutToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(vn) FROM referout WHERE refer_date = CURDATE()")->queryScalar();
    }

    public static function getOpdReferOutToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(vn) FROM referout WHERE (refer_point='OPD' OR refer_point IS NULL) AND refer_date = CURDATE()")->queryScalar();
    }

    public static function getOpdVisitByDep($depcode)
    {
        $sql = "SELECT COUNT(vn) FROM ovst WHERE vstdate = CURDATE() AND main_dep = :depcode";
        return Yii::$app->db->createCommand($sql)->bindValue(':depcode', $depcode)->queryScalar() ?: 0;
    }

    public static function getIpdReferOutToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(vn) FROM referout WHERE refer_point='IPD' AND refer_date = CURDATE()")->queryScalar();
    }

    public static function getErReferOutToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(vn) FROM referout WHERE refer_point='ER' AND refer_date = CURDATE()")->queryScalar();
    }

    public static function getOpdReferInToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(vn) FROM referin WHERE (refer_point='OPD' OR refer_point IS NULL) AND refer_date = CURDATE()")->queryScalar();
    }

    public static function getIpdReferInToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(vn) FROM referin WHERE (refer_point='IPD') AND refer_date = CURDATE()")->queryScalar();
    }

    public static function getErReferInToday()
    {
        return Yii::$app->db->createCommand("SELECT COUNT(vn) FROM referin WHERE refer_point='ER' AND refer_date = CURDATE()")->queryScalar();
    }

    public static function getHospReferOutToday($limit = 5)
    {
        $sql = "
            SELECT h.name AS hosp_name, COUNT(*) AS total
            FROM referout r
            LEFT JOIN hospcode h ON h.hospcode = r.refer_hospcode
            WHERE r.refer_date = CURDATE()
            GROUP BY r.refer_hospcode
            ORDER BY total DESC
            LIMIT :limit
        ";
        return Yii::$app->db->createCommand($sql)->bindValue(':limit', (int)$limit, \PDO::PARAM_INT)->queryAll();
    }

    public static function getHospReferInToday($limit = 5)
    {
        $sql = "
            SELECT h.name AS hosp_name, COUNT(*) AS total
            FROM referin r
            LEFT JOIN hospcode h ON h.hospcode = r.refer_hospcode
            WHERE r.refer_date = CURDATE()
            GROUP BY r.refer_hospcode
            ORDER BY total DESC
            LIMIT :limit
        ";
        // หมายเหตุเดิม query ตาราง referout อยู่ใน SiteController, ตอนนี้ปรับเป็น referin ตามชื่อตัวแปรย่อย
        return Yii::$app->db->createCommand($sql)->bindValue(':limit', (int)$limit, \PDO::PARAM_INT)->queryAll();
    }

    public static function getDeptStatsToday()
    {
        $sql = "SELECT 
                d.department as dept_name, 
                COUNT(v.vn) as total,
                SUM(CASE WHEN v.vsttime BETWEEN '07:30:00' AND '16:30:00' THEN 1 ELSE 0 END) as Intime,
                SUM(CASE WHEN v.vsttime > '16:30:00' THEN 1 ELSE 0 END) as Outtime
            FROM ovst v 
            LEFT JOIN kskdepartment d ON d.depcode = v.main_dep 
            WHERE v.vstdate = CURDATE() 
            GROUP BY v.main_dep 
            ORDER BY total DESC";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getErStatsToday()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(IF(er_pt_type in('1'), 1, 0)) as emergency_case,
                    SUM(IF(er_pt_type in('2','5'), 1, 0)) as accident_case,
                    SUM(IF(er_pt_type IS NULL OR er_pt_type = '' OR er_pt_type in('3','4'), 1, 0)) as general_case,
                    SUM(IF(er_pt_type in('6'), 1, 0)) as uc_another_province
                FROM ovst o
                LEFT JOIN er_regist e ON o.vn = e.vn
                WHERE o.main_dep='011' AND o.vstdate = CURDATE()
                ";
        return Yii::$app->db->createCommand($sql)->queryOne();
    }

    public static function getClinicStatsToday()
    {
        $sql = "
            SELECT c.name as clinic_name, COUNT(v.vn) as total 
            FROM ovst v 
            INNER JOIN clinic c ON v.main_dep = c.depcode 
            WHERE v.vstdate = CURDATE() 
            GROUP BY v.main_dep 
            ORDER BY total DESC
            LIMIT 5
        ";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    // --- Detail Pages ---

    public static function getDeptDetail($start_date, $end_date)
    {
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

        return Yii::$app->db->createCommand($sql)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();
    }

    public static function getPatientList($depcode, $start_date, $end_date)
    {
        $sql = "
            SELECT 
                v.vstdate, v.vsttime, v.hn,v.pttype,t.name AS pttype_name,
                CONCAT(p.pname, p.fname, ' ', p.lname) as fullname,
                vn.pdx, i.name as diag_name
            FROM ovst v
            LEFT JOIN vn_stat vn ON v.vn = vn.vn
            LEFT JOIN pttype t ON v.pttype = t.pttype
            LEFT JOIN patient p ON p.hn = v.hn
            LEFT JOIN icd101 i ON i.code = vn.pdx
            WHERE v.main_dep = :depcode 
            AND v.vstdate BETWEEN :start AND :end
            ORDER BY v.vsttime DESC
        ";

        return Yii::$app->db->createCommand($sql)
            ->bindValue(':depcode', $depcode)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();
    }

    public static function getDiagDetail($start_date, $end_date, $limit = 20)
    {
        $sql = "
            SELECT i.pdx AS icd10 ,d.name AS diag_name,d.tname,COUNT(*) AS total
            FROM vn_stat i
            LEFT OUTER JOIN icd101 d ON d.code = i.pdx
            WHERE i.vstdate BETWEEN :start AND :end AND i.pdx <> ''
            and i.pdx not like 'z%'
            GROUP BY i.pdx
            ORDER BY COUNT(*) DESC
            LIMIT :limit
        ";

        return Yii::$app->db->createCommand($sql)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->bindValue(':limit', (int)$limit, \PDO::PARAM_INT)
            ->queryAll();
    }
    public static function getUniqueDiagCount($start_date, $end_date)
    {
        return Yii::$app->db->createCommand("
            SELECT COUNT(DISTINCT icd10) 
            FROM ovstdiag 
            WHERE vstdate BETWEEN :start AND :end
        ")
        ->bindValue(':start', $start_date)
        ->bindValue(':end',   $end_date)
        ->queryScalar() ?: 0;
    }

    public static function getClinicDiagDetail($depcode, $start_date, $end_date, $limit = 990)
    {
        $sql = "
            SELECT i.pdx AS icd10, d.name AS diag_name, d.tname, COUNT(*) AS total
            FROM vn_stat i
            LEFT JOIN ovst o ON o.vn = i.vn
            LEFT OUTER JOIN icd101 d ON d.code = i.pdx
            WHERE o.main_dep = :depcode
            AND i.vstdate BETWEEN :start AND :end 
            AND i.pdx IS NOT NULL AND i.pdx <> ''
            AND i.pdx NOT LIKE 'z%'
            GROUP BY i.pdx
            ORDER BY total DESC
            LIMIT :limit
        ";

        return Yii::$app->db->createCommand($sql)
            ->bindValue(':depcode', $depcode)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->bindValue(':limit', (int)$limit, \PDO::PARAM_INT)
            ->queryAll();
    }

    public static function getDepartmentName($depcode)
    {
        $sql = "SELECT department FROM kskdepartment WHERE depcode = :depcode";
        return Yii::$app->db->createCommand($sql)->bindValue(':depcode', $depcode)->queryScalar() ?: 'แผนก (' . $depcode . ')';
    }

    public static function getWardDetail()
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

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getIpdDiagDetail($start_date, $end_date, $limit = 10)
    {
        $sql = "
            SELECT i.pdx AS icd10, d.name AS diag_name, d.tname, COUNT(*) AS total
            FROM an_stat i
            LEFT OUTER JOIN icd101 d ON d.code = i.pdx
            WHERE i.regdate BETWEEN :start AND :end 
            AND i.pdx IS NOT NULL AND i.pdx <> ''
            AND i.pdx NOT LIKE 'z%'
            GROUP BY i.pdx
            ORDER BY total DESC
            LIMIT :limit
        ";

        return Yii::$app->db->createCommand($sql)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->bindValue(':limit', (int)$limit, \PDO::PARAM_INT)
            ->queryAll();
    }

    // --- Refer OUT ---
    public static function getReferOutDiagDetail($type, $start_date, $end_date, $limit = 10)
    {
        $sql = "
            SELECT r.pdx AS icd10, d.name AS diag_name, COUNT(*) AS total
            FROM referout r
            LEFT JOIN icd101 d ON d.code = r.pdx
            WHERE r.refer_point = :point 
            AND r.refer_date BETWEEN :start AND :end 
            AND r.pdx IS NOT NULL AND r.pdx <> ''
            GROUP BY r.pdx
            ORDER BY total DESC
            LIMIT :limit
        ";

        return Yii::$app->db->createCommand($sql)
            ->bindValue(':point', $type)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->bindValue(':limit', (int)$limit, \PDO::PARAM_INT)
            ->queryAll();
    }
    public static function getReferOutHospDetail($type, $start_date, $end_date, $limit = 10)
    {
        $sql = "
            SELECT h.name AS hosp_name, COUNT(*) AS total
            FROM referout r
            LEFT JOIN hospcode h ON h.hospcode = r.refer_hospcode
            WHERE r.refer_point = :point 
            AND r.refer_date BETWEEN :start AND :end
            GROUP BY r.refer_hospcode
            ORDER BY total DESC
            LIMIT :limit
        ";
        return Yii::$app->db->createCommand($sql)
            ->bindValue(':point', $type)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->bindValue(':limit', (int)$limit, \PDO::PARAM_INT)
            ->queryAll();
    }
    public static function getAllDeptReferOut($start_date, $end_date)
    {
        $sql = "
            SELECT 
                refer_point as department_name, 
                COUNT(*) as total
            FROM referout
            WHERE refer_date BETWEEN :start AND :end
            AND refer_point IS NOT NULL AND refer_point <> ''
            GROUP BY refer_point
            ORDER BY total DESC
        ";
        return Yii::$app->db->createCommand($sql)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();
    }

    // --- Refer IN ---
    public static function getReferInDiagDetail($type, $start_date, $end_date, $limit = 10)
    {
        $sql = "
            SELECT r.icd10 AS icd10, d.name AS diag_name, COUNT(*) AS total
            FROM referin r
            LEFT JOIN icd101 d ON d.code = r.icd10
            WHERE r.refer_point = :point 
            AND r.refer_date BETWEEN :start AND :end 
            AND r.icd10 IS NOT NULL AND r.icd10 <> ''
            GROUP BY r.icd10
            ORDER BY total DESC
            LIMIT :limit
        ";

        return Yii::$app->db->createCommand($sql)
            ->bindValue(':point', $type)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->bindValue(':limit', (int)$limit, \PDO::PARAM_INT)
            ->queryAll();
    }
    public static function getReferInHospDetail($type, $start_date, $end_date, $limit = 10)
    {
        $sql = "
            SELECT h.name AS hosp_name, COUNT(*) AS total
            FROM referin r
            LEFT JOIN hospcode h ON h.hospcode = r.refer_hospcode
            WHERE r.refer_point = :point 
            AND r.refer_date BETWEEN :start AND :end
            GROUP BY r.refer_hospcode
            ORDER BY total DESC
            LIMIT :limit
        ";
        return Yii::$app->db->createCommand($sql)
            ->bindValue(':point', $type)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->bindValue(':limit', (int)$limit, \PDO::PARAM_INT)
            ->queryAll();
    }
    public static function getAllDeptReferIn($start_date, $end_date)
    {
        $sql = "
            SELECT 
                refer_point as department_name, 
                COUNT(*) as total
            FROM referin
            WHERE refer_date BETWEEN :start AND :end
            AND refer_point IS NOT NULL AND refer_point <> ''
            GROUP BY refer_point
            ORDER BY total DESC
        ";
        return Yii::$app->db->createCommand($sql)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();
    }

    // --- ER ---
    public static function getErDiagDetail($start_date, $end_date, $limit = 10)
    {
        $sql = "
            SELECT i.pdx AS icd10, d.name AS diag_name, COUNT(*) AS total
            FROM er_regist e
            LEFT JOIN vn_stat i ON i.vn = e.vn
            LEFT JOIN icd101 d ON d.code = i.pdx
            WHERE e.vstdate BETWEEN :start AND :end 
            AND i.pdx IS NOT NULL AND i.pdx <> ''
            GROUP BY i.pdx
            ORDER BY total DESC
            LIMIT :limit
        ";
        return Yii::$app->db->createCommand($sql)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->bindValue(':limit', (int)$limit, \PDO::PARAM_INT)
            ->queryAll();
    }
    public static function getErTypeStats($start_date, $end_date)
    {
        $sql = "
            SELECT 
                SUM(IF(er_pt_type in('1'), 1, 0)) as emergency_case,
                SUM(IF(er_pt_type in('2','5'), 1, 0)) as accident_case,
                SUM(IF(er_pt_type IS NULL OR er_pt_type = '' OR er_pt_type in('3','4'), 1, 0)) as general_case,
                SUM(IF(er_pt_type in('6'), 1, 0)) as uc_another_province
            FROM er_regist
            WHERE vstdate BETWEEN :start AND :end
        ";
        return Yii::$app->db->createCommand($sql)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryOne();
    }
    public static function getErDchStats($start_date, $end_date)
    {
        $sql = "SELECT 
                e.er_dch_type AS dch_id,
                IF(ed.name IS NOT NULL, ed.name, 'ไม่ระบุ') AS er_dch_type,
                COUNT(*) as total
            FROM er_regist e 
            LEFT JOIN er_dch_type ed ON e.er_dch_type = ed.er_dch_type
            WHERE e.vstdate BETWEEN :start AND :end
            GROUP BY e.er_dch_type
            ORDER BY dch_id ASC";
        return Yii::$app->db->createCommand($sql)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();
    }

    // --- สำหรับ LAB ---
    public static function getLabStatsToday() {
        $db = Yii::$app->db;
        $today = date('Y-m-d');
        // LAB ทั้งหมด (นับราย VN/AN ที่สั่ง)
        $total = $db->createCommand("SELECT COUNT(DISTINCT vn) FROM lab_head WHERE order_date = CURDATE()")->queryScalar();
        $totalOrder = $db->createCommand("SELECT COUNT(DISTINCT vn) FROM lab_head WHERE order_date = :today", [':today' => $today])->queryScalar();
        // จำนวนรายการ LAB ทั้งหมด (นับทุก Test ที่สั่ง)
        $totalItems = $db->createCommand("SELECT COUNT(*) FROM lab_head WHERE order_date = :today", [':today' => $today])->queryScalar();
        
        // 3. แยก OPD / IPD (นับตามใบสั่ง VN)
        $opd = $db->createCommand("SELECT COUNT(DISTINCT lh.vn) FROM lab_head lh INNER JOIN ovst ov ON lh.vn = ov.vn WHERE lh.order_date = :today", [':today' => $today])->queryScalar();
        $ipd = $db->createCommand("SELECT COUNT(DISTINCT lh.vn) FROM lab_head lh INNER JOIN ipt i ON lh.vn = i.an WHERE lh.order_date = :today", [':today' => $today])->queryScalar();

        return [
            'total' => $total,
            'totalOrder' => $totalOrder,
            'totalItems' => $totalItems,
            'opd' => $opd,
            'ipd' => $ipd
        ];
    }
    public static function getTopLabOrders($start_date, $end_date) {
        $db = Yii::$app->db;
        return $db->createCommand("
            SELECT lo.lab_items_code, lo.lab_items_name_ref as lab_name, COUNT(*) AS total
            FROM lab_head lh
            LEFT JOIN lab_order lo ON lh.lab_order_number = lo.lab_order_number
            WHERE lh.order_date BETWEEN :start AND :end
            AND lo.lab_items_code IS NOT NULL
            GROUP BY lo.lab_items_code, lo.lab_items_name_ref
            ORDER BY total DESC
            LIMIT 30
        ", [':start' => $start_date, ':end' => $end_date])->queryAll();
    }

    // --- สำหรับ X-Ray ---
    public static function getXrayStatsToday() {
        $db = Yii::$app->db;
        $today = date('Y-m-d');
        // X-Ray ทั้งหมด
        $total = $db->createCommand("SELECT COUNT(*) FROM xray_head WHERE order_date_time >= CURDATE()")->queryScalar();
        $totalOrder = $db->createCommand("SELECT COUNT(*) FROM xray_report WHERE request_date >= :today", [':today' => $today])->queryScalar();
        // แยก OPD/IPD
        $opd = $db->createCommand("SELECT COUNT(*) FROM xray_head WHERE order_date_time >= :today AND department = 'OPD'", [':today' => $today])->queryScalar();
        $ipd = $db->createCommand("SELECT COUNT(*) FROM xray_head WHERE order_date_time >= :today AND department = 'IPD'", [':today' => $today])->queryScalar();

        return [
            'total' => $total, 
            'totalOrder' => $totalOrder,
            'opd' => $opd, 
            'ipd' => $ipd];
    }
    public static function getTopXrayOrders($start_date, $end_date) {
        $db = Yii::$app->db;
        return $db->createCommand("
            SELECT i.xray_items_code,i.xray_items_name ,COUNT(*)AS total
            FROM xray_report x
            LEFT JOIN xray_items i on i.xray_items_code = x.xray_items_code
            WHERE x.request_date BETWEEN :start AND :end
            GROUP BY i.xray_items_code
            ORDER BY total DESC
            LIMIT 30
        ", [':start' => $start_date, ':end' => $end_date])->queryAll();
    }

    // อันดับการใช้ยา และ  อันดับมูลค่าการใช้ยา
    public static function getTopDrugStats() {
        $db = Yii::$app->db;
        $today = date('Y-m-d');

        // อันดับจำนวนการใช้สูงสุด
        $topQty = $db->createCommand("
            SELECT CONCAT(d.name,' ',d.strength,' ', d.units) as drugname, 
               SUM(op.qty) as total_qty
            FROM opitemrece op
            LEFT JOIN drugitems d ON op.icode = d.icode 
            WHERE op.vstdate = :today 
                AND op.an IS NULL 
                AND op.icode LIKE '1%'
            GROUP BY op.icode, d.name, d.strength, d.units 
            ORDER BY total_qty DESC 
            LIMIT 20
            "
            , [':today' => $today])->queryAll();

        // อันดับมูลค่าสูงสุด
        $topValue = $db->createCommand("
            SELECT CONCAT(d.name,' ',d.strength,' ', d.units) as drugname, 
               SUM(op.sum_price) as total_value
            FROM opitemrece op
            LEFT JOIN drugitems d ON op.icode = d.icode 
            WHERE op.vstdate = :today 
                AND op.an IS NULL 
                AND op.icode LIKE '1%'
            GROUP BY op.icode, d.name, d.strength, d.units
            ORDER BY total_value DESC 
            LIMIT 20
            "
            , [':today' => $today])->queryAll();
            return [
                'qty' => $topQty, 
                'value' => $topValue
                ];
    }
    // จำนวนผู้รับบริการแยกตามสิทธิ
    public static function getOpdPttypeStats()
    {
        $sql = "SELECT p.name as pttype_name, COUNT(v.vn) as total
                FROM ovst v
                LEFT JOIN pttype p ON p.pttype = v.pttype
                WHERE v.vstdate = CURDATE()
                GROUP BY v.pttype
                ORDER BY total DESC
                LIMIT 20";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }
    public static function getPttypeStatsByRange($start_date, $end_date) 
    {
        // ป้องกันกรณีวันที่ส่งมาเป็นค่าว่างให้ใช้ค่าปัจจุบัน
        $start = !empty($start_date) ? $start_date : date('Y-m-d');
        $end = !empty($end_date) ? $end_date : date('Y-m-d');

        return Yii::$app->db->createCommand("
            SELECT 
                p.name as pttype_name, 
                v.pttype,
                COUNT(v.vn) as total
            FROM ovst v
            LEFT JOIN pttype p ON p.pttype = v.pttype
            WHERE v.vstdate BETWEEN :start AND :end
            GROUP BY v.pttype
            ORDER BY total DESC
            LIMIT 30
        ")
        ->bindValue(':start', $start)
        ->bindValue(':end', $end)
        ->queryAll();
    }
    // สรุปข้อมูลสิทธิการรักษา
    public static function getPTTypePatientList($pttype, $start_date, $end_date)
    {
        $sql = "SELECT  v.hn, p.fname,p.lname, v.vstdate, v.vsttime, 
                v.pttype, t.name as pttype_name,v.main_dep,k.department,COUNT(*) as total
            FROM ovst v
            LEFT JOIN patient p ON p.hn = v.hn
            LEFT JOIN kskdepartment k ON k.depcode = v.main_dep
            LEFT JOIN pttype t ON t.pttype = v.pttype
            WHERE v.pttype = :pttype 
            AND v.vstdate BETWEEN :start AND :end
            GROUP BY v.main_dep
            ORDER BY total DESC
        ";
        return Yii::$app->db->createCommand($sql)
            ->bindValue(':pttype', $pttype)
            ->bindValue(':start', $start_date)
            ->bindValue(':end', $end_date)
            ->queryAll();
    }

    //Report Start
    public static function getIcd10Report($icd10_input, $start_date, $end_date)
    {
        // แยกรหัสโรคที่ผู้ใช้กรอก (เช่น I60, I61 หรือ I60-I69)
        $query = (new \yii\db\Query())
            ->select([
                'od.hn', 
                'p.pname', 'p.fname', 'p.lname', 'p.cid','p.sex','p.birthday',
                'od.icd10', 'i.name as diag_name', 'od.vstdate','v.age_y',
                'p.addrpart', 'p.moopart', 't.full_name', 'p.hometel'
            ])
            ->from('ovstdiag od')
            ->leftJoin('vn_stat v', 'od.vn= v.vn')
            ->leftJoin('icd101 i', 'od.icd10 = i.code')
            ->leftJoin('patient p', 'od.hn = p.hn')
            ->leftJoin('thaiaddress t', 'p.addressid = t.addressid')
            ->where(['between', 'od.vstdate', $start_date, $end_date]);

        // จัดการเงื่อนไข ICD10 แบบช่วง หรือ แบบระบุหลายรหัส
        if (strpos($icd10_input, '-') !== false) {
            // กรณีระบุเป็นช่วง เช่น I60-I69
            list($start_code, $end_code) = explode('-', $icd10_input);
            $query->andWhere(['between', 'od.icd10', trim($start_code), trim($end_code)]);
        } else {
            // กรณีระบุหลายรหัสแยกด้วยคอมม่า เช่น I60, I61
            $codes = array_map('trim', explode(',', $icd10_input));
            $query->andWhere(['in', 'od.icd10', $codes]);
        }

        return $query->groupBy('od.hn')
                    // ->orderBy(['od.vstdate' => SORT_ASC])
                    ->orderBy(['od.vstdate' => SORT_ASC])
                    ->all();
    }
    //Report Close

    // --- Search & Quarterly ---
    public static function getPatientByCid($cid)
    {
        $sql = "SELECT hn, fname, lname, birthday, sex, cid FROM patient WHERE cid = :cid";
        return Yii::$app->db->createCommand($sql, [':cid' => $cid])->queryOne();
    }

    public static function getVisitHistoryByHn($hn, $limit = 10)
    {
        $sql = "SELECT v.vstdate, v.vn, i.pdx as icd10, d.name as diag_name
                FROM vn_stat v
                LEFT JOIN icd101 d ON d.code = v.pdx
                WHERE v.hn = :hn
                ORDER BY v.vstdate DESC
                LIMIT :limit";
        return Yii::$app->db->createCommand($sql)
            ->bindValue(':hn', $hn)
            ->bindValue(':limit', (int)$limit, \PDO::PARAM_INT)
            ->queryAll();
    }
    public static function getQuarterlyStats()
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
        return Yii::$app->db->createCommand($sql)->queryAll();
    }
}
