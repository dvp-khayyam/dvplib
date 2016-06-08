<?php
namespace Developpez\Formats;


use Developpez\Utils\PortailAccessor;
class Markdown
{
	public static function toHtml($source, PortailAccessor $accessor){
		// TODO : modifier la conversion si besoin pour tenir compte de balises spécifiques DVP (ou pour mettre des smileys ou les pièces jointes)
		
		$parser = new \cebe\markdown\Markdown();
		return $parser->parse($source);
	}
	
}
