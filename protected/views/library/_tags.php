<?php
foreach($model->tags0 as $item)
{
    echo CHtml::link(
	$item->name,
	'#removetag',
	array('class'=>'btn btn-xs tag-track btn-custom btn-tag',
	      'style'=>'background-color:'.$item->bg_color.'; color:'.$item->text_color.'; margin-right:3px;',
	      'data-track'=>$model->id,
	      'data-tag'=>$item->id,
	)
    );
}
?>