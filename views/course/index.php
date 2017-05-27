<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title = 'Courses';
?>
<div class="site-index">
    
    <?php foreach ($models as $term=>$courses): ?>
        <h3>
            TERM <?= $term ?> 
            <?= Html::a('(edit)', ['index', 'id' => Yii::$app->request->get('id'), 'term' => $term], ['class' => '']) ?>
            <?php if ($term == count($models)): ?>
                <?= Html::a('(delete)', ['delete', 'id' => Yii::$app->request->get('id'), 'term' => count($models)], ['class' => '']) ?>
            <?php endif ?>
        </h3>
        <ul class="collection">
            <?php foreach ($courses as $course): ?>
                <li class="collection-item white-text <?= $course->is_pass ? 'teal' : 'red' ?>">
                    <?= $course->course->name ?>
                    <span class="right">
                        <?php if ($course->is_pass): ?>
                            <?= Html::a('(ubah belum lulus)', ['toggle-pass', 'user' => Yii::$app->request->get('id'), 'id' => $course->id], ['class' => 'red-text']) ?>
                        <?php else: ?>
                            <?= Html::a('(ubah sudah lulus)', ['toggle-pass', 'user' => Yii::$app->request->get('id'), 'id' => $course->id], ['class' => 'white-text']) ?>
                        <?php endif ?>
                    </span>
                </li>
            <?php endforeach ?>
        </ul>
        <br />
    <?php endforeach ?>

    <?= Html::a('Add Term '. (count($models) + 1), ['index', 'id' => Yii::$app->request->get('id'), 'term' => count($models) + 1], ['class' => 'btn waves-effect waves-light']) ?>
    <?php if (count($models) > 0): ?>
        <?= Html::a('View Recommendation Term ' . (count($models) + 1), ['index', 'id' => Yii::$app->request->get('id'), 'run' => count($models) + 1], ['class' => 'btn waves-effect waves-light']) ?>
    <?php endif ?>


</div>
