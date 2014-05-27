<div class="row">
    <div class="col-sm-4">
        <div class="content-block">
            <h1><?php echo 'Update File: #'.$model->id?></h1>
            <?php $form=$this->beginWidget('CActiveForm', array(
                'id'=>'file-form',
                'enableAjaxValidation'=>false,
                'htmlOptions'=>array('class'=>'form-horizontal', 'role'=>'form'),
            )); ?>

                <?php if(Yii::app()->user->hasFlash('error')):?>
                    <div class="alert in alert-block fade alert-danger">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo Yii::app()->user->getFlash('error'); ?>
                    </div>
                <?php endif; ?>

                <p class="note col-sm-offset-3 col-sm-9">Fields with <span class="required">*</span> are required.</p>

                <?php echo $form->errorSummary($model); ?>

                <div class="form-group">
                    <?php echo $form->labelEx($model,'track_title',array('class'=>'col-sm-3 control-label')); ?>
                    <div class="col-sm-8">
                        <?php echo $form->textField($model,'track_title',array('size'=>60,'maxlength'=>255,'class'=>'form-control')); ?>
                        <?php echo $form->error($model,'track_title'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <?php echo $form->labelEx($model,'artist_name',array('class'=>'col-sm-3 control-label')); ?>
                    <div class="col-sm-8">
                        <?php echo $form->textField($model,'artist_name',array('size'=>60,'maxlength'=>255,'class'=>'form-control')); ?>
                        <?php echo $form->error($model,'artist_name'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <?php echo $form->labelEx($model,'album_title',array('class'=>'col-sm-3 control-label')); ?>
                    <div class="col-sm-8">
                        <?php echo $form->textField($model,'album_title',array('size'=>60,'maxlength'=>255,'class'=>'form-control')); ?>
                        <?php echo $form->error($model,'album_title'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Tags</label>
                    <div class="col-sm-8">
                        <?php echo CHtml::checkBoxList('tags_list',$selected,$tagslist,array('separator'=>'','container'=>'ul','template'=>'<li>{input} {label}</li>'));?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <?php echo CHtml::submitButton('Save',array('class'=>'btn btn-primary')); ?>
                    </div>
                </div>

            <?php $this->endWidget(); ?>
        </div>
    </div>
    <div class="col-sm-4">
        <?php $this->widget('zii.widgets.CDetailView', array( 
            'data'=>$model,
            'attributes'=>array(
                'filepath',
                array(
                    'name'=>'duration',
                    'value'=>gmdate("i:s", $model->duration)
                ),
                'count_played',
                array(
                    'name'=>'in_queued',
                    'value'=>($model->in_queued == 0)?'No':'Yes'
                ),
                'last_played',
            ), 
        )); ?>
    </div>
    <div class="col-sm-4"></div>
</div>