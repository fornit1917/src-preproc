<?php

$paramsCount = count($argv);
if ($paramsCount < 3 || $paramsCount > 4) {
	die ("ERROR: неверное количество параметров\r\n");
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
	die("ERROR: невозможно открыть для записи файл $outName\r\n");
}

$ok = preprocFile($inName, $constants, $fout);
fclose($fout);
if (!$ok && is_file($outName)) {
	unlink($outName);
}
else {
	echo "Обработка успешно завершено\r\n";
}

function preprocFile($inName, $constants, $fout)
{
	$fin = fopen($inName, 'r');
	if ($fin === false) {
		echo "ERROR: Невозможно открыть для чтения файл $inName";
		return false;
	}
	
	while ($s = fgets($fin)) {
		fputs($fout, $s);
	}
	
	fclose($fin);
	return true;
}