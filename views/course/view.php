<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title = 'Courses';
?>
<div class="site-index">

    <h2>Recommendation Term <?= Yii::$app->request->get('run') ?></h2>

    <p>
        <?= Html::a('Back', ['index', 'id' => Yii::$app->request->get('id')], ['class' => 'btn waves-effect waves-light']) ?>
    </p>

    <ul class="collapsible popout" data-collapsible="accordion">
        <?php foreach ($models as $i=>$model): ?>
            <?php if ($model['accuracy'] > 0): ?>
                <li>
                    <div class="collapsible-header">[<?= $model['model']->code ?>] <?= $model['model']->name ?> <span class="right">(<?= round($model['accuracy'] * 100, 2) ?>%)</span></div>
                    <div class="collapsible-body">
                        <p>
                            <?= $model['model']->sks ?> SKS <br><br>

                            Prasyarat: <?= !$model['model']->prerequisiteCourses ? ' - ' : implode(', ', $model['model']->simplePrerequisiteCourses) ?> <br><br>

                            Biasa dibuka pada semester 
                            <?php if ($model['model']->term == 0): ?>
                                <?= $model['model']->year + $model['model']->year - 1 ?> atau 
                                <?= $model['model']->year * 2 ?>
                            <?php elseif ($model['model']->term == 1): ?>
                                <?= $model['model']->year + $model['model']->year - 1 ?>
                            <?php elseif ($model['model']->term == 2): ?>
                                <?= $model['model']->year * 2 ?>
                            <?php endif ?> <br><br>

                            <?php if ($model['model']->is_required): ?>
                                <span class="red-text">Merupakan mata kuliah wajib</span> <br><br>
                            <?php endif ?>

                            <?= $model['model']->courseDescription->description ?>
                        </p>
                    </div>
                </li>
            <?php endif ?>
        <?php endforeach ?>
    </ul>

</div>
