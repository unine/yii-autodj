<?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/signin.css');?>

<div class="container">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
	'htmlOptions'=>array('class'=>'form-signin', 'role'=>'form')
)); ?>
	<h2 class="form-signin-heading">Please sign in</h2>

		<?php echo $form->textField($model,'username',array('class'=>'form-control','placeholder'=>'Username','autofocus'=>true,'required'=>true)); ?>

		<?php echo $form->passwordField($model,'password',array('class'=>'form-control','placeholder'=>'Password','required'=>true)); ?>

		<label class="checkbox">
			<?php echo $form->checkBox($model,'rememberMe'); ?> Remember me
		</label>

		<?php echo CHtml::submitButton('Sign in',array('class'=>'btn btn-lg btn-primary btn-block')); ?>

<?php $this->endWidget(); ?>

</div> <!-- /container -->