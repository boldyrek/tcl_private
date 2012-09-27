<?

class ContainersForm extends Proto {
	
	var $content;
	var $id;
	var $slots;
	var $expeditors;
	var $ports;
	var $orphants;
	var $loaded;
	var $myport;
	var $files;
	var $stuff;
	var $stuff_onboard;
	
	public function drawContent() {
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
	
	function getStuff()
	{
		$sql = "SELECT id, name FROM ccl_".ACCOUNT_SUFFIX."stuff WHERE container='0' and name!='' and deliveried='0'";
		$res = mysql_query($sql);
		if ($res && mysql_num_rows($res)>0)
		{
			while (list($id,$name) = mysql_fetch_array($res)) {
				$this->stuff[$id] = $name;				
			}
		}
	}
	
	function getStuffOnBoard()
	{
		$sql = "SELECT x.id_stuff, y.name FROM ccl_".ACCOUNT_SUFFIX."stuff_container as x, ccl_".ACCOUNT_SUFFIX."stuff as y WHERE x.id_cont='{$this->id}' AND x.id_stuff=y.id ORDER BY y.name ASC";
		$res = mysql_query($sql);
		if ($res && mysql_num_rows($res)>0)
		{
			while (list($id, $name) = mysql_fetch_array($res)) {
				$this->stuff_onboard[] = array('id'=>$id,'name'=>$name);				
			}
		}
	}
	
	function getContent() {
		// готовим данные для формы контейнера
		require ($_SERVER['DOCUMENT_ROOT'].$this->root_path.'mod/containers/templates/container.form.php');
		
		if(isset($_GET['cont_id'])) {
			$this->id = intval($_GET['cont_id']);
			$this->content = mysql_fetch_array($this->mysqlQuery("
			SELECT * FROM `ccl_".ACCOUNT_SUFFIX."containers`
			WHERE `id` = '".$this->id."'"));
			
			if($this->content['id']=='') { 
				$this->errorHandler('<div class="warn" style="width:900px;">'.$this->translate->_('Ошибка! Контейнер с такими параметрами в базе не обнаружен').'</div>', 1);
			}
			$this->getMoreInfo();
			$form_link = '?mod=containers&sw=save&id='.intval($_GET['cont_id']);
		}
		elseif(isset($_GET['add'])) {
			$this->getMoreInfo();
			$form_link = '?mod=containers&sw=add';
		}
		
		$this->getContFiles();
		
		$this->getStuff();
		$this->getStuffOnBoard();
		
		$this->page .= containerForm($form_link,
								$this->content,
								$this->orphants,
								$this->loaded,
								$this->id,
								$this->expeditors,
								$this->ports,
								$this->myport,
								$this->files,
								$this->stuff,
								$this->stuff_onboard);
	}
	
	function getMoreInfo() {
		$this->slots = $this->mysqlQuery(
		"SELECT id,model,frame,container,port 
		FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE 1 
		ORDER BY `container` DESC");
		
		if($this->content['port']!=0 and $this->content['port']!='')
		$this->expeditors = $this->mysqlQuery("
		SELECT * FROM `ccl_".ACCOUNT_SUFFIX."expeditors` WHERE `ports` LIKE('%".$this->content['port'].";%') ORDER BY `name` ASC");
	
		$this->ports = $this->mysqlQuery("
		SELECT * FROM `ccl_".ACCOUNT_SUFFIX."ports` WHERE 1 ORDER BY name ASC");
		if($this->content['port']!=0) $this->getPopulation();
	}
	
	function getPopulation() {
	
		$this->myport = mysql_fetch_array($this->mysqlQuery("
		SELECT * FROM `ccl_".ACCOUNT_SUFFIX."ports`
		WHERE id = '".$this->content['port']."' LIMIT 1"));
		
		$orphants[1] = array();
		
		$i=1;
		$q=1;
		$goods=1;
		$num = mysql_num_rows($this->slots);
		$loaded = array();
		while($q<=$num)
		{
			$check = mysql_fetch_array($this->slots);
			if($check['container']=='0') 
			{
				//список свободных автомобилей
				$this->orphants[$i]['port'] = $check['port'];
				$this->orphants[$i]['id'] = $check['id'];
				$this->orphants[$i]['frame'] = $check['frame'];
				$this->orphants[$i]['model'] = $check['model'];
				$i++;	
			}
	
			$r=1;
			while($r<=5)
			{
				if($this->content['slot'.$r]==$check['id'])
				{
					$this->loaded[$r.'id'] = $check['id']; //список автомобилей в этом контейнере		
					$this->loaded[$r.'model'] = $check['model'];
					$this->loaded[$r.'frame'] = $check['frame'];
					$goods++;
				}
				$r++;
			}
			$q++;
		}
	}
	
	function getContFiles() {
		$this->files = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."container_files` WHERE `container` = '".$this->id."'");
	}
}
?>