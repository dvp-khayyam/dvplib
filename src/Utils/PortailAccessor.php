<?php
namespace Developpez\Utils;

interface PortailAccessor
{
	/**
	 * Méthode permettant d'obtenir une "pièce jointe"
	 * @param string $hash
	 * @return Objet "IUpload"
	 */
	public function getUploadByHash($hash);
	
	// d'autres méthodes seront ajoutées au besoin
}
