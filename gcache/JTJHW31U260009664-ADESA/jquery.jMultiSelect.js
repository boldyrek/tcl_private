/*
 * jQuery MultiSelect 1.0
 * Copyright (c) 2011 Dan Regazzi
 *
 * Based on the jQuery MultiSelect Widget 1.9 by Eric Hynds
 * http://www.erichynds.com/jquery/jquery-ui-multiselect-widget/
 *
 * Rewritten to provide a better way to handle large options lists without performance issues
 * This plugin uses the original select element instead of replacing it with custom html elements
 *
 * Depends:
 *   - jQuery 1.4.2+
 *   - jQuery UI 1.8 widget factory
 *
 * Optional:
 *   - jQuery UI effects
 *   - jQuery UI position utility
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * 6-3-11 - Dan Regazzi
 * Initial Release, major rewrite of Eric Hynds' jQuery MultiSelect Widget
 * http://www.erichynds.com/jquery/jquery-ui-multiselect-widget/
 * Currently only supports multiple select. Continue to use original for single selects.
*/
// JavaScript Document
(function($, undefined){
	
var multiselectID = 0;

$.widget("dan.jMultiselect", {
// default options - Need to implement commented out options
	options: {
			header: true,
//			height: 175,
			width: 300,
//			minWidth: 140,
			menuWidth: 0,
//			menuMinWidth: 140,
			classes: '',
			noneSelectedText: 'Select options',
//			selectedText: '# selected',
//			selectedList: 0,
//			firstOptionAll: false,
			show: '',
			hide: '',
			multiple: false //,
//			position: {}
		 },
	
	_create: function(){
		var el = this.element.hide(),
			o = this.options;
		
		this.speed = $.fx.speeds._default; // default speed for effects
		this._isOpen = false; // assume no
		
		var 
			selectContainer = (this.selectContainer = this.element)
				.height('auto')
				.attr('size',o.multiple ? 15 : 0)
				.addClass('ui-multiselect');
				
			button = (this.button = $('<button type="button"><span class="ui-icon ui-icon-triangle-1-s"></span></button>'))
				.addClass('ui-multiselect ui-widget ui-state-default ui-corner-all')
				.addClass( o.classes )
				.insertAfter( selectContainer ),
				
			buttonlabel = (this.buttonlabel = $('<span />'))
				.appendTo( button ),	
				
			menu = (this.menu = button.next())
				.addClass('ui-multiselect-menu ui-widget ui-widget-content ui-corner-all')
				.addClass( o.classes ) 
				.hide(),
			
			menuHeader = (this.menuHeader = $("<div class='ui-widget-header ui-corner-all ui-multiselect-header ui-helper-clearfix ui-multiselect-hasfilter' />"))
				.prependTo( menu ),
				
			filter = (this.filter = $("<div class='ui-multiselect-filter'>Filter: <input id='txtFilter' type='text' size='25' /><br /></div>"))
				.appendTo( menuHeader ),
			
			checkboxContainer = (this.checkboxContainer = menu.find('ul'))
				.height('175px')
				.attr('size',15)
				.attr('tabindex', 100)
				.addClass('ui-multiselect-checkboxes ui-helper-reset'),
			
			selectedOptions = (this.selectedOptions = selectContainer.find("option:selected"));
					
			if(o.header){
				//Initialize filter
				var cache = (this.cache = $.map( selectContainer.find('option'), function(el, i){
					var cacheItem = {
						"value": el.innerHTML,
						"checked": false
					}
					return cacheItem;
				}));
			} else {
				menuHeader.hide();
			}
		
		// Set Option Styles
		checkboxContainer.find('label')
			.addClass('ui-corner-all');
		
		// Check selected options
		selectedOptions.each(function(){
			var index = selectContainer.find("option").index(this);
			if(index > 0)
				checkboxContainer.find("input").eq( index ).click();
		});
		
		// Set Hint Text and Button Label
		if(selectedOptions.length == 0 || selectContainer.find("option:first").attr('selected'))
			buttonlabel.html(o.noneSelectedText);
		else if(selectedOptions.length == 1 && !selectContainer.find("option:first").attr('selected'))
			buttonlabel.html(	selectContainer.find("option:selected").html());
		else
			buttonlabel.html(selectedOptions.length + " selected");
		
		if(this.element.is(':disabled')){
			this.disable();
		}
		
		// perform event bindings
		this._bindEvents();
		
		this._setButtonWidth();
		this._setMenuWidth();
		// build menu
		//		this.refresh( true );
		
	},
	
	_init: function(){},
	
	// binds events
	_bindEvents: function(){
		var self = this, 
				button = this.button, 
				selectContainer = this.selectContainer, 
				filter = this.filter,
				checkboxContainer = this.checkboxContainer,
				o=this.options;
		
		function clickHandler(){
			self[ self._isOpen ? 'close' : 'open' ]();
			return false;
		}
		
		// webkit doesn't like it when you click on the span :(
		button
			.find('span')
			.bind('click.multiselect', clickHandler);
		
		// button events
		button.bind({
			click: clickHandler,
			keypress: function(e){
				switch(e.which){
					case 27: // esc
					case 38: // up
					case 37: // left
						self.close();
						break;
					case 39: // right
					case 40: // down
						self.open();
						break;
				}
			},
			mouseenter: function(){
				if( !button.hasClass('ui-state-disabled') ){
					$(this).addClass('ui-state-hover');
				}
			},
			mouseleave: function(){
				$(this).removeClass('ui-state-hover');
			},
			focus: function(){
				if( !button.hasClass('ui-state-disabled') ){
					$(this).addClass('ui-state-focus');
				}
			},
			blur: function(){
				$(this).removeClass('ui-state-focus');
			}
		});
		
		// checkbox events
		checkboxContainer
			.delegate('label', 'mouseenter', function(e){
				if( !button.hasClass('ui-state-disabled') ){
					$(this).addClass('ui-state-hover');
				}
			})
			.delegate('label','mouseleave', function(){
				$(this).removeClass('ui-state-hover');
			})
			.delegate('li input', 'click', function(){
				var	selectContainer = self.selectContainer,
						checkboxContainer = self.checkboxContainer,
						selectedOptions = self.selectedOptions,
						buttonlabel = self.buttonlabel,
						o = self.options; /*,
						cache = self.cache;*/
				
				//$("#VF_Consignor").css({position:"absolute","top":"0px",right:"0px"}).show();
				
				var index = checkboxContainer.find('li').index($(this).parent().parent());
				var option = selectContainer.find('option:eq(' + index + ')');
				
				//cache[index].checked = this.checked;
				
				selectAll = selectContainer.find("option:first");
				checkboxAll = checkboxContainer.find("li input:first");
								
				if(index == 0){
					selectContainer.find("option:selected").not(selectContainer.find("option:first")).removeAttr("selected");
					selectAll.attr("selected","selected");
					
					checkboxContainer.find("li input:checked").not(checkboxContainer.find("li input:first")).removeAttr("checked");
					checkboxAll.attr("checked","checked");
					
				} else if(checkboxContainer.find("li input:checked").length == 0) {
					selectContainer.find("option:eq(" + index + ")").removeAttr("selected");
					selectAll.attr("selected","selected");
					checkboxAll.attr("checked","checked");
				} else {
					if( checkboxAll.is(":checked") ){
						selectAll.removeAttr("selected");
						checkboxAll.removeAttr("checked");	
					} else if (selectContainer.find("option:first").is(":selected") ){
						selectAll.removeAttr("selected");
					}
					
					this.checked 
 						? selectContainer.find("option:eq(" + index + ")").attr("selected","selected") 
						: selectContainer.find("option:eq(" + index + ")").removeAttr("selected"); 
				}
			
				// Update button label
				var count = 0;
				var allSelected = false;
				
				//buttonlabel.html("Select Consignor");
				selectContainer.find('option:selected').each(function(){
					count++;
					if( this.innerHTML == "All" ) allSelected = true;
					if( allSelected ){
						buttonlabel.html("All");
						return;
					} else {
						if(count==1){
							buttonlabel.html(this.innerHTML);
						} else {
							buttonlabel.html(count + " selected");
						}
					}
				});
				
				selectContainer.change();
				if( !o.multiple ) self.close();
			})
			.delegate('label', 'mouseover', function(e){
				$(this).find('input').get(0).focus();
			})
			.delegate('input', 'focus', function(){
				$(this).parent().addClass('ui-state-hover');
			})
			.delegate('input', 'blur', function(e){
				$(this).parent().removeClass('ui-state-hover');
			})
			.delegate('label', 'keydown', function(e){
				switch(e.which){
					case 9: // tab
					case 27: // esc
						self.close();
						break;
					case 38: // up
					case 40: // down
					case 37: // left
					case 39: // right
						self._traverse(e.which, this);
						e.preventDefault();
						break;
					case 48:	// 0
					case 49:	// 1
					case 50:	// 2
					case 51:	// 3
					case 52:	// 4
					case 53:	// 5
					case 54:  // 6
					case 55:	// 7
					case 56:  // 8
					case 57:  // 9
					case 65:	// a
					case 66:	// b
					case 67:	// c
					case 68:	// d
					case 69:	// e
					case 70:	// f
					case 71:	// g
					case 72:	// h
					case 73:	// i
					case 74:	// j
					case 75:	// k
					case 76:	// l
					case 77:	// m
					case 78:	// n
					case 79:	// o
					case 80:	// p
					case 81:	// q
					case 82:	// r
					case 83:	// s
					case 84:	// t
					case 85:	// u
					case 86:	// v
					case 87:	// w
					case 88:	// x
					case 89:	// y
					case 90:	// z
					case 96:  // 0
					case 97:  // 1
					case 98:  // 2
					case 99:  // 3
					case 100: // 4
					case 101: // 5
					case 102: // 6
					case 103: // 7
					case 104: // 8
					case 105: // 9
						// Go to value
						e.charCode = self.getCharCode(e.which);
						self._navigate(e.charCode, this);
						break;
					case 13: // enter
						$(this).find('input')[0].click();
						break;
				}
			});
		
		// filter events
		filter.bind({
			keydown: function( e ){
				switch(e.which){
					// prevent the enter key from submitting the form / closing the widget
					case 13: // enter
						e.preventDefault();
						break;
					// traverse options
					case 38: // up
					case 40: // down
						//self._traverse(e.which, self.checkboxContainer.find('li:visible label:first'));
						checkboxContainer.find("label:visible").eq(0).trigger('mouseover').trigger('mouseenter').trigger('focus');
						e.preventDefault();
						break;
				}
			},
			keyup: function(e){ 
			self._filter(e.target); }
		});
		
		// close each widget when clicking on any other element/anywhere else on the page
		$(document).bind('mousedown.multiselect', function(e){
			if(self._isOpen && !$.contains(self.menu[0], e.target) && e.target !== self.button[0]){
				self.close();
			}
		});
		
		// deal with form resets.  the problem here is that buttons aren't
		// restored to their defaultValue prop on form reset, and the reset
		// handler fires before the form is actually reset.  delaying it a bit
		// gives the form inputs time to clear.
		$(this.element[0].form).bind('reset.multiselect', function(){
			setTimeout(function(){ self.update(); }, 10);
		});
	},
	
	// open the menu
	open: function(e){
		var self = this,
			button = self.button,
			menu = self.menu,
			selectContainer = self.selectContainer,
			checkboxContainer = self.checkboxContainer,
			filter = self.filter,
			speed = self.speed,
			o = self.options;
			
		// bail if the multiselectopen event returns false, this widget is disabled, or is already open 
		if( this._trigger('beforeopen') === false || button.hasClass('ui-state-disabled') || this._isOpen ){
			return;
		}
		
		var effect = o.show,
				pos = button.position();
		
		// figure out opening effects/speeds
		if( $.isArray(o.show) ){
			effect = o.show[0];
			speed = o.show[1] || self.speed;
		}
		
		// position and show menu
		if( $.ui.position && !$.isEmptyObject(o.position) ){
			o.position.of = o.position.of || button;
			
			menu
				.show()
				.position( o.position )
				.hide()
				.show( effect, speed );
		
		// if position utility is not available...
		} else {
			menu.css({ 
				top: pos.top+button.outerHeight(),
				left: pos.left
			}).show( effect, speed );
			
		}
		
		// select the first option
		// triggering both mouseover and mouseover because 1.4.2+ has a bug where triggering mouseover
		// will actually trigger mouseenter.  the mouseenter trigger is there for when it's eventually fixed
		checkboxContainer.find("label:visible").eq(0).trigger('mouseover').trigger('mouseenter').trigger('focus');
		button.addClass('ui-state-active');
		this._isOpen = true;
		this._trigger('open');
	},
	
	// close the menu
	close: function(){
		if(this._trigger('beforeclose') === false){
			return;
		}
	
		var o = this.options, effect = o.hide, speed = this.speed;
		
		// figure out opening effects/speeds
		if( $.isArray(o.hide) ){
			effect = o.hide[0];
			speed = o.hide[1] || this.speed;
		}
	
		this.menu.hide(effect, speed);
		this.button.removeClass('ui-state-active').trigger('blur').trigger('mouseleave');
		this._trigger('close');
		this._isOpen = false;
	},
	
	// move up or down within the menu
	_traverse: function(which, start){
		var $start = $(start),
			moveUp = which === 37 || which === 38,
			moveNext = which === 38 || which === 40,
			$next;
			
			
			$next = $start.parent()[moveUp ? 'prevAll' : 'nextAll']('li:visible')[ moveNext ? 'first' : 'last']();
		
		// if not at the first/last element
		if( $next.length ){
			$next.find('label').trigger('mouseover').find('input').trigger('focus');
		}
	},
	
	// move up or down within the menu
	_navigate: function(charCode, start){
		var 
			self = this,
			o =  self.options,
			checkboxContainer = self.checkboxContainer,
			$start = $(start);
		
		var $next = $start.parent().parent().find('li:visible input');
		
		var char = '';
		var matchFound = false;
		var index = checkboxContainer.find('li:visible').index($start.parent())
		var length = $start.parent().parent().find('li:visible').length;
		
		// Find match within possible inputs		
		for(i = index+1; i < length; i++){
			char = $($next[i]).attr('title').substr(0,1);
			if(char == charCode.toUpperCase() || char == charCode) {
				$next = $($next[i]).parent();
				matchFound = true;
				break;
			}
		}
		
		// Return to start if not found
		if(!matchFound){
			for(i = 0; i < index; i++){
				char = $($next[i]).attr('title').substr(0,1);
				if(char == charCode.toUpperCase() || char == charCode) {
					$next = $($next[i]).parent();
					matchFound = true;
					break;
				}
			}
		}
		
		// Hightlight Match
		if(matchFound) $next.trigger('mouseover');
	},
	
	// get Char code
	getCharCode :function(keyCode){
		var charCode = '0'
		switch(keyCode){
			case 48:	charCode = '0'; break;
			case 49:	charCode = '1'; break; 
			case 50:	charCode = '2'; break; 
			case 51:	charCode = '3'; break; 
			case 52:	charCode = '4'; break; 
			case 53:	charCode = '5'; break; 
			case 54:  charCode = '6'; break; 
			case 55:	charCode = '7'; break; 
			case 56:  charCode = '8'; break; 
			case 57:  charCode = '9'; break; 
			case 65:	charCode = 'a'; break; 
			case 66:	charCode = 'b'; break; 
			case 67:	charCode = 'c'; break; 
			case 68:	charCode = 'd'; break; 
			case 69:	charCode = 'e'; break; 
			case 70:	charCode = 'f'; break; 
			case 71:	charCode = 'g'; break; 
			case 72:	charCode = 'h'; break; 
			case 73:	charCode = 'i'; break; 
			case 74:	charCode = 'j'; break; 
			case 75:	charCode = 'k'; break; 
			case 76:	charCode = 'l'; break; 
			case 77:	charCode = 'm'; break; 
			case 78:	charCode = 'n'; break; 
			case 79:	charCode = 'o'; break; 
			case 80:	charCode = 'p'; break; 
			case 81:	charCode = 'q'; break; 
			case 82:	charCode = 'r'; break; 
			case 83:	charCode = 's'; break; 
			case 84:	charCode = 't'; break; 
			case 85:	charCode = 'u'; break; 
			case 86:	charCode = 'v'; break; 
			case 87:	charCode = 'w'; break; 
			case 88:	charCode = 'x'; break; 
			case 89:	charCode = 'y'; break; 
			case 90:	charCode = 'z'; break; 
			case 96:  charCode = '0'; break; 
			case 97:  charCode = '1'; break; 
			case 98:  charCode = '2'; break; 
			case 99:  charCode = '3'; break; 
			case 100: charCode = '4'; break; 
			case 101: charCode = '5'; break; 
			case 102: charCode = '6'; break; 
			case 103: charCode = '7'; break; 
			case 104: charCode = '8'; break; 
			case 105: charCode = '9'; break; 
		}
		
		return	charCode;
	},
	
	// Update Function
	update: function(){
		//$("#VF_Consignor").css({position:"absolute","top":"0px",right:"0px"}).show();
		var	self = this,
				selectContainer = self.selectContainer,
				checkboxContainer = self.checkboxContainer,
				buttonlabel = self.buttonlabel,
				cache = self.cache;
		
		
		
		/*
		// Check for all selected
		if( cache[0].checked ){
			// Unselect all other
			selectContainer.find("option:selected").filter(":not(:first)").removeAttr("selected");
			checkboxContainer.find("li input:checked").filter(":not(:first)").removeAttr("checked");
			
			selectContainer.find("option:first").attr("selected","selected");
		} else if( checkboxContainer.find('li input:checked').length == 0 ){
			//Check All
			alert('none');
		} else {
			
			for(i = 0; i < checkboxContainer.find('li input:checked').length; i++){
				var index = checkboxContainer.find('li').index(checkboxContainer.find('li input:checked').eq(i).parent().parent());
				if( cache[index].checked ){
					//checkboxContainer.find("li input:eq(" + i + ")").attr("checked", "checked");
					selectContainer.find("option:eq(" + index + ")").attr("selected", "selected");
				} else {
					//checkboxContainer.find("li input:eq(" + i + ")").removeAttr("checked");
					selectContainer.find("option:eq(" + index + ")").removeAttr("selected");
				}
				/*
				if(checkboxContainer.find("li input:eq(" + i + ")").is(":checked") ){
					if(!selectContainer.find("option:eq(" + i + ")").is(":selected") ){
						selectContainer.find("option:eq(" + i + ")").attr("selected", "selected")
					}
				} else {
					
				}
			}
		}*/
	},
	
	destroy: function(){
		// remove classes + data
		$.Widget.prototype.destroy.call( this );
		
		this.button.remove();
		this.menu.remove();
		this.element.show();
		
		return this;
	},
	
	// Disable dropdown
	disable: function(){
		var	self = this,
				selectContainer = self.selectContainer,
				checkboxContainer = self.checkboxContainer,
				button = self.button,
				buttonlabel = self.buttonlabel,
				cache = self.cache;
				
		// Disable Code
		button.addClass('ui-state-disabled');
	},
	
	// Enable dropdown
	enable: function(){
		var	self = this,
				selectContainer = self.selectContainer,
				checkboxContainer = self.checkboxContainer,
				buttonlabel = self.buttonlabel,
				cache = self.cache;
	},
	
	// Display 'Loading...' in dropdown
	// Disable Dropdown
	ajaxStart: function(){
		var buttonlabel = this.buttonlabel;
		buttonlabel.html('Loading...');	
	},
	
	// Display value in dropdown
	// Update filter cache
	// Enable Dropdown
	ajaxEnd: function(){
		var o = this.options,
		buttonlabel = this.buttonlabel,
		selectContainer = this.selectContainer,
		selectedOptions = (this.selectedOptions = selectContainer.find("option:selected"));
		
		// Check selected options
		selectedOptions.each(function(){
			var index = selectContainer.find("option").index(this);
			if(index > 0)
				checkboxContainer.find("input").eq( index ).click();
		});
		
		// Set Hint Text and Button Label
		if(selectedOptions.length == 0 || selectContainer.find("option:first").attr('selected'))
			buttonlabel.html(o.noneSelectedText);
		else if(selectedOptions.length == 1 && !selectContainer.find("option:first").attr('selected'))
			buttonlabel.html(	selectContainer.find("option:selected").html());
		else
			buttonlabel.html(selectedOptions.length + " selected");
			
		// Reset Cache
		if(o.header){
			//Initialize filter
			this.cache = $.map( selectContainer.find('option'), function(el, i){
				var cacheItem = {
					"value": el.innerHTML,
					"checked": false
				}
				return cacheItem;
			});
		} else {
			menuHeader.hide();
		}
			
	},
	
	isOpen: function(){
		return this._isOpen;
	},
	
	widget: function(){
		return this.menu;
	},
	
	_filter: function(txtFilter){
		var self = this,
				selectContainer = self.selectContainer,
				checkboxContainer = self.checkboxContainer,
				cache = self.cache;
				
		var rEscape = /[\-\[\]{}()*+?.,\\^$|#\s]/g;
		var term = $.trim( txtFilter.value.toLowerCase() );
		var regex = new RegExp(term.replace(rEscape, "\\$&"), 'gi');
		
		var rows = checkboxContainer.find('li');
		
		if( !term ){
			rows.show();
		} else {
			rows.hide();
			
			// Do Filter
			$.map(cache, function(v,i){
				if( v.value.search(regex) !== -1 ){
					$(rows[i]).show();
					
					return rows.get(i);
				}
				
				return null;
			});
		}	
	},
	
	// set button width
	_setButtonWidth: function(){
		var width = this.element.outerWidth(),
			o = this.options;
			
		if( /\d/.test(o.minWidth) && width < o.minWidth){
			width = o.minWidth;
		}
		
		// set widths
		this.button.width( width );
	},
	
	// set menu width
	_setMenuWidth: function(){
		var m = this.menu,
				o = this.options,
				selectContainer = this.selectContainer,
				checkboxContainer = this.checkboxContainer,
				menuHeader = this.menuHeader,
				width = this.button.outerWidth()-
					parseInt(m.css('padding-left'),10)-
					parseInt(m.css('padding-right'),10)-
					parseInt(m.css('border-right-width'),10)-
					parseInt(m.css('border-left-width'),10);
		
		if(o.menuWidth){		
			m.width(o.menuWidth - 7);
		} else {
			m.width( width || this.button.outerWidth() );
		}
		
//		selectContainer.width(width - (this.button.width() - width) + 2);
//		checkboxContainer.width(width - (this.button.width() - width) + 2);
	},
	
	// react to option changes after initialization
	_setOption: function( key, value ){
		var menu = this.menu;
		
		switch(key){
			case 'header':
				menu.find('div.ui-multiselect-header')[ value ? 'show' : 'hide' ]();
				break;
			case 'height':
				menu.find('ul:last').height( parseInt(value,10) );
				break;
			case 'minWidth':
				this.options[ key ] = parseInt(value,10);
				this._setButtonWidth();
				this._setMenuWidth();
				break;
			case 'selectedText':
			case 'selectedList':
			case 'noneSelectedText':
				this.options[key] = value; // these all needs to update immediately for the update() call
				this.update();
				break;
			case 'classes':
				menu.add(this.button).removeClass(this.options.classes).addClass(value);
				break;
		}
		
		$.Widget.prototype._setOption.apply( this, arguments );
	}
});

})(jQuery);