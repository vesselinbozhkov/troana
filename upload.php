<?php
include('header.php')
?>
<form method="POST" enctype="multipart/form-data" class="upload">
    <fieldset>
    <legend>Качване на снимка в галерията</legend>
        <input type="file" name="image" />
        <input type="submit" />
    </fieldset>
</form>

<?php
include 'constants.php';

$displayPreview = "none;";

//upload logic
if ($_FILES) {
    $tempFilePath = $_FILES['image']['tmp_name'];
    $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $error = $_FILES['image']['error'];
    $verification = verify($tempFilePath, $fileExtension, $error);
    if ($verification) {
        $oldDimensions = getImageDimensions($tempFilePath);
        $resizeMethod = defineResizeMethod($oldDimensions);
        $newDimensions = calculateNewDimensions($resizeMethod);
        $newFileName = defineNewFileName($fileExtension);
        resizeLogic($resizeMethod, $tempFilePath, $newFileName, $newDimensions, $fileExtension, $oldDimensions);
        saveTempData($fileExtension, $newFileName);
        sucessMsg('Файлът е качен успешно!');
        $fileInfo = getPreviewInfo();
        $thumbPath = $fileInfo[2];
        $previewHTML = createImagePreviewCode($thumbPath);
        echo $previewHTML;
    }
}




function getImageDimensions($tempFilePath) {
//    $imgSizeArr = getimagesize($tempFilePath);
//    print_r($imgSizeArr[3]);
//    var_dump($imgSizeArr[3]);
    list($oldDimensions['width'], $oldDimensions['height']) = getimagesize($tempFilePath);
//    $oldDimensions['width'] = $imgSizeArr[3]['width'];
//    $oldDimensions['height'] = $imgSizeArr[3]['height'];
    return $oldDimensions;
}

function defineResizeMethod($oldDimensions) {
    $resizeMethod['type'] = "width";
    $resizeMethod['proportion'] = $oldDimensions['height'] / $oldDimensions['width'];
    $resizeMethod['resizeForBig'] = false;

    if ($oldDimensions['height'] > $oldDimensions['width']) {
        $resizeMethod['type'] = "height";
        $resizeMethod['proportion'] = $oldDimensions['width'] / $oldDimensions['height'];
    }
    
    if ($oldDimensions['width'] > MAX_WIDTH_BIG || $oldDimensions['height'] > MAX_HEIGHT_BIG) {
        $resizeMethod['resizeForBig'] = true;
    }
    return $resizeMethod;
}

function calculateNewDimensions($resizeMethod) {
    if ($resizeMethod['resizeForBig'] == true) {
        $newDimensions = calculateDimensionsForBig($resizeMethod);
    }
    
    if ($resizeMethod['type'] == "width") {
        $newDimensions['widthThumb'] = MAX_WIDTH_THUMB;
        $newDimensions['heightThumb'] = $resizeMethod['proportion'] * MAX_WIDTH_THUMB;
    }
    else {
        $newDimensions['heightThumb'] = MAX_HEIGHT_THUMB;
        $newDimensions['widthThumb'] = $resizeMethod['proportion'] * MAX_HEIGHT_THUMB;
    }
    
    return $newDimensions;
}

function calculateDimensionsForBig($resizeMethod) {
    if ($resizeMethod['type'] == 'width') {
        $newDimensions['widthBig'] = MAX_WIDTH_BIG;
        $newDimensions['heightBig'] = $resizeMethod['proportion'] * MAX_WIDTH_BIG;
    }
    else {
        $newDimensions['heightBig'] = MAX_HEIGHT_BIG;
        $newDimensions['widthBig'] = $resizeMethod['proportion'] * MAX_HEIGHT_BIG;
    }
    return $newDimensions;
}

function defineNewFileName($fileExtension) {
    $dirContent = scandir(BIG_DIR);
    $lastPicture = $dirContent[count($dirContent) - 1];
    $counter = 0;
    $lastPictureNumber = "";
    while ($lastPicture[$counter] != '.') {
        $lastPictureNumber .= $lastPicture[$counter];
        $counter++;
    }
    (int)$lastPictureNumber;
    $lastPictureNumber++;
    $nameOfNewPic = $lastPictureNumber;
    
    $nameOfNewPicLength = strlen((string)$lastPictureNumber);
    $clasification = '00';
    if ($nameOfNewPicLength == 1) {
        $clasification = '000';
    }
    else if ($nameOfNewPic == 3) {
        $clasification = '0';
    }
    else if ($nameOfNewPic == 4) {
        $clasification = '';
    }
    $newFileName['big'] = BIG_DIR . DIRECTORY_SEPARATOR . $clasification . $nameOfNewPic . '.' . $fileExtension;
    $newFileName['thumb'] = THUMB_DIR  . DIRECTORY_SEPARATOR . $clasification . $nameOfNewPic . '.' . $fileExtension;
    return $newFileName;
}

function resizeLogic($resizeMethod, $tempFilePath, $newFileName, $newDimensions, $fileExtension, $oldDimensions) {
    if ($resizeMethod['resizeForBig']) {
        actualResize('widthBig', 'heightBig','big', $tempFilePath, $newFileName, $newDimensions, $fileExtension, $oldDimensions);
    }
    else {
        copy($tempFilePath, $newFileName['big']);
    }
    actualResize('widthThumb', 'heightThumb', 'thumb', $tempFilePath, $newFileName, $newDimensions, $fileExtension, $oldDimensions);
}

function actualResize($width, $height, $name, $tempFilePath, $newFileName, $newDimensions, $fileExtension, $oldDimensions) {
    $new = imagecreatetruecolor($newDimensions[$width], $newDimensions[$height]);
    switch ($fileExtension) {
        case 'jpeg':
        case 'jpg':
            $source = imagecreatefromjpeg($tempFilePath);
            imagecopyresized($new, $source, 0, 0, 0, 0, $newDimensions[$width], $newDimensions[$height], $oldDimensions['width'], $oldDimensions['height']);
            imagejpeg($new, $newFileName[$name]);
            break;
        case 'png':
            $source = imagecreatefrompng($tempFilePath);
            imagecopyresized($new, $source, 0, 0, 0, 0, $newDimensions[$width], $newDimensions[$height], $oldDimensions['width'], $oldDimensions['height']);
            imagepng($new, $newFileName[$name]);
            break;
        case 'gif':
            $source = imagecreatefromgif($tempFilePath);
            imagecopyresized($new, $source, 0, 0, 0, 0, $newDimensions[$width], $newDimensions[$height], $oldDimensions['width'], $oldDimensions['height']);
            imagegif($new, $newFileName[$name]);
            break;
        default:
            break;
    }
    imagedestroy($new);
}

// verification
function verify($tempFilePath, $fileExtension, $error) {
    if ($error != UPLOAD_ERR_OK) {
        switch ($error) {
            case 1:
                errMessage('Грешка 1. Големината на снимката е твърде голяма. Максимално допустимата е 10 mb.');
                return false;
            case 2:
                errMessage('Грешка 2. Файлът е твърде голям.');
                return false;
            case 3:
                errMessage('Грешка 3. Файлът беше качен само отчасти.');
                return false;
            case 4:
                errMessage('Грешка 4. Не сте избрали файл!');
                return false;
            case 6:
                errMessage('Грешка 6. Липсва temp папката.');
                return false;
            case 7:
                errMessage('Грешка 7. Неуспешно записване на файла на диска.');
                return false;
            default :
                errMessage('Непозната грешка при качване на файла!');
                return false;
        }
    }
    else if (!(getimagesize($tempFilePath))) {
        errMessage('Избраният файл трябва да е изображение!');
        return false;
    }
    else if ($fileExtension != 'jpg' &&
             $fileExtension != 'jpeg' &&
             $fileExtension != 'png' &&
             $fileExtension != 'gif') {
        errMessage('Неразрешен формат на файла. Разрешени са JPG/JPEG/PNG/GIF');
        return false;
    }
    return true;
}

function saveTempData($fileExtension, $newFileName) {
    file_put_contents('tempData.txt', $fileExtension.PHP_EOL);
    file_put_contents('tempData.txt', $newFileName['big'].PHP_EOL, FILE_APPEND);
    file_put_contents('tempData.txt', $newFileName['thumb'].PHP_EOL, FILE_APPEND);
}
// messages
function errMessage($msg) {
    echo '<strong class="err-msg">'.$msg.'</strong>';
}

function sucessMsg($msg) {
    echo '<strong class="sucess-msg">'.$msg.'</strong>';
}

?>


<?php

function getPreviewInfo() {
    $dataContent = file_get_contents('tempData.txt');
    $fileInfo = explode(PHP_EOL, $dataContent);
    $thumbPath = $fileInfo[2];
    $previewHTML = createImagePreviewCode($thumbPath);
    return $fileInfo;
}
$fileInfo = getPreviewInfo();
$thumbPath = $fileInfo[2];
$previewHTML = createImagePreviewCode($thumbPath);
manageRotation($previewHTML, $fileInfo);


function rotate($fileInfo) {
    $thumbExtension = $fileInfo[0];
    $bigPath = $fileInfo[1];
    $thumbPath = $fileInfo[2];
    actualRotate($thumbExtension, $bigPath);
    actualRotate($thumbExtension, $thumbPath);
}

function actualRotate($fileExtension, $path) {
    switch ($fileExtension) {
        case 'jpeg':
        case 'jpg':
            $source = imagecreatefromjpeg($path);
            $rotated = imagerotate($source, ROTATE_DEGREE, 0);
            imagejpeg($rotated, $path);
            break;
        case 'png':
            $source = imagecreatefrompng($path);
            $rotated = imagerotate($source, ROTATE_DEGREE, 0);
            imagepng($rotated, $path);
            break;
        case 'gif':
            $source = imagecreatefromgif($path);
            $rotated = imagerotate($source, ROTATE_DEGREE, 0);
            imagegif($rotated, $path);
            break;
        default:
            break;
    }
    imagedestroy($source);
    imagedestroy($rotated);
}

function manageRotation($fileToPreview, $fileInfo) {
    if ($_POST) {   
        $rotate = isset($_POST['rotate']);
        $save = isset($_POST['save']);
        $delete = isset($_POST['delete']);
       
        if ($rotate) {
            echo $fileToPreview;
            rotate($fileInfo);
            return;
        }
        else if ($save) {
            sucessMsg('Снимката е запазена');
            return;
        }
        else if ($delete) {
            deleteFile($fileInfo);
            sucessMsg('Снимката е изтрита!');
            return;
        }
        echo $fileToPreview;
    }
}

function deleteFile($fileInfo) {
    unlink($fileInfo[1]);
    unlink($fileInfo[2]);
}

function createImagePreviewCode($fileToPreview) {
   return '<div class="upload-preview">
    <img src="'.$fileToPreview.'" alt="" id="preview-thumb">
    <form action="" method="POST">
        <input type="submit" name="rotate" value="завърти" id="rotate">
        <input type="submit" name="save" value="запази" id="save">
        <input type="submit" name="delete" value="изтрий" class="delete">
    </form>
    </div>';
}
?>

<a href="manage-gallery.php" class="back-button"><button>
    Управление на качени снимки  &gt;&gt;&gt;
</button></a>


<?php
include('footer.php');