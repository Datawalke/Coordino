///////////////////////////////////////////////////////////////////////////////
//
// This code taken from:
// http://www.aspandjavascript.co.uk/javascript/javascript_api/
//
//
// Conditions of Use (from http://www.aspandjavascript.co.uk/about/conditions.asp)
//
// The Begging Bowl
// ================
// Though the code on the site is free, building, testing and maintaining dHTML
// code is a nasty laborious business and takes up a lot of time. If you use
// code from ASPAndJavaScript.co.uk in a commercial website, or for a paying
// client or use software from the site in a commercial environment, we politely
// invite you to consider making a donation of $20 (or whatever you consider
// appropriate) to help support the site.
//
// Donations can be made through PayPal using the button on the left of the page,
// or "in kind" through our Amazon Wish List (click the link and search for
// jamesdoz@hotmail.com). Donations go towrds the hosting costs of the site.
//
// You can also offer support by visiting our sister site ComputerBookPrices.com
// and using the price comparison system to find great deals on computer books
// and manuals.
//
//
// Copyright
// =========
// All code available on this site is copyrighted to James Austin (unless
// otherwise stated) and must retain any copyright messages.
//
//
// Disclaimer
// ==========
// All code on the site is available to download for free. No liability is
// accepted for any loss or damage, financial or otherwise arising from the
// viewing or use of this web site or any of its contents.
//
//
// Technical Support
// =================
// As stated above, we accept no liability for the contents of the site and do
// not guarantee to offer technical support for it. We do, however, appreciate
// feedback and always try to answer any questions and queries as thouroughly
// as time allows.
//
// If you have problems using the code or just questions about how it all works,
// please contact us at jamesdoz@hotmail.com, bug reports can be submitted to
// the same address
//
// Please include as much information as possible with your query, such as
// operating system, browser etc. Error messages and screenshots (where
// applicable) are helpful too.
//
///////////////////////////////////////////////////////////////////////////////

function sniffBrowsers() {
	var ns4 = document.layers;
	var op5 = (navigator.userAgent.indexOf("Opera 5")!=-1) ||(navigator.userAgent.indexOf("Opera/5")!=-1);
	var op6 = (navigator.userAgent.indexOf("Opera 6")!=-1) ||(navigator.userAgent.indexOf("Opera/6")!=-1);
	var agt=navigator.userAgent.toLowerCase();
	var mac = (agt.indexOf("mac")!=-1);
	var ie = (agt.indexOf("msie") != -1);
	var mac_ie = mac && ie;
}


function getStyleObject(objectId) {
    if(document.getElementById && document.getElementById(objectId)) {
	return document.getElementById(objectId).style;
    } else if (document.all && document.all(objectId)) {
	return document.all(objectId).style;
    } else if (document.layers && document.layers[objectId]) {
		return getObjNN4(document,objectId);
    } else {
	return false;
    }
}

function changeObjectVisibility(objectId, newVisibility) {
    var styleObject = getStyleObject(objectId, document);
    if(styleObject) {
	styleObject.visibility = newVisibility;
	return true;
    } else {
	return false;
    }
}

function findImage(name, doc) {
	var i, img;
	for (i = 0; i < doc.images.length; i++) {
    	if (doc.images[i].name == name) {
			return doc.images[i];
		}
	}
	for (i = 0; i < doc.layers.length; i++) {
    	if ((img = findImage(name, doc.layers[i].document)) != null) {
			img.container = doc.layers[i];
			return img;
    	}
	}
	return null;
}

function getImage(name) {
	if (document.layers) {
    	return findImage(name, document);
	}
	return null;
}

function getObjNN4(obj,name)
{
	var x = obj.layers;
	var foundLayer;
	for (var i=0;i<x.length;i++)
	{
		if (x[i].id == name)
		 	foundLayer = x[i];
		else if (x[i].layers.length)
			var tmp = getObjNN4(x[i],name);
		if (tmp) foundLayer = tmp;
	}
	return foundLayer;
}

function getElementHeight(Elem) {
	if (ns4) {
		var elem = getObjNN4(document, Elem);
		return elem.clip.height;
	} else {
		var elem;
		if(document.getElementById) {
			var elem = document.getElementById(Elem);
		} else if (document.all){
			var elem = document.all[Elem];
		}
		if (op5) {
			xPos = elem.style.pixelHeight;
		} else {
			xPos = elem.offsetHeight;
		}
		return xPos;
	}
}

function getElementWidth(Elem) {
	if (ns4) {
		var elem = getObjNN4(document, Elem);
		return elem.clip.width;
	} else {
		var elem;
		if(document.getElementById) {
			var elem = document.getElementById(Elem);
		} else if (document.all){
			var elem = document.all[Elem];
		}
		if (op5) {
			xPos = elem.style.pixelWidth;
		} else {
			xPos = elem.offsetWidth;
		}
		return xPos;
	}
}

function getElementLeft(Elem) {
	if (ns4) {
		var elem = getObjNN4(document, Elem);
		return elem.pageX;
	} else {
		var elem;
		if(document.getElementById) {
			var elem = document.getElementById(Elem);
		} else if (document.all){
			var elem = document.all[Elem];
		}
		xPos = elem.offsetLeft;
		tempEl = elem.offsetParent;
  		while (tempEl != null) {
  			xPos += tempEl.offsetLeft;
	  		tempEl = tempEl.offsetParent;
  		}
		return xPos;
	}
}


function getElementTop(Elem) {
	if (ns4) {
		var elem = getObjNN4(document, Elem);
		return elem.pageY;
	} else {
		if(document.getElementById) {
			var elem = document.getElementById(Elem);
		} else if (document.all) {
			var elem = document.all[Elem];
		}
		yPos = elem.offsetTop;
		tempEl = elem.offsetParent;
		while (tempEl != null) {
  			yPos += tempEl.offsetTop;
	  		tempEl = tempEl.offsetParent;
  		}
		return yPos;
	}
}


function getImageLeft(myImage) {
	var x, obj;
	if (document.layers) {
		var img = getImage(myImage);
    	if (img.container != null)
			return img.container.pageX + img.x;
		else
			return img.x;
  	} else {
		return getElementLeft(myImage);
	}
	return -1;
}

function getImageTop(myImage) {
	var y, obj;
	if (document.layers) {
		var img = getImage(myImage);
		if (img.container != null)
			return img.container.pageY + img.y;
		else
			return img.y;
	} else {
		return getElementTop(myImage);
	}
	return -1;
}

function getImageWidth(myImage) {
	var x, obj;
	if (document.layers) {
		var img = getImage(myImage);
		return img.width;
	} else {
		return getElementWidth(myImage);
	}
	return -1;
}

function getImageHeight(myImage) {
	var y, obj;
	if (document.layers) {
		var img = getImage(myImage);
		return img.height;
	} else {
		return getElementHeight(myImage);
	}
	return -1;
}

function moveXY(myObject, x, y) {
	obj = getStyleObject(myObject)
	if (ns4) {
		obj.top = y;
 		obj.left = x;
	} else {
		if (op5) {
			obj.pixelTop = y;
 			obj.pixelLeft = x;
		} else {
			obj.top = y + 'px';
 			obj.left = x + 'px';
		}
	}
}

function changeClass(Elem, myClass) {
	var elem;
	if(document.getElementById) {
		var elem = document.getElementById(Elem);
	} else if (document.all){
		var elem = document.all[Elem];
	}
	elem.className = myClass;
}

function changeImage(target, source) {
	var imageObj;

	if (ns4) {
		imageObj = getImage(target);
		if (imageObj) imageObj.src = eval(source).src;
	} else {
		imageObj = eval('document.images.' + target);
		if (imageObj) imageObj.src = eval(source).src;
	}
}

function changeBGColour(myObject, colour) {
	if (ns4) {
		var obj = getObjNN4(document, myObject);
		obj.bgColor=colour;
	} else {
		var obj = getStyleObject(myObject);
		if (op5) {
			obj.background = colour;
		} else {
			obj.backgroundColor = colour;
		}
	}
}
