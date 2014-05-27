<?php Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/font-awesome.min.css');?>
<div class="content-block">
	<?php $this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'tags-grid',
		'dataProvider'=>$model->search(),
        'template'=>'<div class="panel-header"><div class="row"><div class="col-sm-6"><h3>Tags</h3> {summary}</div><div class="col-sm-6"><a class="btn btn-custom btn-custom-l pull-right" href="/tags/create"><i class="fa fa-plus-square"></i> Create new Tag</a></div></div></div>{items}{pager}',
		'itemsCssClass'=>'table table-condensed table-hover table-condensed table-bordered table-striped',
        'htmlOptions'=>array('class'=>'table-responsive'),
		'columns'=>array(
			'id',
			'name',
			'text_color',
			'bg_color',
			'type',
			array(
				'class'=>'CButtonColumn',
				'template'=>'{update}{delete}',
			),
		),
	)); ?>
</div>