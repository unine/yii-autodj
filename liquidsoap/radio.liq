#liquidsoap configuration
		set("log.file.path","/var/log/liquidsoap/radio.log")
		set("init.daemon",true)
		set("log.stdout",false)
		set("log.file",true)
		set("init.daemon.pidfile.path","/var/run/liquidsoap/radio.pid")
		def smart_crossfade (~start_next=5.,~fade_in=5.,
		                     ~fade_out=5., ~width=2.,
		             ~conservative=false,s)
		  high   = -15.
		  medium = -32.
		  margin = 4.
		  fade.out = fade.out(type="sin",duration=fade_out)
		  fade.in  = fade.in(type="sin",duration=fade_in)
		  add = fun (a,b) -> add(normalize=false,[b,a])
		  log = log(label="smart_crossfade")

		  def transition(a,b,ma,mb,sa,sb)

		    list.iter(fun(x)-> log(level=4,"Before: #{x}"),ma)
		    list.iter(fun(x)-> log(level=4,"After : #{x}"),mb)

		    if a <= medium and b <= medium and abs(a - b) <= margin then
		      log("Transition: crossed, fade-in, fade-out.")
		      add(fade.out(sa),fade.in(sb))

		    elsif
		      b >= a + margin and a >= medium and b <= high
		    then
		      log("Transition: crossed, fade-out.")
		      add(fade.out(sa),sb)

		    elsif
		      b >= a + margin and a <= medium and b <= high
		    then
		      log("Transition: crossed, no fade-out.")
		      add(sa,sb)

		    elsif
		      a >= b + margin and b >= medium and a <= high
		    then
		      log("Transition: crossed, fade-in.")
		      add(sa,fade.in(sb))

		    else
		      log("No transition: just sequencing.")
		      sequence([sa, sb])
		    end
		  end

		  smart_cross(width=width, duration=start_next, 
		              conservative=conservative,
		              transition,s)
		end

		def my_request_function() = 
		  result = list.hd(get_process_lines("php /var/www/console.php getfile"))
		  request.create(result)
		end

		# Create the source
		s = request.dynamic(my_request_function)
		s = smart_crossfade(s)
        s = fallback(track_sensitive=false,[s])
        
        s = mksafe(s)
        output.icecast(%mp3(bitrate=128),
                host="localhost", port=8000, 
                password="icecastDJ", mount = "live.nsv",
                url="http://coolwaveradio.com",name="Coolwave Radio",description="music long play",
                s)
        
