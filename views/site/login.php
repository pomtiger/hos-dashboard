<?php
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Sign In | HOSxP Dashboard';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-[#f8fafc]">
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-100/50 blur-[120px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-100/50 blur-[120px]"></div>

    <div class="w-full max-w-[420px] p-4 z-10">
        <div class="bg-white/70 backdrop-blur-xl border border-white shadow-[0_8px_32px_0_rgba(31,38,135,0.07)] rounded-[2.5rem] p-8 md:p-10">
            
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-hos-blue rounded-2xl shadow-lg shadow-blue-200 mb-6 rotate-3">
                     <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-2">HOSxP <span class="text-hos-blue">DASH</span></h2>
                <p class="text-slate-500 text-xs font-medium uppercase tracking-[0.2em]">Medical Intelligence System</p>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'action' => ['site/login'],
                'fieldConfig' => [
                    'template' => "{label}\n<div class='relative'>{input}<div class='absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-hos-blue transition-colors'>{icon}</div></div>\n{error}",
                    'labelOptions' => ['class' => 'block text-[13px] font-bold text-slate-700 mb-2 ml-1'],
                    'inputOptions' => ['class' => 'w-full bg-white/50 border border-slate-200 px-5 py-3.5 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-50 focus:border-hos-blue transition-all text-slate-900 placeholder:text-slate-400'],
                    'errorOptions' => ['class' => 'text-rose-500 text-[11px] mt-2 ml-1 font-medium'],
                ],
            ]); ?>

                <div class="space-y-6">
                    <?= $form->field($model, 'username', [
                        'parts' => ['{icon}' => '<i data-lucide="user" class="w-5 h-5"></i>']
                    ])->textInput(['placeholder' => 'Enter login name', 'autofocus' => true]) ?>

                    <?= $form->field($model, 'password', [
                        'parts' => ['{icon}' => '<i data-lucide="lock" class="w-5 h-5"></i>']
                    ])->passwordInput(['placeholder' => '••••••••']) ?>

                    <div class="flex items-center justify-between py-2">
                        <?= $form->field($model, 'rememberMe')->checkbox([
                            'template' => "<div class=\"flex items-center gap-3\">{input} {label}</div>\n{error}",
                            'labelOptions' => ['class' => 'text-sm font-medium text-slate-500 cursor-pointer select-none'],
                            'class' => 'w-5 h-5 border-2 border-slate-200 rounded-lg checked:bg-hos-blue transition-all cursor-pointer'
                        ]) ?>
                    </div>

                    <div>
                        <?= Html::submitButton('SIGN IN SYSTEM', [
                            'id' => 'btn-login',
                            'class' => 'w-full bg-hos-blue hover:bg-slate-900 text-white font-bold py-4 rounded-2xl shadow-xl shadow-blue-100 hover:shadow-none transform hover:-translate-y-0.5 active:translate-y-0 transition-all tracking-wider text-sm',
                            'name' => 'login-button'
                        ]) ?>
                    </div>
                </div>

            <?php ActiveForm::end(); ?>

            <div class="mt-10 text-center">
                <p class="text-slate-400 text-[11px]">
                    &copy; <?= date('Y') ?> Hospital Information System Dashboard<br>
                    <span class="font-bold">IT Department Buached Team</span>
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    input[type="checkbox"]:checked {
        background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/1999/xlink'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        background-color: #005b96;
        border-color: #005b96;
    }
    /* ปรับแต่งปุ่ม SweetAlert ให้เข้ากับธีม */
    .swal2-confirm {
        background-color: #005b96 !important;
        border-radius: 1rem !important;
        padding: 0.75rem 2rem !important;
        font-family: 'Kanit', sans-serif !important;
    }
    .swal2-popup {
        border-radius: 2.5rem !important;
        font-family: 'Kanit', sans-serif !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    
    // 1. ตรวจสอบ Error จาก Yii2 (Server-side validation)
    // ถ้ามี Error เกิดขึ้น (เช่น Incorrect username) ให้โชว์ SweetAlert
    const errorSummary = document.querySelector('.text-rose-500');
    if (errorSummary && errorSummary.innerText.trim() !== "") {
        Swal.fire({
            icon: 'error',
            title: 'เข้าสู่ระบบไม่สำเร็จ',
            text: 'ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง',
            confirmButtonText: 'ตกลง',
            showClass: { popup: 'animate__animated animate__fadeInDown' }
        });
    }

    // 2. Loading State เมื่อกดปุ่ม
    loginForm.addEventListener('submit', function() {
        if (!loginForm.querySelector('.has-error')) {
            let timerInterval;
            Swal.fire({
                title: 'กำลังตรวจสอบข้อมูล',
                html: 'กรุณารอสักครู่...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
    });
});
</script>