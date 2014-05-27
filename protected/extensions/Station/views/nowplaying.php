<div class="row">
    <div class="col-sm-4 block">
        <div id="prvious-song" class="text-row">
            <strong>Previous: </strong>
            <span id="previous-title"><?php echo ($this->previous !== null)?$this->previous->track_title.' - '.$this->previous->artist_name:' - ';?></span>
        </div>
        <div id="now-playing-info">
            Current: 
            <span id="current-title"><?php echo ($this->nowplaying !== null)?$this->nowplaying->track_title.' - '.$this->nowplaying->artist_name:'Nothing Play';?></span>
        </div>
        <div id="progressbar">
            <div id="progress-bar" class="progress-song"></div>
        </div>
        <div id="next-song" class="text-row">
            <strong>Next: </strong>
            <span id="next-title"><?php echo ($this->next !== null)?$this->next->track_title.' - '.$this->next->artist_name:' - ';?></span>
        </div>
    </div>
    <div id="station-info-block" class="col-sm-4 block">
        <div class="on-air-block block">
            <div id="on-air-info" class="on-air-info <?php echo ($this->online)?'on':'off';?>">ON AIR</div>
            <a href="#listen" class="listen-control-button"><span>Listen</span></a>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="row">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
                <div id="time-info-block">
                    <ul>
                        <li>Station time</li>
                        <li class="time">
                            <span class="hours">-</span><span class="point">:</span><span class="min">-</span><span class="point">:</span><span class="sec">-</span>
                        </li>
                        <li class="time-zone">UTC 
                            <?php $offset = date('Z')/3600;
                            if($offset >= 0)
                                $offset = '+'.$offset;

                            echo $offset;
                            ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
