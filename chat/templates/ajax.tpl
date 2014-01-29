<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<!--

  (c) 2006 TUFaT.com. All Rights Reserved

-->

<head>
  <title>{$title|escape:"htmlall"}</title>
  
  <meta name="GENERATOR" content="Quanta Plus" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  
<script type="text/javascript" src="js/functions.js"></script>

<link type="text/css" rel="stylesheet" id="AC_style_layout" />
{literal}

<script>

	switch(getBrowser ())
	{
		case 2:document.getElementById("AC_style_layout").href = "themes/styleFirefox.css"; break;
			
		case 3:document.getElementById("AC_style_layout").href = "themes/styleOpera.css"; break;
		case 4:
			if(browserISIE7())
			{
				document.getElementById("AC_style_layout").href = "themes/styleIE7.css"; break;
			}
			else
			{
				document.getElementById("AC_style_layout").href = "themes/styleIE.css"; break;
			}
			default:document.getElementById("AC_style_layout").href = "themes/styleIE.css"; break;
	}
	
</script>
	
{/literal}
  
  <link type="text/css" href="themes/dummy.css" rel="stylesheet" id="AC_style_theme" />

<!--[if lte IE 6]>
  <style type="text/css">
  /* <![CDATA[ */
    @import url("themes/fixIE.css");
  /* ]]> */
  </style>
<![endif]-->

<!--[if IE 7]>
  <style type="text/css">
  /* <![CDATA[ */
    @import url("themes/fixIE7.css");
  /* ]]> */
  </style>
<![endif]-->

{literal}

  <style type="text/css">
  /* <![CDATA[ */
    #AC_main {
      position: absolute;
      left: 0;
      top: 0;      
      bottom: 0px;
      right: 0px;      
    }
  /* ]]> */
  </style>
{/literal}

						
	
  <script type="text/javascript" src="config.php"></script>
  <script type="text/javascript" src="js/rgbcolor.js"></script>
  <script type="text/javascript" src="js/md5.js"></script>
  
  <script type="text/javascript" src="js/language.js"></script>
  <script type="text/javascript" src="js/user.js"></script>
  <script type="text/javascript" src="js/communication.js"></script>
  <script type="text/javascript" src="js/settings.js"></script>
  <script type="text/javascript" src="js/sound.js"></script>
  <script type="text/javascript" src="js/protocol.js"></script>
  <script type="text/javascript" src="js/uiLayout.js"></script>
  <script type="text/javascript" src="js/ui.js"></script>
  <!-- ui.js became too large, so it was split in 2 parts: ui.js and ui2.js -->
  <script type="text/javascript" src="js/ui2.js"></script>
  <script type="text/javascript" src="js/dragDrop.js"></script>

  <script type="text/javascript" src="js/chatEngine.js"></script>
  <script type="text/javascript" src="js/socketServer.js"></script>
  

  <!-- include themes -->
  <script type="text/javascript" src="themes/default/theme.js"></script>
  <script type="text/javascript" src="themes/green/theme.js"></script>
  <script type="text/javascript" src="themes/windows_xp/theme.js"></script>
  <script type="text/javascript" src="themes/macintosh_os_x/theme.js"></script>
  <script type="text/javascript" src="themes/silver_gradient/theme.js"></script>
  <script type="text/javascript" src="themes/navy_blue/theme.js"></script>

  <script type="text/javascript">
  /* <![CDATA[ */
    config["theme"] = config["themes"][0];
  /* ]]> */
  </script>
  <script type="text/javascript" src="sound/flashsound.js"></script>  
  <script type="text/javascript" src="socket/XMLSocket.js"></script>  
  {if $debuger==1}
  	{literal}
  		<SCRIPT LANGUAGE="JavaScript">
			var newWindow = window.open("./loggerAjax.php", "logger", "width=500,height=400,left=0,top=0,location=no,menubar=no,resizable=yes,scrollbars=no,status=no,toolbar=no");
		</SCRIPT>
	{/literal}
  {/if}
  <SCRIPT LANGUAGE="JavaScript">
  {literal}


 function onResizeEvent()
 {  
   var winW = 450;
   var winH = 450;
  //alert(document.body.clientWidth);
   if (navigator.appName=="Netscape") 
   {
      //winW = window.innerWidth;
      //winH = window.innerHeight;
    }

   if (navigator.appName.indexOf("Microsoft")!=-1) 
   {
	 //winW = document.body.offsetWidth;
     //winH = document.body.offsetHeight;
   }   

   if( winW < 650 || winH < 650)   
   {
   	 
   }
 }
 
 window.onresize = onResizeEvent; 

 onResizeEvent();

 {/literal}
</SCRIPT>

</head>

<body onload="checkBrowserAndInit();" onunload="doLogoutWin()">
<div id="AC_main"><img src="" name="backgroundimage" id="backgroundimage" width="100%" height="100%"></div>
<div id="xmlsocket-div" style="visibility:hidden"></div>
<script language="Javascript">

var mysound = new FlashSound();
mysound.embedSWF("sound/sound.swf");
xmls = new XMLSocket();
xmls.init("xmlsocket-div");	
</script>
</body>
</html>
