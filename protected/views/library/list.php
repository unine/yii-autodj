<?php
$pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['defaultPageSize']);
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/font-awesome.min.css');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/toastmessage/jquery.toastmessage.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/js/toastmessage/css/jquery.toastmessage.css');

Yii::app()->clientScript->registerScript('row_select',"
    $('body').on('click','.table>tbody>tr>td',function(){
        if(!$(this).is('[class]'))
        {
            var chck = $(this).parent().find('.checkbox-column input');
            if(chck.prop('checked'))
                chck.prop('checked', false);
            else
                chck.prop('checked', true);

        }
    });
");

Yii::app()->clientScript->registerScript('reloadGrid',"
function reloadGrid(data) {
    $.fn.yiiGridView.update('files-grid');
}
");
Yii::app()->clientScript->registerScript('removeTag',"
$('body').on('click','.tag-track',function(){
    if(!confirm('Are you sure you want to remove this tag?')) return false;

    var trackid = [$(this).attr('data-track')];
    var tagid = $(this).attr('data-tag');

    jQuery.ajax({
        'success':reloadGrid,
        'type':'POST',
        'url':'".$this->createUrl('library/removetag')."',
        'cache':false,
        'data':{'files_select':trackid,'tag_select':tagid},
    });
    return false;
})
");
?>
<div class="content-block">
    <?php 
    $form=$this->beginWidget('CActiveForm', array(
            'enableAjaxValidation'=>false,
    ));

    $this->widget('zii.widgets.grid.CGridView', array( 
        'id'=>'files-grid', 
        'dataProvider'=>$model->search(), 
        'filter'=>$model,
        'template'=>'<div class="panel-header"><div class="row"><div class="col-sm-6"><h3>Library</h3> {summary}</div><div class="col-sm-6"><a class="btn btn-custom btn-custom-l pull-right" href="#update" id="yt0"><i class="fa fa-database"></i> Update Library</a></div></div></div>{items}{summary}{pager}',
        'itemsCssClass'=>'table form table-condensed table-hover table-condensed table-bordered table-striped',
        'htmlOptions'=>array('class'=>'table-responsive'),
        'pager'=>array(
            'header'=>'',
            'selectedPageCssClass'=>'active',
            'htmlOptions'=>array('class'=>'pagination')
        ),
        'pagerCssClass'=>'customPager',
        'columns'=>array(
            array(
                'id'=>'files_select',
                'class'=>'CCheckBoxColumn',
                'selectableRows'=>100,
            ),
            'filename',
            'track_title',
            'artist_name',
            'album_title',
            array(
                'name'=>'duration',
                'value'=>'gmdate("i:s", $data->duration)',
                'filter'=>false,
                'headerHtmlOptions'=>array('class'=>'length_sec')
            ),
            array(
                'header'=>'Tags',
                'name'=>'filter_tags',
                'type'=>'raw',
                'value'=>array($this,'gridTags'),
                'headerHtmlOptions'=>array('class'=>'headtags'),
            ),
            array( 
                'class'=>'CButtonColumn',
                'template'=>'{update}',
                'header'=>CHtml::dropDownList('pageSize',
                        $pageSize,
                        array(20=>20,50=>50,100=>100,200=>200,500=>500),
                        array(
                            'onchange'=>"$.fn.yiiGridView.update('files-grid',{ data:{pageSize: $(this).val() }})",
                        )
                ),
                'footer'=>CHtml::dropDownList('pageSize',
                        $pageSize,
                        array(20=>20,50=>50,100=>100,200=>200,500=>500),
                        array(
                            'onchange'=>"$.fn.yiiGridView.update('files-grid',{ data:{pageSize: $(this).val() }})",
                        )
                ),
            ), 
        ),
    ));?>
    <div class="tags_action">
        <div class="row">
            <div class="col-sm-3">
                <?php echo CHtml::dropDownList('tag_select', $model, $tags, array('empty' => '(Select a Tag)', 'class'=>'form-control','style'=>'margin-top:2px;'));?>
            </div>
            <div class="col-sm-9" style="padding-left:0;">
                <?php
                echo CHtml::ajaxButton(
                            'Apply tag',
                            array('library/addtag'),
                            array(
                                  'success'=>'reloadGrid',
                                  'type'=>'POST',
                            ),
                            array('class'=>'btn btn-sm btn-primary','style'=>'margin-right:5px;')
                        );
                echo CHtml::ajaxButton(
                            'Remove tag',
                            array('library/removetag'),
                            array(
                                  'success'=>'reloadGrid',
                                  'type'=>'POST',
                            ),
                            array('class'=>'btn btn-sm btn-danger')
                        );
                ?>
            </div>
        </div>
    </div>
    <?php $this->endWidget();?>
</div>
