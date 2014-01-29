<script type="text/javascript">
// <![CDATA[
// OS / BROWSER VARS - BEGIN
// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf('msie') != -1) && (clientPC.indexOf('opera') == -1));
var is_win = ((clientPC.indexOf('win') != -1) || (clientPC.indexOf('16bit') != -1));
var is_iphone = ((clientPC.indexOf('iphone'))!=-1);

// Other check in vars...
var uAgent = navigator.userAgent;
// NS 4
var ns4 = (document.layers) ? true : false;
// IE 4
var ie4 = (document.all) ? true : false;
// DOM
var dom = (document.getElementById) ? true : false;
// + OP5
var ope = ((uAgent.indexOf("Opera") > -1) && dom) ? true : false;
// IE5
var ie5 = (dom && ie4 && !ope) ? true : false;
// + NS 6
var ns6 = (dom && (uAgent.indexOf("Netscape") > -1)) ? true : false;
// + Konqueror
var khtml = (uAgent.indexOf("khtml") > -1) ? true : false;
//alert("UserAgent: "+uAgent+"\nns4 :"+ns4+"\nie4 :"+ie4+"\ndom :"+dom+"\nie5 :"+ie5+"\nns6 :"+ns6+"\nope :"+ope+"\nkhtml :"+khtml);
// OS / BROWSER VARS - END

var S_SID = '{S_SID}';
var FULL_SITE_PATH = '{FULL_SITE_PATH}';
var ip_root_path = '{IP_ROOT_PATH}';
var php_ext = '{PHP_EXT}';
var POST_FORUM_URL = '{POST_FORUM_URL}';
var POST_TOPIC_URL = '{POST_TOPIC_URL}';
var POST_POST_URL = '{POST_POST_URL}';
var LOGIN_MG = '{LOGIN_MG}';
var PORTAL_MG = '{PORTAL_MG}';
var FORUM_MG = '{FORUM_MG}';
var VIEWFORUM_MG = '{VIEWFORUM_MG}';
var VIEWTOPIC_MG = '{VIEWTOPIC_MG}';
var PROFILE_MG = '{PROFILE_MG}';
var POSTING_MG = '{POSTING_MG}';
var SEARCH_MG = '{SEARCH_MG}';
var form_name = 'post';
var text_name = 'message';
var onload_functions = new Array();
var onunload_functions = new Array();
// ]]>
</script>

<script type="text/javascript" src="{FULL_SITE_PATH}{T_COMMON_TPL_PATH}js/ip_scripts.js"></script>
<script type="text/javascript" src="{FULL_SITE_PATH}{T_COMMON_TPL_PATH}js/prototype.js"></script>
<script type="text/javascript" src="{FULL_SITE_PATH}{T_COMMON_TPL_PATH}js/run_active_content.js"></script>
<script type="text/javascript" src="{FULL_SITE_PATH}{T_COMMON_TPL_PATH}scriptaculous/scriptaculous.js"></script>

<!-- IE conditional comments: http://msdn.microsoft.com/workshop/author/dhtml/overview/ccomment_ovw.asp -->
<!--[if IE]>
<link rel="stylesheet" href="{FULL_SITE_PATH}{T_COMMON_TPL_PATH}common_ie.css" type="text/css" />
<![endif]-->

<!--[if lt IE 7]>
<script type="text/javascript" src="{FULL_SITE_PATH}{T_COMMON_TPL_PATH}js/pngfix.js"></script>
<![endif]-->

<!-- BEGIN switch_ajax_features -->
<script type="text/javascript">
<!--
var ajax_core_defined = 0;
var ajax_page_charset = '{S_CONTENT_ENCODING}';
//-->
</script>

<script type="text/javascript" src="{FULL_SITE_PATH}{T_COMMON_TPL_PATH}js/ajax/ajax_core.js"></script>
<!-- END switch_ajax_features -->

<!-- IF S_LIGHTBOX -->
<link rel="stylesheet" href="{FULL_SITE_PATH}{T_COMMON_TPL_PATH}lightbox/lightbox_old.css" type="text/css" media="screen" />
<script type="text/javascript" src="{FULL_SITE_PATH}{T_COMMON_TPL_PATH}lightbox/lightbox_old.js"></script>
<!-- ENDIF -->

<!-- IF S_HIGHSLIDE -->
<link rel="stylesheet" href="{FULL_SITE_PATH}{T_COMMON_TPL_PATH}highslide/highslide.css" type="text/css" media="screen" />
<script type="text/javascript" src="{FULL_SITE_PATH}{T_COMMON_TPL_PATH}highslide/highslide-full.packed.js"></script>
<script type="text/javascript">
hs.graphicsDir = '{FULL_SITE_PATH}{T_COMMON_TPL_PATH}highslide/graphics/';
hs.align = 'center';
hs.transitions = ['expand', 'crossfade'];
hs.outlineType = 'glossy-dark';
hs.showCredits = false;
hs.fadeInOut = true;
hs.numberOfImagesToPreload = 5;
hs.outlineWhileAnimating = 2; // 0 = never, 1 = always, 2 = HTML only
hs.loadingOpacity = 0.75;
hs.dimmingOpacity = 0.75;

// Add the controlbar
hs.addSlideshow({
	//slideshowGroup: 'group1',
	interval: 5000,
	repeat: false,
	useControls: true,
	fixedControls: 'fit',
	overlayOptions: {
		opacity: .75,
		position: 'bottom center',
		hideOnMouseOut: true
	}
});
</script>
<!-- ENDIF -->

<!-- BEGIN js_include -->
<script type="text/javascript" src="{FULL_SITE_PATH}{T_COMMON_TPL_PATH}{js_include.JS_FILE}"></script>
<!-- END js_include -->

<script type="text/javascript">
// <![CDATA[
/**
* New function for handling multiple calls to window.onload and window.unload by pentapenguin
*/
window.onload = function()
{
	for (var i = 0; i < onload_functions.length; i++)
	{
		eval(onload_functions[i]);
	}
}

window.onunload = function()
{
	for (var i = 0; i < onunload_functions.length; i++)
	{
		eval(onunload_functions[i]);
	}
}
// ]]>
</script>
