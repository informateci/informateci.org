<?php
/*
Mighty Gorgon - Smiley PAK Creator
*/

// CTracker_Ignore: File Checked By Human
// Tell the Security Scanner that reachable code in this file is not a security issue

//$path = 'images/smiles/';
$path = './';
if(is_dir($path))
{
	$dir = opendir($path);
	while(gettype($file = readdir($dir)) != boolean)
	{
		if(!is_dir($file) && strpos($file, '.gif'))
		{
			$filename = str_replace('.gif', '' ,$file);
			echo($file . '=+:' . $filename . '=+::' .$filename . ':' . "<br />\n");
		}
	}
	closedir($dir);
}
?>