// prevent enter key from functioning as submit button
function wvrx_stopRKey() {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
  return true;
}

document.onkeypress = wvrx_stopRKey;
