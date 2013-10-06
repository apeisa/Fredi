var fredi = (function(){

	var modalDiv,outerDiv,closeIcon,iframe;
	
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
			iframe.style.opacity="0";
			iframe.onload = function() {fredi.loaded()};
			iframe.allowTransparency=true;
			
			modalDiv.appendChild(iframe); // Move it inside modal
			
		},
		loaded: function () {
			iframe.style.opacity="1";
			iframe.style.width="100%";
			iframe.style.height="100%";
			modalDiv.className="frediModalOpen";
			modalDiv.appendChild(closeIcon);
		},
		empty: function () {
			iframe.style.width=0;
			iframe.style.height=0;
			modalDiv.removeChild(closeIcon);
			modalDiv.className='loader';

		},
		close: function () {
			modalDiv.removeChild(closeIcon);
			document.body.removeChild(outerDiv);
			document.body.removeChild(modalDiv);
			modalDiv.className="loader";
		},
		refresh: function() {
			var xhReq = new XMLHttpRequest();
			var url = document.URL;
			xhReq.open("GET", url, false);
			xhReq.send(null);
			var serverResponse = xhReq.responseText;
			var dom = new DOMParser().parseFromString(serverResponse, "text/html");
			bodyInner = dom.getElementsByTagName( 'body' )[0].innerHTML;
			document.body.innerHTML = bodyInner;
		}
	};
}());