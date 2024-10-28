#!/usr/bin/php8.2

<?php
$logpath = realpath("logs");

$logfiles = glob($logpath . "/access.log.*");

$newlines = "\r\n";

$clickcount_total = array();
$clickcount_recent = array();

$recent_interval_days = isset($_GET['d']) ? $_GET["d"] : null;

if (is_numeric($recent_interval_days))
	$recent_interval_days = intval($recent_interval_days);
else
	$recent_interval_days = 7;

printf("Recent requests are within the past " . ngettext("%d day", "%d days", $recent_interval_days) . "<br>\n<br>\n", $recent_interval_days);

$recent_interval = date_interval_create_from_date_string("$recent_interval_days days");

$now = new DateTime();

foreach ($logfiles as $file)
{
	$pathinfo = pathinfo($file);

	if ($pathinfo['extension'] == 'gz')
	{
		$process = proc_open("/usr/bin/zcat $file", array(0 => array("pipe", "r"), 1 => array("pipe", "w")), $pipes);
		$content = stream_get_contents($pipes[1]);
		proc_close($process);
	}
	else
		$content = file_get_contents($file);

	$line = strtok($content, $newlines);

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

			print("$timestamp: $clickid<br>\n");
		}

		$line = strtok($newlines);
	}
}

print("<br>\n");
print("Recent:<br>\n<ul>\n");

foreach (array_keys($clickcount_recent) as $key)
{
	print("<li>$key: " . $clickcount_recent[$key] . "</li>\n");
}

print("</ul>\n");
print("<br>\n");
print("Ever:<br>\n<ul>\n");

foreach (array_keys($clickcount_total) as $key)
{
	print("<li>$key: " . $clickcount_total[$key] . "</li>\n");
}

print("</ul>\n");
?>

