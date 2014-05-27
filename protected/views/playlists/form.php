<?php
Yii::app()->clientScript->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/font-awesome.min.css');
Yii::app()->clientScript->registerScript('cue_song',"
$(document).on('click','.items a.btn-cue',function(){
    javascript:void
    window.open($(this).attr('href'),
    '_blank', 'width=520, height=458, toolbar=0, menubar=0, location=0, status=0, scrollbars=0, resizable=1, left=0, top=0');
    return false;});
");
Yii::app()->clientScript->registerScript('manage_items',"
$('body').on('click','.add-items',function(){
    // $('.empty-list').fadeOut().remove();
    var valueTYPE = $(this).attr('data-type');
    var valueID = $(this).attr('data-id');
    var valueNAME = $(this).attr('data-title');
    var color = $(this).attr('data-color');
    var text_color = $(this).attr('data-text');
    var style = '';

    if(valueTYPE == 'tags')
    {
        valueNAME = $(this).text();
        style = 'color:'+text_color+';background-color:'+color+';border-left:none;border-right:none;';
    }

    var itemID = valueTYPE+'-'+valueID;
    var itemType = valueTYPE+'-type';
    var input = '<input type=\"hidden\" name=\"itemlist[]\" value='+itemID+' />';

    var itemOutput = '<div class=\"item-list clearfix\" style=\"display:none;'+style+'\"><div class=\"inner-item '+itemType+'\"><span class=\"item-name\">'+valueNAME+'</span> <a class=\"remove-item pull-right\" href=\"#remove\" title=\"remove\"><i class=\"fa fa-ban\"></i></a>'+input+'</div></div>';

    $('#itemlist').append(itemOutput);
    if($('.head + div').hasClass('empty-list'))
    {
        $('.empty-list').slideUp('fast',function(){
            $(this).remove();
            $('#itemlist').find('.item-list:last').slideDown('fast');
        });
    }
    else
    {
        $('#itemlist').find('.item-list:last').slideDown('fast');
    }
})
");
Yii::app()->clientScript->registerScript('sort_items',"
$('#itemlist').sortable({cursor: 'move',placeholder: 'item-highlight'});
");
Yii::app()->clientScript->registerScript('remove_items',"
$('body').on('click','.item-list a.remove-item',function(){
    var thisparent = $(this).parent();
        $(thisparent).parent().slideUp(200,function(){
                $(this).remove();  
        });
});
");
Yii::app()->clientScript->registerScript('search',"
var ajaxUpdateTimeout;
var ajaxRequest;
$('input#string').keyup(function(){
    ajaxRequest = $(this).serialize();
    clearTimeout(ajaxUpdateTimeout);
    ajaxUpdateTimeout = setTimeout(function () {
        $.fn.yiiGridView.update('tracksList', {data: ajaxRequest})
    },
    // this is the delay
    300);
});"
);
?>
<div class="row">
    <div class="col-sm-8">
        <div class="content-block left">
            <div class="tags_box">
                <h3>Tag List</h3>
                <?php foreach($tags as $item):?>
                <?php echo CHtml::link($item->name,'#addTags',array('class'=>'btn btn-xs add-items','data-color'=>$item->bg_color,'data-text'=>$item->text_color,'data-type'=>'tags','data-id'=>$item->id,'style'=>'background-color:'.$item->bg_color.';color:'.$item->text_color));?>
                <?php endforeach;?>
                <hr />
            </div>
            <div class="song_box">
                <?php
                if($model->isNewRecord)
                {
                    $url='playlists/create';
                }else{
                    $url='playlists/update';
                }
                echo CHtml::beginForm(CHtml::normalizeUrl(array($url)), 'get', array('id'=>'filter-form','class'=>'col-sm-4 pull-right'))
                   . '<div class="input-group"><span class="input-group-addon"><i class="fa fa-search"></i></span>'
                   . CHtml::textField('string',(isset($_GET['string']))?$_GET['string']:'', array('id'=>'string', 'placeholder'=>'title, artist, album, tag', 'class'=>'form-control'))
                   . '</div>'
                   . CHtml::endForm();
                ?>
                <h3>File List</h3>
                <?php $this->widget('zii.widgets.grid.CGridView',array(
                    'dataProvider'=>$files,
                    'id'=>'tracksList',
                    'template'=>'{summary}{items}{summary}{pager}',
                    'itemsCssClass'=>'table table-condensed table-hover table-condensed table-bordered table-striped',
                    'htmlOptions'=>array('class'=>'table-responsive'),
                    'pager'=>array(
                        'header'=>'',
                        'selectedPageCssClass'=>'active',
                        'htmlOptions'=>array('class'=>'pagination')
                    ),
                    'pagerCssClass'=>'customPager',
                    'columns'=>array(
                        'track_title',
                        'artist_name',
                        'album_title',
                        array(
                            'name'=>'duration',
                            'value'=>'gmdate("i:s",$data->duration)'
                        ),
                        array(
                            'header'=>'Tags',
                            'type'=>'raw',
                            'value'=>array($this,'gridTags'),
                            'headerHtmlOptions'=>array('class'=>'headtags'),
                        ),
                        array(
                            'class'=>'ButtonColumn',
                            'template'=>'{addQueue}',
                            'evaluateID'=>true,
                            'buttons'=>array(
                                'addQueue'=>array(
                                    'label'=>'<i class="fa fa-arrow-right"></i>',
                                    'url'=>'"#add"',
                                    'options'=>array(
                                        'title'=>'add to playlist', 
                                        'class'=>'add-items', 
                                        'data-type'=>'song',
                                        'data-id'=>'php->$data->id',
                                        'data-title'=>'php->$data->track_title.\' - \'.$data->artist_name'
                                    )
                                )
                            )
                        ),
                    )
                )); ?>
            </div>
        </div>
    </div>
    <div class="col-sm-4 playlist-wrap">
        <div class="content-block right">
            <?php $form=$this->beginWidget('CActiveForm',array( 
                'id'=>'playlists-form', 
                'enableAjaxValidation'=>false,
                'htmlOptions'=>array('class'=>'form-inline', 'role'=>'form'),
            )); ?>

                <?php echo $form->error($model,'name',array('style'=>'padding-left:54px;')); ?>

                <div class="form-group">
                    <?php echo $form->labelEx($model,'name'); ?>
                    <?php echo $form->textField($model,'name',array('class'=>'form-control','maxlength'=>128)); ?>
                </div>
                <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn btn-primary')); ?>

                <div class="item-wrapper">
                    <div class="head">Playlist Items</div>
                    <?php if(empty($itemlist)):?>
                    <div class="empty-list">No items in playlist.</div>
                    <?php endif;?>
                    <div id="itemlist">
                    <?php
                    if(!empty($itemlist))
                    {
                        foreach($itemlist as $listitem)
                        {
                            $itemOutput = '<div class="item-list item-old clearfix" '.$listitem['style'].'>';
                            $itemOutput.= '<div class="inner-item '.$listitem['type'].'">';
                            $itemOutput.= '<span class="item-name">'.$listitem['name'].'</span>';
                            $itemOutput.= '<a class="remove-item pull-right" title="remove" href="#remove"><i class="fa fa-ban"></i></a>';
                            $itemOutput.= '<input type="hidden" name="itemlist[]" value="'.$listitem['itemId'].'" />';
                            $itemOutput.= '</div></div>';
                            echo $itemOutput;
                        }
                    }
                    ?>
                    </div>
                </div>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>