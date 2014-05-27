<?php
foreach($model->tags0 as $key=>$item)
{
    echo "<span class='label' style='background-color:$item->bg_color; color:$item->text_color; margin-right:3px;'>$item->name</span>";
}
?>