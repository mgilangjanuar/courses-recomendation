<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

$this->title = 'Hi!';
?>
<div class="site-index">
    
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
    ]) ?>

        <?= Select2::widget([
            'name' => 'CoursesUser[courses][]',
            'value' => $model->getCurrentCourses(),
            'data' => $model->getCourses(),
            'options' => ['multiple' => true],
            'maintainOrder' => false,
            'pluginOptions' => [
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                ],
                'ajax' => [
                    'url' => ['course-list'],
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(name) { return name.text; }'),
                'templateSelection' => new JsExpression('function (name) { return name.text; }'),
            ],
        ]) ?>
        <br />

        <div class="form-group center">
            <?= Html::submitButton('<i class="material-icons left">save</i> Save', ['class' => 'btn waves-effect waves-light']) ?>
        </div>

    <?php ActiveForm::end() ?>

</div>
