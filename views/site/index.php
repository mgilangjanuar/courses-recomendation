<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Hi!';
?>
<div class="site-index">

    <h1>About</h1>
    <p>
        Sistem rekomendasi mata kuliah ini dibuat untuk lingkup <strong>Fakultas Ilmu Komputer</strong> dengan 
        <strong>kurikulum 2010</strong> menggunakan metode <em>content base filtering</em> sehingga sistem ini
        bekerja berdasarkan riwayat mata kuliah yang telah diambil oleh pengguna tersebut kemudian sistem
        akan merekomendasikan mata kuliah yang "mungkin" diminati oleh pengguna. Untuk mendapatkan
        data, sistem ini masih harus menuntut pengguna melakukan input manual.
    </p>
    <br />
    
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
    ]) ?>

        <div class="form-group center">
            <?= Html::submitButton('<i class="material-icons left">save</i> Accept & Continue', ['class' => 'btn waves-effect waves-light']) ?>
        </div>

    <?php ActiveForm::end() ?>

</div>
