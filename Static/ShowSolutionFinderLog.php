#!/usr/bin/php

<?php
$logpath = realpath("logs");

$logfiles = glob($logpath . "/access.log.*");

$newlines = "\r\n";

foreach ($logfiles as $file)
{
	$pathinfo = pathinfo($file);

	if ($pathinfo['extension'] == 'gz')
	{
		$process = proc_open("/sur/bn/gzip -d $file", array(0 => array("pipe", "r"), 1 => array("pipe", "w")), $pipes);
		$content = stream_get_contents($pipes[1]);
		proc_close($process);
	}
	else
	{
		$content = file_get_contents($file);
	}

	$line = strtok($content, $newlines);

	$clickcount_total = array();
	$clickcount_recent = array();

	$recent_interval_days = 7;
	$recent_interval = date_interval_create_from_date_string("$recent_interval_days days");

	$now = getdate();

	while ($line !== false)
	{
		$parts = explode(" ", $line, 4);

		$timestamp = $parts[3];
		$timestamp = explode("[", $timestamp)[1];
		$timestamp = explode("]", $timestamp)[0];

		$timestamp_parsed = date_create($timestamp);

		$parts = explode("GET /SolutionFinderLog?", $parts[3]);

		if (count($parts) === 2)
		{
			$parts = explode(" ", $parts[1]);
			$parts = explode("=", $parts[0]);

			$clickid = urldecode($parts[1]);

			if (!isset($clickcount_total[$clickid]))
				$clickcount_total[$clickid] = 1;
			else
				$clickcount_total[$clickid]++;

			if (date_add($timestamp_parsed, $recent_interval) > $now)
			{
				if (!isset($clickcount_recent[$clickid]))
					$clickcount_recent[$clickid] = 1;
				else
					$clickcount_recent[$clickid]++;
			}

			print("$timestamp: $clickid<br>");
		}

		$line = strtok($newlines);
	}
}

print("<br>");
print("Recent:<br><ul>");

foreach (array_keys($clickcount_recent) as $key)
{
	print("<li>$key: " . $clickcount_recent[$key] . "</li>");
}

print("</ul>");
print("<br>");
print("Ever:<br><ul>");

foreach (array_keys($clickcount_total) as $key)
{
	print("<li>$key: " . $clickcount_total[$key] . "</li>");
}

print("</ul>");
?>

