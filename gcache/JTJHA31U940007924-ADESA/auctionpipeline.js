	var makers_out = "";
	var bodies_out = "";
	var inters_out = "";
	var sales_out = "";
	var years_out = "";
	var miles_out = "";
	var trans_out = "";
	var invtype_out ="";
	var aucs_out ="";
	var cyl_out = "";
	var fl_out = "";
	var auction_count ="";
	var auctions_count =0;
    var makers = "";
	var bodies = "";
	var inters = "";
	var sales = "";
	var years = "";
	var miles = "";
	var trans = "";
	var invtype="";
	var aucs ="";
	var cyl = "";
	var fl = "";
	var csn="";
    var mod_out = "";
    var mod = "";
    var csn_out= "";	
    var selectedmodel = "";
    var timer;
    var MakeChanged=false;
   
    var Selectedmakers = "";
	var Selectedbodies = "";
	var Selectedinters = "";
	var Selectedsales = "";
	var Selectedyears = "";
	var Selectedmiles = "";
	var Selectedtrans = "";
	var Selectedinvtype="";
	var Selectedaucs ="";
	var Selectedcyl = "";
	var Selectedfl = "";
	var Selectedcsn="";
    var Selectedmod_out = "";
    var Selectedmod = "";
    var Selectedcsn_out= "";	

 
    
    var makers_list ,bodies_list ,inters_list ,sales_list ,aucs_list;	
    
    var elts_makers, elts_bodies, elts_inters, elts_sales,  elts_aucs;


function InitVars()
{
    //Initialize all global variables

    elts_makers = document.getElementsByClassName("search_selected", $("carmakes"));
	elts_bodies = document.getElementsByClassName("search_selected", $("body_styles"));
	elts_inters = document.getElementsByClassName("search_selected", $("int_styles"));
	elts_sales = document.getElementsByClassName("search_selected", $("saletype"));
	elts_aucs = document.getElementsByClassName("search_selected", $("auctions"));
	
    makers_list = new Array();
 	bodies_list = new Array();
 	inters_list = new Array();
  	sales_list = new Array();
 	aucs_list = new Array();	


}	
    
function load_content_into(container, url, callback) {

    var params = "&hash=" + Math.random();

    var contentAjax = new Ajax.Updater( {success: container},
        url, {
            method: 'get',
            parameters: params,
            evalScripts: true
        }
    )
    
}


function navover(nameofclass) {


	Element.addClassName(nameofclass, "nav_over");
	AppendToLog("After navover");
}

function navout(nameofclass) {
	Element.removeClassName(nameofclass, "nav_over");
}

function toggleselect(elt, checkbox) {

    if(checkbox == "carmakes_all")
     {
        MakeChanged = true;
      }  
	if ($(checkbox).checked) {

		$(checkbox).checked = !$(checkbox).checked;
		new Effect.Opacity($(elt.parentNode), {duration:0.2, from:0.2, to:1.0});
	}
	
	if (Element.hasClassName(elt, "search_selected")) {
		Element.removeClassName(elt, "search_selected");
		if (!Element.hasClassName(elt, "search_select")) 
		{
		    Element.addClassName(elt, "search_select");
		}
	}
	else {
		Element.addClassName(elt, "search_selected");
	}
//createstrings();

}


function toggleyearselect(elt, checkbox) {


//	if ($(checkbox).checked) {
//		$(checkbox).checked = !$(checkbox).checked;
//		new Effect.Opacity($(elt.parentNode), {duration:0.2, from:0.2, to:1.0});
//	}
	
	if (Element.hasClassName(elt, "selected_year_year")) {
		Element.removeClassName(elt, "selected_year_year");
	}
	else {
		Element.addClassName(elt, "selected_year_year");
	}

}


function search_select_highlight(elt) {


	var allselectboxes = document.getElementsByClassName("SrchFld", $(elt));
	var allzero = true;
	
	for (var i = 0; i < allselectboxes.length; i++) {
		 if (allselectboxes[i].selectedIndex != 0) {
		 	allzero = false;
		 }
	}
	
	if (allzero) {Element.removeClassName($(elt), "search_optionbox_selected"); }
	else { Element.addClassName($(elt), "search_optionbox_selected"); }	 	
//createstrings();
}

function toggleradio(elts) {
	document.getElementsByClassName("search_radio_selected", elts.parentNode).each(function(elt) {
		Element.removeClassName(elt, "search_radio_selected");
	});
	Element.addClassName(elts, "search_radio_selected");
//createstrings();

}

function toggleopaque(checkbox, div) {

	if ($(checkbox).checked == true) {
	    
		new Effect.Opacity($(div), {duration:0.2, from:1.0, to:0.3});
		document.getElementsByClassName('search_select', $(div)).each(function(elt) { Element.removeClassName(elt, 'search_selected'); });
        document.getElementsByClassName('search_selected', $(div)).each(function(elt) { Element.removeClassName(elt, 'search_selected'); Element.addClassName(elt, 'search_select');});
	}
	
	if ($(checkbox).checked == false) {
		new Effect.Opacity($(div), {duration:0.2, from:0.3, to:1.0});

	}
//createstrings();
}

// New functions----------------------

function SelectMakes()
    {
        //debugger;
        elts_makers = document.getElementsByClassName("search_selected", $("carmakes"));
	    if ($("carmakes_all").checked == true || elts_makers.length == 0)
	    {
	        makers_out = "";
	        Selectedmakers = "";
	    }
	    else  
	    {   //var makers = new Array();
	        makers_list = new Array();
		    elts_makers.each(function(makers) {makers_list.push(makers.innerHTML);});
		    Selectedmakers = "<font style='color:black;'><b>Makes: </b></font>"+ makers_list.join(",")+"<br />"; 
   	    }

      SelectedItems(Selectedmakers);
    }
function SelectBodyStyles()
    {
        elts_bodies = document.getElementsByClassName("search_selected", $("body_styles"));
	    if ($("bodystyles_all").checked  || elts_bodies.length == 0) 
	    {
	        bodies_out = "";
	        Selectedbodies = "";
	    }
	    else 
	    {   Selectedbodies = ""
	        bodies_list = new Array();
	        elts_bodies.each(function(bodies) { bodies_list.push(bodies.innerHTML); });
		    Selectedbodies  = "<font style='color:black;'><b>BodyStyles: </b></font>"+ bodies_list.join(",")+"<br />"; 	
	    }

	    SelectedItems();

    }
function SelectInterior()
    {
        elts_inters = document.getElementsByClassName("search_selected", $("int_styles"));
	    if ($("intstyles_all").checked || elts_inters.length == 0) 
	    {
	        inters_out = "";
	        Selectedinters  = "";
	    }
	    else { 
	        inters_list = new Array();
	        elts_inters.each(function(inters) { inters_list.push(inters.innerHTML);});
		    Selectedinters  = "<font style='color:black;'><b>Interior: </b></font>"+ inters_list.join(",")+"<br />";			
	    }

	    SelectedItems();
    }
function SelectSale()
    {
        elts_sales = document.getElementsByClassName("search_selected", $("saletype"));
	    if ($("sales_all").checked || elts_sales.length == 0)
	     {
	         sales_out = "";
	         Selectedsales = "";
	     }
	    else { 
	         sales_list = new Array();
             elts_sales.each(function(sales) { sales_list.push(sales.innerHTML); });
 	         Selectedsales  = "<font style='color:black;'><b>Sale Type: </b></font>"+ sales_list.join(",")+"<br />";
	    }
	    SelectedItems();
	    
    }

function SelectAuctions()
    {

        elts_aucs = document.getElementsByClassName("search_selected", $("auctions"));

        if ( $("aucs_all").checked || elts_aucs.length == 0)
         {
             aucs_out = "";
             Selectedaucs = "";
         }
	    else
	     {  
	        aucs_list = new Array();
	        elts_aucs.each(function(aucs) { aucs_list.push(aucs.innerHTML); });
		    Selectedaucs  = "<font style='color:black;'><b>At: </b></font>"+ aucs_list.join(",")+"<br />";
	     }
	        SelectedItems();
	        
    }
function SelectYears()
    {

        var start_year = $("syr").options[$("syr").selectedIndex].value;
        var end_year = $("eyr").options[$("eyr").selectedIndex].value;
   	    if ($("syr").selectedIndex == 0 && $("eyr").selectedIndex == 0) 
   	    { 
   	        years_out = "";
   	        Selectedyears  = "";
        } 
	    if ($("syr").selectedIndex != 0 && $("eyr").selectedIndex == 0) 
	    { 
	        years_out = "syr=" + start_year + "&eyr=2008";
	        Selectedyears = "<font style='color:black;'><b>Year made is newer than : </b></font>" + start_year+"<br />" ;
	       } 
	    if ($("syr").selectedIndex == 0 && $("eyr").selectedIndex != 0) 
	    { 
	        years_out = "syr=0&eyr=" + end_year;
	        Selectedyears = "<font style='color:black;'><b>Year made is older than: </b></font>" + end_year+"<br />";
	       }
	    if ($("syr").selectedIndex != 0 && $("eyr").selectedIndex != 0) 
	    { 
	        years_out = "syr=" + start_year + "&eyr=" + end_year;
	        Selectedyears = "<font style='color:black;'><b>Year made between: </b></font>" + start_year + "<font style='color:black;'><b> and </b></font>" + end_year+"<br />";
	       }
	    if (start_year == end_year) 
	    { 
	        years_out="syr="+start_year.join("&")+ "eyr=" +start_year;
	        Selectedyears = "<font style='color:black;'></b>Made in </b></font>" +start_year+"<br />";
	       }
    	
	    SelectedItems();
	    
    }

function SelectOdometer()
    {

        var start_odo = $("sodo").options[$("sodo").selectedIndex].value;
	    var end_odo = $("eodo").options[$("eodo").selectedIndex].value;
    	
	    if ($("sodo").selectedIndex == 0) {start_odo = "0"}
	    if ($("eodo").selectedIndex == 0) {end_odo = "1100000"}	

	    if ($("sodo").selectedIndex == 0 && $("eodo").selectedIndex == 0) 
	    {   
	        miles_out = "";
	        Selectedmiles ="";
	    }
	    if ($("sodo").selectedIndex != 0 && $("eodo").selectedIndex == 0) 
	    { 
	        miles_out = "sodo=" + start_odo+"&eodo=1100000"; 
	        Selectedmiles ="<font style='color:black;'><b>Millege is above</b> </font>" + start_odo+"<font style='color:black;'><b> miles <b></font>"+"<br />"; 
	    }
	    if ($("sodo").selectedIndex == 0 && $("eodo").selectedIndex != 0) 
	    { 
	        miles_out = "sodo=0&eodo="+ end_odo ; 
	        Selectedmiles = "<font style='color:black;'><b>Millege is Under </b></font>"  + end_odo +"<font style='color:black;'><b> miles </b></font>"+"<br />";
	    }
	    if ($("sodo").selectedIndex != 0 && $("eodo").selectedIndex != 0) 
	    {   
	        miles_out = "sodo=" + start_odo + "&" + "eodo=" + end_odo ; 
	        Selectedmiles = "<font style='color:black;'><b>Millege is between</b></font>" + start_odo + "<font style='color:black;'><b> and </b></font>"+ end_odo+"<br />" ;
	    }
    	
    	
	    SelectedItems();
    }

function SelectTransmission()
    {



	    if ($("tr").selectedIndex == 0) 
	    { 
	        trans_out = "" ;
	        Selectedtrans = "";
	    }
	        else { trans_out = "tr="+$("tr").options[$("tr").selectedIndex].value; 
	        Selectedtrans  = "<font style='color:black;'><b>Transmission: </b></font>"+$("tr").options[$("tr").selectedIndex].innerHTML+"<br />"; 
	    }
	    SelectedItems();
    }


 
function SelectConsignor()
    {

        if ($("csn").selectedIndex == 0)
        {
            csn_out = "";
            Selectedcsn = "";
        }
        else 
        { 
            csn_out = "csn="+$("csn").options[$("csn").selectedIndex].value;
            Selectedcsn = "<font style='color:black;'><b>  Consignor :</b></font>"+$("csn").options[$("csn").selectedIndex].innerHTML  +"<br />";
        }
        
        SelectedItems();
    }

function SelectInventory()
    {
       if ($("inv").selectedIndex == 0) 
       { 
           invtype_out = "" ;
           Selectedinvtype = "";
        }
       else 
        { 
           invtype_out = "inv="+$("inv").options[$("inv").selectedIndex].value;
           Selectedinvtype = "<font style='color:black;'><b>Inventory :</b></font>"+$("inv").options[$("inv").selectedIndex].innerHTML+"<br />";
        }
       SelectedItems();

    }
function SelectedEngine()
    {
        if ($("cyl").selectedIndex == 0) { 
            cyl_out = "";
            Selectedcyl ="";}
	    else {cyl_out = "cyl=" + $("cyl").options[$("cyl").selectedIndex].value ; 
	    Selectedcyl =$("cyl").options[$("cyl").selectedIndex].innerHTML +"<font style='color:black;'><b>  Engine </b></font>"+"<br />"; 
	    }
    	
	    if ($("fl").selectedIndex == 0) { 
	        fl_out = "" ;
	        Selectedfl  ="";
	    }
	    else { 
	        fl_out = "fl="+$("fl").options[$("fl").selectedIndex].value; 
	        Selectedfl = $("fl").options[$("fl").selectedIndex].innerHTML + "<font style='color:black;'><b>  Fuel </b></font>" +"<br />"; 
	    }
	    
	    SelectedItems();
  
 }
    

 function GetModels()
     {
   
      $("divModels").innerHTML = "";
      
      //var elts_makers = document.getElementsByClassName("search_selected", $("carmakes"));

        if ($("carmakes_all").checked == false && elts_makers.length == 1)
	    {	
	        $("model").hide();
            $("divModels").show();
	        var maker =  makers_list;	
	        var SURL = "Model.aspx?mk=" + maker;  
            new Ajax.Updater("divModels",SURL, { 
            asynchronous:true, evalScripts:true,
            onFailure : function(resp) {  alert("Oops, there's been an error.");  } 
              });   
             timer = window.setTimeout("displaySelectedModel()", 1000);
        }
        else
        {
            if (elts_makers.length != 1 || $("carmakes_all").checked == true)
            {$("model").show();
            $("divModels").hide();}
        }
        
    }

function displaySelectedModel()
    {

        if($("Modelselect"))
        {
            if(timer)
            {
                window.clearTimeout(timer);
            }
            //wrtite the logic here to look at the querystring and extract "Model" value and select it in dropdown
               URL = document.location.href;
               var ArrKeys = URL.toQueryParams();
               if(ArrKeys["smod"])
               {
                   if(ArrKeys["smod"].length > 0 && ArrKeys["smod"]!="undefined" && MakeChanged==false)
                    {
                        selectedmodel = ArrKeys["smod"];
                        for(i=0; i < $("Modelselect").options.length; i++)
                        {
                            if($("Modelselect").options[i].value == ArrKeys["smod"])
                            {
                                $("Modelselect").options[i].selected = true;
                                
                                break;
                            }
                        }
                    }
                }
        }
        

    }

function SelectEnhanceBasiclist()
{

       URL = document.location.href;
           var ArrKeys = URL.toQueryParams();
        var index =0;
        if($("EnhanceBasiclist"))
        {
            for(index=0;index<$("EnhanceBasiclist").options.length;index++)
                {
                    if( $("EnhanceBasiclist").options[index].value == ArrKeys["ERBList"].substring(0,1) || $("EnhanceBasiclist2").options[index].value == ArrKeys["ERBList"].substring(0,1) )
                    {
                         $("EnhanceBasiclist").options[index].selected = true;
                         $("EnhanceBasiclist2").options[index].selected = true;
                         break;
                  }
               }
        }

}



//New functions end's here
//----------------------------

// This function supports to display the selected search criteria 
function SelectedItems()
{
    var model_out = "";
         URL = document.location.href;
         var ArrKeys = URL.toQueryParams();
         var model ="";
         $("mod").value = "";
         if (elts_makers.length > 1 || $("carmakes_all").checked == true) 
              { 
                model = ArrKeys["mod"];
                $("mod").value = ArrKeys["mod"];
              }
         else{ 
              if ($("carmakes_all").checked == false && elts_makers.length == 1) 
                 { 
                  model = ArrKeys["smod"];
                  $("mod").value = ArrKeys["mod"];
                 }
              }
          if(! model)
           {
              model_out = "";
               $("mod").value ="";
            }
           else{model_out = "<font style='color:black;'><b>Model :</b></font>" + model +"<br />";    }

// Displaying the selected Search elements
    $("SelectedItems").innerHTML = Selectedmakers+ model_out+ Selectedbodies + Selectedinters + Selectedyears + Selectedmiles + Selectedtrans + Selectedcyl + Selectedfl+ Selectedcsn  + Selectedinvtype + Selectedsales + Selectedaucs;


        URL = document.location.href;
                var ArrKeys = URL.toQueryParams();
                var index =0;
                if($("EnhanceBasiclist"))
                {
                    for(index=0;index<$("EnhanceBasiclist").options.length;index++)
                        {
                            if( $("EnhanceBasiclist").options[index].value == ArrKeys["ERBList"].substring(0,1) || $("EnhanceBasiclist2").options[index].value == ArrKeys["ERBList"].substring(0,1) )
                            {
                                 $("EnhanceBasiclist").options[index].selected = true;
                                 $("EnhanceBasiclist2").options[index].selected = true;
                                 break;
                          }
                       }
                }

}




function createstrings() {

	var querystring
 	
	if ($("carmakes_all").checked == true || elts_makers.length == 0){makers_out = "";}
	else { 
		elts_makers.each(function(makers_out) { makers_list.push(makers_out.attributes["value"].value);});
		makers_out = "mk=" + makers_list.join("&mk="); 
	}
	if ($("bodystyles_all").checked  || elts_bodies.length == 0) {bodies_out = "";}
	else { 
		elts_bodies.each(function(bodies_out) { bodies_list.push(bodies_out.attributes["value"].value); });
		bodies_out = "b=" + bodies_list.join("&b="); 		
	}
	if ($("intstyles_all").checked || elts_inters.length == 0) {inters_out = "";}
	else { 
		elts_inters.each(function(inters_out) { inters_list.push(inters_out.attributes["value"].value);});
		inters_out = "int=" + inters_list.join("&int=");			
	}
	if ($("sales_all").checked || elts_sales.length == 0) {sales_out = "";}
	else { 
 		elts_sales.each(function(sales_out) { sales_list.push(sales_out.attributes["value"].value); });
 		sales_out = "sal=" + sales_list.join("&sal="); 				
	}
	if ( $("aucs_all").checked || elts_aucs.length == 0) {aucs_out = "";}
	else { 
		    elts_aucs.each(function(aucs_out) { aucs_list.push(aucs_out.attributes["value"].value); });
		    aucs_out = "A=" + aucs_list.join("&A=");
	    }
	var start_year = $("syr").options[$("syr").selectedIndex].value;
     var end_year = $("eyr").options[$("eyr").selectedIndex].value;
    
   	if ($("syr").selectedIndex == 0 && $("eyr").selectedIndex == 0) { years_out = "";} 
	if ($("syr").selectedIndex != 0 && $("eyr").selectedIndex == 0) { years_out = "syr=" + start_year + "&eyr=2008";} 
	if ($("syr").selectedIndex == 0 && $("eyr").selectedIndex != 0) { years_out = "syr=0&eyr=" + end_year;}
	if ($("syr").selectedIndex != 0 && $("eyr").selectedIndex != 0) { years_out = "syr=" + start_year + "&eyr=" + end_year;}

	if (start_year == end_year) { years_out="syr="+start_year.join("&")+ "eyr=" +start_year;}

	var start_odo = $("sodo").options[$("sodo").selectedIndex].value;
	var end_odo = $("eodo").options[$("eodo").selectedIndex].value;
	
	if ($("sodo").selectedIndex == 0) {start_odo = "0"}
	if ($("eodo").selectedIndex == 0) {end_odo = "1100000"}	

	if ($("sodo").selectedIndex == 0 && $("eodo").selectedIndex == 0) { miles_out = "";}
	if ($("sodo").selectedIndex != 0 && $("eodo").selectedIndex == 0) { miles_out = "sodo=" + start_odo+"&eodo=1100000"; }
	if ($("sodo").selectedIndex == 0 && $("eodo").selectedIndex != 0) { miles_out = "sodo=0&eodo="+ end_odo ; }
	if ($("sodo").selectedIndex != 0 && $("eodo").selectedIndex != 0) { miles_out = "sodo=" + start_odo + "&" + "eodo=" + end_odo ; }

	if ($("tr").selectedIndex == 0) { trans_out = "" ;}
	else { trans_out = "tr="+$("tr").options[$("tr").selectedIndex].value; }
	
	if ($("cyl").selectedIndex == 0) { cyl_out = "";}
	else {cyl_out = "cyl=" + $("cyl").options[$("cyl").selectedIndex].value ; }
	
	if ($("fl").selectedIndex == 0) { fl_out = "" ;}
	else { fl_out = "fl="+$("fl").options[$("fl").selectedIndex].value; }
       
   if ($("inv").selectedIndex == 0) { invtype_out = "" ;}
   else { invtype_out = "inv="+$("inv").options[$("inv").selectedIndex].value;}

   if ($("csn").selectedIndex == 0){csn_out = "";}
   else { csn_out = "csn="+$("csn").options[$("csn").selectedIndex].value;}
 
 
 }

function Submit_onclick()

{

       if ($("carmakes_all").checked == true || elts_makers.length == 0)
          {
                makers_out = "";}       
	   else { 
		        elts_makers.each(function(makers_out) { makers_list.push(makers_out.attributes["value"].value);});
		        makers_out = "mk=" + makers_list.join("&mk="); 
      	    }
	if ($("bodystyles_all").checked  || elts_bodies.length == 0) 
	    {
            bodies_out = "";
	    }
	else { 
		    elts_bodies.each(function(bodies_out) { bodies_list.push(bodies_out.attributes["value"].value); });
		    bodies_out = "b=" + bodies_list.join("&b="); 		
      	}
	if ($("intstyles_all").checked || elts_inters.length == 0) 
	    {
	        inters_out = "";
	    }
	else { 
		    elts_inters.each(function(inters_out) { inters_list.push(inters_out.attributes["value"].value);});
		    inters_out = "int=" + inters_list.join("&int=");			
	    }
	if ($("sales_all").checked || elts_sales.length == 0) 
	    {
	        sales_out = "";
	    }
	else { 
 		    elts_sales.each(function(sales_out) { sales_list.push(sales_out.attributes["value"].value); });
 		    sales_out = "sal=" + sales_list.join("&sal="); 				
	    }
	if ( $("aucs_all").checked || elts_aucs.length == 0) 
	    {
	        aucs_out = "";
	    }
	else { 
		    elts_aucs.each(function(aucs_out) { aucs_list.push(aucs_out.attributes["value"].value); });
		    aucs_out = "A=" + aucs_list.join("&A=");
	    }
	var start_year = $("syr").options[$("syr").selectedIndex].value;
     var end_year = $("eyr").options[$("eyr").selectedIndex].value;
    
   	if ($("syr").selectedIndex == 0 && $("eyr").selectedIndex == 0) { years_out = "";} 
	if ($("syr").selectedIndex != 0 && $("eyr").selectedIndex == 0) { years_out = "syr=" + start_year + "&eyr=2008";} 
	if ($("syr").selectedIndex == 0 && $("eyr").selectedIndex != 0) { years_out = "syr=0&eyr=" + end_year;}
	if ($("syr").selectedIndex != 0 && $("eyr").selectedIndex != 0) { years_out = "syr=" + start_year + "&eyr=" + end_year;}

	if (start_year == end_year) { years_out="syr="+start_year.join("&")+ "eyr=" +start_year;}

	var start_odo = $("sodo").options[$("sodo").selectedIndex].value;
	var end_odo = $("eodo").options[$("eodo").selectedIndex].value;
	
	if ($("sodo").selectedIndex == 0) {start_odo = "0"}
	if ($("eodo").selectedIndex == 0) {end_odo = "1100000"}	

	if ($("sodo").selectedIndex == 0 && $("eodo").selectedIndex == 0) { miles_out = "";}
	if ($("sodo").selectedIndex != 0 && $("eodo").selectedIndex == 0) { miles_out = "sodo=" + start_odo+"&eodo=1100000"; }
	if ($("sodo").selectedIndex == 0 && $("eodo").selectedIndex != 0) { miles_out = "sodo=0&eodo="+ end_odo ; }
	if ($("sodo").selectedIndex != 0 && $("eodo").selectedIndex != 0) { miles_out = "sodo=" + start_odo + "&" + "eodo=" + end_odo ; }

	if ($("tr").selectedIndex == 0) { trans_out = "" ;}
	else { trans_out = "tr="+$("tr").options[$("tr").selectedIndex].value; }
	
	if ($("cyl").selectedIndex == 0) { cyl_out = "";}
	else {cyl_out = "cyl=" + $("cyl").options[$("cyl").selectedIndex].value ; }
	
	if ($("fl").selectedIndex == 0) { fl_out = "" ;}
	else { fl_out = "fl="+$("fl").options[$("fl").selectedIndex].value; }
       
   if ($("inv").selectedIndex == 0) { invtype_out = "" ;}
   else { invtype_out = "inv="+$("inv").options[$("inv").selectedIndex].value;}

   if ($("csn").selectedIndex == 0){csn_out = "";}
   else { csn_out = "csn="+$("csn").options[$("csn").selectedIndex].value;}
       
        var string = "sb=advanced&vw=pre";
        if (aucs_out.length == 0  )
        {
       // toggleopaque("aucs_all","auctions");
          aucs_out ="";
        }
        else
        {
         aucs_out = aucs_out + "&";
        }
        
        if (makers_out.length == 0  ){makers_out = "mk=";}
        if (bodies_out.length == 0){bodies_out = "b=";}
        if (inters_out.length == 0){inters_out = "int=";}
        if (sales_out.length == 0){sales_out = "sal=";}
        if (trans_out.length == 0){trans_out="tr="}
        if (years_out.length == 0){years_out = "syr=0&eyr=2008";}
        if (miles_out.length == 0){miles_out = "sodo=0&eodo=110000";}
        if (cyl_out.length == 0){cyl_out = "cyl=";}
        if (fl_out.length == 0){fl_out = "fl=";}
        if (invtype_out.length == 0){invtype_out = "inv=";}
        if (csn_out.length == 0){csn_out = "";}

        //elts_makers.each(function(makers) { makers_list.push(makers.innerHTML);});

        if ($("carmakes_all").checked == false && elts_makers.length == 1)
          { mod = encodeURIComponent($("Modelselect").options[$("Modelselect").selectedIndex].value);
            mod_out ="&mod=&smod="+ mod;
          }
         else{
          if (elts_makers.length != 1 || $("carmakes_all").checked == true) 
          { mod = encodeURIComponent($("mod").value);  
            mod_out ="&mod="+ escape(mod);}
          }
 var querystring = aucs_out + Trim(makers_out) + mod_out +"&" + bodies_out + "&" + inters_out + "&" + sales_out + "&"+ trans_out + "&" + years_out + "&" + csn_out + "&" + miles_out + "&" + cyl_out + "&" + fl_out + "&" + invtype_out + "&" + csn_out;

     if ( $("hfSort").value > 0 )
        {    var sort = "";
             var i= $("hfSort").value ;
             if (i == 1){sort = "YMkMd"}
             if (i == 2){sort = "MkYMd"}
             if (i == 3){sort = "MkMdY"}
             if (i == 4){sort = "Style"}
             if (i == 5){sort = "Color"}
             if (i == 6){sort = "Miles"}
             if (i == 7){sort = "AtDtLnRn"}
             if (i == 8){sort = "DtAtLnRn"}
             if (i == 9){sort = "Cert"}
             if (i == 10){sort = "IntColor"}
             if (i == 11){sort = "BTAsc"}
             if (i == 12){sort = "BTDes"}
          querystring = querystring + "&sort=" + sort; 
        }
      else {if ($("hfSort").value == 0) {querystring = querystring + "&sort=YMkMd" ;} }
      var list = "";
 
      if($("hfDispAs"))
        list = $("hfDispAs").value;
      if (list.length > 0 ){ querystring = querystring + "&ERBList=" + list; }
      else {querystring = querystring + "&ERBList=Elist" ;} 
           
           var url = window.location.href;
           var rpp = "";
           if ( url.indexOf("RPP") <= 0 )  { rpp = "&rpp=" + $("RPP").value; }
           //else{rpp = "&rpp=50";}
           
             querystring = querystring + "&"+ string;
           
             window.location.href ="Search2.aspx?" + Trim(querystring) + Trim(rpp);
             return true
             
    AppendToLog("After Submit_onclick");
         
}
  
  
function AppendToLog(sLog)
{
    if ($("hfTimer"))
    {
        var currTime = new Date(); 
       $("hfTimer").value += "<br>" +  sLog + ": " + currTime; 
       //alert($("hfTimer").value)
    }    
}

//This function supports to display the selected items in the search control 


/*
function SelectedItems()
{
    debugger;
var URL = document.location.href;

   if ($("carmakes_all").checked  || elts_makers.length == 0){makers = "";}
	else { 
		elts_makers.each(function(makers) { makers_list.push(makers.innerHTML);});
		makers = "<font style='color:black;'><b>Makes: </b></font>"+ makers_list.join(",")+"<br />"; 
	}

   if ($("bodystyles_all").checked  || elts_bodies.length == 0) {bodies = "";}
	else { 
		elts_bodies.each(function(bodies) { bodies_list.push(bodies.innerHTML); });
		bodies = "<font style='color:black;'><b>BodyStyles: </b></font>"+ bodies_list.join(",")+"<br />"; 		
       } 

	if ($("intstyles_all").checked || elts_inters.length == 0) {inters = "";}
	else { 
		elts_inters.each(function(inters) { inters_list.push(inters.innerHTML);});
		inters ="<font style='color:black;'><b>Interiors: </b></font>"+ inters_list.join(",")+"<br />";			
	  }

	if ($("sales_all").checked || elts_sales.length == 0) {sales = "";}
	else { 
 		elts_sales.each(function(sales) { sales_list.push(sales.innerHTML); });
 		sales = "<font style='color:black;'><b>sales: </b></font>" + sales_list.join(",")+"<br />"; 				
	  }

	if ($("aucs_all").checked || elts_aucs.length == 0) { aucs = ""; }
	else { 
		elts_aucs.each(function(aucs) { aucs_list.push(aucs.innerHTML); });
		aucs = "<font style='color:black;'><b>At: </b></font>"+ aucs_list.join(",")+"<br />";
   if ($("tr").selectedIndex == 0) { trans = "" ;}
	else { trans ="<font style='color:black;'><b>Transmission: </b></font>"+$("tr").options[$("tr").selectedIndex].innerHTML+"<br />"; }

	if ($("cyl").selectedIndex == 0) { cyl = "";}
	else {cyl =  $("cyl").options[$("cyl").selectedIndex].innerHTML +"<font style='color:black;'><b>  Engine </b></font>"+"<br />"; }
	
	if ($("fl").selectedIndex == 0) { fl = "" ;}
	else { fl = $("fl").options[$("fl").selectedIndex].innerHTML + "<font style='color:black;'><b>  Fuel </b></font>" +"<br />"; }
	
	if($("csn").selectedIndex == 0){csn = "";}
	else { csn = "<font style='color:black;'><b>  Consignor :</b></font>"+$("csn").options[$("csn").selectedIndex].innerHTML  +"<br />"; }
	
	
	 if ($("inv").selectedIndex == 0) { invtype = "" ;}
   else { invtype = "<font style='color:black;'><b>Inventory :</b></font>"+$("inv").options[$("inv").selectedIndex].innerHTML;}
	
	var start_year = $("syr").options[$("syr").selectedIndex].innerHTML;
	var end_year = $("eyr").options[$("eyr").selectedIndex].innerHTML;

    if ($("syr").selectedIndex == 0 && $("eyr").selectedIndex == 0) { years = "";} 
	    if ($("syr").selectedIndex != 0 && $("eyr").selectedIndex == 0) { years = "<font style='color:black;'><b>Year made is newer than : </b></font>" + start_year+"<br />" ;} 
	    if ($("syr").selectedIndex == 0 && $("eyr").selectedIndex != 0) { years = "<font style='color:black;'><b>Year made is older than: </b></font>" + end_year+"<br />";}
	    if ($("syr").selectedIndex != 0 && $("eyr").selectedIndex != 0) { years = "<font style='color:black;'><b>Year made between: </b></font>" + start_year + "<font style='color:black;'><b> and </b></font>" + end_year+"<br />";}
	    if (start_year == end_year) { years="<font style='color:black;'></b>Made in </b></font>" +start_year+"<br />";}

	var start_odo = $("sodo").options[$("sodo").selectedIndex].value;
	var end_odo = $("eodo").options[$("eodo").selectedIndex].value;
	
	if ($("sodo").selectedIndex == 0) {start_odo = "0"}
	if ($("eodo").selectedIndex == 0) {end_odo = "1100000"}	

	if ($("sodo").selectedIndex == 0 && $("eodo").selectedIndex == 0) { miles = "";}
	if ($("sodo").selectedIndex != 0 && $("eodo").selectedIndex == 0) { miles = "<font style='color:black;'><b>Millege is above</b> </font>" + start_odo+"<font style='color:black;'><b> miles <b></font>"+"<br />"; }
	if ($("sodo").selectedIndex == 0 && $("eodo").selectedIndex != 0) { miles = "<font style='color:black;'><b>Millege is Under </b></font>"  + end_odo +"<font style='color:black;'><b> miles </b></font>"+"<br />"; }
	if ($("sodo").selectedIndex != 0 && $("eodo").selectedIndex != 0) { miles = "<font style='color:black;'><b>Millege is between</b></font>" + start_odo + "<font style='color:black;'><b> and </b></font>"+ end_odo+"<br />" ; }
    
     var model_out = "";
     URL = document.location.href;
     var ArrKeys = URL.toQueryParams();
     var model ="";
     $("mod").value = "";
     if (elts_makers.length > 1 || $("carmakes_all").checked == true) 
          { 
            model = ArrKeys["mod"];
            $("mod").value = ArrKeys["mod"];
          }
     else{ 
          if ($("carmakes_all").checked == false && elts_makers.length == 1) 
             { 
              model = ArrKeys["smod"];
              $("mod").value = ArrKeys["mod"];
             }
          }
      if(! model)
       {
          model_out = "";
           $("mod").value ="";
        }
       else
       {
          model_out = "<font style='color:black;'><b>Model :</b></font>" + model +"<br />";
       }




    if (invtype.length != 0)
    {$("SelectedItems").innerHTML = makers + model_out + bodies + inters + years + miles + trans + cyl + fl + csn + invtype +"<br />"+ sales + aucs;}
    else
    {$("SelectedItems").innerHTML = makers+ model_out + bodies + inters + years + miles + trans + cyl + fl+ csn  + invtype + sales + aucs;}
    
       
       URL = document.location.href;
           var ArrKeys = URL.toQueryParams();
        var index =0;
        if($("EnhanceBasiclist"))
        {
            for(index=0;index<$("EnhanceBasiclist").options.length;index++)
                {
                    if( $("EnhanceBasiclist").options[index].value == ArrKeys["ERBList"].substring(0,1) || $("EnhanceBasiclist2").options[index].value == ArrKeys["ERBList"].substring(0,1) )
                    {
                         $("EnhanceBasiclist").options[index].selected = true;
                         $("EnhanceBasiclist2").options[index].selected = true;
                         break;
                  }
               }
        }
}
*/

//This function supports to perform a new search 

function Performnewsearch()
    {
   AppendToLog("befor Performnewsearch");



        showHideSearch('false');
   AppendToLog("After Performnewsearch");
    }




function NavigatetoSavedSearch()
{

   AppendToLog("befor NavigatetoSavedSearch");


      var url = $("savedsearch").options[$("savedsearch").selectedIndex].value;
      window.location.href  = url;
   AppendToLog("After NavigatetoSavedSearch");
}


//This function supports to display the Enhance and Basic list
function EnhanceorBasiclist(pos)
{ 


   AppendToLog("befor EnhanceorBasiclist");


     var str1,str2;
     var EorBlist = "";
     var URL = document.location.href;
     if(pos == "Top"){EorBlist = $("EnhanceBasiclist").options[$("EnhanceBasiclist").selectedIndex].value;}
     if(pos == "Bottom") {EorBlist = $("EnhanceBasiclist2").options[$("EnhanceBasiclist2").selectedIndex].value;}
     var i = URL.indexOf("ERBList");
       if( EorBlist.length > 0) 
        { 
             str1 = URL.substring(0,i+8);
             str2 = URL.substring(str1.length+5 ,URL.length);
             if(str2.indexOf ("&") >= 0){URL = str2.substring (str2.indexOf ("&"),str2.length );}
             else {URL = "";}
             if (EorBlist == "B"){ document.location.href=str1 + "Blist" +URL;
                //$("EnhanceBasiclist").selectedIndex = 1;    
                }
             if(EorBlist == "M"){ 
                 document.location.href=str1 + "ModSummary" +URL; 
                 //$("EnhanceBasiclist").selectedIndex = 2;
                 } 
           if(EorBlist == "E") {
                 document.location.href=str1 + "Elist" +URL; 
                 //$("EnhanceBasiclist").selectedIndex = 0;
                 }
         }

   AppendToLog("After EnhanceorBasiclist");
 }
 
 
 
 
function PDFresults()
{
   AppendToLog("befor PDFresults");



     var strurl="";
     var str1,str2;
     strurl = document.location.href;
     var index = strurl.indexOf(".aspx");
     
     if(strurl.substring(index-7,index)== "Search2")
     {
       str1 = strurl.substring(0,index-7);
       str2 = strurl.substring(index+5,strurl.length);
       document.location.href =str1 +"Search.ashx" +str2;
     }

   AppendToLog("After PDFresults");
}


function showHideSearch(bHide)
{

   AppendToLog("befor showHideSearch")


          if(bHide=="true")
          {
            document.getElementById("ctl00_ph1_dvSubmit").style.display = 'none';
            document.getElementById("ctl00_ph1_dvsearchOptions").style.display = 'none';
            document.getElementById("ctl00_ph1_dvNewsearch").style.display = 'block';          
          }
          else
          {
            document.getElementById("ctl00_ph1_dvSubmit").style.display = 'block';
            document.getElementById("ctl00_ph1_dvsearchOptions").style.display = 'block';
            document.getElementById("ctl00_ph1_dvNewsearch").style.display = 'none';            
          }
   AppendToLog("After showHideSearch");
}


//This function supports to trim the string
 function Trim(strTrimMe)
 {
 
  strTrimMe += "";
 
  // alert(typeof(strTrimMe));
 
  // Check if anything was passed at all
  // if undefined
  if(typeof(strTrimMe)== typeof(void(0)))
   return '';
 
  if(strTrimMe.toString() == '')
   return '';
 
  // DevDebug
  // strTrimMe = strTrimMe.toString();
 
  // This function is supposed to trim a string from the left and the right.
  // Could be done, cleaner, but ...
 
  var intStart = parseInt(strTrimMe.length, 10);
  var intLast = 0;
 
  // Find the first non-white space character
  for ( var i = 0; i < strTrimMe.length; i++ )
   if (strTrimMe.charAt(i) != ' '){
    intStart = i;
    break;
   }
 

  // Find the last non-white space character
  for ( var i = (strTrimMe.length - 1); i >= intStart; i-- )
   if (strTrimMe.charAt(i) != ' '){
    intLast = i; // Found the first non-whitespace
    break;
   }
 
  // See the definition of substring()
  //alert(strTrimMe.substring(intStart, (intLast + 1)) + ': First: ' + strTrimMe.substring(intStart, intLast).length + ' Last: ' + intStart + ' End: ' + intLast + ' ' ); // DevDebug
 
  if (intLast < intStart)
   return '';
  else
   return strTrimMe.substring(intStart, (intLast + 1));
 
}