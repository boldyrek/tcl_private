<?php

class Autotrader {

    const OFFSET = 25;

    protected $_id;
    protected $_year;

    protected $_config;

    protected $_total_items = 0;
    protected $_total_pages = 1;

    /**
    * Конструктор.
    * 
    * @param integer $id
    * @param integer $year
    */
    public function __construct($id, $year)
    {
        $this->_id = $id;
        $this->_year = $year;

        $this->_config = Kohana::config('config')->as_array();

        // вычисляем кол-во найденных машин и страниц
        // $this->count_totals();
    }

    public static function factory($id, $year)
    {
        return new self($id, $year);
    }

    /**
    * Возвращает массив данных.
    * 
    * @return array $data
    */
    protected function _data()
    {
        $item = $this->_item();

        $data = array();

        // только при условии, что машины найдены
        if ($this->_total_items > 0)
        {

            $request = new Curl($this->_url(), $this->_config['curl']);
            $response = $request->execute();
            $data = $this->_parse($response);

        }

        return $data;
    }

    /**
    * Вычисляет количество машин и страниц.
    * Также пишет первый запрос в кэш.
    *
    * @return array
    */
    public function count_totals()
    {
        $item = $this->_item();

        $request = new Curl($this->_url(), $this->_config['curl']);

        $response = $request->execute();


        if (preg_match('#<span id="ctl00_ctl00_MainContent_MainContent_lblHeading" class="at_resultCount">(\d*)\D*</span>#sU', $response, $mathes)){

            if (isset($mathes[1])){
                $this->_total_items = $mathes[1];
            }
        }

        Debug::vars('found:',$this->_total_items);

        return $this;
    }

    /**
    * Возвращает данные о модели.
    *
    * @return array
    */
    protected function _item()
    {
        if (! ($item = Arr::get($this->_config['items'], $this->_id)))
        throw new Kohana_Exception('Item not found');

        return $item;
    }

    /**
    * Возвращает URL модели.
    *
    * @return string
    */
    protected function _url()
    {
        $item = $this->_item();

        return $this->_config['domain'].'a/pv/Used/'  // base URL
        .trim($item['uri'], '/').'/all/?'.                   // URI string
        http_build_query($this->_config['query']) // base query string
        //.'&'.http_build_query($item['query']);       // item query string
        .'&yRng='.$this->_year.','.$this->_year.       // item query string
        '&rcp=1000'; //items per page
    }

    /**
    * Парсит запрос.
    * 
    * @param string $content
    * @return array
    */
    protected function _parse($content)
    {
         $result = array();
         
        try
        {
           


            $pattern = '#<div id="ctl00_ctl00_MainContent_MainContent_normalResultsRepeater_ctl\d*_ResultItem2_resultPanel"[^>]*>.*';
            $pattern.='.*<h2><a href=\'([^\']*)\'>.*<span[^>]*>(\d*)</span>.*<span[^>]*>([^>]*)</span>.*</a>.*</h2>';
            $pattern.='.*<div.*id="ctl00_ctl00_MainContent_MainContent_normalResultsRepeater_ctl\d*_ResultItem2_divPrice"[^>]*>([^<]*)</div>';
            $pattern.='.*<div.*id="ctl00_ctl00_MainContent_MainContent_normalResultsRepeater_ctl\d*_ResultItem2_kmPanel"[^>]*>([^<]*)</div>';
            $pattern.= '.*<div style="clear:both;"></div>.*</div>#isU';


            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

            if (!empty($matches))
            foreach ($matches AS $match)
            {

                $url = $match[1];

                $title = $this->_cache($url);


                if (!$title){
                    continue;
                }


                $array = array
                (
                'name' => $title,
                'mileage' => self::_normalize_number($match[4]),
                'price' => self::_normalize_number($match[3])
                );

                // хак. каким-то образом проходят поля с html-тэгами
                $result[$url] = array_map('strip_tags', $array);
            }

            unset($matches);

        
        }
        catch (Kohana_Exception $e)
        {
            //throw $e;
        }
        
            return $result;
    }

    /**
    * Кэш заголовка и описания.
    * 
    * @param string $url
    */
    protected function _cache($url)
    {
        
        $curlurl = $this->_config['domain'].substr( $url, 1 );

        $cache = Jelly::select('calc_cache')
        ->where('url', '=', $url)
        ->limit(1)
        ->execute();

        if ($cache->loaded())
        {
            return $cache->title;
        }
        
        

        $request = Curl::factory($curlurl, $this->_config['curl'])->execute();


        if ($request){

            $pattern= '#.*<h1>([^<]*)<span.*</h1>';
            $pattern.= '.*<div class=\'at_vehicleSpecs\'>.*<div class=\'at_vehicleSpecs\'>(.*)<div class="clear"></div>';
            $pattern.= '.*<span id="ctl00_ctl00_MainContent_MainContent_rptAdDetail_ctl\d*_adDetailControl_tscAdText">(.*)</span>';
            $pattern.= '.*#isU';

            preg_match_all($pattern, $request, $matches, PREG_SET_ORDER);

            if (empty($matches)){
                echo $curlurl;

                echo "<hr /><hr /><hr />";
                echo $request;
                echo "<hr /><hr /><hr />";

                print_r ($matches);
                echo "<hr /><hr /><hr />";
                return '';

            }
            //Features
            $pattern2 = '#.*<h2>.*Features</h2>.*<div class="at_boxContent">(.*)<div class="clear">.*</div>.*#isU';
            preg_match_all($pattern2, $request, $res2);

            $features = '';
            if (isset($res2[1][0])){
                $features = strip_tags($res2[1][0]);
            }

            $title = strip_tags($matches[0][1]);
            $title = str_replace(',', '', $title);
            $title = trim($title);



            $arr = array(
            'add_date' => date('Y-m-d H:i:s'),
            'url' => $url,
            'title' => $title,
            'detalis' => strip_tags($matches[0][3]),
            'technical' => strip_tags($matches[0][2]),
            'features' => $features
            );
            
            Jelly::factory('calc_cache')
            ->set(array_map('trim', $arr))
            ->save();


            return $title;

        }
    }

    /**
    * Возвращает человеко-понятные числа.
    * Например: " 23,456 " --> "23456"
    *
    * @param string $number
    * @return string
    */
    protected static function _normalize_number($number)
    {
        $number= str_replace('km', '', $number);
        $number= str_replace('$', '', $number);
        $number = trim($number);


        return ($number !== '') ? str_replace(',', '', $number) : $number;
    }

}
