var SIBCOM_PROGRESSDIV = '#header-progress';
var SIBCOM_PROBLEMDIV = '#header-problem';


var SIBNOTES = function(el) {
    var self = this;
    this.Container = $(el);
    this.Container.addClass('Grid');
    this.Container.html('<div class=Results><table cellspacing=1 cellpadding=0 border=0 class=TResults style=width:600px><tr class=Hdr><td><div class=button style=float:right>ADD NOTE</div></td></tr></table></div>');
    $('.button', this.Container).click(function() {
        var firsttr = $('tr:eq(1)', self.Container);
        var cls = firsttr.attr('class');
        if ($('textarea', firsttr).length > 0) return;
        cls = (cls == 'lin' ? 'alin' : 'lin');
        $('tr:eq(0)', self.Container).after('<tr class=' + cls + '><td><a href="javascript:{var i;}">SAVE</a>&nbsp;&nbsp;&nbsp;<a href="javascript:{var i;}">CANCEL</a><br><textarea style=width:590px;height:200px;></textarea></td></tr>');
        $('a:contains(\'SAVE\')', self.Container).unbind().click(function() {
            $('a', self.Container).remove();
            var obj = { ParentID: self.ParentID, ParentType: self.NoteType, Note: $('textarea', self.Container).val() }
            SIBCOM.SendData('data.ashx', 'noteinsert', obj, self.noteAdded);
        });
        $('a:contains(\'CANCEL\')', self.Container).unbind().click(function() {
            $('tr:eq(1)', self.Container).remove();
        });
    });

    this.noteAdded = function(data, status) {
        var tr = $('tr:eq(1)', self.Container);
        var cls = tr.attr('class');
        tr.remove();
        $('tr:eq(0)', self.Container).after('<tr class=' + cls + '><td><b>(Just Added)</b><br>' + data.postdata.Note.replace(/[\r\n]/g, '<br>') + '</td></tr>');
    }

    this.setResults = function(data, NoteType, ParentID) {
        this.NoteType = NoteType;
        this.ParentID = ParentID;
        var cont = [];
        for (var i = 0; i < data.length; i++) {
            var dat = data[i];
            var cls = ((i % 2) == 1 ? 'lin' : 'alin');
            cont.push('<tr class=' + cls + ' NoteID=' + dat.NoteID.toString() + '><td>');
            cont.push('<b>' + (new Date(dat.DateCreated)).toLocaleDateTime() + ' - ' + dat.User + '</b><br><font color=gray>');
            cont.push(dat.Note.replace(/[\r\n]/g, '<br>'));
            cont.push('</font></td></tr>');
        }
        $('tr:gt(0)', this.Container).remove();
        $('tr:eq(0)', this.Container).after(cont.join(''));
    }
}




//SIBLING GRID BUILDER
var SIBGRID = function(el, config) {
    el = $(el);
    this.Keys = [];
    this.Config = config;
    this.Container = el;
    this.Functions = config.functions;
    this.PostProcess = config.postProcess;
    el.addClass('Grid')
        .html('<div class=LeftHdr></div><div class=NavHdr></div><div class=clear></div><div class=Results></div>');
    $('.LeftHdr', el).html(config.header);
    var cols = config.layoutspec;
    var cont = [];
    var hd = [];
    cont.push('<table cellpadding="0" cellspacing="1" border="0" class="TResults">');
    hd.push('<tr class=Hdr><td style="width:0%;"></td>');
    for (var i = 0; i < cols.length; i++) {
        var col = cols[i];
        var wid = (col.width ? 'style="width:' + col.width + ';"' : '');
        if (col.key) this.Keys.push(col.fld);
        if (col.ordinal > -1) hd.push('<td ' + wid + '>' + col.label + '</td>');
    }
    hd.push('</tr>');
    this.Header = hd.join('');
    cont.push(this.Header);
    cont.push('<tr class=NoResults><td colspan="' + (cols.length + 1) + '">No Results</td></tr></table>');
    $('.Results', el).html(cont.join(''));

    this.__Page = function(el) {
        el = $(el);
        if ($('img[class="seld"]', el).length > 0) return;
        if ($(el).attr('class') == 'nvnopage') return;
        var iPage = $(el).attr('pg');
        if (this.NavInfo.tresults != null) {
            this.setResults({
                navinfo: {
                    tresults: this.NavInfo.tresults,
                    Page: (new Number(iPage)),
                    PageSize: this.NavInfo.PageSize
                }
            });
        } else {
            this.PostData.Page = iPage;
            SIBCOM.SendData('data.ashx', this.PostData.__proc, this.PostData, this.__Paged, this);
        }
    }
    this.__Paged = function(data, status, sendr) {
        sendr.setResults({
            results: data.rs1,
            navinfo: data.rs2[0],
            postdata: data.postdata
        });
    }

    this.clearResults = function() { this.setResults({ results: [] }); }

    //THIS.SETRESULTS 
    //data.results = Array of objects with data for the grid
    //data.postdata = Parameters sent to get search results (required for navigation)
    //data.navinfo = Information about paging navigation (required for navigation)
    //data.navinfo.Numresults = Total number of results that meet search criteria
    //data.navinfo.StartRec = Record number of first returned result
    //data.navinfo.EndRec = Record number of last returned result
    //data.navinfo.PageSize = Number of results in each paged set
    //data.navinfo.Page = Page number of result set
    //data.functions = Array of functions as links on the left-hand of the grid
    //data.functions.text = Link text for function
    //data.functions.callback = Name of callback function when link is clicked
    //data.functions.img = Location of image for button
    this.setResults = function(data) {
        var nv = data.navinfo;
        this.NavInfo = nv;
        if (nv) {
            if (nv.tresults != null) {
                if (!nv.NumResults) nv.NumResults = nv.tresults.length;
                nv.StartRec = (nv.PageSize * (nv.Page - 1)) + 1;
                nv.EndRec = nv.StartRec + (nv.PageSize - 1);
                if (nv.EndRec > nv.NumResults) nv.EndRec = nv.NumResults;
                data.results = nv.tresults.slice(nv.StartRec - 1, nv.EndRec);
            }
            this.PostData = data.postdata;
            var iPages = ((nv.NumResults % nv.PageSize) == 0 ? Math.round(nv.NumResults / nv.PageSize) : Math.round((nv.NumResults / nv.PageSize) - .5) + 1);
            if (iPages > 1) {
                var iPage = nv.Page;
                var iFloor = ((iPage - 3) < 1 ? 1 : iPage - 3);
                var iMax = ((iFloor + 6) > iPages ? iPages : iFloor + 6);
                iFloor = ((iMax - 6) < 1 ? 1 : iMax - 6);

                $('.LeftHdr', this.Container).html(nv.StartRec.toString() + ' to ' + nv.EndRec.toString() + ' of ' + nv.NumResults.toString());

                var nva = new Array();
                nva.push('<a class=nvfirst pg=1><img border=0 src=img/blank.gif width=16 height=16></a>');
                nva.push('<a class=nvprev pg=' + (iPage - 1).toString() + '><img border=0 src=img/blank.gif width=16 height=16></a>');
                for (var i = iFloor; i <= iMax; i++) nva.push('<a class=nvpage pg=' + i.toString() + '>' + i.toString() + '</a>');
                nva.push('<a class=nvnext pg=' + (iPage + 1).toString() + '><img border=0 src=img/blank.gif width=16 height=16></a>');
                nva.push('<a class=nvlast pg=' + iPages.toString() + '><img border=0 src=img/blank.gif width=16 height=16></a>');
                $('.NavHdr', this.Container).html(nva.join(''));

                if (iPage == 1) {
                    $('.NavHdr .nvfirst img', this.Container).addClass('seld');
                    $('.NavHdr .nvprev img', this.Container).addClass('seld');
                }
                if (iPage == iMax) {
                    $('.NavHdr .nvlast img', this.Container).addClass('seld');
                    $('.NavHdr .nvnext img', this.Container).addClass('seld');
                }
                var iPlace = iPage - iFloor;
                $('.NavHdr .nvpage:eq(' + iPlace.toString() + ')').removeClass('nvpage').addClass('nvnopage');
                var grid = this;
                $('.NavHdr a').click(function() { grid.__Page(this); });
            } else {
                $('.LeftHdr', this.Container).html('');
                $('.NavHdr', this.Container).html('');
            }
        }

        this.Results = data.results;
        this.Items = {};
        var cont = new Array();
        cont.push(this.Header);
        var rs = data.results;
        var funcs = this.Functions;

        if (rs.length > 0) {
            for (var i = 0; i < rs.length; i++) {
                var dat = rs[i];
                var cls = ((i % 2) == 1 ? 'lin' : 'alin');
                cont.push('<tr ');
                var k = '';
                for (var j = 0; j < this.Keys.length; j++) {
                    var fld = this.Keys[j];
                    cont.push(fld + '="' + dat[fld] + '" ');
                    k += '[' + fld + '=\'' + dat[fld] + '\']';
                }
                dat.__key = k;
                this.Items[k] = dat;
                cont.push('class=' + cls + '>');
                if (funcs) {
                    cont.push('<td class=func>');
                    for (var j = 0; j < funcs.length; j++) {
                        var inner = (funcs[j].img ? '<img border=0 over="' + funcs[j].over + '" src="' + funcs[j].img + '" />' : funcs[j].text);
                        cont.push('<a href=javascript:{} class=lnk func=' + funcs[j].callback + '>' + inner + '</a>&nbsp;');
                    }
                    cont.push('</td>');
                } else {
                    cont.push('<td>&nbsp;</td>');
                }
                for (var j = 0; j < cols.length; j++) {
                    var col = cols[j];
                    if (col.ordinal > -1) {
                        switch (col.type) {
                            case 'checkbox':
                                var val = (dat[col.fld] ? 'X' : '');
                                cont.push('<td>' + val + '</td>');
                                break;
                            case 'date':
                                var val = (dat[col.fld] == null ? '' : (new Date(dat[col.fld])).toLocaleDate());
                                cont.push('<td>' + val + '</td>');
                                break;
                            case 'number':
                                var val = (dat[col.fld] == null ? '--' : (new Number(dat[col.fld])));
                                if (col.precision && val != '--') val = val.toFixed(col.precision);
                                cont.push('<td align=right>' + val + '</td>');
                                break;
                            default:
                                var val = (dat[col.fld] == null ? '--' : dat[col.fld]);
                                cont.push('<td>' + val + '</td>');
                        }
                    }
                }
                cont.push('</tr>');
            }
        } else {
            cont.push('<tr class=NoResults><td colspan="' + (cols.length + 1) + '">No Results</td></tr>');
        }
        $('.TResults', this.Container).html(cont.join(''));
        var self = this;
        $('.lnk', this.Container).click(function() { SIBGRID.bubbleGridEvent(this,self); });
        this.SetMouseOver();
        //this.Container.show();
        if (this.PostProcess) eval(this.PostProcess + '();');
    }
    this.SetMouseOver = function() {
        $('.lnk img', this.Container)
            .mouseout(function() {
                var el = $(this);
                var src = el.attr('src');
                el.attr('src', src.substr(0, src.length - 9) + '.png');
                $('#mover').hide();
            })
            .mouseover(function() {
                var el = $(this);
                var src = el.attr('src');
                el.attr('src', src.substr(0, src.length - 4) + '_over.png');
                var pos = el.offset();
                $('#tover').html(el.attr('over'));
                var mover = $('#mover');
                mover.css({ left: ((pos.left - mover.width()) - 5).toString() + 'px', top: pos.top.toString() + 'px' }).show();
            });
    }


    this.updateItem = function(data) {
        var cols = config.layoutspec;
        var k = '';
        for (var i = 0; i < this.Keys.length; i++) {
            var fld = this.Keys[i];
            k += '[' + fld + '=\'' + data[fld] + '\']';
        }
        var item = this.Items[k];
        if (!item) {
            this.Results.push(data);
            this.setResults({ results: this.Results, functions: this.Functions });
        } else {
            var tds = $('.TResults tr' + k + ' td', this.Container);
            for (var i = 0; i < cols.length; i++) {
                var col = cols[i];
                if (col.ordinal > -1) {
                    var value = data[col.fld];
                    if (value) {
                        item[col.fld] = value;
                        var td = $(tds.get(parseInt(col.ordinal) + 1));
                        switch (col.type) {
                            case 'checkbox':
                                var val = (data[col.fld] ? 'X' : '');
                                td.text(val);
                                break;
                            default:
                                var val = (data[col.fld] == null ? '--' : data[col.fld]);
                                td.text(val.toString());
                        }
                    }
                }
            }
        }
    }

    this.deleteItem = function(data) {
        var k = '';
        for (var i = 0; i < this.Keys.length; i++) {
            var fld = this.Keys[i];
            k += '[' + fld + '=\'' + data[fld] + '\']';
        }
        this.Items[k] = null;
        for (var i = 0; i < this.Results.length; i++) {
            if (this.Results[i].__key == k) {
                this.Results.splice(i, 1);
                break;
            }
        }
        $('.TResults tr' + k, this.Container).remove();
    }
};
SIBGRID.bubbleGridEvent = function(el,grid) {
    var func = $(el).attr('func');
    var tr = $($(el).parents('tr').get(0));
    var k = '';
    for (var i = 0; i < grid.Keys.length; i++) {
        var fld = grid.Keys[i];
        k += '[' + fld + '=\'' + tr.attr(fld) + '\']';
    }
    var item = grid.Items[k];
    eval(func + '(tr,item);');
}



//SIBLING FORM BUILDER
var SIBFORM = function(el, config) {
    el = $(el);
    this.Container = el;
    this.Config = config;
    var cont = new Array();

    //take the layout and stick in an array for more freindly handling during form build
    this.Items = {};
    var layout = new Array();
    var spec = config.layoutspec;
    var tab = 1;
    for (var i = 0; i < spec.length; i++) {
        var fld = spec[i];
        this.Items[fld.fld] = fld;
        if (fld.ordinal == -1) { cont.push('<input type=hidden id=fld' + fld.fld + ' />'); } else {
            fld.tab = tab++;
            if (!layout[fld.column]) layout[fld.column] = new Array();
            layout[fld.column][fld.ordinal] = i;
        }
    }

    //find the max ordinal position in the layout
    var maxord = 1;
    for (var i = 0; i < layout.length; i++) maxord = (maxord < layout[i].length ? layout[i].length : maxord);

    //build the form
    cont.push('<table class=Fields cellpadding=0 cellspacing=1 border=0>');
    for (var i = 0; i < maxord; i++) {
        cont.push('<tr>');
        for (var j = 0; j < layout.length; j++) {
            var spitem = spec[layout[j][i]];
            if (spitem) {
                var tp = spitem.type.toLowerCase();
                cont.push((!spitem.nullable ? '<td class=LblReq><span>' : '<td class=Label><span>'));
                if (tp != 'button') cont.push(spitem.label);
                cont.push('</span></td>');
                if (spitem.colspan) {
                    cont.push('<td colspan=4 class=Field><span>');
                } else {
                    if (tp == 'button') {
                        cont.push('<td style=padding:0 class=Field><span>');
                    } else {
                        cont.push('<td class=Field><span>');
                    }
                }
                var dis = (spitem.readonly || config.ReadOnly ? ' readonly="true" ' : ' ');
                tab = ' tabindex="' + spitem.tab.toString() + '"';
                switch (tp) {
                    case ('checkbox'):
                        dis = (spitem.readonly || config.ReadOnly ? ' disabled ' : ' ');
                        cont.push('<input type=checkbox id=fld' + spitem.fld + dis + tab + '/>');
                        break;
                    case ('select'):
                        dis = (spitem.readonly || config.ReadOnly ? ' disabled ' : ' ');
                        cont.push('<select id=fld' + spitem.fld + tab + dis + ' ></select>');
                        break;
                    case ('number'):
                        var max = (spitem.maxlen ? ' maxlength=' + spitem.maxlen.toString() : '');
                        cont.push('<input type=text style="text-align:right; width:70px;" id=fld' + spitem.fld + dis + max + tab + '/>');
                        break;
                    case ('lookup'):
                        cont.push('<table cellpadding=0 cellspacing=0 border=0><tr><td><input type=hidden id=fld' + spitem.fld + ' />');
                        cont.push('<input readonly=true type=text style="width:95px;" id=fld' + spitem.textfield + ' /></td><td>');
                        if (!(spitem.readonly || config.ReadOnly)) cont.push('<a href="javascript:{' + spitem.callback + '();}"><img src="' + spitem.img + '" border=0 /></a>');
                        cont.push('</td></tr></table>');
                        break;
                    case ('button'):
                        var cls = (spitem.cls ? spitem.cls : 'button');
                        cont.push('<div class=' + cls + ' id=fld' + spitem.fld + '>' + spitem.label + '</div>');
                        break;
                    case ('password'):
                        var max = (spitem.maxlen ? ' maxlength=' + spitem.maxlen.toString() : '');
                        cont.push('<input type=password id=fld' + spitem.fld + dis + max + tab + '/>');
                        break;
                    default:
                        var prop = '';
                        if (spitem.colspan) prop = ' style="width:100%"';
                        if (spitem.width) prop = ' style="width:' + spitem.width + '"';
                        var max = (spitem.maxlen ? ' maxlength=' + spitem.maxlen.toString() : '');
                        cont.push('<input type=text id=fld' + spitem.fld + dis + max + prop + tab + '/>');
                        break;
                }
                cont.push('</span></td>');
            } else {
                cont.push('<td>&nbsp;</td><td>&nbsp;</td>');
            }
            if (j < layout.length) cont.push('<td class=Spacer></td>');
        }
        cont.push('</tr>');
    }
    cont.push('</table>');

    this.CollectFields = function(obj) {
        var flds = this.Config.layoutspec;
        for (var i = 0; i < flds.length; i++) {
            switch (flds[i].type) {
                case ('checkbox'):
                    obj[flds[i].fld] = ($('#fld' + flds[i].fld, this.Container).attr('checked') ? true : false);
                    break;
                case ('number'):
                    var val = $('#fld' + flds[i].fld, this.Container).val();
                    obj[flds[i].fld] = (val == '' ? null : (new Number(val)));
                    break;
                default:
                    obj[flds[i].fld] = $('#fld' + flds[i].fld, this.Container).val();
            }
        }
    }

    this.ClearFields = function() {
        this.setItem({});
        return;
        var flds = this.Config.layoutspec;
        for (var i = 0; i < flds.length; i++) {
            $('#fld' + flds[i].fld, this.Container).val('');
        }
    }

    this.setVisibility = function(data) {
        for (var i = 0; i < data.length; i++) {
            var vis = data[i];
            var sp = $('span:has(#fld' + vis.fld + ')', this.Container);
            var sp2 = $('span', sp.parent().prev());
            var fld = $('#fld' + vis.fld, this.Container);
            if (vis.visible) {
                sp.show();
                sp2.show();
            } else {
                sp.hide();
                sp2.hide();
            }
            if (vis.readonly) {
                fld.attr('disabled', 'disabled').addClass('readonly');
            } else {
                fld.attr('disabled', '').removeClass('readonly');
            }
        }
    }

    this.setItem = function(data) {
        var flds = this.Config.layoutspec;
        this.Data = data;
        for (var i = 0; i < flds.length; i++) {
            var fld = flds[i];
            switch (fld.type) {
                case 'checkbox':
                    var chk = (data[fld.fld] ? 'checked' : '');
                    $('#fld' + fld.fld, this.Container).attr('checked', chk);
                    break;
                case 'date':
                    var val = (data[fld.fld] == null ? '' : (new Date(data[fld.fld])).toLocaleDate());
                    $('#fld' + fld.fld, this.Container).val(val);
                    break;
                case 'number':
                    var val = (data[fld.fld] == null ? '' : data[fld.fld]);
                    if (fld.precision && val != '') val = (new Number(val)).toFixed(fld.precision);
                    $('#fld' + fld.fld, this.Container).val(val);
                    break;
                case ('lookup'):
                    var val = (data[fld.fld] == null ? '' : data[fld.fld]);
                    $('#fld' + fld.fld, this.Container).val(val);
                    val = (data[fld.textfield] == null ? '' : data[fld.textfield]);
                    $('#fld' + fld.textfield, this.Container).val(val);
                    break;
                case 'select':
                    var val = (data[fld.fld] == null ? '' : data[fld.fld]);
                    var el = $('#fld' + fld.fld, this.Container);
                    var cont = new Array();
                    if (fld.nullable) cont.push('<option></option>');
                    if (fld.lookup) {
                        for (var k = 0; k < fld.lookup.length; k++) {
                            var text = fld.lookup[k].text;
                            var optval = (fld.lookup[k].id ? ' value="' + fld.lookup[k].id + '"' : ' value="' + text + '"');
                            var sel = (fld.lookup[k].sel ? ' selected' : '');
                            cont.push('<option ' + optval + sel + '>' + text + '</option>');
                        }
                    }
                    el.html(cont.join(''));
                    if ($('option[value=\'' + val + '\']', el).length == 0) el.prepend('<option>' + val + '</option>');
                    el.val(val);
                    break;
                default:
                    var val = (data[fld.fld] == null ? '' : data[fld.fld]);
                    $('#fld' + fld.fld, this.Container).val(val);
                    break;
            }
        }
    }

    //set the content
    el.html(cont.join(''));
}





//SIBLING TAB
function SIB(){}
SIB.InitTags = function() {
    $('input[readonly],select[disabled],input[disabled]').addClass('readonly');

    $('.tab')
    .each(function() {
        var t = $(this);
        var cont = t.html();
        t.html('<div class=tbleft><div class=tbright><div class=tbcont>' + cont + '</div></div></div>');
    })
    .click(function() {
        var dv = $('.sel').removeClass('sel').attr('tab');
        $('#' + dv).hide();
        dv = $(this).addClass('sel').attr('tab');
        $('#' + dv).show();
    });
    $('.button, .brightbutton').each(function() {
        var t = $(this);
        var cont = t.html();
        t.html('<div class=buttonleft><img height=21 width=7 src=img/blank.gif></div><div class=buttoncont>' + cont + '</div><div class=buttonright><img height=21 width=7 src=img/blank.gif></div>');
        var width = t.attr('button-width');
        if (width) {
            width = new Number(width);
            t.css('width', width.toString() + 'px');
            $('.buttoncont', t).css('width', (width - 14).toString() + 'px');
        }
    });
    $('input[autoclick]').each(function() {
        var t = $(this);
        var clicker = t.attr('autoclick');
        t.keydown(function(e) { if (e.keyCode == 9 || e.keyCode == 13) { $('#' + clicker).click(); e.preventDefault(); return false; } });
    });
}


SIB.SetTop = function(cfg) {
    var cont = [];
    cont.push((cfg.Icon ? '<div style=float:left><img src="' + cfg.Icon + '" /></div> ' : ''));
    cont.push('<div style=float:left>' + cfg.Title + '</div>');
    if (cfg.MoreTitle) cont.push(cfg.MoreTitle);
    $('#header-title').html(cont.join(''));
    if (cfg.Buttons) {
        var cont = [];
        var bts = cfg.Buttons;
        for (var i = bts.length - 1; i >= 0; i = i - 1) {
            var bt = bts[i];
            cont.push('<div class=' + bt.Class + ' id=' + bt.id + ' style="float:right;"><div class=buttonleft><img height=21 width=7 src=img/blank.gif></div><div class=buttoncont>'
            + bt.Text + '</div><div class=buttonright><img height=21 width=7 src=img/blank.gif></div></div>');
        }
        $('#header-buttons').html(cont.join(''));
        for (var i = 0; i < bts.length; i++) $('#' + bts[i].id).click(bts[i].Click);
    } else {
        $('#header-buttons').html('');
    }
    if (cfg.Tabs) {
        var cont = [];
        var tbs = cfg.Tabs;
        for (var i = 0; i < tbs.length; i++) {
            var tb = tbs[i];
            cont.push('<div class="tab' + (tb.Selected ? ' sel"' : '"') + (tb.Tab ? ' tab=' + tb.Tab : '') + (tb.id ? ' id=' + tb.id : '') + '><div class=tbleft><div class=tbright><div class=tbcont>' + tb.Text + '</div></div></div></div>');
        }
        $('#header-tabs').html(cont.join(''));
        for (var i = 0; i < tbs.length; i++) {
            var tb = tbs[i];
            if (typeof (tb.Click) == 'function') {
                $('#header-tabs .tab:eq(' + i.toString() + ')').click(tb.Click);
            } else {
                $('#header-tabs .tab:eq(' + i.toString() + ')').click(function() { SIB.SetTab(this); });
            }
        }
        $('#header-tabs .tab:not(.sel)').each(function() { $('#' + $(this).attr('tab')).hide(); });
        $('#header-tabs .sel').click();
    } else {
        $('#header-tabs').html('');
    }
}
SIB.SetTab=function(tab) {
    var dv=$('.sel').removeClass('sel').attr('tab');
    $('#'+dv).hide();
    dv=$(tab).addClass('sel').attr('tab');
    $('#'+dv).show();
}



//SIBLING COMMUNICATIONS
function SIBCOM(){}

SIBCOM.SendData=function(page,rqtype,obj,ret,sendr) {
    obj.__proc=rqtype;
    obj = { data: JSON.stringify(obj), proc: rqtype };
    $.ajax({
        type: 'POST',
        data: obj,
        url: page,
        dataType: 'json',
        success: function(data,status) { SIBCOM.ReceiveData(data,status,ret,sendr); },
        complete: function(data,status) { SIBCOM.CompleteData(data,status,ret,sendr); }
    });
    $(SIBCOM_PROGRESSDIV).show();
    $(SIBCOM_PROBLEMDIV).hide();
}
SIBCOM.ReceiveData = function(data, status, ret, sendr) {
    if (data.status == 'session') {
        window.open('logout.aspx', '_self');
        return;
    }
    if (data.status != 'success') {
        $(SIBCOM_PROBLEMDIV).show();
        $(SIBCOM_PROBLEMDIV).unbind().click(function() {
            $('#ErrForm').draggable().css({ top: '50px', left: '50px' }).show();
        });
        $('#ErrCancel').unbind().click(function() { $('#ErrForm').hide(); });
    } else {
        $(SIBCOM_PROGRESSDIV).hide();
        if (typeof ret == 'function') {
            ret(data, status, sendr);
        } else {
            eval(ret + '(data,status);');
        }
    }
}
SIBCOM.CompleteData=function(data, status, ret, sendr) {
    SIBCOM._LastError=data;
    if (status != 'success') {
        if (typeof ret == 'function') {
            ret(data, status, sendr);
        } else {
            eval(ret + '(data,status);');
        }
    }
}

SIBCOM.LoadSelect = function(select, data, value, text, selval){
    select = $(select);
    cont = new Array();
    var selected = false;
    cont.push('<option></option>');
    for(var i=0;i<data.length;i++) {
        var item = data[i];
        var val = item[value];
        var txt = item[text];
        var itemsel = (val==selval);
        var sel = (itemsel ? ' selected' : '');
        cont.push('<option value="'+val+'"'+sel+'>'+txt+'</option>');
    }
    select.html(cont.join(''));
}
SIBCOM.Preload = function() {
    var d = document;
    if (!d.preload) d.preload = new Array();
    var j = d.preload.length;
    var args = SIBCOM.Preload.arguments;
    args = ((typeof args[0]) == 'string' ? args : args[0]);
    for (var i = 0; i < args.length; i++) {
        d.preload[j] = new Image;
        d.preload[j++].src = args[i];
    }
}




function GetCookie(sName) {
    // cookies are separated by semicolons
    var aCookie = document.cookie.split("; ");
    for (var i = 0; i < aCookie.length; i++) {
        // a name/value pair (a crumb) is separated by an equal sign
        var aCrumb = aCookie[i].split("=");
        if (sName == aCrumb[0])
            return unescape(aCrumb[1]);
    }
    // a cookie with the requested name does not exist
    return '';
}



var PrinterLabel = GetCookie('PrinterLabel');
var PrinterPaper = GetCookie('PrinterPaper');
function PrintReport(url) {
    //        if ($.browser.msie && PrinterLabel.length > 0 && PrinterPaper.length > 0){
    //            arv.DataPath = url;
    //        }else{
    window.open(url + '&format=pdf', '_tab');
    //        }
}

function ReportLoaded() {
    var url = arv.DataPath;
    if (url.indexOf('report=medlabel') > -1) {
        arv.Printer.DeviceName = PrinterLabel;
    } else {
        arv.Printer.DeviceName = PrinterPaper;
    }
    arv.PrintReport(false);
}







if (!this.JSON) {
    JSON = function() {

        function f(n) {
            // Format integers to have at least two digits.
            return n < 10 ? '0' + n : n;
        }

        Date.prototype.toJSON = function(key) {

            return this.getUTCFullYear() + '-' +
                 f(this.getUTCMonth() + 1) + '-' +
                 f(this.getUTCDate()) + 'T' +
                 f(this.getUTCHours()) + ':' +
                 f(this.getUTCMinutes()) + ':' +
                 f(this.getUTCSeconds()) + 'Z';
        };

        String.prototype.toJSON =
        Number.prototype.toJSON =
        Boolean.prototype.toJSON = function(key) {
            return this.valueOf();
        };

        var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
            escapeable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
            gap,
            indent,
            meta = {    // table of character substitutions
                '\b': '\\b',
                '\t': '\\t',
                '\n': '\\n',
                '\f': '\\f',
                '\r': '\\r',
                '"': '\\"',
                '\\': '\\\\'
            },
            rep;


        function quote(string) {

            escapeable.lastIndex = 0;
            return escapeable.test(string) ?
                '"' + string.replace(escapeable, function(a) {
                    var c = meta[a];
                    if (typeof c === 'string') {
                        return c;
                    }
                    return '\\u' + ('0000' +
                            (+(a.charCodeAt(0))).toString(16)).slice(-4);
                }) + '"' :
                '"' + string + '"';
        }


        function str(key, holder) {

            var i,          // The loop counter.
                k,          // The member key.
                v,          // The member value.
                length,
                mind = gap,
                partial,
                value = holder[key];

            if (value && typeof value === 'object' &&
                    typeof value.toJSON === 'function') {
                value = value.toJSON(key);
            }

            if (typeof rep === 'function') {
                value = rep.call(holder, key, value);
            }
            switch (typeof value) {
                case 'string':
                    return quote(value);

                case 'number':

                    return isFinite(value) ? String(value) : 'null';

                case 'boolean':
                case 'null':
                    return String(value);
                case 'object':

                    if (!value) {
                        return 'null';
                    }
                    gap += indent;
                    partial = [];
                    if (typeof value.length === 'number' &&
                        !(value.propertyIsEnumerable('length'))) {
                        length = value.length;
                        for (i = 0; i < length; i += 1) {
                            partial[i] = str(i, value) || 'null';
                        }

                        v = partial.length === 0 ? '[]' :
                        gap ? '[\n' + gap +
                                partial.join(',\n' + gap) + '\n' +
                                    mind + ']' :
                              '[' + partial.join(',') + ']';
                        gap = mind;
                        return v;
                    }
                    if (rep && typeof rep === 'object') {
                        length = rep.length;
                        for (i = 0; i < length; i += 1) {
                            k = rep[i];
                            if (typeof k === 'string') {
                                v = str(k, value);
                                if (v) {
                                    partial.push(quote(k) + (gap ? ': ' : ':') + v);
                                }
                            }
                        }
                    } else {
                        for (k in value) {
                            if (Object.hasOwnProperty.call(value, k)) {
                                v = str(k, value);
                                if (v) {
                                    partial.push(quote(k) + (gap ? ': ' : ':') + v);
                                }
                            }
                        }
                    }

                    v = partial.length === 0 ? '{}' :
                    gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' +
                            mind + '}' : '{' + partial.join(',') + '}';
                    gap = mind;
                    return v;
            }
        }

        return {
            stringify: function(value, replacer, space) {

                var i;
                gap = '';
                indent = '';
                if (typeof space === 'number') {
                    for (i = 0; i < space; i += 1) {
                        indent += ' ';
                    }
                } else if (typeof space === 'string') {
                    indent = space;
                }

                rep = replacer;
                if (replacer && typeof replacer !== 'function' &&
                        (typeof replacer !== 'object' ||
                         typeof replacer.length !== 'number')) {
                    throw new Error('JSON.stringify');
                }

                return str('', { '': value });
            },


            parse: function(text, reviver) {

                var j;

                function walk(holder, key) {

                    var k, v, value = holder[key];
                    if (value && typeof value === 'object') {
                        for (k in value) {
                            if (Object.hasOwnProperty.call(value, k)) {
                                v = walk(value, k);
                                if (v !== undefined) {
                                    value[k] = v;
                                } else {
                                    delete value[k];
                                }
                            }
                        }
                    }
                    return reviver.call(holder, key, value);
                }

                cx.lastIndex = 0;
                if (cx.test(text)) {
                    text = text.replace(cx, function(a) {
                        return '\\u' + ('0000' +
                                (+(a.charCodeAt(0))).toString(16)).slice(-4);
                    });
                }
                if (/^[\],:{}\s]*$/.
test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').
replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

                    j = eval('(' + text + ')');

                    return typeof reviver === 'function' ?
                        walk({ '': j }, '') : j;
                }

                throw new SyntaxError('JSON.parse');
            }
        };
    } ();
}


/// <reference path="isjquery.js" />
Date.prototype.toLocaleDate = function() {
    var dt = new Date(this.getTime() + (this.getTimezoneOffset() * 60000));
    return (dt.getMonth() + 1).toString() + '/' + dt.getDate().toString() + '/' + dt.getFullYear().toString();
}
Date.prototype.toLocaleDateTime = function() {
    var dt = new Date(this.getTime() + (this.getTimezoneOffset() * 60000));
    var min = dt.getMinutes().toString();
    if (min.length == 1) min = '0' + min;
    var sec = dt.getSeconds().toString();
    if (sec.length == 1) sec = '0' + sec;
    return (dt.getMonth() + 1).toString() + '/' + dt.getDate().toString() + '/' + dt.getFullYear().toString() + ' ' +
           dt.getHours().toString() + ':' + min + ':' + sec;
}
Date.prototype.toDate = function() {
    return (this.getMonth() + 1).toString() + '/' + this.getDate().toString() + '/' + this.getFullYear().toString();
}

var is = {
    Null: function(a) {
        return a === null;
    },
    Undefined: function(a) {
        return a === undefined;
    },
    nt: function(a) {
        return (a === null || a === undefined);
    },
    Function: function(a) {
        return (typeof (a) === 'function') ? a.constructor.toString().match(/Function/) !== null : false;
    },
    String: function(a) {
        return (typeof (a) === 'string') ? true : (typeof (a) === 'object') ? a.constructor.toString().match(/string/i) !== null : false;
    },
    Array: function(a) {
        return (typeof (a) === 'object') ? a.constructor.toString().match(/array/i) !== null || a.length !== undefined : false;
    },
    Boolean: function(a) {
        return (typeof (a) === 'boolean') ? true : (typeof (a) === 'object') ? a.constructor.toString().match(/boolean/i) !== null : false;
    },
    Date: function(a) {
        return (typeof (a) === 'date') ? true : (typeof (a) === 'object') ? a.constructor.toString().match(/date/i) !== null : false;
    },
    HTML: function(a) {
        return (typeof (a) === 'object') ? a.constructor.toString().match(/html/i) !== null : false;
    },
    Number: function(a) {
        return (typeof (a) === 'number') ? true : (typeof (a) === 'object') ? a.constructor.toString().match(/Number/) !== null : false;
    },
    Object: function(a) {
        return (typeof (a) === 'object') ? a.constructor.toString().match(/object/i) !== null : false;
    },
    RegExp: function(a) {
        return (typeof (a) === 'function') ? a.constructor.toString().match(/regexp/i) !== null : false;
    }
};

var type = {
    of: function(a) {
        for (var i in is) {
            if (is[i](a)) {
                return i.toLowerCase();
            }
        }
    }
};