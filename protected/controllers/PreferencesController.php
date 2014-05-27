<?php

class PreferencesController extends Controller
{
	public $defaultAction = 'settings';

	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	public function accessRules()
	{
		return array(
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('settings', 'reload'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionSettings()
	{
		$model=Setting::model()->findByPk(1);
		$streams = $model->streams;
		$playlists=CHtml::listdata(Playlists::model()->findAll(),'id','name');
		$time_set = array('Africa','America','Antarctica','Asia','Atlantic','Australia','Europe','Indian','Pacific');

		$no_error = false;

		$streams_input = array();
		if(isset($_POST['Stream']))
			$streams_input = $_POST['Stream'];
		if(isset($_POST['Setting']) || count($streams_input)>0)
        {
            $model->attributes=$_POST['Setting'];
            if($model->pid == '')
            	$model->pid = null;
            if($model->input_type == 'SHOUTcast')
            {
                $model->input_user = '';
                $model->input_mount = '/';
            }

            if($model->save())
            	$no_error = true;

            foreach ($streams_input as $output) {
            	$stream = Stream::model()->findByPk($output['id']);
            	$stream->active = $output['active'];
            	$stream->protocol = $output['protocol'];
            	$stream->type = $output['type'];
            	$stream->bitrate = $output['bitrate'];
            	$stream->host = $output['host'];
            	$stream->port = $output['port'];
            	$stream->username = $output['username'];
            	$stream->password = $output['password'];
            	$stream->admin_user = $output['admin_user'];
            	$stream->admin_pass = $output['admin_pass'];
            	$stream->mount = $output['mount'];
            	$stream->name = $output['name'];
            	$stream->description = $output['description'];
            	$stream->url = $output['url'];
            	if($stream->save())
            		$no_error = true;
                else
                    $no_error = false;
            }

            if($no_error)
            {
            	Yii::app()->user->setFlash('success', "<strong>Saved!</strong>");
            	$this->redirect('settings');
            }
        }

		$this->render('settings',array(
			'model'=>$model,
			'playlists'=>$playlists,
			'timeSet'=>$time_set,
			'streams'=>$streams
		));
	}

    public function actionReload()
    {
        $model=Setting::model()->findByPk(1);
        $config = $this->updateConfig($model);
        // if($config)
        //     $cmd = shell_exec('sudo /etc/init.d/liquidsoap restart');
        $this->redirect('settings');
    }

	protected function updateConfig($model)
	{
		$radio_config = RADIO;
		$console = CONSOLE;

		$config = "#liquidsoap configuration
		set(\"log.file.path\",\"/var/log/liquidsoap/radio.log\")
		set(\"init.daemon\",true)
		set(\"log.stdout\",false)
		set(\"log.file\",true)
		set(\"init.daemon.pidfile.path\",\"/var/run/liquidsoap/radio.pid\")";
        if($model->input_host != '')
        {
            $config .= "
        set(\"harbor.bind_addr\", \"$model->input_host\")";
            if($model->input_type == 'SHOUTcast')
            {
                $config .= "
        set(\"harbor.icy\", true)";
            }
            if($model->input_user != '')
            {
                $config .= "
        set(\"harbor.username\", \"$model->input_user\")";
            }
            $config .= "
        set(\"harbor.password\", \"$model->input_pass\")
        ";
        }
        $config .= "
		def smart_crossfade (~start_next=5.,~fade_in=5.,
		                     ~fade_out=5., ~width=2.,
		             ~conservative=false,s)
		  high   = -15.
		  medium = -32.
		  margin = 4.
		  fade.out = fade.out(type=\"sin\",duration=fade_out)
		  fade.in  = fade.in(type=\"sin\",duration=fade_in)
		  add = fun (a,b) -> add(normalize=false,[b,a])
		  log = log(label=\"smart_crossfade\")

		  def transition(a,b,ma,mb,sa,sb)

		    list.iter(fun(x)-> log(level=4,\"Before: #{x}\"),ma)
		    list.iter(fun(x)-> log(level=4,\"After : #{x}\"),mb)

		    if a <= medium and b <= medium and abs(a - b) <= margin then
		      log(\"Transition: crossed, fade-in, fade-out.\")
		      add(fade.out(sa),fade.in(sb))

		    elsif
		      b >= a + margin and a >= medium and b <= high
		    then
		      log(\"Transition: crossed, fade-out.\")
		      add(fade.out(sa),sb)

		    elsif
		      b >= a + margin and a <= medium and b <= high
		    then
		      log(\"Transition: crossed, no fade-out.\")
		      add(sa,sb)

		    elsif
		      a >= b + margin and b >= medium and a <= high
		    then
		      log(\"Transition: crossed, fade-in.\")
		      add(sa,fade.in(sb))

		    else
		      log(\"No transition: just sequencing.\")
		      sequence([sa, sb])
		    end
		  end

		  smart_cross(width=width, duration=start_next, 
		              conservative=conservative,
		              transition,s)
		end

		def my_request_function() = 
		  result = list.hd(get_process_lines(\"php $console getfile\"))
		  request.create(result)
		end

		# Create the source";

        if($model->input_host != '')
        {
            $config .= "

        live = input.harbor(\"$model->input_mount\")

        ";
        }
        $config .= "
		s = request.dynamic(my_request_function)
		s = smart_crossfade(s)";

        if($model->input_host != '')
        {
            $config .= "
        s = fallback(track_sensitive=false,[live,s])
        ";
        }else{
            $config .= "
        s = fallback(track_sensitive=false,[s])
        ";
        }
		$config .= "
        s = mksafe(s)";
        if(!empty($model->streams))
        {
            foreach ($model->streams as $output) {
                if($output->active == 1)
                {
                    $config .= "
        output.icecast(%$output->type(bitrate=$output->bitrate),
                host=\"$output->host\", port=$output->port, 
                password=\"$output->password\", mount = \"$output->mount\",
                url=\"$output->url\",name=\"$output->name\",description=\"$output->description\",
                s)
        ";
                }
            }
        }
		$fd = fopen($radio_config, "w+");
		$write = fputs($fd, $config . "\n");
		fclose($fd);

        if($write !== false)
            return true;
        else
            return $write;
	}

}