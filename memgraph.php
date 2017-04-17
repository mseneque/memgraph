<?php

// Top 6 memorary usage process Id's
$pidList = shell_exec("ps -auxw --sort rss | tail -6 | awk '{print $2}'");
$pidList = preg_split("/\r\n|\n|\r/", $pidList);
array_pop($pidList);

// Top 6 memory usage values
$memList = shell_exec("ps -auxw --sort rss | tail -6 | awk '{print $4}'");
$memList = preg_split("/\r\n|\n|\r/", $memList);
array_pop($memList);

// Combine key(pids) with values(memUsage)
$pidMem = array_combine($pidList, $memList);

// Reverse sort the array retaining the key value pair
arsort($pidMem);

// Create the display string
$i=0;
foreach($pidMem as $pid => $mem)
{
	// Get process name from pid
	$name[] = trim(shell_exec("ps -p $pid -o comm="));
	$display[] = $name[$i]." (".$mem.")";
	$i++;
}

// Create image canvas
$image = imagecreatetruecolor(600,400);

// Set black and white colour
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);

// Set background colour
imagefill($image, 1, 1, $white);

// Print header and boarder
imagesetthickness($image, 2);
imagerectangle($image, 5, 5, 595, 395, $black);
imagestring($image, 5, 260, 50, "RAM USAGE", $black);
imagesetthickness($image, 1);

// Pie chart parameters
$d=250; $x=150; $y=250;

// Text parameters
$tx=325; $ty=130;

// Draw the Pie Chart and Legend
$i=0;
$finish=0;
foreach($pidMem as $key => $value)
{
	$section = ($value/array_sum($pidMem))*360;
	$start = $finish;
	$finish = $start + $section;
	$color = 15255+(10000*$i);
	imagefilledarc($image, $x, $y, $d, $d, $start-90, $finish-90, $color, IMG_ARC_PIE);
	imagestring($image, 4, $tx, $ty+(40*$i), $display[$i], $black);
	imagefilledrectangle($image, $tx-25, $ty+(40*$i), $tx-5, $ty+15+(40*$i), $color);
	$i++;
}

// Draw arc boarder
imagearc($image, $x, $y, $d, $d, 0, 360, $black);

// Set the datestamp and save the file
$date = date('Ymd-His');
imagepng($image, "RAM-$date.png");

// Clean image from memory
imagedestroy($image);
?>
