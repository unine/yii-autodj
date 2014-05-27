<?php
Yii::app()->clientScript->registerCoreScript('jquery.ui');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/font-awesome.min.css');
Yii::app()->clientScript->registerScript('search',"
    var ajaxUpdateTimeout;
    var ajaxRequest;
    $('input#string').keyup(function(){
        ajaxRequest = $(this).serialize();
        clearTimeout(ajaxUpdateTimeout);
        ajaxUpdateTimeout = setTimeout(function () {
            $.fn.yiiGridView.update('tracksList',{data: ajaxRequest})
        },
        // this is the delay
        300);
    });"
);

Yii::app()->clientScript->registerScript('addQueue',"
$('body').on('click','.add-items',function(){ 
    var valueID = $(this).attr('data-id'); 
    $.ajax({
            'url':'".$this->createUrl('//dashboard/addQueue')."', 
            'type':'POST',
            'async':false,
            'dataType':'json',
            'data':{'fid':valueID},
            'success':function(data){
                $.fn.yiiGridView.update('queue-grid');
            },
            'error': function(request, status, error){
                alert('We are unable to add this item to the queue at this time.  Please try again in a few minutes.');
            }
    });
})
");

Yii::app()->clientScript->registerScript('clearqueue',"
$('.reload').click(function(){
    $.ajax({
            'url':'".$this->createUrl('//dashboard/reload')."', 
            'type':'POST',
            'async':false,
            'dataType':'json',
            'success':function(data){
                $.fn.yiiGridView.update('queue-grid');
            },
            'error': function(request, status, error){
                
            }
    });
});
");

Yii::app()->clientScript->registerScript('installsortable',"
    var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };

function installSortable() {
    $('#queue-grid tbody').sortable({
        forcePlaceholderSize: true,
        forceHelperSize: true,
        items: 'tr',
        update : function () {
            serial = $('#queue-grid tbody').sortable('serialize', {key: 'items[]', attribute: 'class'});
            $.ajax({
                'url': '".$this->createUrl('//dashboard/neworder')."',
                'type': 'post',
                'data': serial,
                'success': function(data){
                    $.fn.yiiGridView.update('queue-grid');
                },
                'error': function(request, status, error){
                    alert('We are unable to set the sort order at this time.  Please try again in a few minutes.');
                }
            });
        },
        helper: fixHelper
    }).disableSelection();
}
installSortable();
");
?>
<div class="row">
    <div class="col-sm-6 col-sm-push-6">
        <div id="queue_list" class="content-block right">
            <div class="panel-header">
                <div class="row">
                    <div class="col-sm-6">
                        <h3>Queued</h3>
                    </div>
                    <div class="col-sm-6">
                    <?php echo CHtml::link('<i class="fa fa-refresh"></i> clear and reload queue', '#reload', array('class' => 'btn-custom pull-right reload')); ?>
                    </div>
                </div>
            </div>
            <?php $this->widget('zii.widgets.grid.CGridView', array(
                'id'=>'queue-grid',
                'dataProvider'=>$queue,
                'template'=>'{items}<div class="row table-footer"><div class="col-sm-4"></div><div class="col-sm-8">{pager}</div></div>',
                'rowCssClassExpression'=>'"items[]_{$data->id}"',
                'afterAjaxUpdate'=>'installSortable',
                'itemsCssClass'=>'table form table-condensed table-hover table-condensed table-bordered table-striped',
                'htmlOptions'=>array('class'=>'table-responsive'),
                'columns'=>array(
                    array(
                        'header'=>'',
                        'value'=>'$row+1',
                    ),
                    'f.track_title',
                    'f.artist_name',
                    array(
                        'name'=>'f.duration',
                        'value'=>'gmdate("i:s",$data->f->duration)'
                    ),
                    array(
                        'class'=>'CButtonColumn',
                        'template'=>'{delete}',
                    ),
                ),
            )); ?>
        </div>
    </div>
	<div class="col-sm-6 col-sm-pull-6">
        <div id="library" class="content-block left">
            <div class="panel-header">
                <div class="row">
                    <div class="col-sm-6">
                        <h3>Library</h3>
                    </div>
                    <div class="col-sm-6">
                    <?php
                    echo CHtml::beginForm(CHtml::normalizeUrl(array('dashboard/nowplaying')), 'get', array('id'=>'filter-form'))
                       . '<div class="input-group"><span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
                       . CHtml::textField('string',(isset($_GET['string']))?$_GET['string']:'', array('id'=>'string','class'=>'form-control','placeholder'=>'title, artist, album, tag'))
                       . '</div>'
                       . CHtml::endForm();
                    ?>
                    </div>
                </div>
            </div>

            <?php $this->widget('zii.widgets.grid.CGridView',array(
                'dataProvider'=>$files,
                'id'=>'tracksList',
                'template'=>'{items}<div class="row table-footer"><div class="col-sm-4">{summary}</div><div class="col-sm-8">{pager}</div></div>',
                'itemsCssClass'=>'table table-condensed table-hover table-condensed table-bordered table-striped',
                'htmlOptions'=>array('class'=>'table-responsive'),
                'pager'=>array(
                    'header'=>'',
                    'selectedPageCssClass'=>'active',
                    'htmlOptions'=>array('class'=>'pagination')
                ),
                'pagerCssClass'=>'customPager pull-right',
                'columns'=>array(
                    'track_title',
                    'artist_name',
                    'album_title',
                    // array(
                    //     'name'=>'duration',
                    //     'value'=>'gmdate("i:s",$data->duration)'
                    // ),
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