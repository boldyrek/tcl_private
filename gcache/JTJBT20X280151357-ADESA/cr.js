// JavaScript Document
function createCRImagesJSON(){
	var crImages = $("#crImagesJSON").val();
	crImages = $.parseJSON(crImages);
	var imagePopup=false;
	
	try{
		crImages = crImages["CR Images"];
	} catch(err){
		crImages = opener.$("#crImagesJSON").val();
		crImages = $.parseJSON(crImages);
		crImages = crImages["CR Images"];
		imagePopup=true;
	}
	
	// Only if images are available
	if(crImages.isImagesAvailable && crImages.isMainImageAvailable){
		if(imagePopup){
			// Build image list
			// First Image
			AddImageLiPopup(crImages.crMainImage.largeImageURL, crImages.crMainImage.url, crImages.crMainImage.caption);
			
			// Rest of Images
			for(v=0;v<crImages.images.list.length;v++){
					AddImageLiPopup(crImages.images.list[v].largeImageURL, crImages.images.list[v].url, crImages.images.list[v].caption);	
				}
				ConfigureGalleryPopUp(crImages);
		} else {
			// Build image list
			// First Image
			AddImageLi(crImages.crMainImage.largeImageURL, crImages.crMainImage.url, crImages.crMainImage.caption);
			
			// Rest of Images
			for(v=0;v<crImages.images.list.length;v++){
				AddImageLi(crImages.images.list[v].largeImageURL, crImages.images.list[v].url, crImages.images.list[v].caption);	
			}
			ConfigureGallery(crImages);
		}
	}
}

function AddImageLi(largeImageURL, url, caption){
	$(".cr-gallery-thumbnails #thumbs ul.thumbs").append("<li>" +
				"<a class='thumb' href='" + largeImageURL + "' title='" + caption +"'>" + 
				"<img src='" + url + "' height='90px' width='120px' alt='" + caption + "'/>" +
				"</a>" + 
				"<div class='caption'>" + 
				"<div class='image-desc'>" + caption + "</div>" +
				"</div>" + 
				"</li>");
	
	$("ul.printOnly").append("<li>" + 
				"<img src='" + url + "' height='90px' width='120px' alt='" + caption + "'/>" + 
				"</li>");
}

function AddImageLiPopup(largeImageURL, url, caption){
	$(".cr-mod-gallery3 #thumbs ul.thumbs").append("<li>" + 
				"<a class='thumb' href='" + largeImageURL + "' title='" + caption +"'>" + 
				"<img src='" + url + "' height='90px' width='120px' alt='" + caption + "'/>" + 
				"</a>" + 
				"<div class='caption'>" + 
				"<div class='image-desc'>" + caption + "</div>" +
				"</div>" + 
				"</li>");
}

function InitializeTimer(time, timeZoneOffset, id){
	//alert("Time: " + time + " | ID: " + id);
	
	// Set Initial values
	var elapsed = 0;
	var sysClock = new Date().getTime();
	var displayTime = getESTDate(time, timeZoneOffset);
	
	// Display Initial time
	$("#" + id).html((displayTime.getHours() > 12 ? displayTime.getHours() - 12 : (displayTime.getHours() == 0 ? 12 : displayTime.getHours())) + ":" + 
			(displayTime.getMinutes() < 10 ? "0" + displayTime.getMinutes() : displayTime.getMinutes()) + (displayTime.getHours() > 11 ? " PM" : " AM") + " EST");
	
	// Start clock
	window.setTimeout(function(){ClockTick(id, elapsed, sysClock, time, timeZoneOffset);},1000);
}

function ClockTick(id, elapsed, sysClock, time, timeZoneOffset){
	elapsed += 1000;
	time += 1000;
	
	var displayTime = getESTDate(time, timeZoneOffset);
	
	// Display New Time every 300 seconds (5 minutes)
	if((elapsed % 300000) === 0){
		$("#" + id).html( 
			(displayTime.getHours() > 12 ? displayTime.getHours() - 12 : (displayTime.getHours() == 0 ? 12 : displayTime.getHours())) + ":" + 
			(displayTime.getMinutes() < 10 ? "0" + displayTime.getMinutes() : displayTime.getMinutes()) + (displayTime.getHours() > 11 ? " PM" : " AM") + " EST");
	}
	
	window.setTimeout(function(){ClockTick(id, elapsed, sysClock, time, timeZoneOffset);}, elapsed - (new Date().getTime() - sysClock));
	
}

// Accepts a date expressed in milliseconds
// Returns the date converted to EST

function getESTDate(ms, timeZoneOffset){
    // create Date object for current location
    localdate = new Date(ms);
    // convert to msec
    // add local time zone offset 
    // get UTC time in msec
    utc = localdate.getTime() + (localdate.getTimezoneOffset() * 60000);
    
    // create new Date object for different city
    // using supplied offset
    ESTDate = new Date(utc + (3600000*timeZoneOffset));
    
    // return time as a string
    return ESTDate;  // Return EST Date
}