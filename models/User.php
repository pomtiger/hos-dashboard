<?php

namespace app\models;

use Yii;
use yii\db\Query;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    public $id;
    public $username;
    public $password;
    public $passweb;
    public $authKey;
    public $accessToken;
    public $name; // เก็บชื่อ-นามสกุลจริงจาก HOSxP

    public static function findIdentity($id)
    {
        $user = (new Query())
            ->from('opduser')
            ->where(['loginname' => $id])
            ->one();

        if ($user) {
            return new static([
                'id' => $user['loginname'],
                'username' => $user['loginname'],
                'password' => $user['password'], // เก็บค่าจากฟิลด์ password
                'passweb' => $user['passweb'],   // เพิ่มการเก็บค่าจากฟิลด์ passweb
                'name' => $user['name']
            ]);
        }
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByUsername($username)
    {
        $user = (new Query())
            ->from('opduser')
            ->where(['loginname' => $username])
            ->one();

        if ($user) {
            return new static([
                'id' => $user['loginname'],
                'username' => $user['loginname'],
                'password' => $user['password'],
                'passweb' => $user['passweb'],
                'name' => $user['name']
            ]);
        }
        return null;
    }



    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    // ใน models/User.php
    public function validatePassword($password)
    {
        // ตรวจสอบด้วย MD5 (ฟิลด์ passweb) 
        if ($this->passweb !== null && strtoupper($this->passweb) === strtoupper(md5($password))) {
            return true;
        }

        // กรณีต้องการทดสอบว่าระบบ Path มีปัญหาหรือไม่ ให้ลองบรรทัดด้านล่างนี้ (เฉพาะตอนทดสอบ!)
        // return ($password === 'รหัสผ่านที่รู้แน่นอน'); 

        return false;
    }
}