var fredi = (function(){

	var modalDiv,outerDiv,closeIcon,iframe;
	
	iframe=document.createElement('iframe'); 
	closeIcon=document.createElement('div'); closeIcon.id='frediClose'; 
	modalDiv=document.createElement('div'); modalDiv.id='frediModal';
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
			iframe.style.opacity="0";
			iframe.onload = function() {fredi.loaded()};
			iframe.allowTransparency=true;
			
			modalDiv.appendChild(iframe); // Move it inside modal
			
		},
		loaded: function () {
			iframe.style.opacity="1";
			modalDiv.className="frediModalOpen";
			modalDiv.appendChild(closeIcon);
		},
		close: function () {
			modalDiv.removeChild(closeIcon);
			document.body.removeChild(outerDiv);
			document.body.removeChild(modalDiv);
			modalDiv.className="";
		}
	};
}());


