<?php namespace Marvin85\Pagemanager;

use Illuminate\Support\Facades\URL as URL;

class Pagemanager {

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
	 * the locale to use as ISO 639 (de_DE, en_US..)
	 * @var string
	 */
	protected $locale = 'en_US';

	/**
	 * default Locale constant
	 */
	const LOCALE = 'en_US';

	/**
	 * allowed meta properties with optional default values
	 * @var array
	 */
	protected $defaultMetaProperties = array(
		'http-equiv' => array('X-UA-Compatible' => 'IE=edge'),
		'name' =>  array('viewport' => 'width=device-width, initial-scale=1', 'msapplication-config' => 'none', 'robots' => 'index,follow'),
		'property' => array('og:title' => '%TITLE%', 'og:url' => '%CURRENTURL%', 'og:type' => 'website', 'og:locale' => '%LOCALE%', 'og:description' => null)
	);

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

	/**
	 * set a meta property
	 * @param  string $key of the meta property
	 * @param  string $val of the meta property
	 * @return Pagemanager      pagemanager instance
	 */
	public function meta($key, $val = null)
	{
		if (is_null($val) and isset($this->meta[$key])){
			unset($this->meta[$key]);
		} else {
			$this->meta[$key] = $val;
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
	public function cssFile($file)
	{
		$this->cssFiles[] = $file;
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

	public function jsFile($file, $position = 'footer')
	{
		$this->jsFiles[$position][] = $file;
		return $this;
	}

	public function js($code, $position = 'footer')
	{
		$this->js[$position][] = $code;
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
		$meta = (!is_null($this->meta)) ? $this->meta : null;
		foreach ($this->defaultMetaProperties as $type => $properties) {
			foreach ($properties as $key => $content) {
				if (isset($meta[$key])) {
					$content = $meta[$key];
					unset($meta[$key]);
				} else {
					switch($content) {
						case '%TITLE%':
							$content = $this->getTitle();
						break;
						case '%LOCALE%':
							$content = $this->locale;
						break;
						case '%CURRENTURL%':
							$content = URL::current();
						break;
					}
				}
				if (trim($content) != '') {
					$html[] = '<meta '.$type.'="'.$key.'" content="'.$content.'" />';
				}
			}
		}
		return (isset($html)) ? implode($html) : null;
	}

	/**
	 * prepare data before rendering
	 * @param  string $position position which will be rendered
	 * @return void           
	 */
	protected function prepare($position)
	{
		if (!is_null($this->cssFiles)) $this->cssFiles = array_unique($this->cssFiles);
		if (!is_null($this->jsFiles[$position])) $this->jsFiles[$position] = array_unique($this->jsFiles[$position]);
	}

	/**
	 * render the html code for the given position
	 * @param  string $position to render
	 * @return html           the rendered html code
	 */
	protected function render($position = 'head')
	{
		$this->prepare($position);

		$html = null;

		if ($position == 'head') {
			$html[] = '<meta charset="'.$this->charset.'">';
			$html[] = '<title>'.$this->getTitle().'</title>';

			if ($this->html5Ie) {
				$html[] = '<!--[if lt IE 9]><script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js"></script><![endif]-->';
			}

			if (!is_null($this->cssFiles)) {
				foreach ($this->cssFiles as $file) {
					$html[] = $this->getLinkTag('stylesheet', $file, 'text/css');
				}
			}
			if (!is_null($this->css)) $html[] = '<style type="text/css">'.implode($this->css).'</style>';

			if (!is_null($this->favicon)) $html[] = $this->getLinkTag('shortcut icon', $this->favicon, 'image/x-icon');
			if (!is_null($this->appleTouchIcon)) $html[] = $this->getLinkTag('apple-touch-icon', $this->appleTouchIcon);

			$meta = $this->getMetaProperties();
			if (!is_null($meta)) $html[] = $meta;
		}

		if (!is_null($this->jsFiles[$position]) and !is_null($this->jsFiles[$position])) {
			foreach ($this->jsFiles[$position] as $file) {
				$html[] = '<script src="'.$file.'"></script>';
			}
		}

		if (!is_null($this->js[$position]) and !is_null($this->js[$position])) $html[] = '<script>'.implode($this->js[$position]).'</script>';

		return (!is_null($html)) ? implode($html)."\n" : null;
	}
}
