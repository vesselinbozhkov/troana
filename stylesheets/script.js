// function manageContent(toShow, thisEl) {
// 	// var toChange = document.getElementById(toShow);
// 	var buttonToChange = document.getElementById(thisEl);
// 	var classContent = buttonToChange.getAttribute('class');
// 	if (classContent === "active-button") {
// 		// toChange.setAttribute('style', 'display: none;');
// 		buttonToChange.setAttribute('class', "");
// 	}
// 	else {
// 		// toChange.setAttribute('style', 'display: block;');
// 		buttonToChange.setAttribute('class', "active-button");
// 	};

// 	var restoreShowContent = ['about', 'gallery', 'contacts'];
// 	var restoreButtons = ['about-button', 'gallery-button', 'contacts-button'];
// 	for (var i = restoreButtons.length - 1; i >= 0; i--) {
// 		if (thisEl !== restoreButtons[i]) {
// 			// toChange = document.getElementById(restoreShowContent[i]);
// 			buttonToChange = document.getElementById(restoreButtons[i]);
// 			// toChange.setAttribute('style', 'display: none;');
// 			buttonToChange.setAttribute('class', "");
// 		}
// 	};
// }

$(document).ready(
	function() {
		$('#ceresit').flash('images/Colors Of Nature 260x125-3_final.swf');
	}
);

 function showBigImage(lel) {
        var path = lel.getAttribute('src');
        var bigPicName = "";
        for (var i = path.length - 1; i >= 0; i--) {
            if (path[i] == '/') {
                break; 
};
            bigPicName += path[i];
        };
	 
        bigPicName = bigPicName.split("").reverse().join("");
        bigPicPath = 'gallery/' + bigPicName;

        var bigElDiv = document.getElementById('bigImgDiv');
        var bigElImg = document.getElementById('bigImg');
        bigElDiv.setAttribute('style', 'display: block;');
        bigElImg.setAttribute('src', bigPicPath);
}	

function closeBig() {
    var bigElDiv = document.getElementById('bigImgDiv');
    bigElDiv.setAttribute('style', 'display: none;');
}
