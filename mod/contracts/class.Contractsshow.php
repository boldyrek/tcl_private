<?

class ContractsShow extends Proto {

    public function drawContent() {
        if($this->checkAuth()) {
            $this->Process();
        }

    }

    function Process() {
        //чистим директорию
        $dir = $_SERVER['DOCUMENT_ROOT'].$this->root_path.'export/contracts';
        $dh = opendir($dir);
        $files = array();
        while (($filename = readdir($dh)) !== false)
        {
            if($filename!='.' and $filename!='..' and $filename!='.svn') unlink($dir.'/'.$filename);
        }
        closedir($dh);



        $content = mysql_fetch_array($this->mysqlQuery("
		SELECT * 
		FROM `ccl_".ACCOUNT_SUFFIX."contracts` 
		WHERE `id` = '".intval($_GET['contract'])."'"));


        if($content['car']=='') $this->redirect($this->root_path.'?mod=contracts&sw=form&contract='.intval($_GET['contract']).'&nocars');
        else {
            $filename = $this->root_path.'export/contracts/contract_'.$content['number'].'.doc';
            require($_SERVER['DOCUMENT_ROOT']."/lib/numwor.php");
            $sum_text = num2strRU($content['sum']);
            $agent_text = num2strRU($content['agent']);

            $cars_list = explode(';',rtrim($content['car'], ';'));

            $i = 0;
            while($i<count($cars_list))
            {
                $carsInfo = mysql_fetch_array($this->mysqlQuery("
				SELECT model,frame, milage
				FROM `ccl_".ACCOUNT_SUFFIX."cars` 
				WHERE `id` = '".intval($cars_list[$i])."'"));
                $cars2print .= ' '.$carsInfo['model'].', (вин код № '.$carsInfo['frame'];
                if($carsInfo['milage']>0) $cars2print .= ', пробег: '.$carsInfo['milage'].' миль';
                $cars2print .= ');';
                $i++;
            }
            $totalcars = $i;
            if($content['data_source']=='client') {
                $client = mysql_fetch_array($this->mysqlQuery("
				SELECT * 
				FROM `ccl_".ACCOUNT_SUFFIX."customers` 
				WHERE `id` = '".intval($content['client'])."'"));
            }
            elseif($content['data_source']=='local') {
                $client['name'] = $content['name'];
                $client['address'] = $content['address'];
                $client['passport'] = $content['passport'];
                $client['contacts'] = $content['contacts'];
            }
            $this_day = date('d');
            $this_month = date('m');
            $this_year = date('y');
            switch($this_month)
            {
                case '01': $month = 'января'; break;
                case '02': $month = 'февраля'; break;
                case '03': $month = 'марта'; break;
                case '04': $month = 'апреля'; break;
                case '05': $month = 'мая'; break;
                case '06': $month = 'июня'; break;
                case '07': $month = 'июля'; break;
                case '08': $month = 'августа'; break;
                case '09': $month = 'сентября'; break;
                case '10': $month = 'октября'; break;
                case '11': $month = 'ноября'; break;
                case '12': $month = 'декабря'; break;
            }

            if (isset($_GET['type']) && $_GET['type'] == 'supply'){
                require('templates/contract_supply.php');
                $filename = str_replace('contract_', 'contract_supply', $filename);
            }
            else{
                require('templates/contract.php');
            }



            //пишем текст контракта в выходной файл
            $handle = fopen($_SERVER['DOCUMENT_ROOT'].$filename, 'w+');

            if (fwrite($handle, $out) === FALSE) {
                echo "Cannot write to file ($filename)";
                exit;
            }

            fclose($handle);

            $this->redirect($filename);
        }

    }

}
?>