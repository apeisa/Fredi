document.addEventListener("DOMContentLoaded", function(event) {
  // Thanks to feature.js / @viljamis
  var touch = !!(("ontouchstart" in window) || window.navigator && window.navigator.msPointerEnabled && window.MSGesture || window.DocumentTouch && document instanceof DocumentTouch);
  var fredilinks = document.getElementsByClassName("fredi");
  var i;
  for (i = 0; i < fredilinks.length; i++) {
    var elem = fredilinks[i];
    if (touch) elem.className = elem.className + " freditouch";
    var parent = elem.parentElement.parentElement;
    parent.className = parent.className + " frediparent";
  }
});