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
			iframe.onload = function() {fredi.loaded()};
			iframe.allowTransparency=true;
			
			modalDiv.appendChild(iframe); // Move it inside modal
			
		},
		loaded: function () {

			modalDiv.className="frediModalOpen";
			modalDiv.appendChild(closeIcon);
			var height = modalDiv.offsetHeight;
			var width = modalDiv.offsetWidth;
			console.log(width + " / "+ height);
		},
		empty: function () {
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


/*
 * DOMParser HTML extension
 * 2012-09-04
 * 
 * By Eli Grey, http://eligrey.com
 * Public domain.
 * NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.
 */

/*! @source https://gist.github.com/1129031 */
/*global document, DOMParser*/

(function(DOMParser) {
	"use strict";

	var
	  DOMParser_proto = DOMParser.prototype
	, real_parseFromString = DOMParser_proto.parseFromString
	;

	// Firefox/Opera/IE throw errors on unsupported types
	try {
		// WebKit returns null on unsupported types
		if ((new DOMParser).parseFromString("", "text/html")) {
			// text/html parsing is natively supported
			return;
		}
	} catch (ex) {}

	DOMParser_proto.parseFromString = function(markup, type) {
		if (/^\s*text\/html\s*(?:;|$)/i.test(type)) {
			var
			  doc = document.implementation.createHTMLDocument("")
			;
	      		if (markup.toLowerCase().indexOf('<!doctype') > -1) {
        			doc.documentElement.innerHTML = markup;
      			}
      			else {
        			doc.body.innerHTML = markup;
      			}
			return doc;
		} else {
			return real_parseFromString.apply(this, arguments);
		}
	};
}(DOMParser));