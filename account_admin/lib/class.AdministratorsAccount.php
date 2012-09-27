<?

class AdministratorsAccount extends AdministratorsAbstract {


	public function makePage () {
		$this -> page	= $this -> templates['header'];

		if ( $this -> checkAuth () ) {
			$this -> page .= $this -> makeTopMenu ();
			$this -> moduleContent ();
		} else {
			$this -> createLoginForm ();
			$this -> page	= str_replace ( '<form action="/"', '<form action="/' . self::MODULE_NAME . '/"', $this -> page );
		}

		$this -> page .= $this -> templates['footer'];

		$this -> errorsPublisher ();
		$this -> publish ();
	}

	private function moduleContent () {
		if( isset ( $_GET['action'] ) && $switch = $_GET['action'] ) {
			switch ( $switch ) {
				case 'edit':
					$this -> actionEdit ();
					break;
				case 'add':
					$this -> actionAdd ();
					break;
									case 'delete':
										$this -> actionDelete ();
										break;
				case 'save':
					$this -> actionSave ();
					break;
				default:
					$this -> actionList ();
					break;
			}
		}
		else {
			$this -> actionList ();
		}
	}


	protected function _tablesGenerate ( $id ) {
		require_once ( $_SERVER['DOCUMENT_ROOT'] . '/' . self::MODULE_NAME . '/lib/tables.inc' );

		foreach ( $tables as $table => $query ) {
			$sql	= (string) str_replace ( '%TABLE_NAME%', 'ccl_'. $id . '_' . $table, $query );

			$this -> mysqlQuery ( $sql );
		}

		$this -> mysqlQuery ( "INSERT INTO `ccl_".$id."_settings` VALUES ('car_location','a:3:{i:1;s:8:\"Auction\";i:2;s:19:\"Forwarders company\";i:3;s:6:\"Seller\";}');" );
		$this -> mysqlQuery ( "INSERT INTO `ccl_".$id."_settings` VALUES ('title_location','a:2:{i:1;s:7:\"Seller\";i:2;s:6:\"Buyer\";}');" );
		$this -> mysqlQuery ( "INSERT INTO `ccl_".$id."_settings` VALUES ('purposes','a:5:{i:0;s:6:\"Other\";i:1;s:14:\"Auction price\";i:2;s:12:\"Auction fee\";i:3;s:11:\"Inspection\";i:4;s:8:\"Delivery\";}');" );

		$sql = "INSERT INTO `ccl_".$id."_usrs` (`log_name`, `pass_code`, `type`, `u_id`, `email`, `lang`) VALUES (
		'".mysql_real_escape_string($_POST['login'])."',
		'".md5(mysql_real_escape_string($_POST['pwd1']))."',
		'1', '0',
		'".mysql_real_escape_string($_POST['mail'])."', 'eng'
		)";
	
		
		$this -> mysqlQuery ( $sql );

	}

	protected function _tablesDelete ( $id ) {
		require_once ( $_SERVER['DOCUMENT_ROOT'] . '/' . self::MODULE_NAME . '/lib/tables.inc' );

		foreach ( $tables as $table=>$query ) {
			$sql	= (string) sprintf ( "DROP TABLE `%s`", 'ccl_'.$id . '_' . $table );
			$this -> mysqlQuery ( $sql );
		}



	}



	public function actionList () {
		$sql	= "SELECT * FROM %s ORDER BY `title` ASC";
		$sql	= sprintf ( $sql, self::TABLE_ACCOUNTS );

		$query	= $this -> mysqlQuery ( $sql );
		$count	= mysql_num_rows ( $query );


		$class	= 'rowA rowB';
		$this -> page .= '
		<div class="location">'.$this->translate->_('Список аккаунтов').' &nbsp; | &nbsp; <a href="'.$this -> root_path . self::MODULE_NAME . '/?mod=account&action=add">Добавить аккаунт</a></div>
			<table width="970" border="0" cellspacing="0" cellpadding="0" class="list">
		  <tr class="title">
		    <td width="50" align="center">id</td>
		    <td>'.$this->translate->_('название').'</td>
		  </tr>';

		$i	= 1;
		while ( $i <= $count ) {
			$item	= mysql_fetch_array ( $query );
			$this -> page .= '<tr class="' . $class . ( ! intval ( $item['active'] ) ? ' redTR' : null ) . '" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"  onclick="document.location=\'' . $this -> root_path . self::MODULE_NAME . '/?mod=account&action=edit&id='. $item['id'] . '\'">
				<td align="center">' . $item['id'] . '&nbsp;</td>
				<td>' . $item['title'] . '</td>
				</tr>';
			$i++;
			if ($class=='rowA') $class='rowA rowB'; else $class='rowA';
		}

		$this -> page .= '</table>';
	}


	public function _makeForm ( $values = null ) {
		return '
		<form class="myForm" style="margin:0px;" action="'.$this -> root_path . self::MODULE_NAME . '/?mod=account&action=save&id=' . $values['id'] . '" method="post">
		<div class="cont_car">
		<h3>' . ( isset ( $values['id'] ) ? $this->translate->_('Данные для аккаунта').' &lt;' . $values['title'] . '&gt;' : $this->translate->_('Создание нового аккаунта') ) . '</h3>
		<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
' . ( isset ( $values['id'] ) ? '
		  <tr>
			<td width="113" align="right" class="title rowB">Id</td>
			<td width="202" class="rowA rowB title">' . $values['id'] . '</td>
			<td align="right" class="title rowB"></td>
			<td class="rowA rowB title"></td>
		  </tr>
' : null ) . '
		  <tr>
			<td width="113" align="right" class="title">'.$this->translate->_('Название').'</td>
			<td width="202" class="rowA title"><input type="text" name="title" value="' . $values['title'] . '" /></td>
			<td align="right" class="title"></td>
			<td class="rowA title" width="200"></td>
		  </tr>
		  <tr>
			<td width="113" align="right" class="title rowB">'.$this->translate->_('E-Mail владельца').'</td>
			<td width="202" class="rowA rowB title"><input type="text" name="mail" value="' . $values['mail'] . '" /></td>
			<td align="right" class="title rowB"></td>
			<td class="rowA rowB title"></td>
		  </tr>
		  <tr>
			<td width="113" align="right" class="title">'.$this->translate->_('Идентицикационное слово').'</td>
			<td width="202" class="rowA title"><input type="text" name="keyword" value="' . $values['keyword'] . '" /></td>
			<td align="right" class="title"></td>
			<td class="rowA title" width="200"></td>
		  </tr>
		  <tr>
			<td width="113" align="right" class="title rowB">'.$this->translate->_('Логин').'</td>
			<td width="202" class="rowA rowB title"><input type="text" name="login" value="' . $values['login'] . '" /></td>
			<td align="right" class="title rowB"></td>
			<td class="rowA rowB title"></td>
		  </tr>
		  <tr>
			<td width="113" align="right" class="title">&nbsp;</td>
			<td width="202" class="rowA title"></td>
			<td align="right" class="title"></td>
			<td class="rowA title" width="200"></td>
		  </tr>
		  <tr>
			<td width="113" align="right" class="title rowB">'.$this->translate->_('Пароль').'</td>
			<td width="202" class="rowA rowB title"><input type="password" name="pwd1" /></td>
			<td align="right" class="title rowB"></td>
			<td class="rowA rowB title"></td>
		  </tr>
		  <tr>
			<td width="113" align="right" class="title">'.$this->translate->_('Подтверждение').'</td>
			<td width="202" class="rowA title"><input type="password" name="pwd2" /></td>
			<td align="right" class="title"></td>
			<td class="rowA title" width="200"></td>
		  </tr>
		  <tr>
			<td width="113" align="right" class="title">&nbsp;</td>
			<td width="202" class="rowA title"></td>
			<td align="right" class="title"></td>
			<td class="rowA title" width="200"></td>
		  </tr>
		  <tr>
			<td width="113" align="right" class="title rowB">'.$this->translate->_('Активен').'</td>
			<td width="202" class="rowA rowB title"><input type="checkbox" name="active" style="height: 15px; width: 15px;"' . ( ! isset ( $values['active'] ) || intval ( $values['active'] ) > 0 ? ' checked' : null ) . ' /></td>
			<td align="right" class="title rowB"></td>
			<td class="rowA rowB title"></td>
		  </tr>
		  </table>
		  </div>

	  <table border="0" cellpadding="0" cellspacing="0" class="list" style="width:692px">
	  <tr>
		<td align="right" class="title">' . ( isset ( $values['id'] ) ? '<input type="button" value="'.$this->translate->_('Удалить аккаунт').'" onclick="javascript:if(confirm(\'Вы уверены, что действительно хотите удалить аккаунт?\\n\\Удаление необратимо.\'))window.location.href=\'/' . self::MODULE_NAME . '/?mod=account&action=delete&hash=' . md5 ( $_SESSION['id'] . $values['id'] ) . '&id=' . $values['id'] . '\'" style="background-color: #FFD4FF; float: left; margin-left: 10px; width: 240px;" />' : null ) . '<input type="submit" value="' . ( isset ( $values['id'] ) ? 'Сохранить' : 'Создать' ) . '" style="width:240px;" /></td>
		<td width="8" align="right" class="title"><br /><br /></td>
	  </tr>
		</table>
	</form>
		';
	}


	public function actionDelete () {
		if ( $_GET['hash'] == md5 ( $_SESSION['id'] . $_GET['id'] ) ) {
			$sql	= "DELETE FROM %s WHERE id = '%d'";
			$sql	= sprintf ( $sql, self::TABLE_ACCOUNTS, $_GET['id'] );

			$query	= $this -> mysqlQuery( $sql );

			$this -> _tablesDelete ( $_GET['id'] );
		}

		$this -> redirect ( $this -> root_path . self::MODULE_NAME . '/?mod=account' );
	}

	public function actionAdd () {
		$this -> page	.= $this -> _makeForm ();
	}

	public function actionEdit () {
		if ( isset ( $_GET['id'] ) && intval ( $_GET['id'] ) < 1 )
		$this -> redirect ( $this -> root_path . self::MODULE_NAME . '/?mod=account' );

		$sql	= "SELECT * FROM `%s` WHERE id = '%d'";
		$sql	= sprintf ( $sql, self::TABLE_ACCOUNTS, $_GET['id'] );

		$query	= $this -> mysqlQuery( $sql );
		$result	= mysql_fetch_array ( $query );

		$this -> page .= $this -> _makeForm ( $result );
	}

	private function actionSave () {


		if ( isset ( $_GET['id'] ) && intval ( $_GET['id'] ) > 0 ) {
			$sql	= "UPDATE %s SET title = '%s', login = '%s', mail = '%s', keyword = '%s', active = '%d' WHERE id = '%d'";
			$sql	= sprintf ( $sql, self::TABLE_ACCOUNTS, $_POST['title'], $_POST['login'], $_POST['mail'], $_POST['keyword'], isset ( $_POST['active'] ) ? 1 : 0, $_GET['id'] );

			$query	= $this -> mysqlQuery ( $sql );

			$this -> changePassword ( self::TABLE_ACCOUNTS, $_GET['id'] );

			$this -> redirect ( $this -> root_path . self::MODULE_NAME . '/?mod=account&action=edit&id=' . $this -> id );
		} else
		{
			$sql	= "INSERT INTO %s SET title = '%s', login = '%s', mail = '%s', keyword = '%s'";
			$sql	= sprintf ( $sql, self::TABLE_ACCOUNTS, $_POST['title'], $_POST['login'], $_POST['mail'], $_POST['keyword'] );

			$query	= $this -> mysqlQuery ( $sql );
			$id	= mysql_insert_id ();

			$this -> changePassword ( self::TABLE_ACCOUNTS, $id );

			$this -> _tablesGenerate ( $id );

			$this -> redirect ( $this -> root_path . self::MODULE_NAME . '/?mod=account&action=edit&id=' . $id );
		}
	}

}

// ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~