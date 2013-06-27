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

$fout = @fopen($outName, 'w');
if ($fout === false) {
	die("ERROR: can not open the file \"$outName\" for writing \r\n");
}

$ok = preprocFile($inName, $constants, $fout);

fclose($fout);
if (!$ok && is_file($outName)) {
	unlink($outName);
}
elseif($ok) {
	echo "Preprocessing completed successfully\r\n";
}

function preprocFile($inName, $constants, $fout)
{
	$fin = @fopen($inName, 'r');
	if ($fin === false) {
		echo "ERROR: can not open the file \"$inName\" for reading \r\n";
		return false;
	}
	
	$preWd = getcwd();
	chdir(dirname($inName));
	
	$i = 0;
	while ($s = fgets($fin)) {
		
		$i++;
		//it's #include
		preg_match('!^\s*(#|\/\/|\/\*)#include\s+(.*?)(\*\/)?\s*$!', $s, $matches);
		if ($matches && ($matches[1] != '/*' || isset($matches[3]))) {
			$includeName = trim($matches[2]);
			if(!is_file($includeName)) {
				echo "ERROR in \"".  realpath($inName)."\", line $i: file \"$includeName\" not exists \r\n";
				chdir($preWd);
				return false;
			}
			
			$ok = preprocFile($includeName, $constants, $fout);
			if (! $ok) {
				chdir($preWd);
				return false;
			}
			continue;
		}	
		
		//it's not command for preprocessor
		fputs($fout, $s);
		if (substr($s, -1) !== "\n") {
			fputs($fout, "\r\n");
		}
	}
	
	fclose($fin);
	chdir($preWd);
	return true;
}