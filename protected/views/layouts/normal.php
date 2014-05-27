<?php $this->beginContent('//layouts/main'); ?>
        <!-- Navbar -->
        <div class="navbar navbar-fixed-top" role="navigation">
            <div id="master-panel">
                <div class="container-fluid">
                    <?php $this->widget('ext.Station.Station');?>
                </div>
            </div>
            <div id="main-nav">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="navbar-collapse collapse">
                        <?php $this->widget('zii.widgets.CMenu',array(
                            'id'=>'main-menu',
                            'items'=>array(
                                array('label'=>'Now Playing', 'url'=>array('/dashboard/nowplaying')),
                                array('label'=>'Library', 'url'=>array('/library/list'), 'active'=>($this->uniqueid == 'library')?true:false),
                                array('label'=>'Tags', 'url'=>array('/tags/list'), 'active'=>($this->uniqueid == 'tags')?true:false),
                                array('label'=>'Playlists', 'url'=>array('/playlists/list'), 'active'=>($this->uniqueid == 'playlists')?true:false),
                                array('label'=>'Calendar', 'url'=>array('/schedule/calendar'), 'active'=>($this->uniqueid == 'schedule')?true:false),
                                array('label'=>'Settings', 'url'=>array('/preferences/settings'), 'active'=>($this->uniqueid == 'preferences')?true:false),
                            ),
                            'htmlOptions'=>array(
                                'class'=>'nav navbar-nav'
                            )
                        )); ?>
                        <?php $this->widget('zii.widgets.CMenu',array(
                            'id'=>'user-menu',
                            'items'=>array(
                                array(
                                    'label'=>Yii::app()->user->name,
                                    'url'=>'#user',
                                    'items'=>array(
                                        array(
                                            'label'=>'Logout',
                                            'url'=>array('/user/logout'),
                                        )
                                    ),
                                    'linkOptions'=>array('class'=>'dropdown-toggle','data-toggle'=>'dropdown'),
                                    'itemOptions'=>array('class'=>'dropdown'),
                                    'submenuOptions'=>array('class'=>'dropdown-menu')
                                ),
                            ),
                            'htmlOptions'=>array(
                                'class'=>'nav navbar-nav navbar-right'
                            )
                        )); ?>
                    </div><!--/.navbar-collapse -->
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 main">
                    <?php echo $content; ?>
                </div>
            </div>
        </div> <!-- /container -->
<?php $this->endContent(); ?>