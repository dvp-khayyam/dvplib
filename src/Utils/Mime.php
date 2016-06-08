<?php
namespace Developpez\Utils;

class Mime
{
	const C = "text/c";
	const CPP = "text/cpp";
	const CS = "text/cs";
	const CSV = "application/octet-stream";
	
	const GIF = "image/gif";
	const JAVA = "text/java";
	const JPG = "image/jpeg";
	const JPEG = "image/jpeg";
	const PDF = "application/pdf";
	const PHP = "text/php";
	const PHP5 = "text/php";
	const PNG = "image/png";
	const RAR = "application/x-rar-compressed";
	
	const XLSX = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
	
	const ZIP = "application/zip";

	// TODO rajouter ici de nouveaux types mime
	
	
	public static function all() {
        $oClass = new ReflectionClass(__CLASS__);
        $constants =  $oClass->getConstants();
        
		return $constants;
    }
}
