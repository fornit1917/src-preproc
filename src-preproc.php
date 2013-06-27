<?php

$paramsCount = count($argv);
if ($paramsCount < 3 || $paramsCount > 4) {
	die ("ERROR: wrong number of parameters\r\n");
}

$inName = $argv[1];
if ($paramsCount == 3) {
	$outName = $argv[2];
	$constants = array();
}
else {
	$outName = $argv[3];
	$constants = explode(',', $argv[2]);
}

$fout = fopen($outName, 'w');
if ($fout === false) {
	die("ERROR: can not open the file \"$outName\" for writing \r\n");
}

$ok = preprocFile($inName, $constants, $fout);
fclose($fout);
if (!$ok && is_file($outName)) {
	unlink($outName);
}
else {
	echo "Preprocessing completed successfully\r\n";
}

function preprocFile($inName, $constants, $fout)
{
	$fin = fopen($inName, 'r');
	if ($fin === false) {
		echo "ERROR: can not open the file \"$inName\" for reading \r\n";
		return false;
	}
	
	$preWd = getcwd();
	chdir(dirname($inName));
	while ($s = fgets($fin)) {

		preg_match('!^\s*(#|\/\/|\/\*)#include\s+(.*?)(\*\/)?\s*$!', $s, $matches);
		if ($matches && ($matches[1] != '/*' || isset($matches[3]))) {
			$includeName = trim($matches[2]);
			$ok = preprocFile(realpath($includeName), $constants, $fout);
			if (! $ok) {
				return false;
			}
			continue;
		}	
		
		fputs($fout, $s);
		if (substr($s, -1) !== "\n") {
			fputs($fout, "\r\n");
		}
	}
	
	fclose($fin);
	chdir($preWd);
	return true;
}