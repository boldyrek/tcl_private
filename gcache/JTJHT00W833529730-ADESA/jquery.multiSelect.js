/*
 * jQuery MultiSelect UI Widget 1.9
 * Copyright (c) 2011 Eric Hynds
 *
 * http://www.erichynds.com/jquery/jquery-ui-multiselect-widget/
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
 * 3-18-11 - Dan Regazzi
 * Edit to fix 'noneSelectedText' option on Single Select
 *
 * 3-25-11 - Dan Regazzi
 * Bug fix to allow option with value of "" to display
 *
 * 3-28-11 - Dan Regazzi
 * Added firstOptionAll function (line 211)
 *
 * 3-28-11 - Dan Regazzi
 * Fix for keyboard input (line 211)
 *
 * 4-8-11 - Dan Regazzi
 * Fix for performance issues in all browsers - load time significantly decreased
 * - Drop-down menu content does not fully load before displayed on page
 * - Filter is applied when content is loaded from within this plug-in
 * - Preselected options should be properly applied at page load
 * - Content continues to load after initial display until completed
 *
 * 5-26-11 - Dan Regazzi
 * Logic fix to address "All" option 
 * - No value will be selected on initial page load and noneSelectedText will display
 * - After making first choice in select 'All' will become the default selected option when nothing is selected
 * - Selected 'All' will deselect all other options
*/

(function($, undefined){

var multiselectID = 0;

$.widget("ech.multiselect", {
	
	// default options
	options: {
		header: true,
		height: 175,
		minWidth: 225,
		classes: '',
		checkAllText: 'Check all',
		uncheckAllText: 'Uncheck all',
		noneSelectedText: 'Select options',
		selectedText: '# selected',
		selectedList: 0,
		firstOptionAll: false,
		show: '',
		hide: '',
		autoOpen: false,
		multiple: true,
		filter: false,
		position: {}
	},

	_create: function(){
		var el = this.element.hide(),
			o = this.options;
		
		this.speed = $.fx.speeds._default; // default speed for effects
		this._isOpen = false; // assume no
		this._initialized = false; // assume no
		this._clicked = false; // assume no
		
		var 
			button = (this.button = $('<button type="button"><span class="ui-icon ui-icon-triangle-1-s"></span></button>'))
				.addClass('ui-multiselect ui-widget ui-state-default ui-corner-all')
				.addClass( o.classes )
				.attr({ 'title':el.attr('title'), 'aria-haspopup':true })
				.insertAfter( el ),
				
			buttonlabel = (this.buttonlabel = $('<span />'))
				.html( o.noneSelectedText )
				.appendTo( button ),	
				
			menu = (this.menu = $('<div />'))
				.addClass('ui-multiselect-menu ui-widget ui-widget-content ui-corner-all')
				.addClass( o.classes )
				.insertAfter( button ),
				
			header = (this.header = $('<div />'))
				.addClass('ui-widget-header ui-corner-all ui-multiselect-header ui-helper-clearfix')
				.appendTo( menu ),
				
			headerLinkContainer = (this.headerLinkContainer = $('<ul />'))
				.addClass('ui-helper-reset')
				.html(function(){
					if( o.header === true ){
						return '<li><a class="ui-multiselect-all" href="#"><span class="ui-icon ui-icon-check"></span><span>' + o.checkAllText + '</span></a></li><li><a class="ui-multiselect-none" href="#"><span class="ui-icon ui-icon-closethick"></span><span>' + o.uncheckAllText + '</span></a></li>';
					} else if(typeof o.header === "string"){
						return '<li>' + o.header + '</li>';
					} else {
						return '';
					}
				})
				.append('<li class="ui-multiselect-close"><a href="#" class="ui-multiselect-close"><span class="ui-icon ui-icon-circle-close"></span></a></li>')
				.appendTo( header ),
			
			checkboxContainer = (this.checkboxContainer = $('<ul />'))
				.addClass('ui-multiselect-checkboxes ui-helper-reset')
				.appendTo( menu );
		
		
		// perform event bindings
		this._bindEvents();
		
		// build menu
		this.refresh( true );
		
		// some addl. logic for single selects
		if( !o.multiple ){
			menu.addClass('ui-multiselect-single');
		}
	},
	
	_init: function(){
		if( this.options.header === false ){
			this.header.hide();
		}
		if( !this.options.multiple ){
			this.headerLinkContainer.find('.ui-multiselect-all, .ui-multiselect-none').hide();
		}
		if( this.options.autoOpen ){
			this.open();
		}
		if( this.element.is(':disabled') ){
			this.disable();
		}
	},
	
	refresh: function( init ){
		var el = this.element,
			o = this.options,
			menu = this.menu,
			button = this.button,
			checkboxContainer = this.checkboxContainer,
			optgroups = [],
			id = el.attr('id') || multiselectID++; // unique ID for the label & option tags
		
		checkboxContainer.empty();

/* Don't Do on Init 	*/
//	if(!init){
		// build items
		var elements = this.element.find('option');
		
//		for(i = 0; i < elements.length; i++){
		var i = 0, that = this;
		if(i < elements.length) setTimeout(function(){buildItem(i, elements);},10);
		function buildItem(i, elements){
			var $this = $(elements[i]), 
				title = $this.html(),
				value = elements[i].value, 
				inputID = elements[i].id || 'ui-multiselect-'+id+'-option-'+i, 
				$parent = $this.parent(), 
				isDisabled = $this.is(':disabled'), 
				labelClasses = ['ui-corner-all'],
				label, li;

			// is this an optgroup?
			if( $parent.is('optgroup') ){
				var optLabel = $parent.attr('label');
				
				// has this optgroup been added already?
				if( $.inArray(optLabel, optgroups) === -1 ){
					$('<li><a href="#">' + optLabel + '</a></li>')
						.addClass('ui-multiselect-optgroup-label')
						.appendTo( checkboxContainer );
					
					optgroups.push( optLabel );o
				}
			}
		
			if( value.length >= 0 ){
				if( isDisabled ){
					labelClasses.push('ui-state-disabled');
				}
				
				li = $('<li />')
					.addClass(isDisabled ? 'ui-multiselect-disabled' : '')
					.appendTo( checkboxContainer );
					
				label = $('<label />')
					.attr('for', inputID)
					.addClass(labelClasses.join(' '))
					.appendTo( li );
				
				// attr's are inlined to support form reset.  double checked attr is to support chrome bug - see #46
				$('<input type="'+(o.multiple ? 'checkbox' : 'radio')+'" '+(elements[i].selected ? 'checked="checked"' : '')+ ' name="multiselect_'+id + '" />')
					.attr({ id:inputID, checked:elements[i].selected, title:title, disabled:isDisabled, 'aria-disabled':isDisabled, 'aria-selected':elements[i].selected })
					.val( value )
					.appendTo( label )
					.after('<span>'+title+'</span>');
			}
			
			i++;
			if(i < elements.length) {
				if(that.button.nextAll('div.multiselect-loading').length > 0){
					that.button.nextAll('div.multiselect-loading').find('span').html("<strong>Loading... " + (i+1) + " of " + elements.length);
				}
				setTimeout(function(){buildItem(i, elements);},10);
			}
			else{
				// Update MS Filter
				if(o.filter){
						(that.element).multiselect().multiselectfilter("updateCache");
				}
			
				that._initialized = true;
				that.update();
				that._clicked = true;
				that.content_load_complete();
			}
			
		}
		
		// cache some moar useful elements
		this.labels = menu.find('label');
		
		// set widths
		this._setButtonWidth();
		this._setMenuWidth();
		
		// remember default value
		button[0].defaultValue = this.update();
		
		// broadcast refresh event; useful for widgets
		if( !init ){
			this._trigger('refresh');
		}
	},
	
	// updates the button text.  call refresh() to rebuild
	update: function(){
		if(this._initialized){
		var o = this.options,
			$inputs = this.menu.find("input[type='radio'],input[type='checkbox']"),
			$checked = $inputs.filter(':checked'),
			numChecked = $checked.length,
			value;
			this.labels = this.menu.find('label');
		} else {
		var o = this.options,
			$inputs = this.element.find('option'),
			$checked = $inputs.filter(':selected'),
			numChecked = $checked.length,
			value;
		}
		
		if( numChecked === 0 ){
			value = o.noneSelectedText;
			
			if(o.firstOptionAll && this._initialized && this._clicked){
				this.element.find('option').removeAttr('selected');
				this.element.find('option:first').attr('selected','selected');
				$($inputs[0]).attr('checked','checked');
				value = $inputs[0].title;
			} else if(o.firstOptionAll && !this._initialized && this._clicked){
				this.element.find('option').removeAttr('selected');
				this.element.find('option:first').attr('selected','selected');
				this.menu.find("input[type='checkbox']:first").attr('checked','checked');
				value = this.menu.find("input[type='checkbox']:first").title;
			}
			
		} else {
			if(($inputs[0].checked && o.firstOptionAll && numChecked == 1) || ($inputs[0].selected && o.firstOptionAll && numChecked == 1)){
				if(this._initialized){
					$($inputs[0]).attr('checked','checked');
				} else {
					this.menu.find("input[type='checkbox']:first").attr('checked','checked');
				}
			} else if(($inputs[0].checked && o.firstOptionAll && numChecked > 1) || ($inputs[0].selected && o.firstOptionAll && numChecked > 1)){
				this.element.find('option:first').removeAttr('selected');	
				
				if(this._initialized){
					$($inputs[0]).removeAttr('checked');
					$checked = $inputs.filter(':checked'); 
				} else {
					this.menu.find("input[type='checkbox']:first").removeAttr('checked');
					$checked = $inputs.filter(':selected'); 
				}
				numChecked = $checked.length;
			}
			
			if($.isFunction(o.selectedText)){
				value = o.selectedText.call(this, numChecked, $inputs.length, $checked.get());
			} else if( /\d/.test(o.selectedList) && o.selectedList > 0 && numChecked <= o.selectedList){
				if((!o.multiple) && ($checked.val() == $($inputs[0]).val())){
					value = o.noneSelectedText;
				} else {
					if(this._initialized){
						value = $checked.map(function(){ return this.title; }).get().join(', ');
					} else{
						value = $checked.map(function(){ return this.innerHTML; }).get().join(', ');
					}
				}
			} else {
				value = o.selectedText.replace('#', numChecked).replace('#', $inputs.length);
			}
		}
		
		
		this.buttonlabel.html( value );
		return value;
	},
	
	// binds events
	_bindEvents: function(){
		var self = this, button = this.button, o=this.options;
		
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

		// header links
		this.header
			.delegate('a', 'click.multiselect', function(e){
				// close link
				if( $(this).hasClass('ui-multiselect-close') ){
					self.close();
			
				// check all / uncheck all
				} else {
					self[ $(this).hasClass('ui-multiselect-all') ? 'checkAll' : 'uncheckAll' ]();
				}
			
				e.preventDefault();
			});
		
		// optgroup label toggle support
		this.menu
			.delegate('li.ui-multiselect-optgroup-label a', 'click.multiselect', function(e){
				e.preventDefault();
				
				var $this = $(this),
					$inputs = $this.parent().nextUntil('li.ui-multiselect-optgroup-label').find('input:visible:not(:disabled)');
				
				// trigger event and bail if the return is false
				if( self._trigger('optgrouptoggle', e, { inputs:$inputs.get(), label:$this.parent().text(), checked:$inputs[0].checked }) === false ){
					return;
				}
				
				// toggle inputs
				self._toggleChecked(
					$inputs.filter(':checked').length !== $inputs.length,
					$inputs
				);
			})
			.delegate('label', 'mouseenter.multiselect', function(){
				if( !$(this).hasClass('ui-state-disabled') ){
					self.menu.find('label').removeClass('ui-state-hover');
					$(this).addClass('ui-state-hover').find('input').focus();
				}
			})
			.delegate('label', 'keydown.multiselect', function(e){
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
						e.preventDefault();
						$(this).find('input')[0].click();
						break;
				}
			})
			.delegate('input[type="checkbox"], input[type="radio"]', 'click.multiselect', function(e){
				var $this = $(this),
					val = this.value,
					checked = this.checked,
					tags = self.element.find('option'),
					$inputs = self.menu.find('input:checkbox'),
					$checked = $inputs.filter(':checked'),
					numChecked = $checked.length;
				
				self._clicked = true;
				
				if(this == $inputs[0]){
					$inputs.removeAttr('checked');
					$(tags).removeAttr('selected');
				}
				
				// bail if this input is disabled or the event is cancelled
				if( $this.is(':disabled') || self._trigger('click', e, { value:val, text:this.title, checked:checked }) === false ){
					e.preventDefault();
					return;
				}
				
				// toggle aria state
				$this.attr('aria-selected', checked);
				
				// set the original option tag to selected
				tags.filter(function(){
					return this.value === val;
				}).attr('selected', (checked ? 'selected' : ''));
				
				// make sure the original option tags are unselected first 
				// in a single select
				if( !self.options.multiple ){
					tags.not(function(){
						return this.value === val;
					}).removeAttr('selected');
					
					self.labels.removeClass('ui-state-active');
					$this.closest('label').toggleClass('ui-state-active', checked );
					
					// close menu
					self.close();
				}
				
				// setTimeout is to fix multiselect issue #14 and #47. caused by jQuery issue #3827
				// http://bugs.jquery.com/ticket/3827 
				setTimeout($.proxy(self.update, self), 10);
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
			width = this.button.outerWidth()-
				parseInt(m.css('padding-left'),10)-
				parseInt(m.css('padding-right'),10)-
				parseInt(m.css('border-right-width'),10)-
				parseInt(m.css('border-left-width'),10);
				
		m.width( width || this.button.outerWidth() );
	},
	
	// move up or down within the menu
	_traverse: function(which, start){
		var $start = $(start),
			moveToLast = which === 38 || which === 37,
			
			// select the first li that isn't an optgroup label / disabled
			$next = $start.parent()[moveToLast ? 'prevAll' : 'nextAll']('li:not(.ui-multiselect-disabled, .ui-multiselect-optgroup-label)')[ moveToLast ? 'last' : 'first']();
		
		// if at the first/last element
		if( !$next.length ){
			var $container = this.menu.find('ul:last');
			
			// move to the first/last
			this.menu.find('label')[ moveToLast ? 'last' : 'first' ]().trigger('mouseover');
			
			// set scroll position
			$container.scrollTop( moveToLast ? $container.height() : 0 );
			
		} else {
			$next.find('label').trigger('mouseover');
		}
	},

	// move up or down within the menu
	_navigate: function(charCode, start){
		var o = this.options;
		$start = $(start);
		
		var $next = $start.parent().parent().find('input');
		
		var char = '';
		var matchFound = false;
		var index = $start.parent().index();
		var length = $start.parent().parent().find('li').length;
		
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
		
	_toggleChecked: function(flag, group){
		var $inputs = (group && group.length) ?
			group :
			this.labels.find('input');
		
		// toggle state on inputs
		$inputs
			.not(':disabled')
			.attr({ 'checked':flag, 'aria-selected':flag }); 
		
		this.update();
		
		var values = $inputs.map(function(){
			return this.value;
		}).get();
		
		// toggle state on original option tags
		this.element.find('option').filter(function(){
			return !this.disabled && $.inArray(this.value, values) > -1;
		}).attr({ 'selected':flag, 'aria-selected':flag });
	},

	_toggleDisabled: function( flag ){
		this.button
			.attr({ 'disabled':flag, 'aria-disabled':flag })[ flag ? 'addClass' : 'removeClass' ]('ui-state-disabled');
		
		this.menu
			.find('input')
			.attr({ 'disabled':flag, 'aria-disabled':flag })
			.parent()[ flag ? 'addClass' : 'removeClass' ]('ui-state-disabled');
		
		this.element
			.attr({ 'disabled':flag, 'aria-disabled':flag });
	},
	
	// open the menu
	open: function(e){
		var self = this,
			button = this.button,
			menu = this.menu,
			speed = this.speed,
			o = this.options;
		
		// bail if the multiselectopen event returns false, this widget is disabled, or is already open 
		if( this._trigger('beforeopen') === false || button.hasClass('ui-state-disabled') || this._isOpen ){
			return;
		}
		
		var $container = menu.find('ul:last'),
			effect = o.show,
			pos = button.position();
		
		// figure out opening effects/speeds
		if( $.isArray(o.show) ){
			effect = o.show[0];
			speed = o.show[1] || self.speed;
		}
		
		// set the scroll of the checkbox container
		$container.scrollTop(0).height(o.height);
		
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
		this.labels.eq(0).trigger('mouseover').trigger('mouseenter').find('input').trigger('focus');
		
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

	enable: function(){
		this._toggleDisabled(false);
	},
	
	disable: function(){
		this._toggleDisabled(true);
	},
	
	checkAll: function(e){
		this._toggleChecked(true);
		this._trigger('checkAll');
	},
	
	uncheckAll: function(){
		this._toggleChecked(false);
		this._trigger('uncheckAll');
	},
	
	content_load: function(){
			this.content_loading = true;
			var $loading = $(this.button).after("<div class='multiselect-loading'>");
			$loading.next().append("<span><strong>Loading...</strong></span>")
			$loading.next()
				.width($loading.width()+4)
				.height($loading.height()+4)
				.offset( {top: $loading.offset().top, left: $loading.offset().left});				
		
		// Capture overlay clicks
		$loading.next().click(function(e){
				e.stopPropagation();
		});
	},

	content_load_complete: function(){
		// Remove Loader
		if(this.content_loading){
			$(this.button).parent().find('div.multiselect-loading').remove();
			this.open();
		}
	},
	
	getChecked: function(){
		return this.menu.find('input').filter(':checked');
	},
	
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
	destroy: function(){
		// remove classes + data
		$.Widget.prototype.destroy.call( this );
		
		this.button.remove();
		this.menu.remove();
		this.element.show();
		
		return this;
	},
	
	isOpen: function(){
		return this._isOpen;
	},
	
	widget: function(){
		return this.menu;
	},
	
	// react to option changes after initialization
	_setOption: function( key, value ){
		var menu = this.menu;
		
		switch(key){
			case 'header':
				menu.find('div.ui-multiselect-header')[ value ? 'show' : 'hide' ]();
				break;
			case 'checkAllText':
				menu.find('a.ui-multiselect-all span').eq(-1).text(value);
				break;
			case 'uncheckAllText':
				menu.find('a.ui-multiselect-none span').eq(-1).text(value);
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
