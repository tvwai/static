<html>
<head>
<title>Dplayer</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<meta http-equiv="content-language" content="zh-CN" />
<meta http-equiv="X-UA-Compatible" content="chrome=1" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta name="referrer" content="never" />
<meta name="renderer" content="webkit" />
<meta name="msapplication-tap-highlight" content="no" />
<meta name="HandheldFriendly" content="true" />
<meta name="x5-page-mode" content="app" />
<meta name="Viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<meta http-equiv="Access-Control-Allow-Origin" content="*">
<script src="https://cdn.jsdelivr.net/gh/linkec/klink@1.1.0/p2p.js"></script>
<script src="https://cdn.jsdelivr.net/gh/linkec/klink@1.1.0/dplayer.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/linkec/klink@1.1.0/DPlayer.min.css">
<style type="text/css">
body,html{width:100%;height:100%;background:#000;padding:0;margin:0;overflow-x:hidden;overflow-y:hidden}
*{margin:0;border:0;padding:0;text-decoration:none}
#dplayer{position:inherit}
</style>
</head>
<body style="background:#000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" oncontextmenu=window.event.returnValue=false>
<div id="dplayer"></div>
<script>
var url='<?php echo($_REQUEST['url']);?>';
var hlsjsConfig = {
	debug: false,
	maxBufferHole: 3,
	p2pConfig: {
		logLevel: 'warn',
		announce: "https://tracker.klink.tech",
		wsSignalerAddr: 'wss://signal.klink.tech/ws',
	}
};
var hls;
var dp = new DPlayer({
	container: document.getElementById('dplayer'),
    autoplay: true,
    theme: '#ff443f',
    loop: false,
    lang: 'zh-cn',
    screenshot: true,
    hotkey: true,
    preload: 'auto',
    mutex: true,
	video: {
		pic: 'loading_ls.png', 
		url: url,
		type: 'customHls',
		customType: {
			'customHls': function(video, player) {
				var isMobile = navigator.userAgent.match(/iPad|iPhone|Linux|Android|iPod/i) != null;
				if (isMobile) {
					var html = '<video src="' + video.src + '" controls="controls" autoplay="autoplay" width="100%" height="100%"></video>';
					document.getElementById('dplayer').innerHTML = html;
				} else {
					hls = new Hls(hlsjsConfig);
					hls.loadSource(video.src);
					hls.attachMedia(video);
					hls.engine.on('stats', function(data) {
						var size = hls.engine.fetcher.totalP2PDownloaded;
						hls.engine.fetcher.totalP2PDownloaded = 0;
						if (size > 0) {
							hls.engine.signaler.signalerWs.send({
								action: 'stat',
								size: size
							});
						}
					})
				}
			}
		}
	}
});
var webdata = {
    set:function(key,val){
        window.sessionStorage.setItem(key,val);
    },
    get:function(key){
        return window.sessionStorage.getItem(key);
    },
    del:function(key){
        window.sessionStorage.removeItem(key);
    },
    clear:function(key){
        window.sessionStorage.clear();
    }
};
dp.seek(webdata.get('vod' + url));
    setInterval(function(){
    webdata.set('vod' + url, dp.video.currentTime)
},1000);
</script>
</body>
</html>