<?php
Yii::import('application.vendor.*');
require_once('getid3/getid3.php');

class ImportCommand extends CConsoleCommand 
{

    public function run($args)
    {
        // Initialize getID3 engine
        $getID3 = new getID3;
        $getID3->setOption(array(
                'option_md5_data' => false,
                'encoding'        => 'UTF-8',
        ));

        $DirToScan = LIBRARY;

        $files = Yii::app()->db->createCommand()->select('id,filepath')->from('files')->queryAll();
        $AlreadyInDatabase = array();

        if(count($files) > 0)
        {
            foreach ($files as $key => $item)
            {
                set_time_limit(300);
                if (!is_file(LIBRARY.$item['filepath']))
                    $delete = Yii::app()->db->createCommand()->delete('files', 'id=:id', array(':id'=>$item['id']));
                else
                    $AlreadyInDatabase[] = LIBRARY.$item['filepath'];
            }
        }
        print_r($AlreadyInDatabase);
        $DirectoriesToScan = array($DirToScan);
        $DirectoriesScanned = array();
        $FilesInDir = array();

        while (count($DirectoriesToScan) > 0)
        {
            foreach ($DirectoriesToScan as $DirectoryKey => $startingdir)
            {
                if ($dir = opendir($startingdir))
                {
                    set_time_limit(300);
                    while (($file = readdir($dir)) !== false)
                    {
                        if (($file != '.') && ($file != '..'))
                        {
                            $RealPathName = realpath($startingdir.'/'.$file);
                            // echo $RealPathName."\r\n";
                            if (is_dir($RealPathName))
                            {
                                if (!in_array($RealPathName, $DirectoriesScanned) && !in_array($RealPathName, $DirectoriesToScan)) 
                                    $DirectoriesToScan[] = $RealPathName;
                            }
                            elseif (is_file($RealPathName))
                            {
                                if (in_array($RealPathName, $AlreadyInDatabase))
                                {
                                    set_time_limit(300);
                                    $filesavepath = substr($RealPathName, strlen(LIBRARY));
                                    $criteria = new CDbCriteria;
                                    $criteria->condition = "filepath = \"$filesavepath\"";
                                    $original=Files::model()->find($criteria);
                                    $new = date('Y-m-d H:i:s', filemtime($RealPathName));
                                    if ($original != null && $new != $original->modified_time)
                                    {
                                        $oldfile = $getID3->analyze($RealPathName);
                                        $artist = $title = $album = '';

                                        if (array_key_exists('tags', $oldfile) && array_key_exists('id3v2', $oldfile['tags']))
                                        {
                                            if (array_key_exists('artist', $oldfile['tags']['id3v2']))
                                                $artist = trim($oldfile['tags']['id3v2']['artist'][0]);
                                            if (array_key_exists('title', $oldfile['tags']['id3v2']))
                                                $title = trim($oldfile['tags']['id3v2']['title'][0]);
                                            if (array_key_exists('album', $oldfile['tags']['id3v2']))
                                                $album = trim($oldfile['tags']['id3v2']['album'][0]);
                                        }

                                        $original->artist_name = $artist;
                                        $original->track_title = $title;
                                        $original->album_title = $album;
                                        $original->duration = ($oldfile['playtime_seconds'])?$oldfile['playtime_seconds']:0;
                                        $original->modified_time = $new;
                                        $original->save();
                                    }
                                }
                                elseif (!in_array($RealPathName, $AlreadyInDatabase))
                                {
                                    $FilesInDir[] = $RealPathName;
                                }
                            }
                        }
                    }
                    closedir($dir);
                }
                $DirectoriesScanned[] = $startingdir;
                unset($DirectoriesToScan[$DirectoryKey]);
            }
        }

        $FilesInDir = array_unique($FilesInDir);
        sort($FilesInDir);

        foreach ($FilesInDir as $key=>$filename)
        {
            set_time_limit(300);
            if (is_file($filename))
            {
                $thisfile = pathinfo($filename);

                if(strtolower($thisfile['extension']) == 'mp3')
                {
                    $ThisFileInfo = $getID3->analyze($filename);
                    $ThisFileInfo['file_modified_time'] = date('Y-m-d H:i:s', filemtime($filename));

                    $artist = $title = $album = '';
                    if(array_key_exists('tags', $ThisFileInfo) && array_key_exists('id3v2', $ThisFileInfo['tags']))
                    {
                            if (array_key_exists('artist', $ThisFileInfo['tags']['id3v2']))
                                $artist = trim($ThisFileInfo['tags']['id3v2']['artist'][0]);
                            if (array_key_exists('title', $ThisFileInfo['tags']['id3v2']))
                                $title = trim($ThisFileInfo['tags']['id3v2']['title'][0]);
                            if (array_key_exists('album', $ThisFileInfo['tags']['id3v2']))
                                $album = trim($ThisFileInfo['tags']['id3v2']['album'][0]);
                    }

                    $track = new Files;
                    $track->filepath = $track->filename = substr($filename, strlen(LIBRARY));
                    $track->artist_name = $artist;
                    $track->track_title = $title;
                    $track->album_title = $album;
                    $track->duration = ($ThisFileInfo['playtime_seconds'])?$ThisFileInfo['playtime_seconds']:0;
                    $track->modified_time = $ThisFileInfo['file_modified_time'];
                    $track->save();
                }
            }
        }
        echo 'Done!';
    }
}
