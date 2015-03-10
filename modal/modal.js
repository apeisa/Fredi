var fredi = (function(){

	var modalDiv,outerDiv,closeIcon,iframe,url;
	url = document.URL;

	iframe=document.createElement('iframe'); 
	closeIcon=document.createElement('div'); closeIcon.id='frediClose'; 
	modalDiv=document.createElement('div'); modalDiv.id='frediModal'; modalDiv.className='loader';
	outerDiv=document.createElement('div'); outerDiv.id='frediOuter';
	outerDiv.onclick = function() {fredi.close()};
	closeIcon.onclick = function() {fredi.close()};
	
	return {
		modal: function( val ){
			// Add modal divs to end of the document. These should be styled with css
			document.body.appendChild(outerDiv);
			document.body.appendChild(modalDiv);
			
			// Load the hidden iframe
			iframe.src=val;
			iframe.onload = function() {fredi.loaded()};
			iframe.allowTransparency=true;
			
			modalDiv.appendChild(iframe); // Move it inside modal
			
		},
		loaded: function () {
			document.body.style.overflow = "hidden";
			modalDiv.className="frediModalOpen";
			modalDiv.appendChild(closeIcon);
		},
		empty: function () {
			modalDiv.removeChild(closeIcon);
			modalDiv.className='loader';
		},
		close: function () {
			document.body.style.overflow = "inherit";
			modalDiv.removeChild(closeIcon);
			document.body.removeChild(outerDiv);
			document.body.removeChild(modalDiv);
			modalDiv.className="loader";
		},
		refresh: function() {
			window.location.href = url;
			location.reload();
		}
	};
}());