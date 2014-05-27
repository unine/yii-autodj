<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/font-awesome.min.css');

Yii::app()->clientScript->registerScript('stream_toggle',"
    $('.toggle legend').click(function(){
        var thisfield = $(this).parent();
        if(thisfield.hasClass('closed'))
            thisfield.removeClass('closed');
        else
            thisfield.addClass('closed');
    });
");

Yii::app()->clientScript->registerScript('input_type',"
    $('.input_type').change(function(){
        var input = $(this).val();
        if(input == 'SHOUTcast')
            $('.input_user,.input_mount').slideUp();
        else
            $('.input_user,.input_mount').slideDown();
    });
");

Yii::app()->clientScript->registerScript('protocol_change',"
    $('.protocol').change(function(){
        var proID = $(this).attr('id');
        var pro = $(this).val();
        if(pro == 'SHOUTcast')
            $('.'+proID).slideUp();
        else
            $('.'+proID).slideDown();
    });
");

Yii::app()->clientScript->registerScript('mode_select',"
    if($('.mode').val() === 'Playlist')
        $('.playlist-select').show();

    $('.mode').change(function() {
        var mode = this.value;
        if(mode == 'Playlist')
            $('.playlist-select').slideDown();
        else
            $('.playlist-select').slideUp();
    });
");

Yii::app()->clientScript->registerScript('timezone',"
    $('.zone-panel li a').on('click',function(){
        var timetype = $(this).text();
        r=confirm('Your timezone is: '+timetype+' to be your timezone?');
        if (r==true)
        {
            $('#Setting_time_zone').val(timetype);
            $('.time-toggle').text(timetype);
            $('#timezoneSet').modal('hide')
        }
    });
");

Yii::app()->clientScript->registerScript('collapse',"
    $('#streams').collapse({
        toggle: false
    })
");
?>
<div class="row">
    <div class="col-sm-12 col-md-8">
        <div class="content-block">
        <?php $form=$this->beginWidget('CActiveForm', array( 
            'id'=>'setting-form', 
            'enableAjaxValidation'=>false,
            'htmlOptions'=>array('class'=>'form-horizontal', 'role'=>'form'),
        )); ?>
        <div class="clearfix">
            <h2 class="pull-left">Settings</h2>
            <?php echo CHtml::link('Reload Config',array('preferences/reload'),array('class'=>'btn btn-warning pull-right')); ?>
            <?php echo CHtml::submitButton('Save',array('class'=>'btn btn-primary pull-right','style'=>'margin-right:5px;')); ?>
        </div>
            <?php if(Yii::app()->user->hasFlash('success')):?>
                <div class="alert in alert-block fade alert-success">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo Yii::app()->user->getFlash('success'); ?>
                </div>
            <?php endif; ?>
            <div class="row">
                <div class="col col-sm-6 left">
                    <fieldset class="padded setting">
                        <legend>Station Setting</legend>                        
                            <?php echo $form->errorSummary($model); ?>
                            <div class="form-group"> 
                                <?php echo $form->labelEx($model,'mode',array('class'=>'col-sm-3 control-label')); ?>
                                <div class="col-sm-9">
                                    <?php echo $form->dropDownList($model,'mode',array('Random'=>'Random','Playlist'=>'Playlist','Schedule'=>'Schedule'),array('class'=>'form-control mode')); ?>
                                    <?php echo $form->error($model,'mode'); ?>
                                </div>
                            </div>

                            <div class="form-group playlist-select"> 
                                <?php echo $form->labelEx($model,'pid',array('class'=>'col-sm-3 control-label')); ?>
                                <div class="col-sm-9">
                                    <?php if($playlists):?>
                                    <?php echo $form->dropDownList($model,'pid',$playlists,array('empty' => ' [ Select Playlist ] ', 'class'=>'form-control','hint'=>'<small>Choose one or leave it blank for use random fallback</small>')); ?>
                                    <?php else:?>
                                    <div class="alert in alert-block fade alert-info">
                                        <strong>No playlist available.</strong> Please create once.
                                    </div>
                                    <?php endif;?>
                                    <?php echo $form->error($model,'pid'); ?>
                                </div>
                            </div> 

                            <div class="form-group">
                                <?php echo $form->labelEx($model,'time_zone',array('class'=>'col-sm-3 control-label')); ?>
                                <div class="col-sm-9">
                                    <?php echo $form->hiddenField($model,'time_zone'); ?>
                                    <div class="modal fade" id="timezoneSet" tabindex="-1" role="dialog" aria-labelledby="timeZoneLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title" id="myModalLabel">Time Zone</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <ul class="nav nav-tabs">
                                                    <?php
                                                    foreach ($timeSet as $id => $zone) {
                                                        $active = '';
                                                        if($id === 0)
                                                            $active = ' class="active"';
                                                        echo "<li $active><a href='#zone_$id' data-toggle='tab'>$zone</a></li>";
                                                    }
                                                    ?>
                                                    </ul>

                                                    <div class="tab-content">
                                                    <?php
                                                    foreach ($timeSet as $id => $zone) {
                                                        $active = '';
                                                        if($id === 0)
                                                            $active = ' active';
                                                        echo '<div class="tab-pane'.$active.'" id="zone_'.$id.'">'.$this->renderPartial('_zone', array(
                                                                        'time_zone'=>$zone,
                                                                        'id'=>'zone_'.$id,
                                                                ),TRUE).'</div>';
                                                    }
                                                    ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="form-control time-toggle" data-toggle="modal" data-target="#timezoneSet">
                                        <?php echo ($model->time_zone)?$model->time_zone:'Select your time zone';?>
                                    </a>
                                    <?php echo $form->error($model,'time_zone'); ?>
                                </div>
                            </div>

                            <div class="form-group"> 
                                <?php echo $form->labelEx($model,'song_repeat',array('class'=>'col-sm-3 control-label')); ?>
                                <div class="col-sm-9">
                                    <?php echo $form->textField($model,'song_repeat',array('class'=>'form-control')); ?>
                                    <?php echo $form->error($model,'song_repeat'); ?>
                                </div>
                            </div> 

                            <!-- <div class="form-group"> 
                                <?php // $form->labelEx($model,'artist_repeat',array('class'=>'col-sm-3 control-label')); ?>
                                <div class="col-sm-9">
                                    <?php //echo $form->textField($model,'artist_repeat',array('class'=>'form-control')); ?>
                                    <?php //echo $form->error($model,'artist_repeat'); ?>
                                </div>
                            </div> 

                            <div class="form-group"> 
                                <?php //echo $form->labelEx($model,'album_repeat',array('class'=>'col-sm-3 control-label')); ?>
                                <div class="col-sm-9">
                                    <?php //echo $form->textField($model,'album_repeat',array('class'=>'form-control')); ?>
                                    <?php //echo $form->error($model,'album_repeat'); ?>
                                </div>
                            </div> -->
                    </fieldset>
                    
                    <fieldset class="padded setting">
                        <legend>Input Stream Settings</legend>
                            <div class="form-group"> 
                                <?php echo $form->labelEx($model,'input_type',array('class'=>'col-sm-3 control-label')); ?>
                                <div class="col-sm-9">
                                    <?php echo $form->dropDownList($model,'input_type',array('Icecast'=>'Icecast','SHOUTcast'=>'SHOUTcast'),array('class'=>'form-control input_type')); ?>
                                    <?php echo $form->error($model,'input_type'); ?>
                                </div>
                            </div> 

                            <div class="form-group"> 
                                <?php echo $form->labelEx($model,'input_host',array('class'=>'col-sm-3 control-label')); ?>
                                <div class="col-sm-9">
                                    <?php echo $form->textField($model,'input_host',array('class'=>'form-control')); ?>
                                    <?php echo $form->error($model,'input_host'); ?>
                                </div>
                            </div> 

                            <div class="form-group"> 
                                <?php echo $form->labelEx($model,'input_port',array('class'=>'col-sm-3 control-label')); ?>
                                <div class="col-sm-9">
                                    <?php echo $form->textField($model,'input_port',array('class'=>'form-control')); ?>
                                    <?php echo $form->error($model,'input_port'); ?>
                                </div>
                            </div> 

                            <div class="form-group input_user" style="overflow:hidden;<?php echo ($model->input_type === 'SHOUTcast')?'display:none;':'';?>"> 
                                <?php echo $form->labelEx($model,'input_user',array('class'=>'col-sm-3 control-label')); ?>
                                <div class="col-sm-9">
                                    <?php echo $form->textField($model,'input_user',array('class'=>'form-control')); ?>
                                    <?php echo $form->error($model,'input_user'); ?>
                                </div>
                            </div> 

                            <div class="form-group"> 
                                <?php echo $form->labelEx($model,'input_pass',array('class'=>'col-sm-3 control-label')); ?>
                                <div class="col-sm-9">
                                    <?php echo $form->textField($model,'input_pass',array('class'=>'form-control')); ?>
                                    <?php echo $form->error($model,'input_pass'); ?>
                                </div>
                            </div> 

                            <div class="form-group input_mount" style="overflow:hidden;<?php echo ($model->input_type === 'SHOUTcast')?'display:none;':'';?>"> 
                                <?php echo $form->labelEx($model,'input_mount',array('class'=>'col-sm-3 control-label')); ?>
                                <div class="col-sm-9">
                                    <?php echo $form->textField($model,'input_mount',array('class'=>'form-control')); ?>
                                    <?php echo $form->error($model,'input_mount'); ?>
                                </div>
                            </div> 
                    </fieldset>
                </div>

                <!-- Output Config -->
                <div class="col col-sm-6 right">
                    <fieldset class="padded setting">
                        <legend>Output Stream Settings</legend>
                        <div class="panel-group" id="streams">
                            <?php foreach ($streams as $id => $stream):?>
                            <div class="panel">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#streams" href="#collapse_<?php echo $id;?>" class="<?php echo ($id != 0)?'collapsed':'';?>">
                                            Stream <?php echo $id+1;?>
                                        </a>
                                    </h4>
                                    <?php echo $form->hiddenField($stream,'['.$id.']id'); ?>
                                </div>
                                <div id="collapse_<?php echo $id;?>" class="panel-collapse collapse<?php echo ($id == 0)?' in':'';?>">
                                    <div class="panel-body">
                                        <div class="form-group"> 
                                            <?php echo $form->labelEx($stream,'['.$id.']active',array('class'=>'col-sm-3 control-label')); ?>
                                            <div class="col-sm-9">
                                                <?php echo $form->checkBox($stream,'['.$id.']active'); ?>
                                                <?php echo $form->error($stream,'['.$id.']active'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group"> 
                                            <?php echo $form->labelEx($stream,'['.$id.']protocol',array('class'=>'col-sm-3 control-label')); ?>
                                            <div class="col-sm-9">
                                                <?php echo $form->dropDownList($stream,'['.$id.']protocol',array('Icecast'=>'Icecast','SHOUTcast'=>'SHOUTcast'),array('class'=>'form-control protocol')); ?>
                                                <?php echo $form->error($stream,'['.$id.']protocol'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group"> 
                                            <?php echo $form->labelEx($stream,'['.$id.']type',array('class'=>'col-sm-3 control-label')); ?>
                                            <div class="col-sm-9">
                                                <?php echo $form->dropDownList($stream,'['.$id.']type',array('aac'=>'AAC','mp3'=>'MP3'),array('class'=>'form-control')); ?>
                                                <?php echo $form->error($stream,'['.$id.']type'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group"> 
                                            <?php echo $form->labelEx($stream,'['.$id.']bitrate',array('class'=>'col-sm-3 control-label')); ?>
                                            <div class="col-sm-9">
                                                <?php echo $form->dropDownList($stream,'['.$id.']bitrate',array(
                                                '24'=>'24 kbit/s',
                                                '32'=>'32 kbit/s',
                                                '48'=>'48 kbit/s',
                                                '64'=>'64 kbit/s',
                                                '96'=>'96 kbit/s',
                                                '128'=>'128 kbit/s',
                                                '160'=>'160 kbit/s',
                                                '192'=>'192 kbit/s',
                                                '224'=>'224 kbit/s',
                                                '256'=>'256 kbit/s',
                                                '320'=>'320 kbit/s'),array('class'=>'form-control')); ?>
                                                <?php echo $form->error($stream,'['.$id.']bitrate'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group"> 
                                            <?php echo $form->labelEx($stream,'['.$id.']host',array('class'=>'col-sm-3 control-label')); ?>
                                            <div class="col-sm-9">
                                                <?php echo $form->textField($stream,'['.$id.']host',array('class'=>'form-control')); ?>
                                                <?php echo $form->error($stream,'['.$id.']host'); ?>
                                            </div>
                                        </div>
                                        <div class="form-group"> 
                                            <?php echo $form->labelEx($stream,'['.$id.']port',array('class'=>'col-sm-3 control-label')); ?>
                                            <div class="col-sm-9">
                                                <?php echo $form->textField($stream,'['.$id.']port',array('class'=>'form-control')); ?>
                                                <?php echo $form->error($stream,'['.$id.']port'); ?>
                                            </div>
                                        </div>
                                        <fieldset class="padded setting toggle">
                                            <legend><i class="fa fa-sort"></i> Authentication Info.</legend>
                                            <div class="zone-toggle">
                                                <div class="form-group <?php echo 'Stream_'.$id.'_protocol';?>" style="overflow:hidden;<?php echo ($stream->protocol === 'SHOUTcast')?'display:none;':'';?>"> 
                                                    <?php echo $form->labelEx($stream,'['.$id.']username',array('class'=>'col-sm-3 control-label')); ?>
                                                    <div class="col-sm-9">
                                                        <?php echo $form->textField($stream,'['.$id.']username',array('class'=>'form-control')); ?>
                                                        <?php echo $form->error($stream,'['.$id.']username'); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group"> 
                                                    <?php echo $form->labelEx($stream,'['.$id.']password',array('class'=>'col-sm-3 control-label')); ?>
                                                    <div class="col-sm-9">
                                                        <?php echo $form->textField($stream,'['.$id.']password',array('class'=>'form-control')); ?>
                                                        <?php echo $form->error($stream,'['.$id.']password'); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group"> 
                                                    <?php echo $form->labelEx($stream,'['.$id.']admin_user',array('class'=>'col-sm-3 control-label')); ?>
                                                    <div class="col-sm-9">
                                                        <?php echo $form->textField($stream,'['.$id.']admin_user',array('class'=>'form-control')); ?>
                                                        <?php echo $form->error($stream,'['.$id.']admin_user'); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group"> 
                                                    <?php echo $form->labelEx($stream,'['.$id.']admin_pass',array('class'=>'col-sm-3 control-label')); ?>
                                                    <div class="col-sm-9">
                                                        <?php echo $form->textField($stream,'['.$id.']admin_pass',array('class'=>'form-control')); ?>
                                                        <?php echo $form->error($stream,'['.$id.']admin_pass'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset class="padded setting toggle closed">
                                            <legend><i class="fa fa-sort"></i> Additional Info.</legend>
                                            <div class="zone-toggle">
                                                <div class="form-group"> 
                                                    <?php echo $form->labelEx($stream,'['.$id.']mount',array('class'=>'col-sm-3 control-label')); ?>
                                                    <div class="col-sm-9">
                                                        <?php echo $form->textField($stream,'['.$id.']mount',array('class'=>'form-control')); ?>
                                                        <?php echo $form->error($stream,'['.$id.']mount'); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group"> 
                                                    <?php echo $form->labelEx($stream,'['.$id.']name',array('class'=>'col-sm-3 control-label')); ?>
                                                    <div class="col-sm-9">
                                                        <?php echo $form->textField($stream,'['.$id.']name',array('class'=>'form-control')); ?>
                                                        <?php echo $form->error($stream,'['.$id.']name'); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group"> 
                                                    <?php echo $form->labelEx($stream,'['.$id.']description',array('class'=>'col-sm-3 control-label')); ?>
                                                    <div class="col-sm-9">
                                                        <?php echo $form->textField($stream,'['.$id.']description',array('class'=>'form-control')); ?>
                                                        <?php echo $form->error($stream,'['.$id.']description'); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group"> 
                                                    <?php echo $form->labelEx($stream,'['.$id.']url',array('class'=>'col-sm-3 control-label')); ?>
                                                    <div class="col-sm-9">
                                                        <?php echo $form->textField($stream,'['.$id.']url',array('class'=>'form-control')); ?>
                                                        <?php echo $form->error($stream,'['.$id.']url'); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach;?>
                        </div>
                    </fieldset>
                </div>

            </div> <!-- row -->
        <?php $this->endWidget(); ?>
        </div> <!-- content-block -->
    </div>
</div>
