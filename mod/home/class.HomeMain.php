<?
class HomeMain extends Proto{

	public function drawContent(){
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
			$this->page .= $this->module_content;
		}
		$this->page .= $this->templates['footer'];

		$this->errorsPublisher();
		$this->publish();
	}

    protected function getContent(){
        $this->page .= '
    <script type="text/javascript" src="/js/jquery-1.4.2_min.js"></script>
	<script type="text/javascript" src="/js/jquery.timeago.js"></script>
        <style>
         p.pagination {text-align:center; margin:10px}
         p.pagination strong, p.pagination a {margin:0 5px}
        </style>

    <div id="home_main_container" style="width:968px;min-height:700px;border:0px;font-size:14px;border:1px solid gray;-moz-border-radius:3px;border-radius:3px;">
        <div id="comments_container" style="float:left;min-width:500px;width:540px;max-width:650px;min-height:650px;overflow:auto;border:1px solid #555;margin:5px;-moz-border-radius: 3px;border-radius: 3px;background-color: white;"></div>
        <div id="last-grabber-cars" style="float:left; width:400px; margin:5px 1px; border:1px solid #555; background-color:#fff">
           <table width="100%" cellpadding="0" cellspacing="0" border="0" class="list">
              <thead>
                 <tr class="title">
                    <td>Дата</td>
                    <td>Модель</td>
                    <td>VIN</td>
                    <td>Цена</td>
                    <td>Пробег</td>
                    <td>Опции</td>
                 </tr>
              </thead>
              <tbody>
              </tbody>
           </table>
        </div>
        <div style="clear:both;height:0;line-height:0;font-size:0"></div>
    </div>
    <script type="text/javascript">
       $.getJSON("http://tcl.makmalauto.com/cron/grabber/export", function(json)
       {
         $("#last-grabber-cars").css("fontSize", "12px");

          var tableBody = "";

          if (! $.isEmptyObject(json))
          {
             var c = "rowA rowB";
             
             $.each(json, function(i, item)
             {
                tableBody += "<tr class=\""+c+"\">";
                tableBody += "<td class=\"sm\">"+item.date_added+"</td>";
                tableBody += "<td class=\"sm\">"+item.name+"</td>";
                tableBody += "<td class=\"sm\">"+item.vincode+"</td>";
                tableBody += "<td class=\"sm\">"+item.price+"</td>";
                tableBody += "<td class=\"sm\">"+item.mileage+"</td>";
                tableBody += "<td class=\"sm\">"+item.options+"</td>";
                tableBody += "</tr>";
                
                c = (c == "rowA") ? "rowA rowB" : "rowA";
             });
             
             $(tableBody).appendTo("#last-grabber-cars table tbody");
          }
       });

       $("p.pagination a").live("click", function(){
          var page = $(this).attr("rel").split("-")[1];
          $("#comments_container").load("/?mod=home&sw=lastcomments&page="+page);
          jQuery("abbr#last_update").timeago();
          return false;
       });

		function reload_comments(){
            $.get(
                "/?mod=home&sw=lastcomments",{},
                function(data){
                    $("#comments_container").html(data);
					jQuery("abbr#last_update").timeago();
                },
                "html"
            );
		}
		
		jQuery(document).ready(function() {
			reload_comments();
			jQuery("abbr.timeago").timeago();
		});

		setInterval("reload_comments()",300000);

    </script>
        ';
    }
}
?>