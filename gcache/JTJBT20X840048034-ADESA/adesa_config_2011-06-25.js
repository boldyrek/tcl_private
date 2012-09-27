// JavaScript Document
var a_p = "";
var d = new Date();
var curr_hour = d.getHours();
if (curr_hour < 12)
   {
   a_p = "AM";
   }
else
   {
   a_p = "PM";
   }
if (curr_hour == 0)
   {
   curr_hour = 12;
   }
if (curr_hour > 12)
   {
   curr_hour = curr_hour - 12;
   }

var curr_min = d.getMinutes();

curr_min = curr_min + "";

if (curr_min.length == 1)
   {
   curr_min = "0" + curr_min;
   }
	 
$(document).ready(function() {
	$('table.cr-info-table tr:nth-child(even)').addClass('rowA');
	$('table.cr-info-table tr:nth-child(odd)').addClass('rowB');
	$('.showhideAll').toggle(function(){
  	$('table#cr-options-left .option-desc').removeClass('hidden');
		$('.showhideAll').html('Hide Option Descriptions');
  	},function(){
  	$('table#cr-options-left .option-desc').addClass('hidden');
		$('.showhideAll').html('Show Option Descriptions');
 	});
	
	$('input.labelify').labelify({labelledClass: "labelHighlight" }); // Input Field Hints		
	
	// Refine Search drop down
	$(".btnDrop.menu-open").click(function(){
																	 
	});
	$("div#refine_menu").mouseup(function() {
			return false
	});
	$(document).mouseup(function(e) {
			if($(e.target).parent("a.btnDrop").length==0) {
					$(".btnDrop").removeClass("menu-open");
					$("div#refine_menu").hide();
			}
	});
	
	/* Vehicle Finder MultiCSS */
	
	if(!(is_touch_device())){
		
		// added 5/17/2011 for July MR - Dan Regazzi
		// Generic setup for multiselect dropdown
		$(".multiselect_single").multiselect({
			header: false,
			multiple: false,
			minWidth: 100,
			noneSelectedText: $(this).find('option:first'),
			selectedList: 1,
			beforeopen: function(event, ui){
				$(this).data('currentValue',$(this).val());
			},
			close: function(event, ui){
				if($(this).data('currentValue') != $(this).val())
					$(this).change();
			}
		});
		
		// added 5/13/2011 for July MR - Mason Shewman
		$("#makeOffer_bidPrice").multiselect({
			header: false,
			multiple: false,
			minWidth: 100,
			noneSelectedText: 'Select',
			selectedList: 1,
			beforeopen: function(event, ui){
				$(this).data('currentValue',$(this).val());
			},
			close: function(event, ui){
				if($(this).data('currentValue') != $(this).val())
					$(this).change();
			}
		});
		$("#auction_pkey.tbSelect").multiselect({
			header: false,
			multiple: false,
			minWidth: '209px',
			noneSelectedText: 'Select an Auction',
			selectedList: 1,
			beforeopen: function(event, ui){
				$(this).data('currentValue',$(this).val());
			},
			close: function(event, ui){
				if($(this).data('currentValue') != $(this).val())
					$(this).change();
			}
		});

		$("#VF_YearFrom").jMultiselect({
			header: false,
			// DR#105675 - Ranjith K - Year From Field should display 'All' - Begin
			//noneSelectedText: 'Oldest'
			noneSelectedText: 'All'
			// DR#105675 - Ranjith K - Year From Field should display 'All'- End
		});
		
		$("#VF_YearTo").jMultiselect({
			header: false,
			// DR#105675 - Ranjith K - Year To Field should display 'All' - Begin
			//noneSelectedText: 'Newest'
			noneSelectedText: 'All'
			// DR#105675 - Ranjith K - Year To Field should display 'All' - End
		});

		$("#VF_Make").jMultiselect({
			noneSelectedText: 'Select Make'
		});
	
		$("#VF_Model").jMultiselect({
			noneSelectedText: 'All',
			menuWidth: 300
		});
			
		$("#VF_Trim").jMultiselect({
			noneSelectedText: 'All',
			menuWidth: 300
		});
				
		$("#VF_Location").jMultiselect({
			noneSelectedText: 'Select Location',
			multiple: true
		});
		
		$("#VF_Consignor").jMultiselect({
			noneSelectedText: 'Select Consignor',
			multiple: true
		});
		
		$(".rs_multiselect").jMultiselect({
			noneSelectedText: 'All',
			menuWidth: 220,
			minWidth: 220
		});
		
		$("#RS_Series").multiselect({
			header: false,
			multiple: false,
			minWidth: 220,
			noneSelectedText: 'All',
			selectedList: 1,
			beforeopen: function(event, ui){
				$(this).data('currentValue',$(this).val());
			},
			close: function(event, ui){
				if($(this).data('currentValue') != $(this).val())
					$(this).change();
			}
		});
		$("#RS_Color").multiselect({
			header: false,
			multiple: false,
			minWidth: 220,
			noneSelectedText: 'All',
			selectedList: 1,
			beforeopen: function(event, ui){
				$(this).data('currentValue',$(this).val());
			},
			close: function(event, ui){
				if($(this).data('currentValue') != $(this).val())
					$(this).change();
			}
		});
		$("#RS_Odo").multiselect({
			header: false,
			multiple: false,
			minWidth: 220,
			noneSelectedText: 'All',
			selectedList: 1,
			beforeopen: function(event, ui){
				$(this).data('currentValue',$(this).val());
			},
			close: function(event, ui){
				if($(this).data('currentValue') != $(this).val())
					$(this).change();
			}
		});
		$("#RS_Consignor").multiselect({
			header: false,
			multiple: false,
			minWidth: 220,
			noneSelectedText: 'All',
			selectedList: 1,
			beforeopen: function(event, ui){
				$(this).data('currentValue',$(this).val());
			},
			close: function(event, ui){
				if($(this).data('currentValue') != $(this).val())
					$(this).change();
			}
		});
		
	}
});
   
function popitup(url) {
	newwindow=window.open(url,'name','height=680,width=720','resize=yes');
	if (window.focus) {newwindow.focus()}
	return false;
};

function is_touch_device() {  

		try {  
			document.createEvent("TouchEvent");  
			return true;  
		} catch (err) {  
			return false;  
		}  
	}