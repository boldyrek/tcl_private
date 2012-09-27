    function InitTZClicks(){
        $('#tblTransport .tzEdit').css({'cursor':'pointer'}).unbind().click(function(){TransZipEdit(this);return false;});
        $('#tblTransport .tzDel').css({'cursor':'pointer'}).unbind().click(function(){TransZipDelete(this);return false;});
        $('#tblTransport .tzOrder').css({'cursor':'pointer'}).unbind().click(function(){TransZipOrder(this);return false;});
    }

    function GetRATTransportData(){
        var ToZipsTRs = $('tr[ToZip]');
        var ToZips = '';
        var TZIDs = '';
        for (var i=0;i<ToZipsTRs.length;i++){
            ToZips += $(ToZipsTRs[i]).attr('ToZip') + ','
            TZIDs += $(ToZipsTRs[i]).attr('TZID') + ','
        }
        if(TZIDs!=''){
            QueryRAT(ToZips,TZIDs);
        };
    }
    
    function QueryRAT(ToZips,TZIDs){
        var tbl = $('#tblTransport');
        var RatVIN = tbl.attr('RatVIN');
        var RatVehicleRuns = tbl.attr('RatVehicleRuns');
        var RatFromZip = tbl.attr('RatFromZip');
        var UserID = tbl.attr('UID');
        var obj = {};
        obj.TZIDs = TZIDs;
        obj.RatFromZip = RatFromZip;
        obj.RatToZip = ToZips;
        obj.RatTZID = TZIDs;
        obj.RatVIN = RatVIN;
        obj.RatVehicleRuns = RatVehicleRuns;
        obj.UserID = UserID;
        
        SIBCOM.SendData('RAT.ashx','TransportZips',obj,'ReturnRATTranportData');
    }

    function ReturnRATTranportData(data,status){
        if(data.Message!=''){
            $('#spRATError').show();
            $('#spRATError').html('Unable to Retrieve Transport Information');
        }else{
            for (var i=0;i<data.DropOffLocs.length;i++){
                var tr = $('tr[TZID=\''+data.DropOffLocs[i].TZID+'\']');
                if(data.DropOffLocs[i].ErrorCode == 0) {
                        $('td:eq(3)',tr).html(data.DropOffLocs[i].DropoffLocation);
                        $('td:eq(4)',tr).html(data.DropOffLocs[i].DaysRequired);
                        $('td:eq(5)',tr).html(data.DropOffLocs[i].TotalPrice);
                        $('td:eq(6)',tr).html('<img src="img/btn_order.gif"/>');
                }else{
                        if(data.DropOffLocs[i].ErrorCode<102 || data.DropOffLocs[i].ErrorCode>105){
                            $('#spRATError').show();
                            $('#spRATError').html(data.DropOffLocs[i].ErrorMessage);
                            $('td:eq(3)',tr).html('&nbsp;');
                            $('td:eq(4)',tr).html('&nbsp;');
                            $('td:eq(5)',tr).html('&nbsp;');
                            $('td:eq(6)',tr).html('&nbsp;');
                        }else{
                            $('td:eq(3)',tr).html('<font color="red">Invalid dropoff zip</font>');
                            $('td:eq(4)',tr).html('&nbsp;');
                            $('td:eq(5)',tr).html('&nbsp;');
                            $('td:eq(6)',tr).html('&nbsp;');
                        };
                };
            }
            InitTZClicks();
         };
     }
    
    function TransZipAdd(){
        varTransZipAddEdit.ClearFields();
        $('#dvTransportResults,#tdZipLinks').hide();
        $('#dvTransZipAdd,#divTransZipResult').show();
    }
    
    function TransZipCancel(){
        $('#dvTransportResults,#tdZipLinks').show();
        $('#dvTransZipAdd').hide();
    }
    
     function TransZipDelete(el){
        var TZID = $(el).parent().attr('TZID');
        if (window.confirm('Are you sure you want to delete this location?')){
            var obj = {};
            obj.TZID = TZID;
            SIBCOM.SendData('data.ashx','TransZipDelete',obj,'');
            var tr = $('tr[TZID=\''+TZID+'\']');
            tr.remove();
        }
        InitTZClicks();
    }
    
    function TransZipOrder(el){
        var TZID = $(el).parent().attr('TZID');
        var tr = $('tr[TZID=\''+TZID+'\']');
        var tbl = $('#tblTransport');
        var RatHRef = tbl.attr('RatHRef') + '&BUYERID=' + TZID + '&BUYERNAME=' + $('td:eq(2)',tr).html() + 
        '&BUYERADD1=' + tr.attr('ToAddr') + '&BUYERCITY=' + tr.attr('ToCity') + '&BUYERSTATE=' + tr.attr('ToState') + 
        '&BUYERZIP=' + tr.attr('ToZip') + '&BUYERCONTACT=' + tr.attr('ToCName') + '&BUYERPHONE' + tr.attr('ToCPhone') +
        '&BUYEREMAIL' + tr.attr('ToCEmail');
        window.open(RatHRef,'_blank','height=540,width=670,top=50,left=50'); 
    }
   
    function TransZipEdit(el){
        var TZID = $(el).parent().attr('TZID');
        varTransZipAddEdit.ClearFields();
        var tr = $('tr[TZID=\''+TZID+'\']');
        var obj = {};
        obj.TransportZipID = TZID;
        obj.DealerName = $('td:eq(2)',tr).html();
        obj.Address = tr.attr('ToAddr');
        obj.City = tr.attr('ToCity');
        obj.State = tr.attr('ToState');
        obj.Zipcode = tr.attr('ToZip');
        obj.ContactName = tr.attr('ToCName');
        obj.ContactPhone = tr.attr('ToCPhone');
        obj.ContactEmail = tr.attr('ToCEmail');
        varTransZipAddEdit.setItem(obj);

        $('#dvTransportResults,#tdZipLinks').hide();
        $('#dvTransZipAdd,#divTransZipResult').show();

        InitTZClicks();
    }


    function TransZipSave(){
        var obj = {};
        varTransZipAddEdit.CollectFields(obj);
        if($.trim(obj.Zipcode)=='' || $.trim(obj.DealerName)==''){
            alert('Missing one or more required elements: Name and Zipcode');
        }else{
            $('#dvTransZipAdd,#spRATError').hide();
            $('#dvTransportResults,#tdZipLinks').show();
            obj.UserID = $('table[RatVIN]').attr('UID');
            if ($('#fldTransportZipID').val().length>0){
                var TZID = obj.TransportZipID;
                var tr = $('tr[TZID=\''+TZID+'\']');
                var OldZip = tr.attr('ToZip');
                if(OldZip==obj.Zipcode){
                    obj.RequeryRAT=0;
                }else{
                    obj.RequeryRAT=1;
                    $('td:eq(2)',tr).html('&nbsp;');
                    $('td:eq(3)',tr).html('&nbsp;');
                    $('td:eq(4)',tr).html('&nbsp;');
                    $('td:eq(5)',tr).html('&nbsp;');
                };
                SIBCOM.SendData('data.ashx','TransZipUpdate',obj,'TransZipUpdated');
            }else{
                SIBCOM.SendData('data.ashx','TransZipInsert',obj,'TransZipInserted');
            };
        };
    }
  
    function TransZipUpdated(data,status){
        var obj = data.postdata;
        obj.TransportZipID = data.id;
        var tr = $('tr[TZID=\''+obj.TransportZipID+'\']');
        $('td:eq(2)',tr).html(obj.DealerName);
        tr.attr('ToCEmail',obj.ContactEmail);
        tr.attr('ToCPhone',obj.ContactPhone);
        tr.attr('ToCName',obj.ContactName);
        tr.attr('ToAddr',obj.Address);
        tr.attr('ToCity',obj.City);
        tr.attr('ToState',obj.State);
        tr.attr('ToZip',obj.Zipcode);
        if (obj.RequeryRAT==1){
            QueryRAT(obj.Zipcode,obj.TransportZipID);
        };
    }

  
    function TransZipInserted(data,status){
        var obj = data.postdata;
        obj.TransportZipID = data.id;
        var cont=[];
        cont.push('<tr style="height:22px;" ToZip="'+obj.Zipcode+'" TZID="'+obj.TransportZipID+'">');
        cont.push('<td class="tzEdit" style="width:16;"><img src="img/grd_edit.png"/></td>');
        cont.push('<td class="tzDel" style="width:17;"><img src="img/grd_del.png"/></td>');
        cont.push('<td class="ulText name" style="width:200px;">'+obj.DealerName+'</td>');
        cont.push('<td class="ulText loc">&nbsp;</td>');
        cont.push('<td class="ulText days" style="width:95; text-align:center;">&nbsp;</td>');
        cont.push('<td class="ulText price" style="text-align:right; width:65px;">&nbsp;</td>');
        cont.push('<td class="tzOrder" style="width:55; text-align:center;"><img src="img/btn_order.gif"/></td></tr>');
        $('table[RatFromZip]').append(cont.join(''));
        var tr = $('tr[TZID=\''+obj.TransportZipID+'\']');
        tr.attr('ToCEmail',obj.ContactEmail);
        tr.attr('ToCPhone',obj.ContactPhone);
        tr.attr('ToCName',obj.ContactName);
        tr.attr('ToAddr',obj.Address);
        tr.attr('ToCity',obj.City);
        tr.attr('ToState',obj.State);
        QueryRAT(obj.Zipcode,obj.TransportZipID);
        InitTZClicks();
    }


    $(document).ready(function () {

        SIB.InitTags();
        GetRATTransportData();
        InitTZClicks();
        msgMaxBidLessCurrent = msgMaxBidLessCurrent + addSeparatorsNF($('#ph1_hidBidCurrent').attr('value'), '.', '.', ',') + ".";
        varTransZipAddEdit = new SIBFORM('#divTransZipResult', Layouts.TransZipAdd);
        zoomOptions = {
            zoomWidth: 410,
            zoomHeight: 400,
            xOffset: 10,
            position: 'left',
            title: false,
            showPreload: true,
            preloadText: 'Loading Zoom'
        }

        $('.ImgLink, #vehImageZoom #imgSlide').click(
            function () {
                BigPic($(this).attr('imageIndex'));
            }
        );

        $('#ph1_txtMaxBid').keyup(
           function () {
               var BidBuyWarning = $("#ph1_litBidBuyWarning");
               var BidCurrent = $('#ph1_hidBidCurrent').attr('value');
               var BidAction = $('#ph1_hidBidAction').attr('value');
               if ((this.value != '') && BidAction == 'change') {
                   if ((Number(this.value)) < (BidCurrent)) {
                       BidBuyWarning.text(msgMaxBidLessCurrent);
                       BidBuyWarning.css('font-weight', 'bold');
                   }
                   else if (!Number(this.value)) {
                       BidBuyWarning.text(msgInvalidBid);
                   }
                   else {
                       BidBuyWarning.text(msgChangeBid + addSeparatorsNF(this.value, '.', '.', ',') + ".");
                       BidBuyWarning.css('font-weight', 'normal');
                   }
               } else if (BidAction == 'place') {
                   BidBuyWarning.text(msgPlaceBid);
               } else {
                   BidBuyWarning.text('');
               }
           })

        $('#ph1_chkDeleteBid').change(
            function () {
                var MaxBid = $('#ph1_txtMaxBid');
                var Submit = $('#ph1_cmdSubmit');
                var BidWarning = $("#ph1_litBidBuyWarning");
                var BidAction = $('#ph1_hidBidAction');
                if (this.checked) {
                    MaxBid.attr('value', '');
                    MaxBid.attr('disabled', 'disabled');
                    MaxBid.css('backgroundColor', 'lightgray');
                    Submit.attr('value', 'Confirm');
                    BidWarning.text(msgDeleteBid);
                } else {
                    BidAction.value = 'change';
                    BidWarning.text('');
                    MaxBid.removeAttr('disabled');
                    MaxBid.css('backgroundColor', 'white');
                    Submit.attr('value', 'Change Bid');
                }
            })

        if (!bArchivedVehicle) {
            $('#dvMagnification').toggle(
                function () {
                    $('#imgSlide').unbind();
                    $('#vehImageZoom').jqzoom(zoomOptions);
                    $('#spMagSetting').html('On');
                    if ($.browser.safari) {
                        NextImage();
                        PrevImage();
                    };
                    tgImageZoomOn = true;
                }, function () {
                    $('#vehImageZoom').unbind();
                    $('#imgSlide').click(function () { BigPic($(this).attr('imageIndex')); });
                    $('#spMagSetting').html('Off');
                    tgImageZoomOn = false;
                }
            );
        } else {
            //$('div.preload,#dvMagnification').remove();
            $('div.preload').remove();
            $('#dvMagnification').attr('style', 'height:5px;').html('');
        }

        $('a.popupAutoChk').click(function () {
            $('a.close,#fade').unbind();
            if ($('#txtFilled').val() != 'filled') {
                SIBCOM.SendData('data.ashx', 'AutoCheckGetDealers', {}, 'ReturnAutoChkDealers');
            }
            $('#popupDealershipEdit').fadeIn().css({ 'width': Number(550) }).prepend('<a href="#" class="close"><img src="img/close_pop.png" class="btn_close" title="Close Window" alt="Close" border="0" /></a>');


            var popMargTop = ($('#popupDealershipEdit').height() + 80) / 2;
            var popMargLeft = ($('#popupDealershipEdit').width() + 80) / 2;

            $('#popupDealershipEdit').css({
                'margin-top': -popMargTop,
                'margin-left': -popMargLeft
            });

            $('body').append('<div id="fade"></div>');
            $('#fade').css({ 'filter': 'alpha(opacity=80)' }).fadeIn();

            return false;
        });
        $('a.close, #fade').live('click', function () {
            $('#fade , .popup_block').fadeOut(function () {$('#fade, a.close').remove();});
            return false;
        });

    });

    function ReturnAutoChkDealers(data, status) {
        var cont = [];
        for (var i = 0; i < data.rs1.length; i++) {
            cont.push('<tr UDID=' + data.rs1[i].UserDealerID + ' SID=' + data.rs1[i].AutoCheckSID + '>');
            cont.push('<td' + ((i % 2 == 0) ? ' style="background-color:#DDDDDD;padding:5px;">' : ' style="background-color:#f1f1f1;padding-left:5px;">') + data.rs1[i].DealerName + '</td>');
            if (data.rs1[i].AutoCheckSID.length == 0) {
                cont.push('<td style="height:45px;vertical-align:middle;padding:5px;background-color:' + ((i % 2 == 0) ? '#DDDDDD;' : '#f1f1f1;') + '"><input style="width:80px"><span style="color:#1a60d8;cursor:pointer;"><a class="SaveSID">&nbsp;Save</a></span></td><td style="height:45px;vertical-align:middle;padding:5px;background-color:' + ((i % 2 == 0) ? '#DDDDDD;' : '#f1f1f1;') + '"></td>');
            } else {
                cont.push('<td style="height:45px;vertical-align:middle;padding:5px;background-color:' + ((i % 2 == 0) ? '#DDDDDD;' : '#f1f1f1;') + '">' + data.rs1[i].AutoCheckSID + '&nbsp;&nbsp;<span style="color:#1a60d8;cursor:pointer"><a class="EditSID">Edit</a></span></td><td style="height:45px;vertical-align:middle;padding:5px;background-color:' + ((i % 2 == 0) ? '#DDDDDD;' : '#f1f1f1;') + '"><span style="color:#1a60d8;float:right;cursor:pointer;"><a class="acLink"><img src="img/acGetReport.png" alt="Report" /></a></span></td>');
            }
            cont.push('</tr>');
        }
        if (data.rs1.length > 0) {
            $('#divNoDealers').hide();
            $('#tblAutoChkDealerships tbody:last').append(cont.join(''));
            $('#tblAutoChkDealerships').show();
            $('#txtFilled').val('filled');
        } else {
            $('#divNoDealers').show();
        }
        $('#imgLoadDealers').hide();
        $('a.EditSID').live("click", function () {
            $(this).parentsUntil('table', 'tr').children().eq(2).html('');
            $(this).parentsUntil('tr', 'td').html('<input style="width:80px" value="' + $(this).parentsUntil('tbody', 'tr').attr('SID') + '"><span style="color:#1a60d8;cursor:pointer;"><a class="SaveSID">&nbsp;Save</a></span>');
        });
        $('a.SaveSID').live("click", function () {
            var obj = {};
            obj.UserDealerID = $(this).parentsUntil('tbody', 'tr').attr('UDID');
            obj.SID = $(this).parentsUntil('tr', 'td').children('input').val();
            $(this).parentsUntil('tbody', 'tr').attr('SID', obj.SID);
            SIBCOM.SendData('data.ashx', 'AutoCheckSaveDealerSID', obj, '');
            if ($(this).parentsUntil('tr', 'td').children('input').val().trim() != '') {
                $(this).parentsUntil('table', 'tr').children().eq(2).html('<span style="color:#1a60d8;float:right;cursor:pointer;"><a class="acLink"><img src="img/acGetReport.png" alt="Report" /></a></span>');
                $(this).parentsUntil('tr', 'td').html(obj.SID + '&nbsp;&nbsp;<span style="color:#1a60d8;cursor:pointer;"><a class="EditSID">Edit</a></span>');
            }
        });
        $('a.acLink').live("click", function () {
            $('#fade,.popup_block').fadeOut(function () { $('#fade, a.close').remove(); });
            $('#txtACVIN').val($('#tdVIN').text());
            $('#txtACSID').val($(this).parentsUntil('tbody', 'tr').attr('SID'));
            $('#ACRpt').submit();
        });
        $('a.acClose').live("click", function () {
            $('#fade,.popup_block').fadeOut(function () { $('#fade, a.close').remove(); });
        });

    }
