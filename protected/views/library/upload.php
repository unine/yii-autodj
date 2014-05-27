<div class="content-block">
        <? $this->widget('ext.EAjaxUpload.EAjaxUpload',
        array(
                'id'=>'uploadFile',
                'config'=>array(
                        'action'=>Yii::app()->createUrl('library/uploadfiles'),
                        'allowedExtensions'=>array("mp3"),//array("jpg","jpeg","gif","exe","mov" and etc...
                        'sizeLimit'=>20*1024*1024,// maximum file size in bytes
                        'minSizeLimit'=>0.1*1024*1024,// minimum file size in bytes
                        'maxConnections'=>3,
                        'multiple'=>true,
                        //'onComplete'=>"js:function(id, fileName, responseJSON){ alert(fileName); }",
                        //'messages'=>array(
                        //                  'typeError'=>"{file} has invalid extension. Only {extensions} are allowed.",
                        //                  'sizeError'=>"{file} is too large, maximum file size is {sizeLimit}.",
                        //                  'minSizeError'=>"{file} is too small, minimum file size is {minSizeLimit}.",
                        //                  'emptyError'=>"{file} is empty, please select files again without it.",
                        //                  'onLeave'=>"The files are being uploaded, if you leave now the upload will be cancelled."
                        //                 ),
                        //'showMessage'=>"js:function(message){ alert(message); }"
                        )
        )); ?>
</div>