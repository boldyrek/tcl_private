<?
class LastComments extends Proto{

	public function drawContent(){
		if($this->checkAuth()) {
			$this->getContent();
		}
		$this->publish();
	}

	/* Works out the time since the entry post, takes a an argument in unix time (seconds) */
	protected function time_since($original) {
    // array of time period chunks
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'year'),
        array(60 * 60 * 24 * 30 , 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24 , 'day'),
        array(60 * 60 , 'hour'),
        array(60 , 'minute'),
    );

    $today = time(); /* Current unix time  */
    $since = $today - $original;

    // $j saves performing the count function each time around the loop
    for ($i = 0, $j = count($chunks); $i < $j; $i++) {

        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];

        // finding the biggest chunk (if the chunk fits, break)
        if (($count = floor($since / $seconds)) != 0) {
            // DEBUG print "<!-- It's $name -->\n";
            break;
        }
    }

    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";

    if ($i + 1 < $j) {
        // now getting the second item
        $seconds2 = $chunks[$i + 1][0];
        $name2 = $chunks[$i + 1][1];

        // add second item if it's greater than 0
        if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
            $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
        }
    }
    return $print;
}

    protected function getContent(){

       $offset = 5;

       $page = intval(isset($_GET['page']) ? ($_GET['page'] == 0 ? 1 : $_GET['page']) : 1);

       $current_page = abs($page);

		$this->page = '<h3 style="margin:5px">Last comments</h3>';
		$query = "
            SELECT cmnt.*, car.model, usr.log_name
				FROM ccl_".ACCOUNT_SUFFIX."car_comment as cmnt
				INNER JOIN ccl_".ACCOUNT_SUFFIX."cars as car
					ON car.id = cmnt.car_id
				INNER JOIN ccl_".ACCOUNT_SUFFIX."usrs as usr
					ON usr.id = cmnt.user_id
                                ".($_SESSION['user_type'] == 1 ? '' : " WHERE cmnt.type = '1'")."
				ORDER BY cmnt.dat DESC";

        $total = mysql_num_rows(mysql_query($query));

        $total_pages = ceil($total/$offset);

        $query .= sprintf(' LIMIT %d, %d', ($current_page-1)*$offset, $offset);

        $q = mysql_query($query);

		while($r=mysql_fetch_assoc($q)){
            $color = ($r['type'] == '1') ? '#c3d8ff' : '#fff2c9';    
            $this->page .= '
            <div style="width:520px;min-height:80px;max-height:120;border:1px solid navy;margin:4px;padding:0px;overflow:auto;background-color:#eee;font-size:12px;">
                <div style="width:98.2%;border:1px solid navy;margin:0px;padding:0px 4px;overflow:auto;background-color: white;background-color:'.$color.'">
                    <table width="100%">
                        <tr><td width="150"><i>Author:</i> <b>'.$r['log_name'].'</td>
                        <td><a href="/?mod=cars&sw=form&car_id='.$r['car_id'].'">'.$r['model'].'</a></b></td>
                        <td align="right"><span style="font-size:11px;font-style:italic;font-weight:bold;" title="'.date('d.m.Y H:i:s',$r['dat']).'">'.$this->time_since($r['dat']).' ago</span></td>
                        </tr>
                    </table>
                </div>
                <div style="padding:7px; height:80px">'.(($r['type'] == '1') ? '' : '<b>[hidden]</b> &nbsp; &nbsp; ').$r['text'].'</div>
            </div>
            ';  // ['.$r['id'].'] - 
		}

                $this->page .= $this->pagination($total_pages, $current_page);

		$this->page .= '<div style="padding:7px;font-size:8px;">Last update: <abbr id="last_update" class="timeago" title="'.date('Y-m-d\TH:i:s\Z').'" style="font-weight:bold;">'.date('M d, Y H:i:s').'</abbr> &nbsp; &nbsp; <a href="javascript:void(0);" onclick="reload_comments()">reload now</a></div>';
    }

   protected function pagination($total_pages, $current_page)
   {
      // Number of page links in the begin and end of whole range
      $count_out = 3;
      // Number of page links on each side of current page
      $count_in = 3;

      // Beginning group of pages: $n1...$n2
      $n1 = 1;
      $n2 = min($count_out, $total_pages);

      // Ending group of pages: $n7...$n8
      $n7 = max(1, $total_pages - $count_out + 1);
      $n8 = $total_pages;

      // Middle group of pages: $n4...$n5
      $n4 = max($n2 + 1, $current_page - $count_in);
      $n5 = min($n7 - 1, $current_page + $count_in);
      $use_middle = ($n5 >= $n4);

      // Point $n3 between $n2 and $n4
      $n3 = (int) (($n2 + $n4) / 2);
      $use_n3 = ($use_middle && (($n4 - $n2) > 1));

      // Point $n6 between $n5 and $n7
      $n6 = (int) (($n5 + $n7) / 2);
      $use_n6 = ($use_middle && (($n7 - $n5) > 1));

      // Links to display as array(page => content)
      $links = array();

      // Generate links data in accordance with calculated numbers
      for ($i = $n1; $i <= $n2; $i++)
      {
         $links[$i] = $i;
      }
      if ($use_n3)
      {
         $links[$n3] = '&hellip;';
      }
      for ($i = $n4; $i <= $n5; $i++)
      {
         $links[$i] = $i;
      }
      if ($use_n6)
      {
         $links[$n6] = '&hellip;';
      }
      for ($i = $n7; $i <= $n8; $i++)
      {
         $links[$i] = $i;
      }

      $first_page = ($current_page === 1) ? FALSE : 1;
      $previous_page = ($current_page > 1) ? $current_page-1 : FALSE;
      $next_page = ($current_page < $total_pages) ? $current_page+1 : FALSE;
      $last_page = ($current_page >= $total_pages) ? FALSE : $total_pages;

      $url = '/?mod=home&amp;sw=lastcomments&amp;page=';

      $out = '<p class="pagination">';

      $out .= ($first_page !== FALSE) ? '<a href="'.$url.$first_page.'" rel="page-'.$first_page.'">&lt;&lt;</a>' : '';

      $out .= ($previous_page !== FALSE) ? '<a href="'.$url.$previous_page.'" rel="page-'.$previous_page.'">&lt;</a>' : '';

      foreach ($links as $number => $content)
      {
         $out .= ($number === $current_page) ? '<strong>'.$content.'</strong>' : '<a href="'.$url.$number.'" rel="page-'.$number.'">'.$content.'</a>';
      }
      
      $out .= ($next_page !== FALSE) ? '<a href="'.$url.$next_page.'" rel="page-'.$next_page.'">&gt;</a>' : '';
      
      $out .= ($last_page !== FALSE) ? '<a href="'.$url.$last_page.'" rel="page-'.$last_page.'">&gt;&gt;</a>' : '';

      $out .= '</p>';

      return $out;
   }
}
