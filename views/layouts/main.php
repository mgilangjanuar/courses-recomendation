<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use ercling\pace\PaceWidget;
use yii\widgets\Pjax;
use kartik\growl\Growl;

AppAsset::register($this);
PaceWidget::widget([
    'color'=>'blue',
    'theme'=>'minimal',
    'options'=>[
        'ajax'=>['trackMethods'=>['GET','POST','AJAX']]
    ]
]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> - <?= Yii::$app->name ?></title>
    <!-- STYLES CONTENT -->
    <?php $this->head() ?>
    <!-- END OF STYLES CONTENT -->
</head>
<body>
<?php $this->beginBody() ?>

<header id="header-content">
    <nav class="top-nav">
        <div class="container">
            <div class="nav-wrapper">
                <?= Html::a(Yii::$app->name, Yii::$app->homeUrl, ['class' => 'page-title truncate spf-link']) ?>
            </div>
        </div>
    </nav>
    <div class="container">
        <a href="#!" data-activates="nav-mobile" class="button-collapse top-nav full hide-on-large-only">
            <i class="material-icons">menu</i>
        </a>
    </div>
    <ul id="nav-mobile" class="side-nav fixed">
        <div class="side-header">
            <div class="social-icons center">
                <h5 class="center my-name truncate"><?= Yii::$app->user->isGuest ? 'Hi, guest!' : 'Hi, ' . Yii::$app->user->identity->username . '!' ?></h5>
            </div>
        </div>
        <li class="search">
            <div class="search-wrapper card">
                <form action="<?= Url::to(['/site/search']) ?>" method="get">
                    <?php if (Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'search'): ?>
                        <input id="search" placeholder="Search..." name="id" value="<?= Yii::$app->request->get('id') ?>"><i class="material-icons">search</i>
                    <?php else: ?>
                        <input id="search" placeholder="Search..." name="id"><i class="material-icons">search</i>
                    <?php endif ?>
                </form>
            </div>
        </li>
        <li class="bold">
            <a href="<?= Yii::$app->homeUrl ?>" class="spf-link waves-effect waves-blue-grey">
                <i class="material-icons">home</i>
                Home
            </a>
        </li>
        <li class="divider"></li>
        <?php if (! Yii::$app->user->isGuest): ?>
            <li class="bold">
                <a href="<?= Url::to(['/user/profile']) ?>" class="spf-link waves-effect waves-blue-grey">
                    <i class="material-icons">person</i>
                    Profile
                </a>
            </li>
            <li class="bold">
                <a href="<?= Url::to(['/user/logout']) ?>" class="spf-link waves-effect waves-blue-grey" data-method="post">
                    <i class="material-icons">lock_open</i>
                    Logout
                </a>
            </li>
        <?php else: ?>
            <li class="bold">
                <a href="<?= Url::to(['/user/login']) ?>" class="spf-link waves-effect waves-blue-grey">
                    <i class="material-icons">lock</i>
                    Login
                </a>
            </li>
        <?php endif ?>
    </ul>
</header>
<main id="main-content">
    <!-- MAIN CONTENT -->
    <?php foreach (Yii::$app->session->allFlashes as $type => $message): ?>
        <?php $this->registerJs("Materialize.toast(\"".($type == 'danger' || $type == 'error' ? 'Oh snap!' : (ucfirst($type) . '!'))."<br />".$message."\", 3000 )") ?>
    <?php endforeach ?>
    <div class="container">
        <div class="card">
            <div class="card-content">
                <?= $content ?>
            </div>
        </div>
    </div>
    <!-- END OF MAIN CONTENT -->
</main>
<footer>
    <div class="container">
        <p class="center"><?= Yii::$app->name ?> &copy; <?= date('Y') ?></p>
    </div>
</footer>
<!-- SCRIPTS CONTENT -->
<?php $this->off(\yii\web\View::EVENT_END_BODY, [\yii\debug\Module::getInstance(), 'renderToolbar']); ?>

<?php $this->endBody() ?>
<!-- END OF SCRIPTS CONTENT -->
</body>
</html>
<?php $this->endPage() ?>
