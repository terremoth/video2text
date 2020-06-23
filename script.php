<?php

define('DS', DIRECTORY_SEPARATOR);

$file = readline('Video file with location: ');
$frame_rate_command = 'ffmpeg -i '.$file.' 2>&1 | sed -n "s/.*, \(.*\) fp.*/\1/p"';
$frame_rate = trim(shell_exec($frame_rate_command));

$project_dir = getcwd().DS.'ascii-movie-'.time();

$images_dir      = $project_dir.DS.'images';
$frame_text_dir  = $project_dir.DS.'frametext';
$ascii_image_dir = $project_dir.DS.'asciiimage';

mkdir($project_dir,     0777);
mkdir($images_dir,      0777);
mkdir($frame_text_dir,  0777);
mkdir($ascii_image_dir, 0777);

$get_images_command = 'ffmpeg -v quiet -stats -loglevel warning -i '.$file.' -r '.$frame_rate.' '.$images_dir.DS.'image-%d.jpeg';

$get_images = shell_exec($get_images_command);
chdir($dir);

$fi = new FilesystemIterator($project_dir.DS.'images', FilesystemIterator::SKIP_DOTS);
$total_images_files = iterator_count($fi);

for ($i=1; $i <= $total_images_files; $i++) {
    echo 'Transforming image-'.$i.'.jpeg (total: '.$total_images_files.') into ascii text...';
    // transform jpeg files into text and send to frametext folder
    $command = 'jp2a '.$dir.DS.'images'.DS.'image-'.$i.'.jpeg --width=209 --output='.$frame_text_dir'.DS.'image-'.$i.'.txt';
    shell_exec($command);
    echo 'Done.'.PHP_EOL;
}
