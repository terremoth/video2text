<?php

define('DS', DIRECTORY_SEPARATOR);

$file = readline('Video file with location: ');
$frame_rate_command = 'ffmpeg -i '.$file.' 2>&1 | sed -n "s/.*, \(.*\) fp.*/\1/p"';
$frame_rate = trim(shell_exec($frame_rate_command));

$dir = getcwd().DS.'ascii-movie-'.time();

mkdir($dir, 0777);
mkdir($dir.DS.'images', 0777);
mkdir($dir.DS.'frametext', 0777);
mkdir($dir.DS.'asciiimage', 0777);

$get_images_command = 'ffmpeg -v quiet -stats -loglevel warning -i '.$file.' -r '.$frame_rate.' '.$dir.'/images/image-%d.jpeg';

$get_images = shell_exec($get_images_command);
chdir($dir);

$fi = new FilesystemIterator($dir.DIRECTORY_SEPARATOR.'images', FilesystemIterator::SKIP_DOTS);
$total_images_files = iterator_count($fi);

for ($i=1; $i <= $total_images_files; $i++) {
    echo 'Transforming image-'.$i.'.jpeg (total: '.$total_images_files.') into ascii text...';
    // transform jpeg files into text and send to frametext folder
    $command = 'jp2a '.$dir.DS.'images'.DS.'image-'.$i.'.jpeg --width=209 --output='.$dir.DS.'frametext'.DS.'image-'.$i.'.txt';
    shell_exec($command);
    echo 'Done.'.PHP_EOL;
}
