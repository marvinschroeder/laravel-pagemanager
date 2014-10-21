<?php namespace Marvin85\Pagemanager;

use Illuminate\Support\Facades\URL as URL;
use Illuminate\Support\Facades\HTML as HTML;
use Illuminate\Support\Str as Str;

class Pagemanager {

	protected $bodyClass = array();

	/**
	 * css files to include
	 * @var array
	 */
	protected $cssFiles = null;

	/**
	 * css code to include
	 * @var array
	 */
	protected $css = null;

	/**
	 * js files to include by position
	 * @var array
	 */
	protected $jsFiles = array('head' => null, 'footer' => null);

	/**
	 * js code to include by position
	 * @var array
	 */
	protected $js = array('head' => null, 'footer' => null);

	/**
	 * url of the favicon
	 * @var string
	 */
	protected $favicon = null;

	protected $canonical = null;

	/**
	 * url of the apple touch icon
	 * @var string
	 */
	protected $appleTouchIcon = null;

	/**
	 * status indicator for using html5shiv for < IE 9
	 * @var boolean
	 */
	protected $html5Ie = false;

	/**
	 * charset to use
	 * @var string
	 */
	protected $charset = 'utf-8';

	/**
	 * meta properties to include
	 * @var array
	 */
	protected $meta = null;

	/**
	 * open graph properties to include
	 * @var array
	 */
	protected $og = null;

	protected $facebook_sdk = null;

	protected $twitter_sdk = null;

	protected $youtube_sdk = null;

	protected $google_plus_sdk = null;

	protected $breadcrumb = null;

	/**
	 * the default title
	 * @var string
	 */
	protected $defaultTitle = null;

	/**
	 * the title to append to the document title
	 * @var string
	 */
	protected $appendTitle = null;

	/**
	 * the current document title
	 * @var string
	 */
	protected $title = null;

	/**
	 * the current document description
	 * @var string
	 */
	protected $description = null;

	/**
	 * the locale to use as ISO 639 (de_DE, en_US..)
	 * @var string
	 */
	protected $locale = 'en_US';

	/**
	 * default Locale constant
	 */
	const LOCALE = 'en_US';

	/**
	 * possible meta types
	 * @var array
	 */
	protected $metaTypes = array('name', 'property', 'http-equiv');

	/**
	 * allowed meta properties with optional default values
	 * @var array
	 */
	protected $defaultMetaProperties = array(
		'name' =>  array('viewport' => 'width=device-width, initial-scale=1', 'robots' => 'index,follow')
	);

	/**
	 * default opengraph tag values
	 * @var array
	 */
	protected $openGraphDefaults = array('og:title' => '%TITLE%', 'og:url' => '%CURRENTURL%', 'og:type' => 'website', 'og:locale' => '%LOCALE%', 'og:description' => '%DESCRIPTION%');

	/**
	 * opengraph tags to override when defined multiple times
	 * @var array
	 */
	protected $openGraphSingleProperties = array('title', 'url', 'type', 'locale', 'description', 'site_name');

	/**
	 * open graph standard properties prefixable with og:
	 * @var array
	 */
	protected $openGraphStandardProperties = array('title', 'url', 'type', 'locale', 'description', 'site_name', 'image');

	/**
	 * language texts
	 * @var array
	 */
	protected $lang = array(
		'de_DE' => array(
			'browsehappy' => 'Du benutzt einen %sveralteten%s Browser. Bitte %saktualisiere Deinen Browser%s um alle Funktionen unserer Seite uneingeschränkt nutzen zu können.',
			'noTitle' => 'WARNUNG: Kein Seitentitel definiert'
		),
		'en_US' => array(
			'browsehappy' => 'You are using an %soutdated%s browser. Please %supgrade your browser%s to improve your experience.',
			'noTitle' => 'WARNING: No (default) page title defined'
		)
	);

	protected $pageTitle = null;

	public function pageTitle($str = null)
	{
		$this->pageTitle = $str;
		return $this;
	}

	public function getPageTitle($wrap = null)
	{
		$wrap_str = (!is_null($wrap)) ? array('<'.$wrap.'>', '</'.$wrap.'>') : array('', '');
		return (!is_null($this->pageTitle)) ? $wrap_str[0].$this->pageTitle.$wrap_str[1] : '';
	}

	public function addCrumb($title, $url = null)
	{
		$this->breadcrumb[] = array($title, $url);
		return $this;
	}

	public function getBreadcrumb()
	{
		return $this->breadcrumb;
	}
		
	/**
	 * setting the default title, when no title is specified
	 * @param  string $str the default title string
	 * @return Pagemanager     pagemanager instance
	 */
	public function defaultTitle($str)
	{
		$this->defaultTitle = $str;
		return $this;
	}

	/**
	 * sets a string to append on a title
	 * @param  string $str the appending string
	 * @return Pagemanager     pagemanager instance
	 */
	public function appendTitle($str)
	{
		$this->appendTitle = $str;
		return $this;
	}

	/**
	 * set the individual title
	 * @param  string $str the title string
	 * @return Pagemanager     pagemanager instance
	 */
	public function title($str)
	{
		$this->title = $str;
		return $this;
	}

	public function addBodyClass($class)
	{
		$this->bodyClass[] = Str::slug(str_replace('.', '-', $class));
		return $this;
	}

	public function getBodyClass()
	{
		if (!empty($this->bodyClass)) {
			foreach (array_unique($this->bodyClass) as $class) {
				$classes[] = 'has-'.$class;
			}
		}
		return (isset($classes)) ? ' class="'.implode(' ', $classes).'"' : '';
	}

	/**
	 * set the description
	 * @param  string $str the description string
	 * @return Pagemanager     pagemanager instance
	 */
	public function description($str)
	{
		$this->description = $str;
		$this->meta('description', $str);
		return $this;
	}

	public function addFacebookSdk($opts = array())
	{
		if (isset($opts['appId'])) {
			if (!isset($opts['version'])) $opts['version'] = 'v2.0';
			$this->facebook_sdk = $opts;
		}
		return $this;
	}

	public function addTwitterSdk()
	{
		$this->twitter_sdk = true;
		return $this;
	}

	public function addYoutubeSdk()
	{
		$this->youtube_sdk = true;
		return $this;
	}

	public function addGooglePlusSdk()
	{
		$this->google_plus_sdk = true;
		return $this;
	}

	/**
	 * set an open graph tag
	 * @param  string $key tag name
	 * @param  string|array $val value of the tag
	 * @return Pagemanager      pagemanager instance
	 */
	public function og($key, $val)
	{
		$work_key = (in_array($key, $this->openGraphStandardProperties)) ? 'og:'.$key : $key;
		if (in_array($key, $this->openGraphSingleProperties)) $this->ogForget($work_key);
		$this->og[] = array($work_key, $val);
		
		return $this;
	}

	/**
	 * remove an open graph tag
	 * @param  string $key tag name
	 * @return void      
	 */
	protected function ogForget($key)
	{
		if (!is_null($this->og)) {
			foreach ($this->og as $index => $tag) {
				if ($tag[0] == $key) unset($this->og[$index]);
			}
		}
	}

	/**
	 * renders the open graph tags
	 * @return html open graph tag code
	 */
	protected function renderOgTags()
	{
		if (!is_null($this->og)) {
			foreach ($this->og as $index => $tag) {
				switch ($tag[0]) {
					case 'og:image':
						if (is_array($tag[1])) {
							$tags[] = array('og:image:url', $tag[1]['url']);
							if (isset($tag[1]['secure_url'])) $tags[] = array('og:image:secure_url', $tag[1]['secure_url']);
							if (isset($tag[1]['type'])) $tags[] = array('og:image:type', $tag[1]['type']);
							if (isset($tag[1]['width'])) $tags[] = array('og:image:width', $tag[1]['width']);
							if (isset($tag[1]['height'])) $tags[] = array('og:image:height', $tag[1]['height']);
						} else {
							$tags[] = array($tag[0], $tag[1]);
						}
					break;
					default:
						$tags[] = array($tag[0], $tag[1]);
					break;
				}
			}
		}

		foreach ($this->openGraphDefaults as $key => $default_val) {
			unset($matched_index);
			$set_default_value = $found = false;

			if (isset($tags)) {
				foreach ($tags as $index => $tag) {
					if ($tag[0] == $key) {
						$found = true;
						if(is_string($tag[1]) and trim($tag[1]) == ''){
							$set_default_value = true;
							$matched_index = $index;
						}
					}
				}
			}

			if (!$found or $set_default_value) {
				if (isset($matched_index)) {
					$tags[$matched_index] = array($key, $this->replaceTemplate($default_val));
				} else {
					$tags[] = array($key, $this->replaceTemplate($default_val));
				}
			}
		}
			
		if (isset($tags)) {
			foreach ($tags as $tag) {
				if (trim($tag[1]) != '') {
					$html[] = '<meta property="'.$tag[0].'" content="'.$this->replaceTemplate($tag[1]).'" />';
				}
			}
		}

		return (isset($html)) ? implode(PHP_EOL, $html) : null;
	}

	/**
	 * replace template value
	 * @param  string $str to search in
	 * @return string      the replaced string
	 */
	protected function replaceTemplate($str)
	{
		$search = array('%TITLE%', '%LOCALE%', '%CURRENTURL%', '%DESCRIPTION%');
		$replace = array($this->getTitle(), $this->locale, URL::current(), $this->description);
		return str_replace($search, $replace, $str);
	}

	/**
	 * set a meta property
	 * @param  string $key of the meta property
	 * @param  string $val of the meta property
	 * @return Pagemanager      pagemanager instance
	 */
	public function meta($key, $val = null, $type = 'name') 
	{
		if (!in_array($type, $this->metaTypes)) return $this;
		if (is_null($val) and isset($this->meta[$type][$key])){
			unset($this->meta[$type][$key]);
		} else {
			$this->meta[$type][$key] = $val;
		}
		return $this;
	}

	/**
	 * set the locale as ISO 639 (de_DE, en_US..)
	 * @param  string $str the ISO Code
	 * @return Pagemanager     pagemanager instance
	 */
	public function locale($str)
	{
		$this->locale = $str;
		return $this;
	}

	/**
	 * set the charset
	 * @param  string $str 	the charset code
	 * @return Pagemanager  pagemanager instance
	 */
	public function charset($str)
	{
		$this->charset = $str;
		return $this;
	}

	/**
	 * set status to use html5shiv for < IE9
	 * @param  boolean $status indicator
	 * @return Pagemanager         pagemanager instance
	 */
	public function html5Ie($status = true)
	{
		$this->html5Ie = (bool) $status;
		return $this;
	}
	
	/**
	 * add a css file
	 * @param  string $file path to the file
	 * @return Pagemanager      pagemanager instance
	 */
	public function cssFile($file, $opts = array())
	{
		$default_opts = array('index' => 0);
		$opts = array_merge($default_opts, $opts);
		$this->cssFiles[$opts['index']][] = array($file, $opts);
		return $this;
	}

	/**
	 * add css code
	 * @param  string $code the css code to add
	 * @return Pagemanager      pagemanager instance
	 */
	public function css($code)
	{
		$this->css[] = $code;
		return $this;
	}

	/**
	 * add a js file in the given position
	 * @param  string $file     the file url
	 * @param  string $position the position to place the url
	 * @return Pagemanager           pagemanager instance
	 */
	public function jsFile($file, $position = 'footer', $opts = array())
	{
		$default_opts = array('index' => 0);
		$opts = array_merge($default_opts, $opts);
		$this->jsFiles[$position][$opts['index']][] = array($file, $opts);
		return $this;
	}

	/**
	 * add js code in the given position
	 * @param  string $code     the code to add
	 * @param  string $position the position to place the code
	 * @return Pagemanager           pagemanager instance
	 */
	public function js($code, $position = 'footer', $opts = array())
	{
		$default_opts = array('jquery' => true, 'index' => 0);
		$opts = array_merge($default_opts, $opts);
		$this->js[$position][$opts['index']][] = array($code, $opts);
		return $this;
	}

	/**
	 * set path to favicon
	 * @param  string $file path to the file
	 * @return Pagemanager       pagemanager instance
	 */
	public function favicon($file)
	{
		$this->favicon = $file;
		return $this;
	}

	public function canonical($url)
	{
		$this->canonical = $url;
		return $this;
	}

	/**
	 * set path to apple touch icon
	 * @param  string $file path to the file
	 * @return Pagemanager       pagemanager instance
	 */
	public function appleTouchIcon($file)
	{
		$this->appleTouchIcon = $file;
		return $this;
	}

	/**
	 * include browshappy notification for < IE 8
	 * @return html the browsehappy notification code
	 */
	public function browseHappy()
	{
		$replace_with = array('<strong>', '</strong>', '<a href="http://browsehappy.com/" target="_blank">', '</a>');
		return '<!--[if lt IE 8]><p id="browsehappy">'.vsprintf($this->getLangText('browsehappy'), $replace_with).'</p><![endif]-->';
	}

	/**
	 * Output code for head position
	 * @return html the rendered code for <head>..</head>
	 */
	public function head()
	{
		return $this->render();
	}

	/**
	 * return code for footer position
	 * @return html the rendered code before </body>
	 */
	public function footer()
	{
		return $this->render('footer');
	}

	/**
	 * helper function to get localized texts based on use locale
	 * @param  string $key of text to retreive
	 * @return string      text
	 */
	protected function getLangText($key)
	{
		return (isset($this->lang[$this->locale][$key])) ? $this->lang[$this->locale][$key] : $this->lang[Pagemanager::LOCALE][$key];
	}

	/**
	 * get the defined title (with appended string) or default title or warning if none is defined
	 * @return string the title
	 */
	protected function getTitle()
	{
		if(!is_null($this->title)){
			$title = $this->title.((!is_null($this->appendTitle)) ? $this->appendTitle : '');
		} else if (!is_null($this->defaultTitle)){
			$title = $this->defaultTitle;
		} else {
			$title = '! '.$this->getLangText('noTitle').' !';
		}
		return $title;
	}

	/**
	 * produces html link tag
	 * @param  string $rel  the relation key
	 * @param  string $href path to the resource
	 * @param  string $type type of the resource
	 * @return html       the link tag
	 */
	protected function getLinkTag($rel, $href, $type = null)
	{
		$type = (!is_null($type)) ? ' type="'.$type.'"' : '';
		return '<link rel="'.$rel.'"'.$type.' href="'.$href.'" />';
	}

	/**
	 * get all <meta../> properties
	 * @return html the meta code
	 */
	public function getMetaProperties()
	{
		if (!is_null($this->defaultMetaProperties)) {
			foreach ($this->defaultMetaProperties as $type => $tags) {
				foreach ($tags as $key => $content) {
					if (!isset($this->meta[$type][$key]) or trim($this->meta[$type][$key]) == '') {
						$this->meta[$type][$key] = $content;
					}
				}
			}
		}

		if (!is_null($this->meta)) {
			foreach ($this->meta as $type => $tags) {
				foreach ($tags as $key => $content) {
					$content = $this->replaceTemplate($content);
					if (trim($content) != '') {
						$html[] = '<meta '.$type.'="'.$key.'" content="'.$content.'" />';
					}
				}
			}
		}

		return (isset($html)) ? implode(PHP_EOL, $html) : null;
	}

	

	/**
	 * render the html code for the given position
	 * @param  string $position to render
	 * @return html           the rendered html code
	 */
	protected function render($position = 'head')
	{

		$html = null;

		if ($position == 'head') {
			$html[] = '<meta charset="'.$this->charset.'">';
			$html[] = '<title>'.$this->getTitle().'</title>';

			if ($this->html5Ie) {
				$html[] = '<!--[if lt IE 9]>'.PHP_EOL.HTML::script('//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js').'<![endif]-->';
			}

			if (!is_null($this->cssFiles)) {
				ksort($this->cssFiles, SORT_NUMERIC);

				foreach ($this->cssFiles as $index => $items) {
					foreach ($items as $item) {
						$html[] = HTML::style($item[0]);
					}
				}
			}
			if (!is_null($this->css)) $html[] = '<style type="text/css">'.implode($this->css).'</style>';

			if (!is_null($this->favicon)) $html[] = $this->getLinkTag('shortcut icon', $this->favicon, 'image/x-icon');
			if (!is_null($this->appleTouchIcon)) $html[] = $this->getLinkTag('apple-touch-icon', $this->appleTouchIcon);
			if (!is_null($this->canonical)) $html[] = $this->getLinkTag('canonical', $this->canonical);

			$meta = $this->getMetaProperties();
			if (!is_null($meta)) $html[] = $meta;
			$og_tags = $this->renderOgTags();
			if (!is_null($og_tags)) $html[] = $og_tags;
		}

		if ($position == 'footer') {
			if (!is_null($this->facebook_sdk)){
				$html[] = '<div id="fb-root"></div>';
				$this->js($this->getFacebookSdkCode(), 'footer', array('jquery' => false));
			}
			if (!is_null($this->twitter_sdk)){
				$this->js($this->getTwitterSdkCode(), 'footer', array('jquery' => false));
			}
			if (!is_null($this->youtube_sdk) or !is_null($this->google_plus_sdk)){
				$this->js($this->getGoogleSdkCode(), 'footer', array('jquery' => false));
			}
		}

		if (!is_null($this->jsFiles[$position])) {
			
			ksort($this->jsFiles[$position], SORT_NUMERIC);
			foreach ($this->jsFiles[$position] as $index => $items) {
				foreach ($items as $item) {
					$html[] = HTML::script($item[0]);
				}
			}
		}

		if (!is_null($this->js[$position])){
			ksort($this->js[$position]);
			foreach ($this->js[$position] as $index => $items) {
				foreach ($items as $item) {
					if (isset($item[1]['jquery']) and $item[1]['jquery']===true) {
						$jquery_code[] = $item[0];
					} else {
						$js_code[] = $item[0];
					}
				}
			}
			if (isset($jquery_code)) $js_code[] = 'jQuery(document).ready(function($){ '.implode($jquery_code).' });';
			$html[] = '<script>'.implode($js_code).'</script>';
		}

		return (!is_null($html)) ? implode($html) : null;
	}

	protected function getFacebookSdkCode()
	{
		return '(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/'.$this->locale.'/sdk.js#xfbml=1&appId='.$this->facebook_sdk['appId'].'&version='.$this->facebook_sdk['version'].'";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));';
	}

	protected function getTwitterSdkCode()
	{
		return '!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");';
	}

	protected function getGoogleSdkCode()
	{
		return '(function() {
    var go = document.createElement("script"); go.type = "text/javascript"; go.async = true;
    go.src = "//apis.google.com/js/platform.js";
    var go_s = document.getElementsByTagName("script")[0]; go_s.parentNode.insertBefore(go, go_s);
  })();';
	}
}
