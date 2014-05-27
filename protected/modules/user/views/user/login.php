<?php 
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl.'/css/signin.css');
$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Login");
$this->breadcrumbs=array(
	UserModule::t("Login"),
);
?>

<div class="container">
<?php if(Yii::app()->user->hasFlash('loginMessage')): ?>
<div class="success">
	<?php echo Yii::app()->user->getFlash('loginMessage'); ?>
</div>
<?php endif; ?>

<?php echo CHtml::beginForm(array('/user/login'), 'post',array('class'=>'form-signin', 'role'=>'form')); ?>
	
	<h2 class="form-signin-heading">Welcome Back!</h2>
	<?php echo CHtml::errorSummary($model,'','',array('firstError'=>true, 'class'=>'alert alert-danger')); ?>
	
	<?php echo CHtml::activeTextField($model,'username',array('class'=>'form-control','placeholder'=>'Username','autofocus'=>true,'required'=>true)); ?>

	<?php echo CHtml::activePasswordField($model,'password',array('class'=>'form-control','placeholder'=>'Password','required'=>true)); ?>

	<?php echo CHtml::submitButton(UserModule::t('Log in'),array('class'=>'btn btn-lg btn-primary btn-block')); ?>

    <label class="checkbox">
        <?php echo CHtml::activeCheckBox($model,'rememberMe'); ?> Remember me
    </label>

<?php echo CHtml::endForm(); ?>
</div> <!-- /container -->

<?php
$form = new CForm(array(
    'elements'=>array(
        'username'=>array(
            'type'=>'text',
            'maxlength'=>32,
        ),
        'password'=>array(
            'type'=>'password',
            'maxlength'=>32,
        ),
        'rememberMe'=>array(
            'type'=>'checkbox',
        )
    ),

    'buttons'=>array(
        'login'=>array(
            'type'=>'submit',
            'label'=>'Login',
        ),
    ),
), $model);
?>