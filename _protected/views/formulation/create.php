<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Formulation */

$this->title = Yii::t('app', 'Create Formulation');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Formulations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if(isset($_POST['errorlist'])) {
    $this->params['breadcrumbs'][] = $errorlist;
}
?>
<div class="formulation-create">

    <div class="col-md-12">
        <?= $this->render('_form', [
            'errorlist' => $errorlist ?? null,
            'model' => $model, 'list'=>$list,
            'titleList' => $titleList,
            'specificList' => $specificList
        ]) ?>
    </div>
</div>
