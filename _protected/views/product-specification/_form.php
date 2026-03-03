<?php

use app\models\Part;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProductSpecification */
/* @var $form yii\widgets\ActiveForm */
$notRawParts = ArrayHelper::map(Part::find()->where(['state' => [Part::STATE_SEMI, Part::STATE_FINISHED]])->all(), 'id', 'partinfo');
?>

<div class="product-specification-form">
    <?php 
        $action = null;
        if(Yii::$app->controller->action->id === 'duplicate') {
            $form = ActiveForm::begin(['action'=>'/product-specification/create']); 
        } else $form = ActiveForm::begin(); 
    ?>
    
    <div class="row">
        <div class="col-sm-4 col-md-4 col-lg-4">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4 col-md-4 col-lg-4">
            <?= $form->field($model, 'part_id')->dropDownList($notRawParts,['class'=>'form-control select2']) ?>
        </div>
        <div class="col-sm-4 col-md-4 col-lg-4">
            <?= $form->field($model, 'amount')->textInput(['type'=>'number', 'step'=>'any']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8 col-md-8 col-lg-8">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4 col-md-4 col-lg-4">
            <?= $form->field($model, 'status')->dropDownList($model->statusList()) ?>
        </div>
    </div>

    <?= $this->render('_details', compact('model','items','errorlist')) ?>

    <div class="form-group pull-right">
        <?=Html::a(Yii::t('app', 'btn-cancel'), ['index'], ['class' => 'btn btn-default btn-sm'])?>
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-sm']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
