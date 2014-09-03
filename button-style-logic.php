<?php
$currentURI = $_SERVER['REQUEST_URI'];

$counter = 0;
$currentPage = "";
while ($currentURI[$counter] != '.') {
    $currentPage .= $currentURI[$counter];
    $counter++;
}
$index_button = "";
$gallery_button = "";
$contacts_button = "";

if ($currentPage == "/index") {
	$index_button = "active-button";
} else if ($currentPage == "/gallery") {
	$gallery_button = "active-button";
} else if ($currentPage == "/contacts") {
	$contacts_button = "active-button";
};
?>

	<div class="button-wrapper">
		<a href="index.php">
			<button class="<?=$index_button  ?>" id="about-button" onclick="manageContent('about', 'about-button')">
			<div class="triangle"></div>За нас
			<img class="button-icon" src="images/icon_worker.png" alt="">
			</button>
		</a>
		<a href="gallery.php">
			<button class="<?=$gallery_button  ?>" id="gallery-button" onclick="manageContent('gallery', 'gallery-button')">
			<div class="triangle"></div>Галерия
			<img class="button-icon" src="images/icon_gallery.png" alt="">
			</button>
		</a>
		<a href="contacts.php">
			<button class="<?=$contacts_button  ?>" id="contacts-button" onclick="manageContent('contacts', 'contacts-button')">
			<div class="triangle"></div>Контакти
			<img class="button-icon" src="images/icon_phone_small.png" alt="">
			</button>
		</a>
	</div>

	