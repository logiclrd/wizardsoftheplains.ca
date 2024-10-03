#!/usr/bin/pwsh

for ($i = 0; $i -lt 80; $i++)
{
  $x = (($i + 35) % 80) * 20
  convert ../pulse.png -roll "+$($x)+0" "pulse$($i.ToString("d3")).png"
}

ffmpeg -framerate 10 -i 'pulse%03d.png' -c:v ffv1 ../pulse.mkv
