var Attacklab = Attacklab || {};

Attacklab.wmdBase = function(){

	// A few handy aliases for readability.
	var wmd  = top.Attacklab;
	var doc  = top.document;
	var re   = top.RegExp;
	var nav  = top.navigator;
	
	// Some namespaces.
	wmd.Util = {};
	wmd.Position = {};
	wmd.Command = {};
	wmd.Global = {};
	
	var util = wmd.Util;
	var position = wmd.Position;
	var command = wmd.Command;
	var global = wmd.Global;
	
	
	// Used to work around some browser bugs where we can't use feature testing.
	global.isIE 		= /msie/.test(nav.userAgent.toLowerCase());
	global.isIE_5or6 	= /msie 6/.test(nav.userAgent.toLowerCase()) || /msie 5/.test(nav.userAgent.toLowerCase());
	global.isIE_7plus 	= global.isIE && !global.isIE_5or6;
	global.isOpera 		= /opera/.test(nav.userAgent.toLowerCase());
	global.isKonqueror 	= /konqueror/.test(nav.userAgent.toLowerCase());
	
	
	// -------------------------------------------------------------------
	//  YOUR CHANGES GO HERE
	//
	// I've tried to localize the things you are likely to change to 
	// this area.
	// -------------------------------------------------------------------
	
	// The text that appears on the upper part of the dialog box when
	// entering links.
	var imageDialogText = "<p style='margin-top: 0px'><b>Enter the image URL.</b></p><p>You can also add a title, which will be displayed as a tool tip.</p><p>Example:<br />http://wmd-editor.com/images/cloud1.jpg   \"Optional title\"</p>";
	var linkDialogText = "<p style='margin-top: 0px'><b>Enter the web address.</b></p><p>You can also add a title, which will be displayed as a tool tip.</p><p>Example:<br />http://wmd-editor.com/   \"Optional title\"</p>";
	
	// The default text that appears in the dialog input box when entering
	// links.
	var imageDefaultText = "http://";
	var linkDefaultText = "http://";
	
	// The location of your button images relative to the base directory.
	var imageDirectory = "images/";
	
	// Some intervals in ms.  These can be adjusted to reduce the control's load.
	var previewPollInterval = 500;
	var pastePollInterval = 100;
	
	// The link and title for the help button
	var helpLink = "http://wmd-editor.com/";
	var helpHoverTitle = "WMD website";
	var helpTarget = "_blank";
	
	// -------------------------------------------------------------------
	//  END OF YOUR CHANGES
	// -------------------------------------------------------------------
	
	// A collection of the important regions on the page.
	// Cached so we don't have to keep traversing the DOM.
	wmd.PanelCollection = function(){
		this.buttonBar = doc.getElementById("wmd-button-bar");
		this.preview = doc.getElementById("wmd-preview");
		this.output = doc.getElementById("wmd-output");
		this.input = doc.getElementById("wmd-input");
	};
	
	// This PanelCollection object can't be filled until after the page
	// has loaded.
	wmd.panels = undefined;
	
	// Internet explorer has problems with CSS sprite buttons that use HTML
	// lists.  When you click on the background image "button", IE will 
	// select the non-existent link text and discard the selection in the
	// textarea.  The solution to this is to cache the textarea selection
	// on the button's mousedown event and set a flag.  In the part of the
	// code where we need to grab the selection, we check for the flag
	// and, if it's set, use the cached area instead of querying the
	// textarea.
	//
	// This ONLY affects Internet Explorer (tested on versions 6, 7
	// and 8) and ONLY on button clicks.  Keyboard shortcuts work
	// normally since the focus never leaves the textarea.
	wmd.ieCachedRange = null;		// cached textarea selection
	wmd.ieRetardedClick = false;	// flag
	
	// Returns true if the DOM element is visible, false if it's hidden.
	// Checks if display is anything other than none.
	util.isVisible = function (elem) {
	
	    if (window.getComputedStyle) {
	        // Most browsers
			return window.getComputedStyle(elem, null).getPropertyValue("display") !== "none";
		}
		else if (elem.currentStyle) {
		    // IE
			return elem.currentStyle["display"] !== "none";
		}
	};
	
	
	// Adds a listener callback to a DOM element which is fired on a specified
	// event.
	util.addEvent = function(elem, event, listener){
		if (elem.attachEvent) {
			// IE only.  The "on" is mandatory.
			elem.attachEvent("on" + event, listener);
		}
		else {
			// Other browsers.
			elem.addEventListener(event, listener, false);
		}
	};

	
	// Removes a listener callback from a DOM element which is fired on a specified
	// event.
	util.removeEvent = function(elem, event, listener){
		if (elem.detachEvent) {
			// IE only.  The "on" is mandatory.
			elem.detachEvent("on" + event, listener);
		}
		else {
			// Other browsers.
			elem.removeEventListener(event, listener, false);
		}
	};

	// Converts \r\n and \r to \n.
	util.fixEolChars = function(text){
		text = text.replace(/\r\n/g, "\n");
		text = text.replace(/\r/g, "\n");
		return text;
	};

	// Extends a regular expression.  Returns a new RegExp
	// using pre + regex + post as the expression.
	// Used in a few functions where we have a base
	// expression and we want to pre- or append some
	// conditions to it (e.g. adding "$" to the end).
	// The flags are unchanged.
	//
	// regex is a RegExp, pre and post are strings.
	util.extendRegExp = function(regex, pre, post){
		
		if (pre === null || pre === undefined)
		{
			pre = "";
		}
		if(post === null || post === undefined)
		{
			post = "";
		}
		
		var pattern = regex.toString();
		var flags = "";
		
		// Replace the flags with empty space and store them.
		// Technically, this can match incorrect flags like "gmm".
		var result = pattern.match(/\/([gim]*)$/);
		if (result === null) {
			flags = result[0];
		}
		else {
			flags = "";
		}
		
		// Remove the flags and slash delimiters from the regular expression.
		pattern = pattern.replace(/(^\/|\/[gim]*$)/g, "");
		pattern = pre + pattern + post;
		
		return new RegExp(pattern, flags);
	}

	
	// Sets the image for a button passed to the WMD editor.
	// Returns a new element with the image attached.
	// Adds several style properties to the image.
	util.createImage = function(img){
		
		var imgPath = imageDirectory + img;
		
		var elem = doc.createElement("img");
		elem.className = "wmd-button";
		elem.src = imgPath;

		return elem;
	};
	

	// This simulates a modal dialog box and asks for the URL when you
	// click the hyperlink or image buttons.
	//
	// text: The html for the input box.
	// defaultInputText: The default value that appears in the input box.
	// makeLinkMarkdown: The function which is executed when the prompt is dismissed, either via OK or Cancel
	util.prompt = function(text, defaultInputText, makeLinkMarkdown){
	
		// These variables need to be declared at this level since they are used
		// in multiple functions.
		var dialog;			// The dialog box.
		var background;		// The background beind the dialog box.
		var input;			// The text box where you enter the hyperlink.
		

		if (defaultInputText === undefined) {
			defaultInputText = "";
		}
		
		// Used as a keydown event handler. Esc dismisses the prompt.
		// Key code 27 is ESC.
		var checkEscape = function(key){
			var code = (key.charCode || key.keyCode);
			if (code === 27) {
				close(true);
			}
		};
		
		// Dismisses the hyperlink input box.
		// isCancel is true if we don't care about the input text.
		// isCancel is false if we are going to keep the text.
		var close = function(isCancel){
			util.removeEvent(doc.body, "keydown", checkEscape);
			var text = input.value;

			if (isCancel){
				text = null;
			}
			else{
				// Fixes common pasting errors.
				text = text.replace('http://http://', 'http://');
				text = text.replace('http://https://', 'https://');
				text = text.replace('http://ftp://', 'ftp://');
				
				if (text.indexOf('http://') === -1 && text.indexOf('ftp://') === -1 && text.indexOf('https://') === -1) {
					text = 'http://' + text;
				}
			}
			
			dialog.parentNode.removeChild(dialog);
			background.parentNode.removeChild(background);
			makeLinkMarkdown(text);
			return false;
		};
		
		// Creates the background behind the hyperlink text entry box.
		// Most of this has been moved to CSS but the div creation and
		// browser-specific hacks remain here.
		var createBackground = function(){
		
			background = doc.createElement("div");
			background.className = "wmd-prompt-background";
			style = background.style;
			style.position = "absolute";
			style.top = "0";
			
			style.zIndex = "1000";
			
			// Some versions of Konqueror don't support transparent colors
			// so we make the whole window transparent.
			//
			// Is this necessary on modern konqueror browsers?
			if (global.isKonqueror){
				style.backgroundColor = "transparent";
			}
			else if (global.isIE){
				style.filter = "alpha(opacity=50)";
			}
			else {
				style.opacity = "0.5";
			}
			
			var pageSize = position.getPageSize();
			style.height = pageSize[1] + "px";
			
			if(global.isIE){
				style.left = doc.documentElement.scrollLeft;
				style.width = doc.documentElement.clientWidth;
			}
			else {
				style.left = "0";
				style.width = "100%";
			}
			
			doc.body.appendChild(background);
		};
		
		// Create the text input box form/window.
		var createDialog = function(){
		
			// The main dialog box.
			dialog = doc.createElement("div");
			dialog.className = "wmd-prompt-dialog";
			dialog.style.padding = "10px;";
			dialog.style.position = "fixed";
			dialog.style.width = "400px";
			dialog.style.zIndex = "1001";
			
			// The dialog text.
			var question = doc.createElement("div");
			question.innerHTML = text;
			question.style.padding = "5px";
			dialog.appendChild(question);
			
			// The web form container for the text box and buttons.
			var form = doc.createElement("form");
			form.onsubmit = function(){ return close(false); };
			style = form.style;
			style.padding = "0";
			style.margin = "0";
			style.cssFloat = "left";
			style.width = "100%";
			style.textAlign = "center";
			style.position = "relative";
			dialog.appendChild(form);
			
			// The input text box
			input = doc.createElement("input");
			input.type = "text";
			input.value = defaultInputText;
			style = input.style;
			style.display = "block";
			style.width = "80%";
			style.marginLeft = style.marginRight = "auto";
			form.appendChild(input);
			
			// The ok button
			var okButton = doc.createElement("input");
			okButton.type = "button";
			okButton.onclick = function(){ return close(false); };
			okButton.value = "OK";
			style = okButton.style;
			style.margin = "10px";
			style.display = "inline";
			style.width = "7em";

			
			// The cancel button
			var cancelButton = doc.createElement("input");
			cancelButton.type = "button";
			cancelButton.onclick = function(){ return close(true); };
			cancelButton.value = "Cancel";
			style = cancelButton.style;
			style.margin = "10px";
			style.display = "inline";
			style.width = "7em";

			// The order of these buttons is different on macs.
			if (/mac/.test(nav.platform.toLowerCase())) {
				form.appendChild(cancelButton);
				form.appendChild(okButton);
			}
			else {
				form.appendChild(okButton);
				form.appendChild(cancelButton);
			}

			util.addEvent(doc.body, "keydown", checkEscape);
			dialog.style.top = "50%";
			dialog.style.left = "50%";
			dialog.style.display = "block";
			if(global.isIE_5or6){
				dialog.style.position = "absolute";
				dialog.style.top = doc.documentElement.scrollTop + 200 + "px";
				dialog.style.left = "50%";
			}
			doc.body.appendChild(dialog);
			
			// This has to be done AFTER adding the dialog to the form if you
			// want it to be centered.
			dialog.style.marginTop = -(position.getHeight(dialog) / 2) + "px";
			dialog.style.marginLeft = -(position.getWidth(dialog) / 2) + "px";
			
		};
		
		createBackground();
		
		// Why is this in a zero-length timeout?
		// Is it working around a browser bug?
		top.setTimeout(function(){
		
			createDialog();

			var defTextLen = defaultInputText.length;
			if (input.selectionStart !== undefined) {
				input.selectionStart = 0;
				input.selectionEnd = defTextLen;
			}
			else if (input.createTextRange) {
				var range = input.createTextRange();
				range.collapse(false);
				range.moveStart("character", -defTextLen);
				range.moveEnd("character", defTextLen);
				range.select();
			}
			
			input.focus();
		}, 0);
	};
	
	
	// UNFINISHED
	// The assignment in the while loop makes jslint cranky.
	// I'll change it to a better loop later.
	position.getTop = function(elem, isInner){
		var result = elem.offsetTop;
		if (!isInner) {
			while (elem = elem.offsetParent) {
				result += elem.offsetTop;
			}
		}
		return result;
	};
	
	position.getHeight = function (elem) {
		return elem.offsetHeight || elem.scrollHeight;
	};

	position.getWidth = function (elem) {
		return elem.offsetWidth || elem.scrollWidth;
	};

	position.getPageSize = function(){
		
		var scrollWidth, scrollHeight;
		var innerWidth, innerHeight;
		
		// It's not very clear which blocks work with which browsers.
		if(self.innerHeight && self.scrollMaxY){
			scrollWidth = doc.body.scrollWidth;
			scrollHeight = self.innerHeight + self.scrollMaxY;
		}
		else if(doc.body.scrollHeight > doc.body.offsetHeight){
			scrollWidth = doc.body.scrollWidth;
			scrollHeight = doc.body.scrollHeight;
		}
		else{
			scrollWidth = doc.body.offsetWidth;
			scrollHeight = doc.body.offsetHeight;
		}
		
		if(self.innerHeight){
			// Non-IE browser
			innerWidth = self.innerWidth;
			innerHeight = self.innerHeight;
		}
		else if(doc.documentElement && doc.documentElement.clientHeight){
			// Some versions of IE (IE 6 w/ a DOCTYPE declaration)
			innerWidth = doc.documentElement.clientWidth;
			innerHeight = doc.documentElement.clientHeight;
		}
		else if(doc.body){
			// Other versions of IE
			innerWidth = doc.body.clientWidth;
			innerHeight = doc.body.clientHeight;
		}
		
        var maxWidth = Math.max(scrollWidth, innerWidth);
        var maxHeight = Math.max(scrollHeight, innerHeight);
        return [maxWidth, maxHeight, innerWidth, innerHeight];
	};
	
	// Watches the input textarea, polling at an interval and runs
	// a callback function if anything has changed.
	wmd.inputPoller = function(callback, interval){
	
		var pollerObj = this;
		var inputArea = wmd.panels.input;
		
		// Stored start, end and text.  Used to see if there are changes to the input.
		var lastStart;
		var lastEnd;
		var markdown;
		
		var killHandle; // Used to cancel monitoring on destruction.
		// Checks to see if anything has changed in the textarea.
		// If so, it runs the callback.
		this.tick = function(){
		
			if (!util.isVisible(inputArea)) {
				return;
			}
			
			// Update the selection start and end, text.
			if (inputArea.selectionStart || inputArea.selectionStart === 0) {
				var start = inputArea.selectionStart;
				var end = inputArea.selectionEnd;
				if (start != lastStart || end != lastEnd) {
					lastStart = start;
					lastEnd = end;
					
					if (markdown != inputArea.value) {
						markdown = inputArea.value;
						return true;
					}
				}
			}
			return false;
		};
		
		
		var doTickCallback = function(){
		
			if (!util.isVisible(inputArea)) {
				return;
			}
			
			// If anything has changed, call the function.
			if (pollerObj.tick()) {
				callback();
			}
		};
		
		// Set how often we poll the textarea for changes.
		var assignInterval = function(){
			// previewPollInterval is set at the top of the namespace.
			killHandle = top.setInterval(doTickCallback, interval);
		};
		
		this.destroy = function(){
			top.clearInterval(killHandle);
		};
		
		assignInterval();
	};
	
	// Handles pushing and popping TextareaStates for undo/redo commands.
	// I should rename the stack variables to list.
	wmd.undoManager = function(callback){
	
		var undoObj = this;
		var undoStack = []; // A stack of undo states
		var stackPtr = 0; // The index of the current state
		var mode = "none";
		var lastState; // The last state
		var poller;
		var timer; // The setTimeout handle for cancelling the timer
		var inputStateObj;
		
		// Set the mode for later logic steps.
		var setMode = function(newMode, noSave){
		
			if (mode != newMode) {
				mode = newMode;
				if (!noSave) {
					saveState();
				}
			}
			
			if (!global.isIE || mode != "moving") {
				timer = top.setTimeout(refreshState, 1);
			}
			else {
				inputStateObj = null;
			}
		};
		
		var refreshState = function(){
			inputStateObj = new wmd.TextareaState();
			poller.tick();
			timer = undefined;
		};
		
		this.setCommandMode = function(){
			mode = "command";
			saveState();
			timer = top.setTimeout(refreshState, 0);
		};
		
		this.canUndo = function(){
			return stackPtr > 1;
		};
		
		this.canRedo = function(){
			if (undoStack[stackPtr + 1]) {
				return true;
			}
			return false;
		};
		
		// Removes the last state and restores it.
		this.undo = function(){
		
			if (undoObj.canUndo()) {
				if (lastState) {
					// What about setting state -1 to null or checking for undefined?
					lastState.restore();
					lastState = null;
				}
				else {
					undoStack[stackPtr] = new wmd.TextareaState();
					undoStack[--stackPtr].restore();
					
					if (callback) {
						callback();
					}
				}
			}
			
			mode = "none";
			wmd.panels.input.focus();
			refreshState();
		};
		
		// Redo an action.
		this.redo = function(){
		
			if (undoObj.canRedo()) {
			
				undoStack[++stackPtr].restore();
				
				if (callback) {
					callback();
				}
			}
			
			mode = "none";
			wmd.panels.input.focus();
			refreshState();
		};
		
		// Push the input area state to the stack.
		var saveState = function(){
		
			var currState = inputStateObj || new wmd.TextareaState();
			
			if (!currState) {
				return false;
			}
			if (mode == "moving") {
				if (!lastState) {
					lastState = currState;
				}
				return;
			}
			if (lastState) {
				if (undoStack[stackPtr - 1].text != lastState.text) {
					undoStack[stackPtr++] = lastState;
				}
				lastState = null;
			}
			undoStack[stackPtr++] = currState;
			undoStack[stackPtr + 1] = null;
			if (callback) {
				callback();
			}
		};
		
		var handleCtrlYZ = function(event){
		
			var handled = false;
			
			if (event.ctrlKey || event.metaKey) {
			
				// IE and Opera do not support charCode.
				var keyCode = event.charCode || event.keyCode;
				var keyCodeChar = String.fromCharCode(keyCode);
				
				switch (keyCodeChar) {
				
					case "y":
						undoObj.redo();
						handled = true;
						break;
						
					case "z":
						if (!event.shiftKey) {
							undoObj.undo();
						}
						else {
							undoObj.redo();
						}
						handled = true;
						break;
				}
			}
			
			if (handled) {
				if (event.preventDefault) {
					event.preventDefault();
				}
				if (top.event) {
					top.event.returnValue = false;
				}
				return;
			}
		};
		
		// Set the mode depending on what is going on in the input area.
		var handleModeChange = function(event){
		
			if (!event.ctrlKey && !event.metaKey) {
			
				var keyCode = event.keyCode;
				
				if ((keyCode >= 33 && keyCode <= 40) || (keyCode >= 63232 && keyCode <= 63235)) {
					// 33 - 40: page up/dn and arrow keys
					// 63232 - 63235: page up/dn and arrow keys on safari
					setMode("moving");
				}
				else if (keyCode == 8 || keyCode == 46 || keyCode == 127) {
					// 8: backspace
					// 46: delete
					// 127: delete
					setMode("deleting");
				}
				else if (keyCode == 13) {
					// 13: Enter
					setMode("newlines");
				}
				else if (keyCode == 27) {
					// 27: escape
					setMode("escape");
				}
				else if ((keyCode < 16 || keyCode > 20) && keyCode != 91) {
					// 16-20 are shift, etc. 
					// 91: left window key
					// I think this might be a little messed up since there are
					// a lot of nonprinting keys above 20.
					setMode("typing");
				}
			}
		};
		
		var setEventHandlers = function(){
		
			util.addEvent(wmd.panels.input, "keypress", function(event){
				// keyCode 89: y
				// keyCode 90: z
				if ((event.ctrlKey || event.metaKey) && (event.keyCode == 89 || event.keyCode == 90)) {
					event.preventDefault();
				}
			});
			
			var handlePaste = function(){
				if (global.isIE || (inputStateObj && inputStateObj.text != wmd.panels.input.value)) {
					if (timer == undefined) {
						mode = "paste";
						saveState();
						refreshState();
					}
				}
			};
			
			// pastePollInterval is specified at the beginning of this namespace.
			poller = new wmd.inputPoller(handlePaste, pastePollInterval);
			
			util.addEvent(wmd.panels.input, "keydown", handleCtrlYZ);
			util.addEvent(wmd.panels.input, "keydown", handleModeChange);
			
			util.addEvent(wmd.panels.input, "mousedown", function(){
				setMode("moving");
			});
			wmd.panels.input.onpaste = handlePaste;
			wmd.panels.input.ondrop = handlePaste;
		};
		
		var init = function(){
			setEventHandlers();
			refreshState();
			saveState();
		};
		
		this.destroy = function(){
			if (poller) {
				poller.destroy();
			}
		};
		
		init();
	};
	
	// I think my understanding of how the buttons and callbacks are stored in the array is incomplete.
	wmd.editor = function(previewRefreshCallback){
	
		if (!previewRefreshCallback) {
			previewRefreshCallback = function(){};
		}
		
		var inputBox = wmd.panels.input;
		
		var offsetHeight = 0;
		
		var editObj = this;
		
		var mainDiv;
		var mainSpan;
		
		var div; // This name is pretty ambiguous.  I should rename this.
		
		// Used to cancel recurring events from setInterval.
		var creationHandle;
		
		var undoMgr; // The undo manager
		
		// Perform the button's action.
		var doClick = function(button){
		
			inputBox.focus();
			
			if (button.textOp) {
				
				if (undoMgr) {
					undoMgr.setCommandMode();
				}
				
				var state = new wmd.TextareaState();
				
				if (!state) {
					return;
				}
				
				var chunks = state.getChunks();
				
				// Some commands launch a "modal" prompt dialog.  Javascript
				// can't really make a modal dialog box and the WMD code
				// will continue to execute while the dialog is displayed.
				// This prevents the dialog pattern I'm used to and means
				// I can't do something like this:
				//
				// var link = CreateLinkDialog();
				// makeMarkdownLink(link);
				// 
				// Instead of this straightforward method of handling a
				// dialog I have to pass any code which would execute
				// after the dialog is dismissed (e.g. link creation)
				// in a function parameter.
				//
				// Yes this is awkward and I think it sucks, but there's
				// no real workaround.  Only the image and link code
				// create dialogs and require the function pointers.
				var fixupInputArea = function(){
				
					inputBox.focus();
					
					if (chunks) {
						state.setChunks(chunks);
					}
					
					state.restore();
					previewRefreshCallback();
				};
				
				var useDefaultText = true;
				var noCleanup = button.textOp(chunks, fixupInputArea, useDefaultText);
				
				if(!noCleanup) {
					fixupInputArea();
				}
				
			}
			
			if (button.execute) {
				button.execute(editObj);
			}
		};
			
		var setUndoRedoButtonStates = function(){
			if(undoMgr){
				setupButton(document.getElementById("wmd-undo-button"), undoMgr.canUndo());
				setupButton(document.getElementById("wmd-redo-button"), undoMgr.canRedo());
			}
		};
		
		var setupButton = function(button, isEnabled) {
		
			var normalYShift = "0px";
			var disabledYShift = "-20px";
			var highlightYShift = "-40px";
			
			if(isEnabled) {
				button.style.backgroundPosition = button.XShift + " " + normalYShift;
				button.onmouseover = function(){
					this.style.backgroundPosition = this.XShift + " " + highlightYShift;
				};
							
				button.onmouseout = function(){
					this.style.backgroundPosition = this.XShift + " " + normalYShift;
				};
				
				// IE tries to select the background image "button" text (it's
				// implemented in a list item) so we have to cache the selection
				// on mousedown.
				if(global.isIE) {
					button.onmousedown =  function() { 
						wmd.ieRetardedClick = true;
						wmd.ieCachedRange = document.selection.createRange(); 
					};
				}
				
				if (!button.isHelp)
				{
					button.onclick = function() {
						if (this.onmouseout) {
							this.onmouseout();
						}
						doClick(this);
						return false;
					}
				}
			}
			else {
				button.style.backgroundPosition = button.XShift + " " + disabledYShift;
				button.onmouseover = button.onmouseout = button.onclick = function(){};
			}
		}
	
		var makeSpritedButtonRow = function(){
		 	
			var buttonBar = document.getElementById("wmd-button-bar");
 	
			var normalYShift = "0px";
			var disabledYShift = "-20px";
			var highlightYShift = "-40px";
			
			var buttonRow = document.createElement("ul");
			buttonRow.id = "wmd-button-row";
			buttonRow = buttonBar.appendChild(buttonRow);

			
			var boldButton = document.createElement("li");
			boldButton.className = "wmd-button";
			boldButton.id = "wmd-bold-button";
			boldButton.title = "Strong <strong> Ctrl+B";
			boldButton.XShift = "0px";
			boldButton.textOp = command.doBold;
			setupButton(boldButton, true);
			buttonRow.appendChild(boldButton);
			
			var italicButton = document.createElement("li");
			italicButton.className = "wmd-button";
			italicButton.id = "wmd-italic-button";
			italicButton.title = "Emphasis <em> Ctrl+I";
			italicButton.XShift = "-20px";
			italicButton.textOp = command.doItalic;
			setupButton(italicButton, true);
			buttonRow.appendChild(italicButton);

			var spacer1 = document.createElement("li");
			spacer1.className = "wmd-spacer";
			spacer1.id = "wmd-spacer1";
			buttonRow.appendChild(spacer1); 

			var linkButton = document.createElement("li");
			linkButton.className = "wmd-button";
			linkButton.id = "wmd-link-button";
			linkButton.title = "Hyperlink <a> Ctrl+L";
			linkButton.XShift = "-40px";
			linkButton.textOp = function(chunk, postProcessing, useDefaultText){
				return command.doLinkOrImage(chunk, postProcessing, false);
			};
			setupButton(linkButton, true);
			buttonRow.appendChild(linkButton);

			var quoteButton = document.createElement("li");
			quoteButton.className = "wmd-button";
			quoteButton.id = "wmd-quote-button";
			quoteButton.title = "Blockquote <blockquote> Ctrl+Q";
			quoteButton.XShift = "-60px";
			quoteButton.textOp = command.doBlockquote;
			setupButton(quoteButton, true);
			buttonRow.appendChild(quoteButton);
			
			var codeButton = document.createElement("li");
			codeButton.className = "wmd-button";
			codeButton.id = "wmd-code-button";
			codeButton.title = "Code Sample <pre><code> Ctrl+K";
			codeButton.XShift = "-80px";
			codeButton.textOp = command.doCode;
			setupButton(codeButton, true);
			buttonRow.appendChild(codeButton);

			var imageButton = document.createElement("li");
			imageButton.className = "wmd-button";
			imageButton.id = "wmd-image-button";
			imageButton.title = "Image <img> Ctrl+G";
			imageButton.XShift = "-100px";
			imageButton.textOp = function(chunk, postProcessing, useDefaultText){
				return command.doLinkOrImage(chunk, postProcessing, true);
			};
			setupButton(imageButton, true);
			buttonRow.appendChild(imageButton);

			var spacer2 = document.createElement("li");
			spacer2.className = "wmd-spacer";
			spacer2.id = "wmd-spacer2";
			buttonRow.appendChild(spacer2); 

			var olistButton = document.createElement("li");
			olistButton.className = "wmd-button";
			olistButton.id = "wmd-olist-button";
			olistButton.title = "Numbered List <ol> Ctrl+O";
			olistButton.XShift = "-120px";
			olistButton.textOp = function(chunk, postProcessing, useDefaultText){
				command.doList(chunk, postProcessing, true, useDefaultText);
			};
			setupButton(olistButton, true);
			buttonRow.appendChild(olistButton);
			
			var ulistButton = document.createElement("li");
			ulistButton.className = "wmd-button";
			ulistButton.id = "wmd-ulist-button";
			ulistButton.title = "Bulleted List <ul> Ctrl+U";
			ulistButton.XShift = "-140px";
			ulistButton.textOp = function(chunk, postProcessing, useDefaultText){
				command.doList(chunk, postProcessing, false, useDefaultText);
			};
			setupButton(ulistButton, true);
			buttonRow.appendChild(ulistButton);
			
			var headingButton = document.createElement("li");
			headingButton.className = "wmd-button";
			headingButton.id = "wmd-heading-button";
			headingButton.title = "Heading <h1>/<h2> Ctrl+H";
			headingButton.XShift = "-160px";
			headingButton.textOp = command.doHeading;
			setupButton(headingButton, true);
			buttonRow.appendChild(headingButton); 
			
			var hrButton = document.createElement("li");
			hrButton.className = "wmd-button";
			hrButton.id = "wmd-hr-button";
			hrButton.title = "Horizontal Rule <hr> Ctrl+R";
			hrButton.XShift = "-180px";
			hrButton.textOp = command.doHorizontalRule;
			setupButton(hrButton, true);
			buttonRow.appendChild(hrButton); 
			
			var spacer3 = document.createElement("li");
			spacer3.className = "wmd-spacer";
			spacer3.id = "wmd-spacer3";
			buttonRow.appendChild(spacer3); 
			
			var undoButton = document.createElement("li");
			undoButton.className = "wmd-button";
			undoButton.id = "wmd-undo-button";
			undoButton.title = "Undo - Ctrl+Z";
			undoButton.XShift = "-200px";
			undoButton.execute = function(manager){
				manager.undo();
			};
			setupButton(undoButton, true);
			buttonRow.appendChild(undoButton); 
			
			var redoButton = document.createElement("li");
			redoButton.className = "wmd-button";
			redoButton.id = "wmd-redo-button";
			redoButton.title = "Redo - Ctrl+Y";
			if (/win/.test(nav.platform.toLowerCase())) {
				redoButton.title = "Redo - Ctrl+Y";
			}
			else {
				// mac and other non-Windows platforms
				redoButton.title = "Redo - Ctrl+Shift+Z";
			}
			redoButton.XShift = "-220px";
			redoButton.execute = function(manager){
				manager.redo();
			};
			setupButton(redoButton, true);
			buttonRow.appendChild(redoButton); 
			
			var helpButton = document.createElement("li");
			helpButton.className = "wmd-button";
			helpButton.id = "wmd-help-button";
			helpButton.XShift = "-240px";
			helpButton.isHelp = true;
			
			var helpAnchor = document.createElement("a");
			helpAnchor.href = helpLink;
			helpAnchor.target = helpTarget
			helpAnchor.title = helpHoverTitle;
			helpButton.appendChild(helpAnchor);
			
			setupButton(helpButton, true);
			buttonRow.appendChild(helpButton);
			
			setUndoRedoButtonStates();
		}
		
		var setupEditor = function(){
		
			if (/\?noundo/.test(doc.location.href)) {
				wmd.nativeUndo = true;
			}
			
			if (!wmd.nativeUndo) {
				undoMgr = new wmd.undoManager(function(){
					previewRefreshCallback();
					setUndoRedoButtonStates();
				});
			}
			
			makeSpritedButtonRow();
			
			
			var keyEvent = "keydown";
			if (global.isOpera) {
				keyEvent = "keypress";
			}
			
			util.addEvent(inputBox, keyEvent, function(key){
				
				// Check to see if we have a button key and, if so execute the callback.
				if (key.ctrlKey || key.metaKey) {
			
					var keyCode = key.charCode || key.keyCode;
					var keyCodeStr = String.fromCharCode(keyCode).toLowerCase();
					
					switch(keyCodeStr) {
						case "b":
							doClick(document.getElementById("wmd-bold-button"));
							break;
						case "i":
							doClick(document.getElementById("wmd-italic-button"));
							break;
						case "l":
							doClick(document.getElementById("wmd-link-button"));
							break;
						case "q":
							doClick(document.getElementById("wmd-quote-button"));
							break;
						case "k":
							doClick(document.getElementById("wmd-code-button"));
							break;
						case "g":
							doClick(document.getElementById("wmd-image-button"));
							break;
						case "o":
							doClick(document.getElementById("wmd-olist-button"));
							break;
						case "u":
							doClick(document.getElementById("wmd-ulist-button"));
							break;
						case "h":
							doClick(document.getElementById("wmd-heading-button"));
							break;
						case "r":
							doClick(document.getElementById("wmd-hr-button"));
							break;
						case "y":
							doClick(document.getElementById("wmd-redo-button"));
							break;
						case "z":
							if(key.shiftKey) {
								doClick(document.getElementById("wmd-redo-button"));
							}
							else {
								doClick(document.getElementById("wmd-undo-button"));
							}
							break;
						default:
							return;
					}
					

					if (key.preventDefault) {
						key.preventDefault();
					}
					
					if (top.event) {
						top.event.returnValue = false;
					}
				}
			});
			
			// Auto-continue lists, code blocks and block quotes when
			// the enter key is pressed.
			util.addEvent(inputBox, "keyup", function(key){
				if (!key.shiftKey && !key.ctrlKey && !key.metaKey) {
					var keyCode = key.charCode || key.keyCode;
					// Key code 13 is Enter
					if (keyCode === 13) {
						fakeButton = {};
						fakeButton.textOp = command.doAutoindent;
						doClick(fakeButton);
					}
				}
			});
			
			// Disable ESC clearing the input textarea on IE
			if (global.isIE) {
				util.addEvent(inputBox, "keydown", function(key){
					var code = key.keyCode;
					// Key code 27 is ESC
					if (code === 27) {
						return false;
					}
				});
			}
			
			if (inputBox.form) {
				var submitCallback = inputBox.form.onsubmit;
				inputBox.form.onsubmit = function(){
					convertToHtml();
					if (submitCallback) {
						return submitCallback.apply(this, arguments);
					}
				};
			}
		};
		
		// Convert the contents of the input textarea to HTML in the output/preview panels.
		var convertToHtml = function(){
		
			if (wmd.showdown) {
				var markdownConverter = new wmd.showdown.converter();
			}
			var text = inputBox.value;
			
			var callback = function(){
				inputBox.value = text;
			};
			
			if (!/markdown/.test(wmd.wmd_env.output.toLowerCase())) {
				if (markdownConverter) {
					inputBox.value = markdownConverter.makeHtml(text);
					top.setTimeout(callback, 0);
				}
			}
			return true;
		};
		
		
		this.undo = function(){
			if (undoMgr) {
				undoMgr.undo();
			}
		};
		
		this.redo = function(){
			if (undoMgr) {
				undoMgr.redo();
			}
		};
		
		// This is pretty useless.  The setupEditor function contents
		// should just be copied here.
		var init = function(){
			setupEditor();
		};
		
		this.destroy = function(){
			if (undoMgr) {
				undoMgr.destroy();
			}
			if (div.parentNode) {
				div.parentNode.removeChild(div);
			}
			if (inputBox) {
				inputBox.style.marginTop = "";
			}
			top.clearInterval(creationHandle);
		};
		
		init();
	};
	
	// The input textarea state/contents.
	// This is used to implement undo/redo by the undo manager.
	wmd.TextareaState = function(){
	
		// Aliases
		var stateObj = this;
		var inputArea = wmd.panels.input;
		
		this.init = function() {
		
			if (!util.isVisible(inputArea)) {
				return;
			}
				
			this.setInputAreaSelectionStartEnd();
			this.scrollTop = inputArea.scrollTop;
			if (!this.text && inputArea.selectionStart || inputArea.selectionStart === 0) {
				this.text = inputArea.value;
			}
			
		}
		
		// Sets the selected text in the input box after we've performed an
		// operation.
		this.setInputAreaSelection = function(){
		
			if (!util.isVisible(inputArea)) {
				return;
			}
			
			if (inputArea.selectionStart !== undefined && !global.isOpera) {
			
				inputArea.focus();
				inputArea.selectionStart = stateObj.start;
				inputArea.selectionEnd = stateObj.end;
				inputArea.scrollTop = stateObj.scrollTop;
			}
			else if (doc.selection) {
				
				if (doc.activeElement && doc.activeElement !== inputArea) {
					return;
				}
					
				inputArea.focus();
				var range = inputArea.createTextRange();
				range.moveStart("character", -inputArea.value.length);
				range.moveEnd("character", -inputArea.value.length);
				range.moveEnd("character", stateObj.end);
				range.moveStart("character", stateObj.start);
				range.select();
			}
		};
		
		this.setInputAreaSelectionStartEnd = function(){
		
			if (inputArea.selectionStart || inputArea.selectionStart === 0) {
			
				stateObj.start = inputArea.selectionStart;
				stateObj.end = inputArea.selectionEnd;
			}
			else if (doc.selection) {
				
				stateObj.text = util.fixEolChars(inputArea.value);
				
				// IE loses the selection in the textarea when buttons are
				// clicked.  On IE we cache the selection and set a flag
				// which we check for here.
				var range;
				if(wmd.ieRetardedClick && wmd.ieCachedRange) {
					range = wmd.ieCachedRange;
					wmd.ieRetardedClick = false;
				}
				else {
					range = doc.selection.createRange();
				}

				var fixedRange = util.fixEolChars(range.text);
				var marker = "\x07";
				var markedRange = marker + fixedRange + marker;
				range.text = markedRange;
				var inputText = util.fixEolChars(inputArea.value);
					
				range.moveStart("character", -markedRange.length);
				range.text = fixedRange;

				stateObj.start = inputText.indexOf(marker);
				stateObj.end = inputText.lastIndexOf(marker) - marker.length;
					
				var len = stateObj.text.length - util.fixEolChars(inputArea.value).length;
					
				if (len) {
					range.moveStart("character", -fixedRange.length);
					while (len--) {
						fixedRange += "\n";
						stateObj.end += 1;
					}
					range.text = fixedRange;
				}
					
				this.setInputAreaSelection();
			}
		};
		
		// Restore this state into the input area.
		this.restore = function(){
		
			if (stateObj.text != undefined && stateObj.text != inputArea.value) {
				inputArea.value = stateObj.text;
			}
			this.setInputAreaSelection();
			inputArea.scrollTop = stateObj.scrollTop;
		};
		
		// Gets a collection of HTML chunks from the inptut textarea.
		this.getChunks = function(){
		
			var chunk = new wmd.Chunks();
			
			chunk.before = util.fixEolChars(stateObj.text.substring(0, stateObj.start));
			chunk.startTag = "";
			chunk.selection = util.fixEolChars(stateObj.text.substring(stateObj.start, stateObj.end));
			chunk.endTag = "";
			chunk.after = util.fixEolChars(stateObj.text.substring(stateObj.end));
			chunk.scrollTop = stateObj.scrollTop;
			
			return chunk;
		};
		
		// Sets the TextareaState properties given a chunk of markdown.
		this.setChunks = function(chunk){
		
			chunk.before = chunk.before + chunk.startTag;
			chunk.after = chunk.endTag + chunk.after;
			
			if (global.isOpera) {
				chunk.before = chunk.before.replace(/\n/g, "\r\n");
				chunk.selection = chunk.selection.replace(/\n/g, "\r\n");
				chunk.after = chunk.after.replace(/\n/g, "\r\n");
			}
			
			this.start = chunk.before.length;
			this.end = chunk.before.length + chunk.selection.length;
			this.text = chunk.before + chunk.selection + chunk.after;
			this.scrollTop = chunk.scrollTop;
		};

		this.init();
	};
	
	// before: contains all the text in the input box BEFORE the selection.
	// after: contains all the text in the input box AFTER the selection.
	wmd.Chunks = function(){
	};
	
	// startRegex: a regular expression to find the start tag
	// endRegex: a regular expresssion to find the end tag
	wmd.Chunks.prototype.findTags = function(startRegex, endRegex){
	
		var chunkObj = this;
		var regex;
		
		if (startRegex) {
			
			regex = util.extendRegExp(startRegex, "", "$");
			
			this.before = this.before.replace(regex, 
				function(match){
					chunkObj.startTag = chunkObj.startTag + match;
					return "";
				});
			
			regex = util.extendRegExp(startRegex, "^", "");
			
			this.selection = this.selection.replace(regex, 
				function(match){
					chunkObj.startTag = chunkObj.startTag + match;
					return "";
				});
		}
		
		if (endRegex) {
			
			regex = util.extendRegExp(endRegex, "", "$");
			
			this.selection = this.selection.replace(regex,
				function(match){
					chunkObj.endTag = match + chunkObj.endTag;
					return "";
				});

			regex = util.extendRegExp(endRegex, "^", "");
			
			this.after = this.after.replace(regex,
				function(match){
					chunkObj.endTag = match + chunkObj.endTag;
					return "";
				});
		}
	};
	
	// If remove is false, the whitespace is transferred
	// to the before/after regions.
	//
	// If remove is true, the whitespace disappears.
	wmd.Chunks.prototype.trimWhitespace = function(remove){
	
		this.selection = this.selection.replace(/^(\s*)/, "");
		
		if (!remove) {
			this.before += re.$1;
		}
		
		this.selection = this.selection.replace(/(\s*)$/, "");
		
		if (!remove) {
			this.after = re.$1 + this.after;
		}
	};
	
	
	wmd.Chunks.prototype.addBlankLines = function(nLinesBefore, nLinesAfter, findExtraNewlines){
	
		if (nLinesBefore === undefined) {
			nLinesBefore = 1;
		}
		
		if (nLinesAfter === undefined) {
			nLinesAfter = 1;
		}
		
		nLinesBefore++;
		nLinesAfter++;
		
		var regexText;
		var replacementText;
		
		this.selection = this.selection.replace(/(^\n*)/, "");
		this.startTag = this.startTag + re.$1;
		this.selection = this.selection.replace(/(\n*$)/, "");
		this.endTag = this.endTag + re.$1;
		this.startTag = this.startTag.replace(/(^\n*)/, "");
		this.before = this.before + re.$1;
		this.endTag = this.endTag.replace(/(\n*$)/, "");
		this.after = this.after + re.$1;
		
		if (this.before) {
		
			regexText = replacementText = "";
			
			while (nLinesBefore--) {
				regexText += "\\n?";
				replacementText += "\n";
			}
			
			if (findExtraNewlines) {
				regexText = "\\n*";
			}
			this.before = this.before.replace(new re(regexText + "$", ""), replacementText);
		}
		
		if (this.after) {
		
			regexText = replacementText = "";
			
			while (nLinesAfter--) {
				regexText += "\\n?";
				replacementText += "\n";
			}
			if (findExtraNewlines) {
				regexText = "\\n*";
			}
			
			this.after = this.after.replace(new re(regexText, ""), replacementText);
		}
	};
	
	// The markdown symbols - 4 spaces = code, > = blockquote, etc.
	command.prefixes = "(?:\\s{4,}|\\s*>|\\s*-\\s+|\\s*\\d+\\.|=|\\+|-|_|\\*|#|\\s*\\[[^\n]]+\\]:)";
	
	// Remove markdown symbols from the chunk selection.
	command.unwrap = function(chunk){
		var txt = new re("([^\\n])\\n(?!(\\n|" + command.prefixes + "))", "g");
		chunk.selection = chunk.selection.replace(txt, "$1 $2");
	};
	
	command.wrap = function(chunk, len){
		command.unwrap(chunk);
		var regex = new re("(.{1," + len + "})( +|$\\n?)", "gm");
		
		chunk.selection = chunk.selection.replace(regex, function(line, marked){
			if (new re("^" + command.prefixes, "").test(line)) {
				return line;
			}
			return marked + "\n";
		});
		
		chunk.selection = chunk.selection.replace(/\s+$/, "");
	};
	
	command.doBold = function(chunk, postProcessing, useDefaultText){
		return command.doBorI(chunk, 2, "strong text");
	};
	
	command.doItalic = function(chunk, postProcessing, useDefaultText){
		return command.doBorI(chunk, 1, "emphasized text");
	};
	
	// chunk: The selected region that will be enclosed with */**
	// nStars: 1 for italics, 2 for bold
	// insertText: If you just click the button without highlighting text, this gets inserted
	command.doBorI = function(chunk, nStars, insertText){
	
		// Get rid of whitespace and fixup newlines.
		chunk.trimWhitespace();
		chunk.selection = chunk.selection.replace(/\n{2,}/g, "\n");
		
		// Look for stars before and after.  Is the chunk already marked up?
		chunk.before.search(/(\**$)/);
		var starsBefore = re.$1;
		
		chunk.after.search(/(^\**)/);
		var starsAfter = re.$1;
		
		var prevStars = Math.min(starsBefore.length, starsAfter.length);
		
		// Remove stars if we have to since the button acts as a toggle.
		if ((prevStars >= nStars) && (prevStars != 2 || nStars != 1)) {
			chunk.before = chunk.before.replace(re("[*]{" + nStars + "}$", ""), "");
			chunk.after = chunk.after.replace(re("^[*]{" + nStars + "}", ""), "");
		}
		else if (!chunk.selection && starsAfter) {
			// It's not really clear why this code is necessary.  It just moves
			// some arbitrary stuff around.
			chunk.after = chunk.after.replace(/^([*_]*)/, "");
			chunk.before = chunk.before.replace(/(\s?)$/, "");
			var whitespace = re.$1;
			chunk.before = chunk.before + starsAfter + whitespace;
		}
		else {
		
			// In most cases, if you don't have any selected text and click the button
			// you'll get a selected, marked up region with the default text inserted.
			if (!chunk.selection && !starsAfter) {
				chunk.selection = insertText;
			}
			
			// Add the true markup.
			var markup = nStars <= 1 ? "*" : "**"; // shouldn't the test be = ?
			chunk.before = chunk.before + markup;
			chunk.after = markup + chunk.after;
		}
		
		return;
	};
	
	command.stripLinkDefs = function(text, defsToAdd){
	
		text = text.replace(/^[ ]{0,3}\[(\d+)\]:[ \t]*\n?[ \t]*<?(\S+?)>?[ \t]*\n?[ \t]*(?:(\n*)["(](.+?)[")][ \t]*)?(?:\n+|$)/gm, 
			function(totalMatch, id, link, newlines, title){	
				defsToAdd[id] = totalMatch.replace(/\s*$/, "");
				if (newlines) {
					// Strip the title and return that separately.
					defsToAdd[id] = totalMatch.replace(/["(](.+?)[")]$/, "");
					return newlines + title;
				}
				return "";
			});
		
		return text;
	};
	
	command.addLinkDef = function(chunk, linkDef){
	
		var refNumber = 0; // The current reference number
		var defsToAdd = {}; //
		// Start with a clean slate by removing all previous link definitions.
		chunk.before = command.stripLinkDefs(chunk.before, defsToAdd);
		chunk.selection = command.stripLinkDefs(chunk.selection, defsToAdd);
		chunk.after = command.stripLinkDefs(chunk.after, defsToAdd);
		
		var defs = "";
		var regex = /(\[(?:\[[^\]]*\]|[^\[\]])*\][ ]?(?:\n[ ]*)?\[)(\d+)(\])/g;
		
		var addDefNumber = function(def){
			refNumber++;
			def = def.replace(/^[ ]{0,3}\[(\d+)\]:/, "  [" + refNumber + "]:");
			defs += "\n" + def;
		};
		
		var getLink = function(wholeMatch, link, id, end){
		
			if (defsToAdd[id]) {
				addDefNumber(defsToAdd[id]);
				return link + refNumber + end;
				
			}
			return wholeMatch;
		};
		
		chunk.before = chunk.before.replace(regex, getLink);
		
		if (linkDef) {
			addDefNumber(linkDef);
		}
		else {
			chunk.selection = chunk.selection.replace(regex, getLink);
		}
		
		var refOut = refNumber;
		
		chunk.after = chunk.after.replace(regex, getLink);
		
		if (chunk.after) {
			chunk.after = chunk.after.replace(/\n*$/, "");
		}
		if (!chunk.after) {
			chunk.selection = chunk.selection.replace(/\n*$/, "");
		}
		
		chunk.after += "\n\n" + defs;
		
		return refOut;
	};
	
	command.doLinkOrImage = function(chunk, postProcessing, isImage){
	
		chunk.trimWhitespace();
		chunk.findTags(/\s*!?\[/, /\][ ]?(?:\n[ ]*)?(\[.*?\])?/);
		
		if (chunk.endTag.length > 1) {
		
			chunk.startTag = chunk.startTag.replace(/!?\[/, "");
			chunk.endTag = "";
			command.addLinkDef(chunk, null);
			
		}
		else {
		
			if (/\n\n/.test(chunk.selection)) {
				command.addLinkDef(chunk, null);
				return;
			}
			
			// The function to be executed when you enter a link and press OK or Cancel.
			// Marks up the link and adds the ref.
			var makeLinkMarkdown = function(link){
			
				if (link !== null) {
				
					chunk.startTag = chunk.endTag = "";
					var linkDef = " [999]: " + link;
					
					var num = command.addLinkDef(chunk, linkDef);
					chunk.startTag = isImage ? "![" : "[";
					chunk.endTag = "][" + num + "]";
					
					if (!chunk.selection) {
						if (isImage) {
							chunk.selection = "alt text";
						}
						else {
							chunk.selection = "link text";
						}
					}
				}
				postProcessing();
			};
			
			if (isImage) {
				util.prompt(imageDialogText, imageDefaultText, makeLinkMarkdown);
			}
			else {
				util.prompt(linkDialogText, linkDefaultText, makeLinkMarkdown);
			}
			return true;
		}
	};
	
	util.makeAPI = function(){
		wmd.wmd = {};
		wmd.wmd.editor = wmd.editor;
		wmd.wmd.previewManager = wmd.previewManager;
	};
	
	util.startEditor = function(){
	
		if (wmd.wmd_env.autostart === false) {
			util.makeAPI();
			return;
		}

		var edit;		// The editor (buttons + input + outputs) - the main object.
		var previewMgr;	// The preview manager.
		
		// Fired after the page has fully loaded.
		var loadListener = function(){
		
			wmd.panels = new wmd.PanelCollection();
			
			previewMgr = new wmd.previewManager();
			var previewRefreshCallback = previewMgr.refresh;
						
			edit = new wmd.editor(previewRefreshCallback);
			
			previewMgr.refresh(true);
			
		};
		
		util.addEvent(top, "load", loadListener);
	};
	
	wmd.previewManager = function(){
		
		var managerObj = this;
		var converter;
		var poller;
		var timeout;
		var elapsedTime;
		var oldInputText;
		var htmlOut;
		var maxDelay = 3000;
		var startType = "delayed"; // The other legal value is "manual"
		
		// Adds event listeners to elements and creates the input poller.
		var setupEvents = function(inputElem, listener){
		
			util.addEvent(inputElem, "input", listener);
			inputElem.onpaste = listener;
			inputElem.ondrop = listener;
			
			util.addEvent(inputElem, "keypress", listener);
			util.addEvent(inputElem, "keydown", listener);
			// previewPollInterval is set at the top of this file.
			poller = new wmd.inputPoller(listener, previewPollInterval);
		};
		
		var getDocScrollTop = function(){
		
			var result = 0;
			
			if (top.innerHeight) {
				result = top.pageYOffset;
			}
			else 
				if (doc.documentElement && doc.documentElement.scrollTop) {
					result = doc.documentElement.scrollTop;
				}
				else 
					if (doc.body) {
						result = doc.body.scrollTop;
					}
			
			return result;
		};
		
		var makePreviewHtml = function(){
		
			// If there are no registered preview and output panels
			// there is nothing to do.
			if (!wmd.panels.preview && !wmd.panels.output) {
				return;
			}
			
			var text = wmd.panels.input.value;
			if (text && text == oldInputText) {
				return; // Input text hasn't changed.
			}
			else {
				oldInputText = text;
			}
			
			var prevTime = new Date().getTime();
			
			if (!converter && wmd.showdown) {
				converter = new wmd.showdown.converter();
			}
			
			if (converter) {
				text = converter.makeHtml(text);
			}
			
			// Calculate the processing time of the HTML creation.
			// It's used as the delay time in the event listener.
			var currTime = new Date().getTime();
			elapsedTime = currTime - prevTime;
			
			text = text.replace("<code>", '<code class="prettyprint">');
			
			pushPreviewHtml(text);
			prettyPrint();
			
			htmlOut = text;
		};
		
		// setTimeout is already used.  Used as an event listener.
		var applyTimeout = function(){
		
			if (timeout) {
				top.clearTimeout(timeout);
				timeout = undefined;
			}
			
			if (startType !== "manual") {
			
				var delay = 0;
				
				if (startType === "delayed") {
					delay = elapsedTime;
				}
				
				if (delay > maxDelay) {
					delay = maxDelay;
				}
				timeout = top.setTimeout(makePreviewHtml, delay);
			}
		};
		
		var getScaleFactor = function(panel){
			if (panel.scrollHeight <= panel.clientHeight) {
				return 1;
			}
			return panel.scrollTop / (panel.scrollHeight - panel.clientHeight);
		};
		
		var setPanelScrollTops = function(){
		
			if (wmd.panels.preview) {
				wmd.panels.preview.scrollTop = (wmd.panels.preview.scrollHeight - wmd.panels.preview.clientHeight) * getScaleFactor(wmd.panels.preview);
				;
			}
			
			if (wmd.panels.output) {
				wmd.panels.output.scrollTop = (wmd.panels.output.scrollHeight - wmd.panels.output.clientHeight) * getScaleFactor(wmd.panels.output);
				;
			}
		};
		
		this.refresh = function(requiresRefresh){
		
			if (requiresRefresh) {
				oldInputText = "";
				makePreviewHtml();
			}
			else {
				applyTimeout();
			}
		};
		
		this.processingTime = function(){
			return elapsedTime;
		};
		
		// The output HTML
		this.output = function(){
			return htmlOut;
		};
		
		// The mode can be "manual" or "delayed"
		this.setUpdateMode = function(mode){
			startType = mode;
			managerObj.refresh();
		};
		
		var isFirstTimeFilled = true;
		
		var pushPreviewHtml = function(text){
		
			var emptyTop = position.getTop(wmd.panels.input) - getDocScrollTop();
			
			// Send the encoded HTML to the output textarea/div.
			if (wmd.panels.output) {
				// The value property is only defined if the output is a textarea.
				if (wmd.panels.output.value !== undefined) {
					wmd.panels.output.value = text;
					wmd.panels.output.readOnly = true;
				}
				// Otherwise we are just replacing the text in a div.
				// Send the HTML wrapped in <pre><code>
				else {
					var newText = text.replace(/&/g, "&amp;");
					newText = newText.replace(/</g, "&lt;");
					wmd.panels.output.innerHTML = "<pre><code>" + newText + "</code></pre>";
				}
			}
			
			if (wmd.panels.preview) {
				wmd.panels.preview.innerHTML = text;
			}
			
			setPanelScrollTops();
			
			if (isFirstTimeFilled) {
				isFirstTimeFilled = false;
				return;
			}
			
			var fullTop = position.getTop(wmd.panels.input) - getDocScrollTop();
			
			if (global.isIE) {
				top.setTimeout(function(){
					top.scrollBy(0, fullTop - emptyTop);
				}, 0);
			}
			else {
				top.scrollBy(0, fullTop - emptyTop);
			}
		};
		
		var init = function(){
		
			setupEvents(wmd.panels.input, applyTimeout);
			makePreviewHtml();
			
			if (wmd.panels.preview) {
				wmd.panels.preview.scrollTop = 0;
			}
			if (wmd.panels.output) {
				wmd.panels.output.scrollTop = 0;
			}
		};
		
		this.destroy = function(){
			if (poller) {
				poller.destroy();
			}
		};
		
		init();
	};

	// Moves the cursor to the next line and continues lists, quotes and code.
	command.doAutoindent = function(chunk, postProcessing, useDefaultText){
		
		chunk.before = chunk.before.replace(/(\n|^)[ ]{0,3}([*+-]|\d+[.])[ \t]*\n$/, "\n\n");
		chunk.before = chunk.before.replace(/(\n|^)[ ]{0,3}>[ \t]*\n$/, "\n\n");
		chunk.before = chunk.before.replace(/(\n|^)[ \t]+\n$/, "\n\n");
		
		useDefaultText = false;
		
		if(/(\n|^)[ ]{0,3}([*+-])[ \t]+.*\n$/.test(chunk.before)){
			if(command.doList){
				command.doList(chunk, postProcessing, false, true);
			}
		}
		if(/(\n|^)[ ]{0,3}(\d+[.])[ \t]+.*\n$/.test(chunk.before)){
			if(command.doList){
				command.doList(chunk, postProcessing, true, true);
			}
		}
		if(/(\n|^)[ ]{0,3}>[ \t]+.*\n$/.test(chunk.before)){
			if(command.doBlockquote){
				command.doBlockquote(chunk, postProcessing, useDefaultText);
			}
		}
		if(/(\n|^)(\t|[ ]{4,}).*\n$/.test(chunk.before)){
			if(command.doCode){
				command.doCode(chunk, postProcessing, useDefaultText);
			}
		}
	};
	
	command.doBlockquote = function(chunk, postProcessing, useDefaultText){
		
		chunk.selection = chunk.selection.replace(/^(\n*)([^\r]+?)(\n*)$/,
			function(totalMatch, newlinesBefore, text, newlinesAfter){
				chunk.before += newlinesBefore;
				chunk.after = newlinesAfter + chunk.after;
				return text;
			});
			
		chunk.before = chunk.before.replace(/(>[ \t]*)$/,
			function(totalMatch, blankLine){
				chunk.selection = blankLine + chunk.selection;
				return "";
			});
		
		var defaultText = useDefaultText ? "Blockquote" : "";
		chunk.selection = chunk.selection.replace(/^(\s|>)+$/ ,"");
		chunk.selection = chunk.selection || defaultText;
		
		if(chunk.before){
			chunk.before = chunk.before.replace(/\n?$/,"\n");
		}
		if(chunk.after){
			chunk.after = chunk.after.replace(/^\n?/,"\n");
		}
		
		chunk.before = chunk.before.replace(/(((\n|^)(\n[ \t]*)*>(.+\n)*.*)+(\n[ \t]*)*$)/,
			function(totalMatch){
				chunk.startTag = totalMatch;
				return "";
			});
			
		chunk.after = chunk.after.replace(/^(((\n|^)(\n[ \t]*)*>(.+\n)*.*)+(\n[ \t]*)*)/,
			function(totalMatch){
				chunk.endTag = totalMatch;
				return "";
			});
		
		var replaceBlanksInTags = function(useBracket){
			
			var replacement = useBracket ? "> " : "";
			
			if(chunk.startTag){
				chunk.startTag = chunk.startTag.replace(/\n((>|\s)*)\n$/,
					function(totalMatch, markdown){
						return "\n" + markdown.replace(/^[ ]{0,3}>?[ \t]*$/gm, replacement) + "\n";
					});
			}
			if(chunk.endTag){
				chunk.endTag = chunk.endTag.replace(/^\n((>|\s)*)\n/,
					function(totalMatch, markdown){
						return "\n" + markdown.replace(/^[ ]{0,3}>?[ \t]*$/gm, replacement) + "\n";
					});
			}
		};
		
		if(/^(?![ ]{0,3}>)/m.test(chunk.selection)){
			command.wrap(chunk, wmd.wmd_env.lineLength - 2);
			chunk.selection = chunk.selection.replace(/^/gm, "> ");
			replaceBlanksInTags(true);
			chunk.addBlankLines();
		}
		else{
			chunk.selection = chunk.selection.replace(/^[ ]{0,3}> ?/gm, "");
			command.unwrap(chunk);
			replaceBlanksInTags(false);
			
			if(!/^(\n|^)[ ]{0,3}>/.test(chunk.selection) && chunk.startTag){
				chunk.startTag = chunk.startTag.replace(/\n{0,2}$/, "\n\n");
			}
			
			if(!/(\n|^)[ ]{0,3}>.*$/.test(chunk.selection) && chunk.endTag){
				chunk.endTag=chunk.endTag.replace(/^\n{0,2}/, "\n\n");
			}
		}
		
		if(!/\n/.test(chunk.selection)){
			chunk.selection = chunk.selection.replace(/^(> *)/,
			function(wholeMatch, blanks){
				chunk.startTag += blanks;
				return "";
			});
		}
	};

	command.doCode = function(chunk, postProcessing, useDefaultText){
		
		var hasTextBefore = /\S[ ]*$/.test(chunk.before);
		var hasTextAfter = /^[ ]*\S/.test(chunk.after);
		
		// Use 'four space' markdown if the selection is on its own
		// line or is multiline.
		if((!hasTextAfter && !hasTextBefore) || /\n/.test(chunk.selection)){
			
			chunk.before = chunk.before.replace(/[ ]{4}$/,
				function(totalMatch){
					chunk.selection = totalMatch + chunk.selection;
					return "";
				});
				
			var nLinesBefore = 1;
			var nLinesAfter = 1;
			
			
			if(/\n(\t|[ ]{4,}).*\n$/.test(chunk.before) || chunk.after === ""){
				nLinesBefore = 0; 
			}
			if(/^\n(\t|[ ]{4,})/.test(chunk.after)){
				nLinesAfter = 0; // This needs to happen on line 1
			}
			
			chunk.addBlankLines(nLinesBefore, nLinesAfter);
			
			if(!chunk.selection){
				chunk.startTag = "    ";
				chunk.selection = useDefaultText ? "enter code here" : "";
			}
			else {
				if(/^[ ]{0,3}\S/m.test(chunk.selection)){
					chunk.selection = chunk.selection.replace(/^/gm, "    ");
				}
				else{
					chunk.selection = chunk.selection.replace(/^[ ]{4}/gm, "");
				}
			}
		}
		else{
			// Use backticks (`) to delimit the code block.
			
			chunk.trimWhitespace();
			chunk.findTags(/`/, /`/);
			
			if(!chunk.startTag && !chunk.endTag){
				chunk.startTag = chunk.endTag="`";
				if(!chunk.selection){
					chunk.selection = useDefaultText ? "enter code here" : "";
				}
			}
			else if(chunk.endTag && !chunk.startTag){
				chunk.before += chunk.endTag;
				chunk.endTag = "";
			}
			else{
				chunk.startTag = chunk.endTag="";
			}
		}
	};
	
	command.doList = function(chunk, postProcessing, isNumberedList, useDefaultText){
				
		// These are identical except at the very beginning and end.
		// Should probably use the regex extension function to make this clearer.
		var previousItemsRegex = /(\n|^)(([ ]{0,3}([*+-]|\d+[.])[ \t]+.*)(\n.+|\n{2,}([*+-].*|\d+[.])[ \t]+.*|\n{2,}[ \t]+\S.*)*)\n*$/;
		var nextItemsRegex = /^\n*(([ ]{0,3}([*+-]|\d+[.])[ \t]+.*)(\n.+|\n{2,}([*+-].*|\d+[.])[ \t]+.*|\n{2,}[ \t]+\S.*)*)\n*/;
		
		// The default bullet is a dash but others are possible.
		// This has nothing to do with the particular HTML bullet,
		// it's just a markdown bullet.
		var bullet = "-";
		
		// The number in a numbered list.
		var num = 1;
		
		// Get the item prefix - e.g. " 1. " for a numbered list, " - " for a bulleted list.
		var getItemPrefix = function(){
			var prefix;
			if(isNumberedList){
				prefix = " " + num + ". ";
				num++;
			}
			else{
				prefix = " " + bullet + " ";
			}
			return prefix;
		};
		
		// Fixes the prefixes of the other list items.
		var getPrefixedItem = function(itemText){
		
			// The numbering flag is unset when called by autoindent.
			if(isNumberedList === undefined){
				isNumberedList = /^\s*\d/.test(itemText);
			}
			
			// Renumber/bullet the list element.
			itemText = itemText.replace(/^[ ]{0,3}([*+-]|\d+[.])\s/gm,
				function( _ ){
					return getItemPrefix();
				});
				
			return itemText;
		};
		
		chunk.findTags(/(\n|^)*[ ]{0,3}([*+-]|\d+[.])\s+/, null);
		
		if(chunk.before && !/\n$/.test(chunk.before) && !/^\n/.test(chunk.startTag)){
			chunk.before += chunk.startTag;
			chunk.startTag = "";
		}
		
		if(chunk.startTag){
			
			var hasDigits = /\d+[.]/.test(chunk.startTag);
			chunk.startTag = "";
			chunk.selection = chunk.selection.replace(/\n[ ]{4}/g, "\n");
			command.unwrap(chunk);
			chunk.addBlankLines();
			
			if(hasDigits){
				// Have to renumber the bullet points if this is a numbered list.
				chunk.after = chunk.after.replace(nextItemsRegex, getPrefixedItem);
			}
			if(isNumberedList == hasDigits){
				return;
			}
		}
		
		var nLinesBefore = 1;
		
		chunk.before = chunk.before.replace(previousItemsRegex,
			function(itemText){
				if(/^\s*([*+-])/.test(itemText)){
					bullet = re.$1;
				}
				nLinesBefore = /[^\n]\n\n[^\n]/.test(itemText) ? 1 : 0;
				return getPrefixedItem(itemText);
			});
			
		if(!chunk.selection){
			chunk.selection = useDefaultText ? "List item" : " ";
		}
		
		var prefix = getItemPrefix();
		
		var nLinesAfter = 1;
		
		chunk.after = chunk.after.replace(nextItemsRegex,
			function(itemText){
				nLinesAfter = /[^\n]\n\n[^\n]/.test(itemText) ? 1 : 0;
				return getPrefixedItem(itemText);
			});
			
		chunk.trimWhitespace(true);
		chunk.addBlankLines(nLinesBefore, nLinesAfter, true);
		chunk.startTag = prefix;
		var spaces = prefix.replace(/./g, " ");
		command.wrap(chunk, wmd.wmd_env.lineLength - spaces.length);
		chunk.selection = chunk.selection.replace(/\n/g, "\n" + spaces);
		
	};
	
	command.doHeading = function(chunk, postProcessing, useDefaultText){
		
		// Remove leading/trailing whitespace and reduce internal spaces to single spaces.
		chunk.selection = chunk.selection.replace(/\s+/g, " ");
		chunk.selection = chunk.selection.replace(/(^\s+|\s+$)/g, "");
		
		// If we clicked the button with no selected text, we just
		// make a level 2 hash header around some default text.
		if(!chunk.selection){
			chunk.startTag = "## ";
			chunk.selection = "Heading";
			chunk.endTag = " ##";
			return;
		}
		
		var headerLevel = 0;		// The existing header level of the selected text.
		
		// Remove any existing hash heading markdown and save the header level.
		chunk.findTags(/#+[ ]*/, /[ ]*#+/);
		if(/#+/.test(chunk.startTag)){
			headerLevel = re.lastMatch.length;
		}
		chunk.startTag = chunk.endTag = "";
		
		// Try to get the current header level by looking for - and = in the line
		// below the selection.
		chunk.findTags(null, /\s?(-+|=+)/);
		if(/=+/.test(chunk.endTag)){
			headerLevel = 1;
		}
		if(/-+/.test(chunk.endTag)){
			headerLevel = 2;
		}
		
		// Skip to the next line so we can create the header markdown.
		chunk.startTag = chunk.endTag = "";
		chunk.addBlankLines(1, 1);

		// We make a level 2 header if there is no current header.
		// If there is a header level, we substract one from the header level.
		// If it's already a level 1 header, it's removed.
		var headerLevelToCreate = headerLevel == 0 ? 2 : headerLevel - 1;
		
		if(headerLevelToCreate > 0){
			
			// The button only creates level 1 and 2 underline headers.
			// Why not have it iterate over hash header levels?  Wouldn't that be easier and cleaner?
			var headerChar = headerLevelToCreate >= 2 ? "-" : "=";
			var len = chunk.selection.length;
			if(len > wmd.wmd_env.lineLength){
				len = wmd.wmd_env.lineLength;
			}
			chunk.endTag = "\n";
			while(len--){
				chunk.endTag += headerChar;
			}
		}
	};	
	
	command.doHorizontalRule = function(chunk, postProcessing, useDefaultText){
		chunk.startTag = "----------\n";
		chunk.selection = "";
		chunk.addBlankLines(2, 1, true);
	}
};


Attacklab.wmd_env = {};
Attacklab.account_options = {};
Attacklab.wmd_defaults = {version:1, output:"HTML", lineLength:40, delayLoad:false};

if(!Attacklab.wmd)
{
	Attacklab.wmd = function()
	{
		Attacklab.loadEnv = function()
		{
			var mergeEnv = function(env)
			{
				if(!env)
				{
					return;
				}
			
				for(var key in env)
				{
					Attacklab.wmd_env[key] = env[key];
				}
			};
			
			mergeEnv(Attacklab.wmd_defaults);
			mergeEnv(Attacklab.account_options);
			mergeEnv(top["wmd_options"]);
			Attacklab.full = true;
			
			var defaultButtons = "bold italic link blockquote code image ol ul heading hr";
			Attacklab.wmd_env.buttons = Attacklab.wmd_env.buttons || defaultButtons;
		};
		Attacklab.loadEnv();

	};
	
	Attacklab.wmd();
	Attacklab.wmdBase();
	Attacklab.Util.startEditor();
};

