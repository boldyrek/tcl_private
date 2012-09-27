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
   public function  __construct($id, $year)
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
         for ($i = 0; $i < $this->_total_pages; $i++)
         {
            if ($i == 0)
            {
               // первая страница из кэша
               $response = Cache::instance()->get($item['name']);
            }
            else
            {
               // запрос остальных страниц
               $request = new Curl($this->_url().'&rcs='.($i*self::OFFSET), $this->_config['curl']);

               $response = $request->execute();
            }

            // парсим постранично и складываем в массив
            $data += $this->_parse($response);
         }

         // стираем кэш
         Cache::instance()->delete($item['name']);
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

      if (preg_match('#<div class="title_breadcrumb_container">.*<h1>.*(\d+) Used.*</h1>.*</div>#sU', $response, $mathes))
      {
         // считаем кол-во машин на текущий момент
         if (($total = (int) Arr::get($mathes, 1)) > 0)
         {
            // пишем результат в кэш для парсинга первой страницы
            Cache::instance()->set($item['name'], $response);

            // вычисляем кол-во страниц
            if ($total > self::OFFSET)
            {
               $this->_total_pages = (int) ceil($total/self::OFFSET);
            }

            $this->_total_items = $total;
         }
         else
         {
            /*
            throw new Kohana_Exception('No used vehicles found for :name',
               array(':name' => $item['name']));
            */
         }
      }

      Debug::vars('found:',$this->_total_items, 'pages:', $this->_total_pages);

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
      .trim($item['uri'], '/').'/'                   // URI string
      .'?'.http_build_query($this->_config['query']) // base query string
      //.'&'.http_build_query($item['query']);       // item query string
      .'&yRng='.$this->_year.','.$this->_year;       // item query string
   }

   /**
    * Парсит запрос.
    * 
    * @param string $content
    * @return array
    */
   protected function _parse($content)
   {
      $content = preg_replace(array('/(\n|\r)+/', '/>\s+</', '/\s+/'), array('', '><', ' '), $content);

      $pattern  = '#<div class="used_result_container(?:\s+highlight)?">.*';
      $pattern .= '<div class="makemodeltrim">';
      $pattern .= '<a href="(.+)".*class="carlink">(.+)</a>'; // url, model
      $pattern .= '</div>.*';
      $pattern .= '<div class="kilometers">.*';
      $pattern .= '(?:(.+)kms)?'; // mileage
      $pattern .= '</div>.*';
      $pattern .= '<div.*class="price">.*';
      $pattern .= '(?:\$(.+))?'; // price
      $pattern .= '</div>.*';
      $pattern .= '<div class="description">.*';
      $pattern .= '<span.*>(.*)(?:<a[^>]+>.*</a>)?</span>.*'; // description
      $pattern .= '</div>.*';
      $pattern .= '</div>';
      $pattern .= '#sU';

      try
      {
         $result = array();

         preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

         foreach ($matches AS $match)
         {
            $url = trim(parse_url(Arr::get($match, 1), PHP_URL_PATH), '/'); // url for array index (only path)

            $this->_cache($url);

            $array = array
            (
               'name' => trim(Arr::get($match, 2)),
               'mileage' => self::_normalize_number(Arr::get($match, 3)),
               'price' => self::_normalize_number(Arr::get($match, 4))
            );

            // хак. каким-то образом проходят поля с html-тэгами
            $result[$url] = array_map('strip_tags', $array);
         }

         unset($matches);

         return $result;
      }
      catch (Kohana_Exception $e)
      {
         throw $e;
      }
   }

   /**
    * Кэш заголовка и описания.
    * 
    * @param string $url
    */
   protected function _cache($url)
   {
      $new_url = $this->_config['domain']
      .str_replace(' ', '+', $url)
      .'/?ms=trucks_vans';

      $request = Curl::factory($new_url, $this->_config['curl'])->execute();

      preg_match('#<h1 class="title_matchPageSize".*>(.+)</h1>#sU', $request, $title);
      preg_match('#<div class="d_Box"[^>]+>\s+<div class="d_title">More Details</div>\s+<div class="d_content">(.*)</div>\s+</div>\s+<div class="d_Box">.*#sU', $request, $detalis);
      preg_match('#<div class="d_Box">\s+<div class="d_title">Technical</div>\s+<div class="d_content">(.*)</div>\s+</div>.*#sU', $request, $technical);
      
      $cache = Jelly::select('calc_cache')
      ->where('url', '=', $url)
      ->limit(1)
      ->execute();

      if (! $cache->loaded())
      {
         Jelly::factory('calc_cache')
         ->set(array_map('trim', array(
            'url' => $url,
            'title' => Arr::get($title, 1),
            'detalis' => strip_tags(Arr::get($detalis, 1)),
            'technical' => strip_tags(Arr::get($technical, 1))
         )))
         ->save();
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
      $number = trim($number);

      return ($number !== '') ? str_replace(',', '', $number) : $number;
   }

}
