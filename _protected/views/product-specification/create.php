<?php
/* @var $this yii\web\View */
/* @var $model app\models\ProductSpecification */

$this->title = Yii::t('app', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Product specification'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-specification-create">
    <?= $this->render('_form', compact('model','items','errorlist')) ?>
</div>