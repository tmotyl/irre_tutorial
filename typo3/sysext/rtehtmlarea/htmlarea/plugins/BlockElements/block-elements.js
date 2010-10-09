/***************************************************************
*  Copyright notice
*
*  (c) 2007-2010 Stanislas Rolland <typo3(arobas)sjbr.ca>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/*
 * BlockElements Plugin for TYPO3 htmlArea RTE
 *
 * TYPO3 SVN ID: $Id: block-elements.js 7966 2010-06-19 14:19:13Z stan $
 */
HTMLArea.BlockElements = HTMLArea.Plugin.extend({
		
	constructor : function(editor, pluginName) {
		this.base(editor, pluginName);
	},
	
	/*
	 * This function gets called by the class constructor
	 */
	configurePlugin : function (editor) {
		
		/*
		 * Setting up some properties from PageTSConfig
		 */
		this.buttonsConfiguration = this.editorConfiguration.buttons;
		if (this.buttonsConfiguration.blockstyle) {
			this.tags = this.editorConfiguration.buttons.blockstyle.tags;
		}
		this.useClass = {
			Indent		: "indent",
			JustifyLeft	: "align-left",
			JustifyCenter	: "align-center",
			JustifyRight	: "align-right",
			JustifyFull	: "align-justify"
		};
		this.useAlignAttribute = false;
		for (var buttonId in this.useClass) {
			if (this.useClass.hasOwnProperty(buttonId)) {
				if (this.editorConfiguration.buttons[this.buttonList[buttonId][2]]) {
					this.useClass[buttonId] = this.editorConfiguration.buttons[this.buttonList[buttonId][2]].useClass ? this.editorConfiguration.buttons[this.buttonList[buttonId][2]].useClass : this.useClass[buttonId];
					if (buttonId === "Indent") {
						this.useBlockquote = this.editorConfiguration.buttons.indent.useBlockquote ? this.editorConfiguration.buttons.indent.useBlockquote : false;
					} else {
						if (this.editorConfiguration.buttons[this.buttonList[buttonId][2]].useAlignAttribute) {
							this.useAlignAttribute = true;
						}
					}
				}
			}
		}
		this.allowedAttributes = new Array("id", "title", "lang", "xml:lang", "dir", "class");
		if (Ext.isIE) {
			this.addAllowedAttribute("className");
		}
		this.indentedList = null;
			// Standard block formating items
		var standardElements = new Array("address", "blockquote", "div", "h1", "h2", "h3", "h4", "h5", "h6", "p", "pre");
		this.standardBlockElements = new RegExp( "^(" + standardElements.join("|") + ")$", "i");
			// Process block formating customization configuration
		this.formatBlockItems = {};
		if (this.buttonsConfiguration
			&& this.buttonsConfiguration.formatblock
			&& this.buttonsConfiguration.formatblock.items) {
				this.formatBlockItems = this.buttonsConfiguration.formatblock.items;
		}
			// Build lists of mutually exclusive class names
		for (var tagName in this.formatBlockItems) {
			if (this.formatBlockItems.hasOwnProperty(tagName) && this.formatBlockItems[tagName].tagName && this.formatBlockItems[tagName].addClass) {
				if (!this.formatBlockItems[this.formatBlockItems[tagName].tagName]) {
					this.formatBlockItems[this.formatBlockItems[tagName].tagName] = {};
				}
				if (!this.formatBlockItems[this.formatBlockItems[tagName].tagName].classList) {
					this.formatBlockItems[this.formatBlockItems[tagName].tagName].classList = new Array();
				}
				this.formatBlockItems[this.formatBlockItems[tagName].tagName].classList.push(this.formatBlockItems[tagName].addClass);
			}
		}
		for (var tagName in this.formatBlockItems) {
			if (this.formatBlockItems.hasOwnProperty(tagName) && this.formatBlockItems[tagName].classList) {
				this.formatBlockItems[tagName].classList = new RegExp( "^(" + this.formatBlockItems[tagName].classList.join("|") + ")$");
			}
		}
		
		/*
		 * Registering plugin "About" information
		 */
		var pluginInformation = {
			version		: "1.4",
			developer	: "Stanislas Rolland",
			developerUrl	: "http://www.sjbr.ca/",
			copyrightOwner	: "Stanislas Rolland",
			sponsor		: this.localize("Technische Universitat Ilmenau"),
			sponsorUrl	: "http://www.tu-ilmenau.de/",
			license		: "GPL"
		};
		this.registerPluginInformation(pluginInformation);
		
		/*
		 * Registering the dropdown list
		 */
		var buttonId = "FormatBlock";
		var dropDownConfiguration = {
			id: buttonId,
			tooltip: this.localize(buttonId + "-Tooltip"),
			options: this.buttonsConfiguration.formatblock ? this.buttonsConfiguration.formatblock.options : [],
			action: "onChange"
		};
		if (this.buttonsConfiguration.formatblock) {
			dropDownConfiguration.width = this.buttonsConfiguration.formatblock.width ? parseInt(this.buttonsConfiguration.formatblock.width, 10) : 200;
			if (this.buttonsConfiguration.formatblock.listWidth) {
				dropDownConfiguration.listWidth = parseInt(this.buttonsConfiguration.formatblock.listWidth, 10);
			}
			if (this.buttonsConfiguration.formatblock.maxHeight) {
				dropDownConfiguration.maxHeight = parseInt(this.buttonsConfiguration.formatblock.maxHeight, 10);
			}
		}
		this.registerDropDown(dropDownConfiguration);
		/*
		 * Establishing the list of allowed block elements
		 */
		var blockElements = new Array();
		Ext.each(dropDownConfiguration.options, function (option) {
			if (option[1] != 'none') {
				blockElements.push(option[1]);
			}
		});
		this.allowedBlockElements = new RegExp( "^(" + blockElements.join("|") + ")$", "i");

		/*
		 * Registering hot keys for the dropdown list items
		 */
		Ext.each(blockElements, function (blockElement) {
			var configuredHotKey = this.defaultHotKeys[blockElement];
			if (this.editorConfiguration.buttons.formatblock
					&& this.editorConfiguration.buttons.formatblock.items
					&& this.editorConfiguration.buttons.formatblock.items[blockElement]
					&& this.editorConfiguration.buttons.formatblock.items[blockElement].hotKey) {
				configuredHotKey = this.editorConfiguration.buttons.formatblock.items[blockElement].hotKey;
			}
			if (configuredHotKey) {
				var hotKeyConfiguration = {
					id		: configuredHotKey,
					cmd		: buttonId,
					element		: blockElement
				};
				this.registerHotKey(hotKeyConfiguration);
			}
		}, this);
		/*
		 * Registering the buttons
		 */
		for (var buttonId in this.buttonList) {
			if (this.buttonList.hasOwnProperty(buttonId)) {
				var button = this.buttonList[buttonId];
				var buttonConfiguration = {
					id		: buttonId,
					tooltip		: this.localize(buttonId + '-Tooltip') || this.localize(button[2]),
					iconCls		: 'htmlarea-action-' + button[3],
					contextMenuTitle: this.localize(buttonId + '-contextMenuTitle'),
					action		: 'onButtonPress',
					hotKey		: ((this.buttonsConfiguration[button[2]] && this.buttonsConfiguration[button[2]].hotKey) ? this.buttonsConfiguration[button[2]].hotKey : (button[1] ? button[1] : null))
				};
				this.registerButton(buttonConfiguration);
			}
		}
		return true;
	},
	/*
	 * The list of buttons added by this plugin
	 */
	buttonList: {
		Indent			: [null, 'TAB', 'indent', 'indent'],
		Outdent			: [null, 'SHIFT-TAB', 'outdent', 'outdent'],
		Blockquote		: [null, null, 'blockquote', 'blockquote'],
		InsertParagraphBefore	: [null, null, 'insertparagraphbefore', 'paragraph-insert-before'],
		InsertParagraphAfter	: [null, null, 'insertparagraphafter', 'paragraph-insert-after'],
		JustifyLeft		: [null, 'l', 'left', 'justify-left'],
		JustifyCenter		: [null, 'e', 'center', 'justify-center'],
		JustifyRight		: [null, 'r', 'right', 'justify-right'],
		JustifyFull		: [null, 'j', 'justifyfull', 'justify-full'],
		InsertOrderedList	: [null, null, 'orderedlist', 'ordered-list'],
		InsertUnorderedList	: [null, null, 'unorderedlist', 'unordered-list'],
		InsertHorizontalRule	: [null, null, 'inserthorizontalrule', 'horizontal-rule-insert']
	},
	/*
	 * The list of hotkeys associated with block elements and registered by default by this plugin
	 */
	defaultHotKeys: {
			'p'	: 'n',
			'h1'	: '1',
			'h2'	: '2',
			'h3'	: '3',
			'h4'	: '4',
			'h5'	: '5',
			'h6'	: '6'
	},
	/*
	 * The function returns true if the type of block element is allowed in the current configuration
	 */
	isAllowedBlockElement : function (blockName) {
		return this.allowedBlockElements.test(blockName);
	},
	
	/*
	 * This function adds an attribute to the array of attributes allowed on block elements
	 *
	 * @param	string	attribute: the name of the attribute to be added to the array
	 *
	 * @return	void
	 */
	addAllowedAttribute : function (attribute) {
		this.allowedAttributes.push(attribute);
	},
	
	/*
	 * This function gets called when some block element was selected in the drop-down list
	 */
	onChange : function (editor, combo, record, index) {
		this.applyBlockElement(combo.itemId, combo.getValue());
	},
	
	applyBlockElement : function(buttonId, blockElement) {
		var tagName = blockElement;
		var className = null;
		if (this.formatBlockItems[tagName]) {
			if (this.formatBlockItems[tagName].addClass) {
				className = this.formatBlockItems[tagName].addClass;
			}
			if (this.formatBlockItems[tagName].tagName) {
				tagName = this.formatBlockItems[tagName].tagName;
			}
		}
		if (this.standardBlockElements.test(tagName) || tagName == "none") {
			switch (tagName) {
				case "blockquote" :
					this.onButtonPress(this.editor, "Blockquote", null, className);
					break;
				case "div"     :
				case "address" :
				case "none"    :
					this.onButtonPress(this.editor, tagName, null, className);
					break;
				default	:
					var element = tagName;
					if (Ext.isIE) {
						element = "<" + element + ">";
					}
					this.editor.focus();
					if (Ext.isWebKit) {
						if (!this.editor._doc.body.hasChildNodes()) {
							this.editor._doc.body.appendChild((this.editor._doc.createElement("br")));
						}
							// WebKit sometimes leaves empty block at the end of the selection
						this.editor._doc.body.normalize();
					}
					try {
						this.editor._doc.execCommand(buttonId, false, element);
					} catch(e) {
						this.appendToLog("applyBlockElement", e + "\n\nby execCommand(" + buttonId + ");");
					}
					this.addClassOnBlockElements(tagName, className);
			}
		}
	},
	
	/*
	 * This function gets called when a button was pressed.
	 *
	 * @param	object		editor: the editor instance
	 * @param	string		id: the button id or the key
	 * @param	object		target: the target element of the contextmenu event, when invoked from the context menu
	 * @param	string		className: the className to be assigned to the element
	 *
	 * @return	boolean		false if action is completed
	 */
	onButtonPress : function (editor, id, target, className) {
			// Could be a button or its hotkey
		var buttonId = this.translateHotKey(id);
		buttonId = buttonId ? buttonId : id;
		this.editor.focus();
		var selection = editor._getSelection();
		var range = editor._createRange(selection);
		var statusBarSelection = this.editor.statusBar ? this.editor.statusBar.getSelection() : null;
		var parentElement = statusBarSelection ? statusBarSelection : this.editor.getParentElement(selection, range);
		if (target) {
			parentElement = target;
		}
		while (parentElement && (!HTMLArea.isBlockElement(parentElement) || /^li$/i.test(parentElement.nodeName))) {
			parentElement = parentElement.parentNode;
		}
		var blockAncestors = this.getBlockAncestors(parentElement);
		var tableCell = null;
		if (id === "TAB" || id === "SHIFT-TAB") {
			for (var i = blockAncestors.length; --i >= 0;) {
				if (/^(td|th)$/i.test(blockAncestors[i].nodeName)) {
					tableCell = blockAncestors[i];
					break;
				}
			}
		}
		var fullNodeTextSelected = (!Ext.isIE && parentElement.textContent === range.toString()) || (Ext.isIE && parentElement.innerText === range.text);
		switch (buttonId) {
			case "Indent" :
				if (/^(ol|ul)$/i.test(parentElement.nodeName) && !(fullNodeTextSelected && !/^(li)$/i.test(parentElement.parentNode.nodeName))) {
					if (Ext.isOpera) {
						try {
							this.editor._doc.execCommand(buttonId, false, null);
						} catch(e) {
							this.appendToLog("onButtonPress", e + "\n\nby execCommand(" + buttonId + ");");
						}
						this.indentedList = parentElement;
						this.makeNestedList(parentElement);
						this.editor.selectNodeContents(this.indentedList.lastChild, false);
					} else {
						this.indentSelectedListElements(parentElement, range);
					}
				} else if (tableCell) {
	
					var tablePart = tableCell.parentNode.parentNode;
						// Get next cell in same table part
					var nextCell = tableCell.nextSibling ? tableCell.nextSibling : (tableCell.parentNode.nextSibling ? tableCell.parentNode.nextSibling.cells[0] : null);
						// Next cell is in other table part
					if (!nextCell) {
						switch (tablePart.nodeName.toLowerCase()) {
						    case "thead":
							nextCell = tablePart.parentNode.tBodies[0].rows[0].cells[0];
							break;
						    case "tbody":
							nextCell = tablePart.nextSibling ? tablePart.nextSibling.rows[0].cells[0] : null;
							break;
						    case "tfoot":
							this.editor.selectNodeContents(tablePart.parentNode.lastChild.lastChild.lastChild, true);
						}
					}
					if (!nextCell) {
						if (this.editor.plugins.TableOperations) {
							this.editor.plugins.TableOperations.instance.onButtonPress(this.editor, "TO-row-insert-under");
						} else {
							nextCell = tablePart.parentNode.rows[0].cells[0];
						}
					}
					if (nextCell) {
						if (Ext.isOpera && !nextCell.hasChildNodes()) {
							nextCell.appendChild(this.editor.document.createElement('br'));
						}
						this.editor.selectNodeContents(nextCell, true);
					}
				} else  if (this.useBlockquote) {
					try {
						this.editor._doc.execCommand(buttonId, false, null);
					} catch(e) {
						this.appendToLog("onButtonPress", e + "\n\nby execCommand(" + buttonId + ");");
					}
				} else if (this.isAllowedBlockElement("div")) {
					if (/^div$/i.test(parentElement.nodeName) && !HTMLArea._hasClass(parentElement, this.useClass[buttonId])) {
						HTMLArea._addClass(parentElement, this.useClass[buttonId]);
					} else if (!/^div$/i.test(parentElement.nodeName) && /^div$/i.test(parentElement.parentNode.nodeName) && !HTMLArea._hasClass(parentElement.parentNode, this.useClass[buttonId])) {
						HTMLArea._addClass(parentElement.parentNode, this.useClass[buttonId]);
					} else {
						var bookmark = this.editor.getBookmark(range);
						var newBlock = this.wrapSelectionInBlockElement("div", this.useClass[buttonId], null, true);
						this.editor.selectRange(this.editor.moveToBookmark(bookmark));
					}
				} else {
					this.addClassOnBlockElements(buttonId);
				}
				break;
			case "Outdent" :
				if (/^(ol|ul)$/i.test(parentElement.nodeName) && !HTMLArea._hasClass(parentElement, this.useClass.Indent)) {
					if (/^(li)$/i.test(parentElement.parentNode.nodeName)) {
						if (Ext.isOpera) {
							try {
								this.editor._doc.execCommand(buttonId, false, null);
							} catch(e) {
								this.appendToLog("onButtonPress", e + "\n\nby execCommand(" + buttonId + ");");
							}
						} else {
							this.outdentSelectedListElements(parentElement, range);
						}
					}
				} else if (tableCell) {
					var previousCell = tableCell.previousSibling ? tableCell.previousSibling : (tableCell.parentNode.previousSibling ? tableCell.parentNode.previousSibling.lastChild : null);
					if (!previousCell) {
						var table = tableCell.parentNode.parentNode.parentNode;
						var tablePart = tableCell.parentNode.parentNode.nodeName.toLowerCase();
						switch (tablePart) {
							case "tbody":
								if (table.tHead) {
									previousCell = table.tHead.rows[table.tHead.rows.length-1].cells[table.tHead.rows[table.tHead.rows.length-1].cells.length-1];
									break;
								}
							case "thead":
								if (table.tFoot) {
									previousCell = table.tFoot.rows[table.tFoot.rows.length-1].cells[table.tFoot.rows[table.tFoot.rows.length-1].cells.length-1];
									break;
								}
							case "tfoot":
								previousCell = table.tBodies[table.tBodies.length-1].rows[table.tBodies[table.tBodies.length-1].rows.length-1].cells[table.tBodies[table.tBodies.length-1].rows[table.tBodies[table.tBodies.length-1].rows.length-1].cells.length-1];
						}
					}
					if (previousCell) {
						if (Ext.isOpera && !previousCell.hasChildNodes()) {
							previousCell.appendChild(this.editor.document.createElement('br'));
						}
						this.editor.selectNodeContents(previousCell, true);
					}
				} else  if (this.useBlockquote) {
					try {
						this.editor._doc.execCommand(buttonId, false, null);
					} catch(e) {
						this.appendToLog("onButtonPress", e + "\n\nby execCommand(" + buttonId + ");");
					}
				} else if (this.isAllowedBlockElement("div")) {
					for (var i = blockAncestors.length; --i >= 0;) {
						if (HTMLArea._hasClass(blockAncestors[i], this.useClass.Indent)) {
							var bookmark = this.editor.getBookmark(range);
							var newBlock = this.wrapSelectionInBlockElement("div", false, blockAncestors[i]);
								// If not directly under the div, we need to backtrack
							if (newBlock.parentNode !== blockAncestors[i]) {
								var parent = newBlock.parentNode;
								this.removeElement(newBlock);
								while (parent.parentNode !== blockAncestors[i]) {
									parent = parent.parentNode;
								}
								blockAncestors[i].insertBefore(newBlock, parent);
								newBlock.appendChild(parent);
							}
							newBlock.className = blockAncestors[i].className;
							HTMLArea._removeClass(newBlock, this.useClass.Indent);
							if (!newBlock.previousSibling) {
								while (newBlock.hasChildNodes()) {
									if (newBlock.firstChild.nodeType == 1) {
										newBlock.firstChild.className = newBlock.className;
									}
									blockAncestors[i].parentNode.insertBefore(newBlock.firstChild, blockAncestors[i]);
								}
							} else if (!newBlock.nextSibling) {
								if (blockAncestors[i].nextSibling) {
									while (newBlock.hasChildNodes()) {
										if (newBlock.firstChild.nodeType == 1) {
											newBlock.lastChild.className = newBlock.className;
										}
										blockAncestors[i].parentNode.insertBefore(newBlock.lastChild, blockAncestors[i].nextSibling);
									}
								} else {
									while (newBlock.hasChildNodes()) {
										if (newBlock.firstChild.nodeType == 1) {
											newBlock.firstChild.className = newBlock.className;
										}
										blockAncestors[i].parentNode.appendChild(newBlock.firstChild);
									}
								}
							} else {
								var clone = blockAncestors[i].cloneNode(false);
								if (blockAncestors[i].nextSibling) {
									blockAncestors[i].parentNode.insertBefore(clone, blockAncestors[i].nextSibling);
								} else {
									blockAncestors[i].parentNode.appendChild(clone);
								}
								while (newBlock.nextSibling) {
									clone.appendChild(newBlock.nextSibling);
								}
								while (newBlock.hasChildNodes()) {
									if (newBlock.firstChild.nodeType == 1) {
										newBlock.firstChild.className = newBlock.className;
									}
									blockAncestors[i].parentNode.insertBefore(newBlock.firstChild, clone);
								}
							}
							blockAncestors[i].removeChild(newBlock);
							if (!blockAncestors[i].hasChildNodes()) {
								blockAncestors[i].parentNode.removeChild(blockAncestors[i]);
							}
							this.editor.selectRange(this.editor.moveToBookmark(bookmark));
							break;
						}
					}
				} else {
					this.addClassOnBlockElements(buttonId);
				}
				break;
			case "InsertParagraphBefore" :
			case "InsertParagraphAfter"  :
				this.insertParagraph(buttonId === "InsertParagraphAfter");
				break;
			case "Blockquote" :
				var commandState = false;
				for (var i = blockAncestors.length; --i >= 0;) {
					if (/^blockquote$/i.test(blockAncestors[i].nodeName)) {
						commandState = true;
						this.removeElement(blockAncestors[i]);
						break;
					}
				}
				if (!commandState) {
					var bookmark = this.editor.getBookmark(range);
					var newBlock = this.wrapSelectionInBlockElement("blockquote", className, null, true);
					this.editor.selectRange(this.editor.moveToBookmark(bookmark));
				}
				break;
			case "address" :
			case "div"     :
				var bookmark = this.editor.getBookmark(range);
				var newBlock = this.wrapSelectionInBlockElement(buttonId, className, null, true);
				this.editor.selectRange(this.editor.moveToBookmark(bookmark));
				break;
			case "JustifyLeft"   :
			case "JustifyCenter" :
			case "JustifyRight"  :
			case "JustifyFull"   :
				if (this.useAlignAttribute) {
					try {
						this.editor._doc.execCommand(buttonId, false, null);
					} catch(e) {
						this.appendToLog("onButtonPress", e + "\n\nby execCommand(" + buttonId + ");");
					}
				} else {
					this.addClassOnBlockElements(buttonId);
				}
				break;
			case "InsertOrderedList":
			case "InsertUnorderedList":
				this.insertList(buttonId, parentElement);
				break;
			case "InsertHorizontalRule":
				this.editor.execCommand('InsertHorizontalRule');
				break;
			case "none" :
				if (this.isAllowedBlockElement(parentElement.nodeName)) {
					this.removeElement(parentElement);
				}
				break;
			default	:
				break;
		}
		return false;
	},
	
	/*
	* Get the block ancestors of an element within a given block
	*/
	getBlockAncestors : function(element, withinBlock) {
		var ancestors = new Array();
		var ancestor = element;
		while (ancestor && (ancestor.nodeType === 1) && !/^(body)$/i.test(ancestor.nodeName) && ancestor != withinBlock) {
			if (HTMLArea.isBlockElement(ancestor)) {
				ancestors.unshift(ancestor);
			}
			ancestor = ancestor.parentNode;
		}
		ancestors.unshift(ancestor);
		return ancestors;
	},
	
	/*
	 * This function wraps the block elements intersecting the current selection in a block element of the given type
	 *
	 * @param	string		blockName: the type of element to be used as wrapping block
	 * @param	string		useClass: a class to be assigned to the wrapping block
	 * @param	object		withinBlock: only elements contained in this block will be wrapped
	 * @param	boolean		keepValid: make only valid wraps (working wraps may produce temporary invalid hierarchies)
	 *
	 * @return	object		the wrapping block
	 */
	wrapSelectionInBlockElement : function(blockName, useClass, withinBlock, keepValid) {
		var endBlocks = this.editor.getEndBlocks(this.editor._getSelection());
		var startAncestors = this.getBlockAncestors(endBlocks.start, withinBlock);
		var endAncestors = this.getBlockAncestors(endBlocks.end, withinBlock);
		var i = 0;
		while (i < startAncestors.length && i < endAncestors.length && startAncestors[i] === endAncestors[i]) {
			++i;
		}
		
		if ((endBlocks.start === endBlocks.end && /^(body)$/i.test(endBlocks.start.nodeName)) || !startAncestors[i] || !endAncestors[i]) {
			--i;
		}
		if (keepValid) {
			if (endBlocks.start === endBlocks.end) {
				while (i && /^(thead|tbody|tfoot|tr|dt)$/i.test(startAncestors[i].nodeName)) {
					--i;
				}
			} else {
				while (i && (/^(thead|tbody|tfoot|tr|td|li|dd|dt)$/i.test(startAncestors[i].nodeName) || /^(thead|tbody|tfoot|tr|td|li|dd|dt)$/i.test(endAncestors[i].nodeName))) {
					--i;
				}
			}
		}
		var blockElement = this.editor._doc.createElement(blockName);
		if (useClass) {
			HTMLArea._addClass(blockElement, useClass);
		}
		var contextElement = endAncestors[0];
		if (i) {
			contextElement = endAncestors[i-1];
		}
		var nextElement = endAncestors[i].nextSibling;
		var block = startAncestors[i], sibling;
		if ((!/^(body|td|th|li|dd)$/i.test(block.nodeName) || /^(ol|ul|dl)$/i.test(blockName)) && block != withinBlock) {
			while (block && block != nextElement) {
				sibling = block.nextSibling;
				blockElement.appendChild(block);
				block = sibling;
			}
			if (nextElement) {
				blockElement = nextElement.parentNode.insertBefore(blockElement, nextElement);
			} else {
				blockElement = contextElement.appendChild(blockElement);
			}
		} else {
			contextElement = block;
			block = block.firstChild;
			while (block) {
				sibling = block.nextSibling;
				blockElement.appendChild(block);
				block = sibling;
			}
			blockElement = contextElement.appendChild(blockElement);
		}
			// Things go wrong in some browsers when the node is empty
		if (Ext.isWebKit && !blockElement.hasChildNodes()) {
			blockElement = blockElement.appendChild(this.editor._doc.createElement("br"));
		}
		return blockElement;
	},
	
	/*
	 * This function adds a class attribute on blocks sibling of the block containing the start container of the selection
	 */
	addClassOnBlockElements : function(buttonId, className) {
		var selection = this.editor._getSelection();
		var endBlocks = this.editor.getEndBlocks(selection);
		var startAncestors = this.getBlockAncestors(endBlocks.start);
		var endAncestors = this.getBlockAncestors(endBlocks.end);
		var index = 0;
		while (index < startAncestors.length && index < endAncestors.length && startAncestors[index] === endAncestors[index]) {
			++index;
		}
		if (endBlocks.start === endBlocks.end) {
			--index;
		}
		if (!/^(body)$/i.test(startAncestors[index].nodeName)) {
			for (var block = startAncestors[index]; block; block = block.nextSibling) {
				if (HTMLArea.isBlockElement(block)) {
					switch (buttonId) {
						case "Indent" :
							if (!HTMLArea._hasClass(block, this.useClass[buttonId])) {
								HTMLArea._addClass(block, this.useClass[buttonId]);
							}
							break;
						case "Outdent" :
							if (HTMLArea._hasClass(block, this.useClass["Indent"])) {
								HTMLArea._removeClass(block, this.useClass["Indent"]);
							}
							break;
						case "JustifyLeft"   :
						case "JustifyCenter" :
						case "JustifyRight"  :
						case "JustifyFull"   :
							this.toggleAlignmentClass(block, buttonId);
							break;
						default :
							if (this.standardBlockElements.test(buttonId.toLowerCase()) && buttonId.toLowerCase() == block.nodeName.toLowerCase()) {
								this.cleanClasses(block);
								if (className) {
									HTMLArea._addClass(block, className);
								}
							}
							break;
					}
				}
				if (block == endAncestors[index]) {
					break;
				}
			}
		}
	},
	
	/*
	 * This function toggles the alignment class on the given block
	 */
	toggleAlignmentClass : function(block, buttonId) {
		for (var alignmentButtonId in this.useClass) {
			if (this.useClass.hasOwnProperty(alignmentButtonId) && alignmentButtonId !== "Indent") {
				if (HTMLArea._hasClass(block, this.useClass[alignmentButtonId])) {
					HTMLArea._removeClass(block, this.useClass[alignmentButtonId]);
				} else if (alignmentButtonId === buttonId) {
					HTMLArea._addClass(block, this.useClass[alignmentButtonId]);
				}
			}
		}
		if (/^div$/i.test(block.nodeName) && !this.hasAllowedAttributes(block)) {
			this.removeElement(block);
		}
	},
	
	/*
	 * This function verifies if the element has any of the allowed attributes
	 */
	hasAllowedAttributes : function(element) {
		for (var i = 0; i < this.allowedAttributes.length; ++i) {
			if (element.getAttribute(this.allowedAttributes[i])) {
				return true;
			}
		}
		return false;
	},
	
	/*
	 * This function removes the given element but keeps its contents
	 */
	removeElement : function(element) {
		var selection = this.editor._getSelection();
		var range = this.editor._createRange(selection);
		var lastChild;
		var bookmark = this.editor.getBookmark(range);
		var parent = element.parentNode;
		while (element.firstChild) {
			lastChild = parent.insertBefore(element.firstChild, element);
		}
		parent.removeChild(element);
		var range = this.editor.moveToBookmark(bookmark);
		this.editor.selectRange(range);
	},
	
	insertList : function (buttonId, parentElement) {
		if (/^(dd)$/i.test(parentElement.nodeName)) {
			var list = parentElement.appendChild(this.editor._doc.createElement((buttonId === "OrderedList") ? "ol" : "ul"));
			var first = list.appendChild(this.editor._doc.createElement("li"));
			first.innerHTML = "<br />";
			this.editor.selectNodeContents(first,true);
		} else {
				// parentElement may be removed by following command
			var parentNode = parentElement.parentNode;
			try {
				this.editor._doc.execCommand(buttonId, false, null);
			} catch(e) {
				this.appendToLog("onButtonPress", e + "\n\nby execCommand(" + buttonId + ");");
			}
			if (Ext.isWebKit) {
				this.editor.cleanAppleStyleSpans(parentNode);
			}
		}
	},
	
	/*
	 * Indent selected list elements
	 */
	indentSelectedListElements : function (list, range) {
		var bookmark = this.editor.getBookmark(range);
			// The selected elements are wrapped into a list element
		var indentedList = this.wrapSelectionInBlockElement(list.nodeName.toLowerCase(), null, list);
			// which breaks the range
		var range = this.editor.moveToBookmark(bookmark);
		bookmark = this.editor.getBookmark(range);
		
			// Check if the last element has children. If so, outdent those that do not intersect the selection
		var last = indentedList.lastChild.lastChild;
		if (last && /^(ol|ul)$/i.test(last.nodeName)) {
			var child = last.firstChild, next;
			while (child) {
				next = child.nextSibling;
				if (!this.editor.rangeIntersectsNode(range, child)) {
					indentedList.appendChild(child);
				}
				child = next;
			}
			if (!last.hasChildNodes()) {
				HTMLArea.removeFromParent(last);
			}
		}
		if (indentedList.previousSibling && indentedList.previousSibling.hasChildNodes()) {
				// Indenting some elements not including the first one
			if (/^(ol|ul)$/i.test(indentedList.previousSibling.lastChild.nodeName)) {
					// Some indented elements exist just above our selection
					// Moving to regroup with these elements
				while (indentedList.hasChildNodes()) {
					indentedList.previousSibling.lastChild.appendChild(indentedList.firstChild);
				}
				list.removeChild(indentedList);
			} else {
				indentedList = indentedList.previousSibling.appendChild(indentedList);
			}
		} else {
				// Indenting the first element and possibly some more
			var first = this.editor._doc.createElement("li");
			first.innerHTML = "&nbsp;";
			list.insertBefore(first, indentedList);
			indentedList = first.appendChild(indentedList);
		}
		this.editor.selectRange(this.editor.moveToBookmark(bookmark));
	},
	
	/*
	 * Outdent selected list elements
	 */
	outdentSelectedListElements : function (list, range) {
			// We wrap the selected li elements and thereafter move them one level up
		var bookmark = this.editor.getBookmark(range);
		var wrappedList = this.wrapSelectionInBlockElement(list.nodeName.toLowerCase(), null, list);
			// which breaks the range
		var range = this.editor.moveToBookmark(bookmark);
		bookmark = this.editor.getBookmark(range);
		
		if (!wrappedList.previousSibling) {
				// Outdenting the first element(s) of an indented list
			var next = list.parentNode.nextSibling;
			var last = wrappedList.lastChild;
			while (wrappedList.hasChildNodes()) {
				if (next) {
					list.parentNode.parentNode.insertBefore(wrappedList.firstChild, next);
				} else {
					list.parentNode.parentNode.appendChild(wrappedList.firstChild);
				}
			}
			list.removeChild(wrappedList);
			last.appendChild(list);
		} else if (!wrappedList.nextSibling) {
				// Outdenting the last element(s) of the list
				// This will break the gecko bookmark
			this.editor.moveToBookmark(bookmark);
			while (wrappedList.hasChildNodes()) {
				if (list.parentNode.nextSibling) {
					list.parentNode.parentNode.insertBefore(wrappedList.firstChild, list.parentNode.nextSibling);
				} else {
					list.parentNode.parentNode.appendChild(wrappedList.firstChild);
				}
			}
			list.removeChild(wrappedList);
			this.editor.selectNodeContents(list.parentNode.nextSibling, true);
			bookmark = this.editor.getBookmark(this.editor._createRange(this.editor._getSelection()));
		} else {
				// Outdenting the middle of a list
			var next = list.parentNode.nextSibling;
			var last = wrappedList.lastChild;
			var sibling = wrappedList.nextSibling;
			while (wrappedList.hasChildNodes()) {
				if (next) {
					list.parentNode.parentNode.insertBefore(wrappedList.firstChild, next);
				} else {
					list.parentNode.parentNode.appendChild(wrappedList.firstChild);
				}
			}
			while (sibling) {
				wrappedList.appendChild(sibling);
				sibling = sibling.nextSibling;
			}
			last.appendChild(wrappedList);
		}
			// Remove the list if all its elements have been moved up
		if (!list.hasChildNodes()) {
			list.parentNode.removeChild(list);
		} 
		this.editor.selectRange(this.editor.moveToBookmark(bookmark));
	},
	
	/*
	 * Make XHTML-compliant nested list
	 * We need this for Opera
	 */
	makeNestedList : function(el) {
		var previous;
		for (var i = el.firstChild; i; i = i.nextSibling) {
			if (/^li$/i.test(i.nodeName)) {
				for (var j = i.firstChild; j; j = j.nextSibling) {
					if (/^(ol|ul)$/i.test(j.nodeName)) {
						this.makeNestedList(j);
					}
				}
			} else if (/^(ol|ul)$/i.test(i.nodeName)) {
				previous = i.previousSibling;
				this.indentedList = i.cloneNode(true);
				if (!previous) {
					previous = el.insertBefore(this.editor._doc.createElement("li"), i);
					this.indentedList = previous.appendChild(this.indentedList);
				} else {
					this.indentedList = previous.appendChild(this.indentedList);
				}
				HTMLArea.removeFromParent(i);
				this.makeNestedList(el);
				break;
			}
		}
	},
	
	/*
	 * Insert a paragraph
	 */
	insertParagraph : function(after) {
		var endBlocks = this.editor.getEndBlocks(this.editor._getSelection());
		var ancestors = after ? this.getBlockAncestors(endBlocks.end) : this.getBlockAncestors(endBlocks.start);
		var endElement = ancestors[ancestors.length-1];
		for (var i = ancestors.length; --i >= 0;) {
			if (/^(table|div|ul|ol|dl|blockquote|address|pre)$/i.test(ancestors[i].nodeName) && !/^(li)$/i.test(ancestors[i].parentNode.nodeName)) {
				endElement = ancestors[i];
				break;
			}
		}
		if (endElement) {
			var parent = endElement.parentNode;
			var paragraph = this.editor._doc.createElement("p");
			if (Ext.isIE) {
				paragraph.innerHTML = "&nbsp";
			} else {
				paragraph.appendChild(this.editor._doc.createElement("br"));
			}
			if (after && !endElement.nextSibling) {
				parent.appendChild(paragraph);
			} else {
				parent.insertBefore(paragraph, after ? endElement.nextSibling : endElement);
			}
			this.editor.selectNodeContents(paragraph, true);
		}
	},
	/*
	 * This function gets called when the plugin is generated
	 */
	onGenerate: function () {
			// Register the enter key handler for IE when the cursor is at the end of a dt or a dd element
		if (Ext.isIE) {
			this.editor.iframe.keyMap.addBinding({
				key: Ext.EventObject.ENTER,
				shift: false,
				handler: this.onKey,
				scope: this
			});
		}
	},
	/*
	 * This function gets called when the enter key was pressed in IE
	 * It will process the enter key for IE when the cursor is at the end of a dt or a dd element
	 *
	 * @param	string		key: the key code
	 * @param	object		event: the Ext event object (keydown)
	 *
	 * @return	boolean		false, if the event was taken care of
	 */
	onKey: function (key, event) {
		var selection = this.editor._getSelection();
		if (this.editor._selectionEmpty(selection)) {
			var range = this.editor._createRange(selection);
			var parentElement = this.editor.getParentElement(selection, range);
			while (parentElement && !HTMLArea.isBlockElement(parentElement)) {
				parentElement = parentElement.parentNode;
			}
			if (/^(dt|dd)$/i.test(parentElement.nodeName)) {
				var nodeRange = this.editor._createRange();
				nodeRange.moveToElementText(parentElement);
				range.setEndPoint("EndToEnd", nodeRange);
				if (!range.text || range.text == "\x20") {
					var item = parentElement.parentNode.insertBefore(this.editor._doc.createElement((parentElement.nodeName.toLowerCase() === "dt") ? "dd" : "dt"), parentElement.nextSibling);
					item.innerHTML = "\x20";
					this.editor.selectNodeContents(item, true);
					event.stopEvent();
					return false;
				}
			} else if (/^(li)$/i.test(parentElement.nodeName)
					&& !parentElement.innerText
					&& parentElement.parentNode.parentNode
					&& /^(dd|td|th)$/i.test(parentElement.parentNode.parentNode.nodeName)) {
				var item = parentElement.parentNode.parentNode.insertBefore(this.editor._doc.createTextNode("\x20"), parentElement.parentNode.nextSibling);
				this.editor.selectNodeContents(parentElement.parentNode.parentNode, false);
				parentElement.parentNode.removeChild(parentElement);
				event.stopEvent();
				return false;
			}
		}
		return true;
	},
	/*
	 * This function removes any disallowed class or mutually exclusive classes from the class attribute of the node
	 */
	cleanClasses : function(node) {
		var classNames = node.className.trim().split(" ");
		var nodeName = node.nodeName.toLowerCase();
		for (var i = classNames.length; --i >= 0;) {
			if (!HTMLArea.reservedClassNames.test(classNames[i])) {
				if (this.tags && this.tags[nodeName] && this.tags[nodeName].allowedClasses) {
					if (!this.tags[nodeName].allowedClasses.test(classNames[i])) {
						HTMLArea._removeClass(node, classNames[i]);
					}
				} else if (this.tags && this.tags.all && this.tags.all.allowedClasses) {
					if (!this.tags.all.allowedClasses.test(classNames[i])) {
						HTMLArea._removeClass(node, classNames[i]);
					}
				}
				if (this.formatBlockItems[nodeName] && this.formatBlockItems[nodeName].classList && this.formatBlockItems[nodeName].classList.test(classNames[i])) {
					HTMLArea._removeClass(node, classNames[i]);
				}
			}
		}
	},
	
	/*
	 * This function gets called when the toolbar is updated
	 */
	onUpdateToolbar: function (button, mode, selectionEmpty, ancestors, endPointsInSameBlock) {
		if (mode === 'wysiwyg' && this.editor.isEditable()) {
			var statusBarSelection = this.editor.statusBar ? this.editor.statusBar.getSelection() : null;
			var parentElement = statusBarSelection ? statusBarSelection : this.editor.getParentElement();
			if (!/^body$/i.test(parentElement.nodeName)) {
				while (parentElement && !HTMLArea.isBlockElement(parentElement) || /^li$/i.test(parentElement.nodeName)) {
					parentElement = parentElement.parentNode;
				}
				var blockAncestors = this.getBlockAncestors(parentElement);
				var endBlocks = this.editor.getEndBlocks(this.editor._getSelection());
				var startAncestors = this.getBlockAncestors(endBlocks.start);
				var endAncestors = this.getBlockAncestors(endBlocks.end);
				var index = 0;
				while (index < startAncestors.length && index < endAncestors.length && startAncestors[index] === endAncestors[index]) {
					++index;
				}
				if (endBlocks.start === endBlocks.end || !startAncestors[index]) {
					--index;
				}
				var commandState = false;
				switch (button.itemId) {
					case 'FormatBlock':
						this.updateDropDown(button, blockAncestors[blockAncestors.length-1], startAncestors[index]);
						break;
					case "Outdent" :
						if (this.useBlockquote) {
							for (var j = blockAncestors.length; --j >= 0;) {
								if (/^blockquote$/i.test(blockAncestors[j].nodeName)) {
									commandState = true;
									break;
								}
							}
						} else if (/^(ol|ul)$/i.test(parentElement.nodeName)) {
							commandState = true;
						} else {
							for (var j = blockAncestors.length; --j >= 0;) {
								if (HTMLArea._hasClass(blockAncestors[j], this.useClass.Indent) || /^(td|th)$/i.test(blockAncestors[j].nodeName)) {
									commandState = true;
									break;
								}
							}
						}
						button.setDisabled(!commandState);
						break;
					case "Indent" :
						break;
					case "InsertParagraphBefore" :
					case "InsertParagraphAfter"  :
						button.setDisabled(/^(body)$/i.test(startAncestors[index].nodeName));
						break;
					case "Blockquote" :
						for (var j = blockAncestors.length; --j >= 0;) {
							if (/^blockquote$/i.test(blockAncestors[j].nodeName)) {
								commandState = true;
								break;
							}
						}
						button.setInactive(!commandState);
						break;
					case "JustifyLeft"   :
					case "JustifyCenter" :
					case "JustifyRight"  :
					case "JustifyFull"   :
						if (this.useAlignAttribute) {
							try {
								commandState = this.editor._doc.queryCommandState(button.itemId);
							} catch(e) {
								commandState = false;
							}
						} else {
							if (/^(body)$/i.test(startAncestors[index].nodeName)) {
								button.setDisabled(true);
							} else {
								button.setDisabled(false);
								commandState = true;
								for (var block = startAncestors[index]; block; block = block.nextSibling) {
									commandState = commandState && HTMLArea._hasClass(block, this.useClass[button.itemId]);
									if (block == endAncestors[index]) {
										break;
									}
								}
							}
						}
						button.setInactive(!commandState);
						break;
					case "InsertOrderedList":
					case "InsertUnorderedList":
						try {
							commandState = this.editor._doc.queryCommandState(button.itemId);
						} catch(e) {
							commandState = false;
						}
						button.setInactive(!commandState);
						break;
					default	:
						break;
				}
			} else {
					// The selection is not contained in any block
				switch (button.itemId) {
					case 'FormatBlock':
						this.updateDropDown(button);
						break;
					case 'Outdent' :
						button.setDisabled(true);
						break;
					case 'Indent' :
						break;
					case 'InsertParagraphBefore' :
					case 'InsertParagraphAfter'  :
						button.setDisabled(true);
						break;
					case 'Blockquote' :
						button.setInactive(true);
						break;
					case 'JustifyLeft'   :
					case 'JustifyCenter' :
					case 'JustifyRight'  :
					case 'JustifyFull'   :
						button.setInactive(true);
						button.setDisabled(true);
						break;
					case 'InsertOrderedList':
					case 'InsertUnorderedList':
						button.setInactive(true);
						break;
					default	:
						break;
				}
			}
		}
	},
	
	/*
	 * This function updates the drop-down list of block elements
	 */
	updateDropDown : function(select, deepestBlockAncestor, startAncestor) {
		var store = select.getStore();
		store.removeAt(0);
		var index = -1;
		if (deepestBlockAncestor) {
			var nodeName = deepestBlockAncestor.nodeName.toLowerCase();
				// Could be a custom item ...
			index = store.findBy(function(record, id) {
				var item = this.formatBlockItems[record.get('value')];
				return item && item.tagName == nodeName && item.addClass && HTMLArea._hasClass(deepestBlockAncestor, item.addClass);
			}, this);
			if (index == -1) {
					// ... or a standard one
				index = store.findExact('value', nodeName);
			}
		}
		if (index == -1) {
			store.insert(0, new store.recordType({
				text: this.localize('No block'),
				value: 'none'
			}));
			select.setValue('none');
		} else {
			store.insert(0, new store.recordType({
				text: this.localize('Remove block'),
				value: 'none'
			}));
			select.setValue(store.getAt(index+1).get('value'));
		}
	},
	
	/*
	* This function handles the hotkey events registered on elements of the dropdown list
	*/	
	onHotKey : function(editor, key) {
		var blockElement;
		var hotKeyConfiguration = this.getHotKeyConfiguration(key);
		if (hotKeyConfiguration) {
			var blockElement = hotKeyConfiguration.element;
		}
		if (blockElement && this.isAllowedBlockElement(blockElement)) {
			this.applyBlockElement(this.translateHotKey(key), blockElement);
			return false;
		}
		return true;
	}
});

