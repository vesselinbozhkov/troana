<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <link rel="stylesheet" href="stylesheets/style.css">
</head>
<body>
    
<a href="upload.php" class="back-button"><button>&lt;&lt;&lt;  Обратно в качване на снимки</button></a>
<?php

    include 'constants.php';

    if ($_POST) {
//        print_r($_POST);
        foreach ($_POST as $key => $value) {
            $pathToDelete = $key;
            $pathToDelete = str_replace('_', '.', $pathToDelete);
//            echo $pathToDelete;
            unlink(THUMB_DIR . DIRECTORY_SEPARATOR . $pathToDelete);
            unlink(BIG_DIR. DIRECTORY_SEPARATOR . $pathToDelete);
    }
}
    $allPictures = scandir(THUMB_DIR);
    unset($allPictures[0]);
    unset($allPictures[1]);
    
    array_multisort($allPictures);
    $msg = '';
    $allPicturesLength = count($allPictures);
    for ($i = 0; $i < $allPicturesLength; $i++) {
        $path = THUMB_DIR.DIRECTORY_SEPARATOR.$allPictures[$i];
        $name = $allPictures[$i];
        echo '<div class="gallery-manager-wrapper">'
        . '<img src="'.$path.'" alt="">'
        .'  <form action="" method="POST" class="delete-form">
            <input type="submit" class="delete-gallery" name="'.$name.'" value="Изтрий снимката">
            </form>'
        . '</div>'
        . '<strong class="sucess-msg">'.$msg.'</strong>';
    }
    

?>
</body>
</html>
