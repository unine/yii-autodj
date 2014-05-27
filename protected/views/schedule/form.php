<?php
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/datetimepicker/bootstrap-datetimepicker.min.js',CClientScript::POS_END); 
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/datetimepicker/bootstrap-datetimepicker.min.css');
Yii::app()->clientScript->registerScript('datetimepicker'," 
    $('.datetimepicker').datetimepicker({
        autoclose: true,
        minuteStep: 15
    });
"); 
Yii::app()->clientScript->registerScript('deleted',"

    $('#deleted').click(function(){
        var action = $('#shows-form').attr('action');
        if($(this).is(':checked'))
        {
            if(!confirm('After you hit the save button it will deleted this show!\\n\\nAre you sure you want to delete this show?'))
                return false;
            action = action.replace('update','delete');
        }
        else
        {
            action = action.replace('delete','update');
        }
        $('#shows-form').attr('action',action);
    });
"); 
?>
<div class="content-block">
    <h1><?php echo ($model->isNewRecord)?'Create Shedule':'Edit: '.$model->name?></h1>

    <?php $form=$this->beginWidget('CActiveForm', array( 
        'id'=>'shows-form', 
        // Please note: When you enable ajax validation, make sure the corresponding 
        // controller action is handling ajax validation correctly. 
        // There is a call to performAjaxValidation() commented in generated controller code. 
        // See class documentation of CActiveForm for details on this. 
        'enableAjaxValidation'=>false,
        'htmlOptions'=>array('class'=>'form-horizontal', 'role'=>'form'),
    )); ?>

        <p class="note">Fields with <span class="required">*</span> are required.</p> 

        <?php echo $form->errorSummary($model); ?>

        <div class="form-group"> 
            <?php echo $form->labelEx($model,'name',array('class'=>'col-sm-2 control-label')); ?>
            <div class="col-sm-10 col-md-5">
                <?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255,'class'=>'form-control')); ?>
                <?php echo $form->error($model,'name'); ?>
            </div>
        </div> 

        <div class="form-group"> 
            <?php echo $form->labelEx($model,'text_color',array('class'=>'col-sm-2 control-label')); ?>
            <div class="col-sm-10 col-md-5">
                <?php $this->widget('ext.SMiniColors.SActiveColorPicker', array( 
                    'model' => $model, 
                    'attribute' => 'text_color', 
                ));
                ?>
                <?php echo $form->error($model,'text_color'); ?>
            </div>
        </div> 

        <div class="form-group"> 
            <?php echo $form->labelEx($model,'bg_color',array('class'=>'col-sm-2 control-label')); ?>
            <div class="col-sm-10 col-md-5">
                <?php $this->widget('ext.SMiniColors.SActiveColorPicker', array( 
                    'model' => $model, 
                    'attribute' => 'bg_color', 
                ));
                ?>
                <?php echo $form->error($model,'bg_color'); ?>
            </div>
        </div> 

        <div class="form-group"> 
            <?php echo $form->labelEx($model,'start_date',array('class'=>'col-sm-2 control-label')); ?>
            <div class="col-sm-10 col-md-3">
                <div class="input-group date datetimepicker" data-date="<?php echo $model->start_date;?>" data-date-format="yyyy-mm-dd hh:ii:ss">
                    <?php echo $form->textField($model,'start_date',array('class'=>'form-control')); ?>
                    <span class="input-group-addon"> 
                        <i data-time-icon="icon-time" data-date-icon="icon-calendar" class="glyphicon glyphicon-calendar"></i> 
                    </span>
                </div>
                <?php echo $form->error($model,'start_date'); ?>
            </div>
        </div> 

        <div class="form-group"> 
            <?php echo $form->labelEx($model,'end_date',array('class'=>'col-sm-2 control-label')); ?>
            <div class="col-sm-10 col-md-3">
                <div class="input-group date datetimepicker" data-date="<?php echo $model->end_date;?>" data-date-format="yyyy-mm-dd hh:ii:ss">
                    <?php echo $form->textField($model,'end_date',array('class'=>'form-control')); ?>
                    <span class="input-group-addon"> 
                        <i data-time-icon="icon-time" data-date-icon="icon-calendar" class="glyphicon glyphicon-calendar"></i> 
                    </span>
                </div>
                <?php echo $form->error($model,'end_date'); ?>
            </div>
        </div> 

        <div class="form-group"> 
            <?php echo $form->labelEx($model,'repeat_frq',array('class'=>'col-sm-2 control-label')); ?>
            <div class="col-sm-10 col-md-5">
                <?php echo $form->checkBoxList($model,'repeat_frq',array('s'=>'Sun','m'=>'Mon','tu'=>'Tue','w'=>'Wed','th'=>'Thu','f'=>'Fri','sa'=>'Sat'),array('separator'=>'','container'=>'ul','template'=>'<li>{input} {label}</li>')); ?>
                <?php echo $form->error($model,'repeat_frq'); ?>
            </div>
        </div> 

        <div class="form-group"> 
            <?php echo $form->labelEx($model,'repeat_end',array('class'=>'col-sm-2 control-label')); ?>
            <div class="col-sm-10 col-md-3">
                <div class="input-group date datetimepicker" data-date="<?php echo ($model->repeat_end !== null)?$model->repeat_end:date('Y-m-d H:i:s');?>" data-date-format="yyyy-mm-dd hh:ii:ss">
                    <?php echo $form->textField($model,'repeat_end',array('class'=>'form-control')); ?>
                    <span class="input-group-addon">
                        <i data-time-icon="icon-time" data-date-icon="icon-calendar" class="glyphicon glyphicon-calendar"></i> 
                    </span>
                </div>
                <?php echo $form->error($model,'repeat_end'); ?>
            </div>
        </div>

        <div class="form-group"> 
            <div class="col-sm-offset-2 col-sm-10 col-md-5">
                <?php echo $form->checkBox($model,'loop'); ?>
                <?php echo $form->labelEx($model,'loop'); ?>
                <?php echo $form->error($model,'loop'); ?>
            </div>
        </div> 

        <div class="form-group"> 
            <?php echo $form->labelEx($model,'pid',array('class'=>'col-sm-2 control-label')); ?>
            <div class="col-sm-10 col-md-3">
                    <?php if($playlists):?>
                    <?php echo $form->dropDownList($model,'pid',$playlists,array('empty' => ' -=[ Select once or none ]=- ', 'class'=>'form-control')); ?>
                    <?php else:?>
                    <div class="alert in alert-block fade alert-info">
                        <strong>No playlist available.</strong> Please create once.
                    </div>
                    <?php endif;?>
                    <?php echo $form->error($model,'pid'); ?>
            </div>
        </div> 

        <?php if(!$model->isNewRecord):?>
        <div class="form-group"> 
            <div class="col-sm-offset-2 col-sm-10 col-md-5">
                <label>
                    <?php echo CHtml::checkBox('deleted'); ?>
                    Delete?
                </label>            
            </div>
        </div>
        <?php endif;?>

        <div class="form-group buttons">
            <div class="col-sm-offset-2 col-sm-10 col-md-5">
                <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn btn-primary')); ?>
            </div>
        </div> 

    <?php $this->endWidget(); ?>
</div>