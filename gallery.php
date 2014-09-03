<?php 
	include('header.php');
        
        include 'constants.php';
        $allPictures = scandir(THUMB_DIR);
        unset($allPictures[0]);
        unset($allPictures[1]);
        array_multisort($allPictures);

        $allPicturesLength = count($allPictures);
        $numberOfPages = (($allPicturesLength / PICS_PER_PAGE));
        
        if (($numberOfPages - (int)$numberOfPages) === 0) {
            $numberOfPages--;
        }
        $allPicturesPaginated;
//        print_r($allPictures);
        for ($z = 0; $z <= $numberOfPages; $z++) {
            $index = 0;
            $firstPic = (int)($z * PICS_PER_PAGE);
            for ($i = $firstPic; $i <= $firstPic + PICS_PER_PAGE; $i++) {
                if (isset($allPictures[$i])) {
                    $allPicturesPaginated[$z][$index] = $allPictures[$i];
                }
                $index++;
            }
        }
        $currentNumber = 0;
        if ($_GET) {
           foreach ($_GET as $key => $value) {
           $currentNumber = $value - 1;
        }
    
}
//        print_r($allPicturesPaginated);
 ?>
 <div class="pagination">
     <?php createPagination($numberOfPages, $currentNumber); ?>
 </div>
<div class="gallery">
        <?php
 if ($_GET) {

     for ($i = 0; $i < PICS_PER_PAGE; $i++) {
         if (isset($allPicturesPaginated[$currentNumber][$i])) {
           $path = THUMB_DIR.DIRECTORY_SEPARATOR.$allPicturesPaginated[$currentNumber][$i];
           echo '<div class="gallery-image-wrapper"><img onclick="showBigImage(this);" src="'.$path.'" alt=""></div>';
         }
       }
}
else {
    for ($i = 0; $i < PICS_PER_PAGE; $i++) {
        if (isset($allPicturesPaginated[0][$i])) {
            $path = THUMB_DIR.DIRECTORY_SEPARATOR.$allPicturesPaginated[0][$i];
            echo '<div class="gallery-image-wrapper"><img onclick="showBigImage(this);" src="'.$path.'" alt=""></div>';
        }
    }
}

function createPagination($numberOfPages, $currentNumber) {
    for ($i = 0; $i <= $numberOfPages; $i++) {
         $pageNumber = 'page'.$i;
         if ($i == $currentNumber) {
              $paginationNumber = '<form action="" method="GET">
            <input class="active-pag" type="submit" name="'.$pageNumber.'" value="'.($i + 1).'">
            </form>';
         }
         else {
             $paginationNumber = '<form action="" method="GET">
            <input type="submit" name="'.$pageNumber.'" value="'.($i + 1).'">
            </form>';
         }
         echo $paginationNumber;
    }
}
                
                
?>


</div>
 <div class="pagination">
     <?php createPagination($numberOfPages, $currentNumber); ?>
 </div>

 <?php 
 	include('footer.php');
  ?>
<!--  <form action="" method="POST" class="delete-form">
      <input type="submit" class="delete">
  </form>-->
