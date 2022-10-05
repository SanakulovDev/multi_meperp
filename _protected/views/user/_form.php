<?php
	use app\models\Report;
	use app\models\UserAccess;
	use app\models\Warehouse;
	use app\rbac\models\AuthItem;
	use yii\helpers\ArrayHelper;
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;

	$js = <<< JS
    $('#findUser').click(function(){
       url = $(this).attr('url');
       q = $('#searchText').val();
       
       $('#divAlert').hide();
       $('#user-username').val('');
       $('#user-fullname').val('');  
       $('#user-tabno').val('');
       $('#user-email').val('');
       $.ajax({
           url: 'get-info',
           dataType: "json",
           data: {
              q: q
           },
           success: function(data) {         
            if(data){
                $('#user-username').val(data.username);
                $('#user-fullname').val(data.fullname);  
                $('#user-tabno').val(data.employer_id);
                $('#user-email').val(data.mail);    
				$('#user-account_suffix').val(data.account_suffix);    
            }else{
                $('#divAlert').show();
            }
            
           },
           
        });
    });
JS;
	$this->registerJs($js);
	/* @var $this yii\web\View */
	/* @var $user app\models\User */
	/* @var $form yii\widgets\ActiveForm */
?>
<div class="col-lg-12">
	<? if($user->isNewRecord){ ?>
		<!--    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissible" role="alert" id="divAlert" style="display: none;">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <strong>Error !</strong> User not found.
            </div>

            <div class="input-group" style="margin-bottom: 10px;">
              <input type="text" class="form-control" placeholder="Пользователь ... " id="searchText">
              <span class="input-group-btn">
                <button class="btn btn-default" type="button" id="findUser" url="<? //=Yii::$app->params['urlOfControlPanel']?>">Find</button>
              </span>

            </div>
        </div>
    </div>-->
	<? } ?>
	<?php $form = ActiveForm::begin(['id' => 'form-user']); ?>

	<div class="row">
		<div class="col-lg-4">
			<div class="row">
				<div class="col-lg-12 col-md-12">
					<?=$form->field($user, 'username')->textInput(['maxlength' => true])?>
				</div>
				
				<div class="col-lg-12 col-md-12">
					<?=$form->field($user, 'fullname')->textInput(['maxlength' => true])?>
				</div>
				
				<div class="col-lg-12 col-md-12">
					<?=$form->field($user, 'account_suffix')->input('text', [])?>
				</div>
				<div class="col-lg-12 col-md-12">
					<?
						if($user->username != Yii::$app->user->identity->username)
							echo $form->field($user, 'status')->dropDownList($user->statusList)
					?>
				</div>
				<div class="col-lg-6 col-md-6">
					<?php foreach(AuthItem::getRoles() as $item_name): ?>
						<?php
              //if(Yii::$app->user->identity->roleName != 'superadmin' and $item_name->name == 'superadmin') continue;
              if(Yii::$app->user->identity->roleName != 'superadmin' and $item_name->name == 'superadmin') continue;
              $roles[$item_name->name] = $item_name->name;
						?>
					<?php endforeach ?>
					<?
						$select = 'operator';
						if($user->item_name)
							$select = $user->item_name;
						if($user->username != Yii::$app->user->identity->username)
							echo $form->field($user, 'item_name')
							          ->dropDownList($roles,
							                         [
								                         'options' =>
									                         [
										                         $select => ['selected' => true]
									                         ],
							                         ]
							          ) ?>
				</div>

				<div class="col-lg-6 col-md-6">
					<?
						echo $form->field($user, 'act_access')->dropDownList($user->actAccessList)
					?>
				</div>
        <div class="col-lg-12 col-md-12">
					<?=$form->field($user, 'tabno')->textInput(['maxlength' => true])?>
				</div>
        <div class="col-lg-12 col-md-12">
					<?=$form->field($user, 'email')->textInput(['maxlength' => true])?>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<?=$form->field($user, 'warehouse_ids')->dropDownList(
				ArrayHelper::map(Warehouse::find()->all(), 'id', 'name'),
				[
					'class' => 'form-control',
					'multiple' => true,
					'size' => 27,
				])?>
		</div>
		<div class="col-lg-4">
			<?=$form->field($user, 'report_ids')->dropDownList(
				ArrayHelper::map(Report::find()->all(), 'id', 'description'),
				[
					'class' => 'form-control',
					'multiple' => true,
					'size' => 27,
				])?>
		</div>
	</div>

	<div class="form-group">
		<?=Html::submitButton(Yii::t('app', 'btn-save'), ['class' => 'btn btn-primary btn-sm'])?>
		<?=Html::a(Yii::t('app', 'btn-cancel'), ['user/index'], ['class' => 'btn btn-default  btn-sm'])?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
