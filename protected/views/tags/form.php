<div class="row">
    <div class="col-sm-4">
		<div class="content-block">
			<h1><?php echo ($model->isNewRecord)?'Create Tags':'Update Tags: #'.$model->id?></h1>
			<?php $form=$this->beginWidget('CActiveForm', array(
				'id'=>'tags-form',
				// Please note: When you enable ajax validation, make sure the corresponding
				// controller action is handling ajax validation correctly.
				// There is a call to performAjaxValidation() commented in generated controller code.
				// See class documentation of CActiveForm for details on this.
				'enableAjaxValidation'=>false,
				'htmlOptions'=>array('class'=>'form-horizontal', 'role'=>'form'),
			)); ?>

				<p class="note col-sm-offset-3 col-sm-9">Fields with <span class="required">*</span> are required.</p>

				<?php echo $form->errorSummary($model); ?>

				<div class="form-group">
					<?php echo $form->labelEx($model,'name',array('class'=>'col-sm-3 control-label')); ?>
					<div class="col-sm-8">
						<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255,'class'=>'form-control')); ?>
						<?php echo $form->error($model,'name'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $form->labelEx($model,'text_color',array('class'=>'col-sm-3 control-label')); ?>
					<div class="col-sm-8">
						<? $this->widget('ext.SMiniColors.SActiveColorPicker', array( 
				            'model' => $model, 
				            'attribute' => 'text_color', 
				        ));
				        ?>
						<?php echo $form->error($model,'text_color'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $form->labelEx($model,'bg_color',array('class'=>'col-sm-3 control-label')); ?>
					<div class="col-sm-8">
						<? $this->widget('ext.SMiniColors.SActiveColorPicker', array( 
				            'model' => $model, 
				            'attribute' => 'bg_color', 
				        ));
				        ?>
						<?php echo $form->error($model,'bg_color'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $form->labelEx($model,'type',array('class'=>'col-sm-3 control-label')); ?>
					<div class="col-sm-4 col-md-3">
						<?php echo $form->dropDownList($model,'type',array('Music'=>'Music','None Music'=>'None Music'),array('class'=>'form-control')); ?>
						<?php echo $form->error($model,'type'); ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9">
						<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn btn-primary')); ?>
					</div>
				</div>

			<?php $this->endWidget(); ?>
		</div>
	</div>
	<div class="col-sm-4"></div>
	<div class="col-sm-4"></div>
</div>