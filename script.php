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
$font = 5;
$font_size = 50;

for ($i = 1; $i <= $total_images_files; $i++) {
    
    $padding = 1;
    echo 'Transforming image-'.$i.'.jpeg (total: '.$total_images_files.') into ascii text...';
    // transform jpeg files into text and send to frametext folder
    $command = 'jp2a '.$project_dir.DS.'images'.DS.'image-'.$i.'.jpeg --width=209 --output='.$frame_text_dir.DS.'image-'.$i.'.txt';
    shell_exec($command);
    echo 'Done.'.PHP_EOL;


    $text_from_file = file_get_contents($frame_text_dir.DS.'image-'.$i.'.txt');

    $img = imagecreate(1920,1080) or die("Cannot Initialize new GD image stream");
    $background_color = imagecolorallocate($img, 0, 0, 0);
    $text_color = imagecolorallocate($img, 255, 255, 255);
    
    $lines = explode("\n",  $text_from_file);

    foreach ($lines as $line) {

        imagestring($img, $font, 5, $padding,  $line, $text_color); 
        $padding += 18;
    }

    imagejpeg($img, $ascii_image_dir.DS.'image-ascii-'.$i.'.jpeg');
    imagedestroy($img);
}

// print in only one line
// fix set fps
// gerar um video das imagens produzidas
// extrair o audio e inserir o audio no video
// ver se bate o video em ascii com o video original
// port para windows
// verificar as dependencias para instalar: ffmpeg, php-gd
// criar package do php com composer
// 
