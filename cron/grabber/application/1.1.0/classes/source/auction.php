<?php defined('SYSPATH') or die('No direct script access.');

class Source_Auction extends Source implements Kohana_Source {

    public function execute()
    {
        $search = $this->_config['search'];

        foreach ($this->_get_active_items($search['items']) AS $search_id)
        {

            $condition = Kohana::config('search.'.$search_id);

            $car_id = $condition['parent'];

            $car = Jelly::select('admin_cars')
            ->where(':primary_key', '=', $car_id)
            ->execute()
            ->current();

            $fields = $search['fields']+ $condition['fields'] + array(
            //'fromYear' => 2003,
            //'toYear' => 2010
            'fromYear' => $car->year_from,
            'toYear' => $car->year_to
            );



            $options = $this->_remote_options + array(
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => http_build_query($fields),
            );


            $response = Remote::factory($search['url'], $options)->execute();

            /*ob_start();
            require_once($_SERVER['DOCUMENT_ROOT'].'/cron/auction.php');
            $response =  ob_get_contents();
            ob_clean();*/


            $urls = $this->_get_urls($response);
            $total = count($urls);

            if (!$total){
                Kohana::$log->add(Log::ERROR, 'Request returns empty result');
                return ;
            }

            $filtered_by_colors = 0;
            $filtered_by_options = 0;
            $added = 0;
            $passed = 0;
            $cached = 0;

            foreach ($urls AS $item_url)
            {

                $item_url = $this->_config['url'].$item_url;

                $item_url = str_replace('&amp;', '&', $item_url);

                $cached_item=Jelly::select('cars_cache')->where('url','=',$item_url)->execute()->current();




                if ($cached_item->url==null)
                {
                    $response = Remote::factory($item_url, $this->_remote_options)->execute();

                    /*ob_start();
                    require_once($_SERVER['DOCUMENT_ROOT'].'/cron/auction_item.php');
                    $response =  ob_get_contents();
                    ob_clean();*/



                    $filters = $condition['filters'] + array(
                    'mileage' => $car->mileage
                    );
                    $item = $this->_parse($response, $filters);

                }
                else
                {
                    $item = Jelly::factory('cars_cache')->get_item_from_cache($cached_item);
                }


                if (! empty($item)  && !$this->_is_exist_vincode($item['vincode'],$search_id))
                {
                    $item['url'] = $item_url;
                    $item['source_id'] = $this->_id;
                    $item['target_id'] = $car_id;
                    $item['search_id'] = $search_id;
                    $item['options'] = $this->_get_options($this, $item_url);

                    if ($cached_item->url==null)
                    {


                        Filecache::factory()
                        ->set_url($item_url)
                        ->set_path(strtoupper($item['vincode']).'-AUCTION')
                        ->easyImport($response);
                    }

                    Jelly::factory('cars')
                    ->set($item)
                    ->save();

                    //Kohana::$log->add(Log::NOTICE, 'ADDD VIN '.$item['vincode']);

                    if ($cached_item->url==null)
                    {
                        Jelly::factory('cars_cache')
                        ->set($item)
                        ->save();

                        $cached += $this->_cache($item['vincode'], $condition['mark'], $car_id, $condition['cache']);
                    }
                    $passed++;
                }
            }

            unset($urls);

            $colors = $this->_get_colors($car_id);

            if (! empty($colors))
            {
                $filtered_by_colors = Jelly::factory('cars')
                ->color_filter($search_id, $colors);
            }

            $filtered_by_options = $this->_cache_options($car_id, $this->_id);

            $added = $passed - ($filtered_by_colors + $filtered_by_options);


            Jelly::factory('statuses')
            ->set(array(
            'target_id' => $car_id,
            'source_id' => $this->_id,
            'items_added' => $added,
            ))
            ->save();

            $this->_log($car->name, array(
            'found: '.$total,
            'pass primary filters: '.$passed,
            'did not pass the filter by colors: '.$filtered_by_colors,
            'did not pass the filter by options: '.$filtered_by_options,
            'added: '.$added,
            'new vincodes: '.$cached
            ));

        }





    }

    protected function _get_urls($content)
    {
        try {
            preg_match_all('#<td><a href="(.+)"><img[^>]+></a></td>#U', $content, $matches);
            return Arr::get($matches, 1);
        } catch (Kohana_Exception $e) {
            throw $e;
        }
    }



    public function get_ident($url)
    {
        preg_match('!.*car=([^&]*).*!', $url, $res);
        if (isset($res[1])){
            return $res[1];
        }

        return false;

    }


    protected function _parse($content, array $filters = NULL)
    {
        preg_match('#<td[^>]+>Марка:</td><td><span>(.+)</span>#U', $content, $make);
        preg_match('#<td[^>]+>Модель:</td><td><span>(.+)</span>#U', $content, $model);
        preg_match('#<td[^>]+>Модификация:</td><td[^>]+><span>(.+)</span>#U', $content, $modification);
        preg_match('#<strong>Дата и время проведения аукциона:</strong><br />(.+)</td>#U', $content, $date_auction);
        preg_match('#<td[^>]+>Пробег:</td><td><span>(.+)(?:\s+mi)?</span>#U', $content, $mileage);
        preg_match('#<td[^>]+>VIN:</td><td><span>(.+)</span>#U', $content, $vincode);
        preg_match('#<td[^>]+>Цвет кузова:</td><td>(.+)</td>#U', $content, $exterior);
        preg_match('#<br />Купить сейчас на OVE.com - \$([^<]*)</td>#U', $content, $price);
        //preg_match('#<br /><strong>Прогнозируемая цена:</strong><br />\$([^<]*)</td>#U', $content, $price);
        //preg_match('#<td>.*<strong>Состояние:</strong><a[^>]+>.+</a><br />\$(.+)</td>#U', $content, $price);

        preg_match('#<td[^>]+>Штат:</td><td><span>(.+)</span>#U', $content, $state);
        //preg_match('#<strong>Местоположение:</strong><br />(.+)\-(.+)<br /><strong>#U', $content, $state);

        $name = Arr::get($make, 1).' '.Arr::get($model, 1).' '.Arr::get($modification, 1);
        $date_auction = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s\|\s(?:(\d{2}:\d{2})?).*#', '$3-$1-$2 $4', Arr::get($date_auction, 1));
        $vincode = strtoupper(strip_tags(Arr::get($vincode, 1)));
        $mileage = (int) str_replace(',', '', Arr::get($mileage, 1));
        $price = (int) str_replace(',', '', Arr::get($price, 1));
        $exterior = Arr::get($exterior, 1);
        $state = Arr::get($state, 1);

        try
        {
            $matches = array(
            'vincode' => $vincode,
            'mileage' => $mileage,
            'color'   => $exterior,
            'series' => $name,
            );


            if (Filter::factory($filters, $matches)->validate())
            {

                $result =  array(
                'date_auction' => trim($date_auction),
                'name' => $name,
                'vincode' => $vincode,
                'mileage' => $mileage,
                'price' => $price,
                'exterior' => $exterior,
                'state' => $state,
                'picture' => preg_match('#http://images.dev6.jl.by/jlabProxyList/getImg.php?.*"[^>]+>#', $content)
                );

                return $result;
            }


        }
        catch (Kohana_Exception $e)
        {

            throw $e;
        }
    }




}
