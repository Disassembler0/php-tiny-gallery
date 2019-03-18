<?php
define('THUMB_SIZE', 200); // Set thumbnail size in pixels
define('THUMB_DIR', 'thumbs'); // Set thumbnail directory name
define('ALLOWED_EXT', ['bmp', 'gif', 'jpe', 'jpeg', 'jpg', 'png']); // Set allowed image extensions

function has_allowed_extension($filename) {
    return in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), ALLOWED_EXT);
}

function thumb_name($filename) {
    return THUMB_DIR.DIRECTORY_SEPARATOR.pathinfo($filename, PATHINFO_FILENAME).'.jpg';
}

function generate_thumb($filename, $thumbname) {
    if (!file_exists(THUMB_DIR)) {
        mkdir(THUMB_DIR);
    }
    if (extension_loaded('imagick')) {
        $image = new Imagick($filename);
        $image->thumbnailImage(THUMB_SIZE, THUMB_SIZE, true);
        $image->writeImage($thumbname);
        $image->destroy();
    } else {
        $image = imagecreatefromstring(file_get_contents($filename));
        $img_w = imagesx($image);
        $img_h = imagesy($image);
        $thumb_w = $thumb_h = THUMB_SIZE;
        if ($img_w > $img_h) {
            $thumb_w = THUMB_SIZE;
            $thumb_h = $img_h / $img_w * THUMB_SIZE;
        } else if ($img_w < $img_h) {
            $thumb_w = $img_w / $img_h * THUMB_SIZE;
            $thumb_h = THUMB_SIZE;
        }
        $thumb = imagecreatetruecolor($thumb_w, $thumb_h);
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $thumb_w, $thumb_h, $img_w, $img_h);
        imagejpeg($thumb, $thumbname);
        imagedestroy($thumb);
        imagedestroy($image);
    }
}

if (isset($_GET['placeholder'])) {
    header('Content-type: image/png');
    die(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAMgAAADHCAMAAABr0Ox5AAAAQlBMVEUAAADMzMzMzMzLy8vU1NTOzs7MzMzNzc3MzMzNzc3Nzc3MzMzMzMzNzc3Ozs7MzMzMzMzMzMzNzc3Ozs7Nzc3MzMyUNbfUAAAAFXRSTlMA6tLACSfZXal7aIyfSRqW4bNxMD1d4OnhAAADTElEQVR42u3d25KiMBSF4aA5EQ6CuN7/Vadbpyb2KCEUCHvT+38Aq76LpSZqqSRJkiRJeq4AwwqBEEsg1BIItQRCLYFQSyDUEgi1BEItgVBLINT6jZAzkZZCTopIJ4EI5J5ABCKQdAIRiEDSCUQgAkknEIEIJJ1ABCKQdALZEzI05XfuplWMHeRWdfFh6+afhRdEl93/j1z9pbCCNJERK4L6jhOkxPtqrZRiBKkx1vlLwgaie4x3avlAPFKdNBdIQLoLE4gpMFHDA9Jjqk5zgNwwXeAAOWO6ggGkRU4NfcgVOdX0IT1y6uhDCmTVUoe0yKuhDhmQl6MOaZDX9SiQ8iiQQB1ymI1o5DVQhyiLrDR5yAU5nem/suet/Uofogtk1NKHqArT9QzOI6otMNmNAyTjRFLzuHzQFumKlgdEGaRrmNxrTT0FBz5XpupWJN9l8YEo043to1FKMYKo9ox3WaO+4gRRKhR4qdTqO14QpV2H54qyVY+YQb4ot9Dj0bl8+oCaHeSRuQ3qZ0whLwmEIaQ2x4AEWH0EiAFwOQDkcQ5w/CEe9wx3SMAjq3lDTDwRs4Y8H5QdZ4gHYoYvJOA5q7lCDH5WM4W83iQ5nhCPlwxHiEMszoQfxOBdNTvI2FWr4wbxGMnwgjiMZTUniMF4NSOItkjk+EA8khkuEId0VvOAGEzlWUC0xWSOA8QjI0Mf4pCT1dQhBnl54hBtkZmjDfHIzlCGOORnNV2IwZw8WYi2mFWgCvGYmaEJcZib1btALv2wzkBifg/IBUCkLBpILGwPueCeN6mfj87PbAeJjkhZPpA4k80g0REpiwcS85tBoiNWm6UDiYVNINGRpNRYkNkMUuFddbtwIHEmG0EqjHRp40CW5D8NiY4kRVssLGwBqZCsalWNxQ2fh1SYymN5nabwJf816j8MqbBV4aOQCts1rAbZ1xFnsj6kwqb1a0OiY+PCupDo2LxhHcjuDnR6fUiJPepXgezvAMIaEAIOYFgBQsGBTi+HUHAA/XIICQcQFkNoOIBiHcgVNJsBIe2YAyHtmAMh7ZgDIe2YAyHtmAMh7fiV/yxGO4FQSyDUEgi1BEItgVBLINQSCLUEQi2BUEsg1Do0RJIkSZLm9wesykvhYeAvCwAAAABJRU5ErkJggg=='));
}

if (isset($_GET['genthumb'])) {
    $filename = rawurldecode(filter_input(INPUT_GET, 'genthumb'));
    $filenames = scandir('.');
    if (!in_array($filename, $filenames) || !has_allowed_extension($filename)) {
        http_response_code(400); exit;
    }
    $thumbname = thumb_name($filename);
    if (!file_exists($thumbname)) {
        generate_thumb($filename, $thumbname);
    }
    die($thumbname);
}

header('Access-Control-Allow-Origin: *');
header('Content-Security-Policy: require-sri-for script style');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Gallery</title>
        <style>
            figure {display:inline-block; margin:0px 2px 2px 0px; padding:2px; border:#ccc 1px dotted; text-align:center; vertical-align:middle;}
            figure a {display:table-cell; vertical-align:middle; width:<?php echo THUMB_SIZE ?>px; height:<?php echo THUMB_SIZE ?>px;}
            img {border-style:none; max-width:100%; max-height:100%; }
        </style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" integrity="sha256-Vzbj7sDDS/woiFS3uNKo8eIuni59rjyNGtXfstRzStA=" crossorigin="anonymous" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js" integrity="sha256-yt2kYMy0w8AbtF89WXb2P1rfjcP/HTHLT7097U8Y5b8=" crossorigin="anonymous"></script>
        <script>
            var thumb_queue = [];

            $(function() {
                $('a').fancybox();
                $('img[src="index.php?placeholder"]').each(function() {
                    thumb_queue.push($(this).parent().attr('href'));
                });
                process_next_thumb();
            });

            function process_next_thumb() {
                if (thumb_queue.length) {
                    var filename = thumb_queue.shift();
                    $.get('index.php', {'genthumb': filename}, function(thumb) {
                        $('a[href="'+filename+'"]').children().attr('src', thumb);
                        process_next_thumb();
                    });
                }
            }
        </script>
    </head>
    <body>

<?php
$filenames = scandir('.');
foreach ($filenames as $filename) {
    if (has_allowed_extension($filename)) {
        $thumbname = thumb_name($filename);
        $thumbname = file_exists($thumbname) ? implode('/', array_map('rawurlencode', explode('/', $thumbname))) : 'index.php?placeholder';
        echo '<figure><a href="'.rawurlencode($filename).'" data-fancybox="gal"><img src="'.$thumbname.'" alt="'.htmlspecialchars($filename).'"></a></figure>';
    }
}
?>

    </body>
</html>
