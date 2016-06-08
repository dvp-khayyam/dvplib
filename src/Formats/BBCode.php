<?php
namespace Developpez\Formats;

use GeSHi;
use Developpez\Utils\PortailAccessor;

class BBCode
{
	public static function toHtml($source, PortailAccessor $accessor){
	    $source = static::processSimpleReplacements($source);
		$source = static::processCodeBlocs($source); 
		$source = static::processSmileys($source);		
		$source = static::processPiecesJointes($source, $accessor);	
		
	    return $source;
	}
	
	private static function processSimpleReplacements($source){
		// remplacements "simples"
		$availableParsers = array(
			'bold' => array(
					'pattern' => '/\[b\](.*?)\[\/b\]/si',
					'replace' => '<strong>$1</strong>',
			),
			'italic' => array(
					'pattern' => '/\[i\](.*?)\[\/i\]/si',
					'replace' => '<em>$1</em>',
			),
			'underLine' => array(
					'pattern' => '/\[u\](.*?)\[\/u\]/si',
					'replace' => '<u>$1</u>',
			),
			'lineThrough' => array(
					'pattern' => '/\[s\](.*?)\[\/s\]/si',
					'replace' => '<strike>$1</strike>',
			),
			'fontSize' => array(
					'pattern' => '/\[size\=([1-7])\](.*?)\[\/size\]/si',
					'replace' => '<span style="font-size: $1px;">$2</span>',
			),
			'fontColor' => array(
					'pattern' => '/\[color\=(#[A-f0-9]{6}|#[A-f0-9]{3})\](.*?)\[\/color\]/si',
					'replace' => '<span style="color: $1;">$2</span>',
			),
			'center' => array(
					'pattern' => '/\[center\](.*?)\[\/center\]/si',
					'replace' => '<div style="text-align:center;">$1</div>',
			),
			'quote' => array(
					'pattern' => '/\[quote\](.*?)\[\/quote\]/si',
					'replace' => '<blockquote>$1</blockquote>',
					'iterate' => 3,
			),
			'namedQuote' => array(
					'pattern' => '/\[quote\=(.*?)\](.*)\[\/quote\]/si',
					'replace' => '<blockquote><small>$1</small>$2</blockquote>',
					'iterate' => 3,
			),
			'link' => array(
					'pattern' => '/\[url\](.*?)\[\/url\]/si',
					'replace' => '<a href="$1">$1</a>',
			),
			'namedLink' => array(
					'pattern' => '/\[url\=(.*?)\](.*?)\[\/url\]/si',
					'replace' => '<a href="$1">$2</a>',
			),
			'image' => array(
					'pattern' => '/\[img\](.*?)\[\/img\]/si',
					'replace' => '<img src="$1">',
			),
			'orderedListNumerical' => array(
					'pattern' => '/\[list=1\](.*?)\[\/list\]/si',
					'replace' => '<ol>$1</ol>',
			),
			'orderedListAlpha' => array(
					'pattern' => '/\[list=a\](.*?)\[\/list\]/si',
					'replace' => '<ol type="a">$1</ol>',
			),
			'orderedListDeprecated' => array(
					'pattern' => '/\[ol\](.*?)\[\/ol\]/si',
					'replace' => '<ol>$1</ol>',
			),
			'unorderedList' => array(
					'pattern' => '/\[list\](.*?)\[\/list\]/si',
					'replace' => '<ul>$1</ul>',
			),
			'unorderedListDeprecated' => array(
					'pattern' => '/\[ul\](.*?)\[\/ul\]/si',
					'replace' => '<ul>$1</ul>',
			),
			'listItem' => array(
					'pattern' => '/\[\*\](.*)/',
					'replace' => '<li>$1</li>',
			),
			'youtube' => array(
					'pattern' => '/\[youtube\](.*?)\[\/youtube\]/si',
					'replace' => '<iframe width="560" height="315" src="//www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>',
			),
			'linebreak' => array(
					'pattern' => '/\r/',
					'replace' => '##DVP##<br />',
			)
		);
		
		foreach ($availableParsers as $name => $parser) {
			if(isset($parser['iterate']))
			{
				for ($i=0; $i <= $parser['iterate']; $i++) {
					$source = preg_replace($parser['pattern'], $parser['replace'], $source);
				}
			}
			else {
				$source = preg_replace($parser['pattern'], $parser['replace'], $source);
			}
		}
		
		return $source;
	}
	
	private static function processSmileys($source){
		$smileys = array(
			":fleche:" => '<img src="http://www.developpez.net/forums/images/smilies/fleche.gif" />'
		);
		$source = str_replace(array_keys($smileys), array_values($smileys), $source);
		
		$source = str_replace("##DVP##<br />", "<br />", $source);
		
		return $source;
	}
	
	private static function processPiecesJointes($source, PortailAccessor $accessor){
		$source = preg_replace_callback("/\[ATTACH(=CONFIG)?\]([a-zA-Z0-9]*?)\[\/ATTACH\]/msi", function($match){
			$hash = $match[2];
									
			$upload = $accessor->getUploadByHash($hash);
			if ($upload != null){
				if ($upload->isImage()){
					return '<img style="max-height:400px; max-width:400px" src="'.$upload->getUrl().'" title="'.$upload->get("name").'" ></a>';
				}else{
					return '<div><a href="'. $upload->getUrl() .'" target="_blank">'.$upload->get("name").'</a></div>';
				}
			}else{
				return "*lien cassé vers une pièce jointe*";
			}
		
		}, $source);
		return $source;		
	}
	
	private static function processCodeBlocs($source){
		$source = preg_replace_callback("/\[code(=[a-z]*)?\](.*?)\[\/code\]/msi", function($match){
			$language = $match[1];
			$language = trim(substr($language, 1));
			$code = $match[2];
		
			$code = str_replace("##DVP##<br />", "", $code);
		
			$codeAliases = array(
				"c++" => "cpp"
			);
			
			if (isset($codeAliases[$code])){
				$code = $codeAliases[$code];
			}
			
			$geshi = new GeSHi($code, $language);
		
			$libellesDeCode = array(
					"ada" => "ADA",
					"c" => "C",
					"cpp" => "C++",
					"html" => "HTML",
					"java" => "Java",
					"js" => "Javascript",
					"php" => "PHP",
		
					// rajouter ici tous les languages supportés par geshi
					// https://github.com/wikimedia/mediawiki-extensions-SyntaxHighlight_GeSHi/blob/master/SyntaxHighlight_GeSHi.lexers.php
			);
		
			if (isset($libellesDeCode[$language])){
				$libelle = $libellesDeCode[$language];
			}else{
				$libelle = $language;
			}
		
		
			return '<div><div class="codeTitle">Code '.$libelle.'</div>' . $geshi->parse_code() . '</div>';
		
		}, $source);
		return $source;
	}
}
