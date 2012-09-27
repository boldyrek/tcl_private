<?

class AdministratorsAdmin extends AdministratorsAbstract {

	private $id = '';


	public function makePage () {
		$this -> setId ();
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

	private function setId() {
		if ( isset ( $_GET['id'] ) ) $this -> id = $_GET['id'];
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



	public function actionList () {
		$sql	= "SELECT * FROM `%s` ORDER BY `name` ASC";
		$sql	= sprintf ( $sql, self::TABLE_ADMINISTRATORS );

		$query	= $this -> mysqlQuery ( $sql );
		$count	= mysql_num_rows ( $query );

		$class	= 'rowA rowB';
		$this -> page .= '
		<div class="location">'.$this->translate->_('Администраторы аккаунтов').' &nbsp; | &nbsp; <a href="'.$this -> root_path . self::MODULE_NAME . '/?mod=admin&action=add">'.$this->translate->_('Добавить администратора').'</a></div>
			<table width="970" border="0" cellspacing="0" cellpadding="0" class="list">
		  <tr class="title">
		    <td width="200">'.$this->translate->_('логин').'</td>
		    <td>'.$this->translate->_('имя').'</td>
		  </tr>';

		$i	= 1;
		while ( $i <= $count ) {
			$item	= mysql_fetch_array ( $query );
			$this -> page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"  onclick="document.location=\'' . $this -> root_path . self::MODULE_NAME . '/?mod=admin&action=edit&id='. $item['id'] . '\'">
				<td>' . $item['login'] . '&nbsp;</td>
				<td>' . $item['name'] . '&nbsp;</td>
				</tr>';
			$i++;
			if ($class=='rowA') $class='rowA rowB'; else $class='rowA';
		}

		$this -> page .= '</table>';
	}


	public function _makeForm ( $values = null ) {
		return '
		<form class="myForm" style="margin:0px;" action="'.$this -> root_path . self::MODULE_NAME . '/?mod=admin&action=save&id=' . $values['id'] . '" method="post">
		<div class="cont_car">
		<h3>' . ( isset ( $values['id'] ) ? 'Данные для аккаунта администратора &lt;' . $values['name'] . '&gt;' : 'Добавление нового администратора' ) . '</h3>
		<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
		  <tr>
			<td width="113" align="right" class="title">Логин</td>
			<td width="202" class="rowA title">' . ( isset ( $values['id'] ) ? $values['login'] : '<input type="text" name="login" />' ) . '</td>
			<td align="right" class="title"></td>
			<td class="rowA title" width="200"></td>
		  </tr>
		  <tr>
			<td width="113" align="right" class="title rowB">Имя</td>
			<td width="202" class="rowA rowB title"><input type="text" name="name" value="' . $values['name'] . '" /></td>
			<td align="right" class="title rowB"></td>
			<td class="rowA rowB title"></td>
		  </tr>
		  <tr>
			<td width="113" align="right" class="title">Язык</td>
			<td width="202" class="rowA title">
				<select name="lang">
					<option value="rus"' . ( 'rus' == $values['lang'] ? ' selected' : null ) . '>Русский</option>
					<option value="eng"' . ( 'eng' == $values['lang'] ? ' selected' : null ) . '>Английский</option>
				</select>
			</td>
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
			<td width="113" align="right" class="title rowB">Пароль</td>
			<td width="202" class="rowA rowB title"><input type="password" name="pwd1" /></td>
			<td align="right" class="title rowB"></td>
			<td class="rowA rowB title"></td>
		  </tr>
		  <tr>
			<td width="113" align="right" class="title">Подтверждение</td>
			<td width="202" class="rowA title"><input type="password" name="pwd2" /></td>
			<td align="right" class="title"></td>
			<td class="rowA title" width="200"></td>
		  </tr>
' . ( isset ( $values['id'] ) ? '
		  <tr>
			<td width="113" align="right" class="title">&nbsp;</td>
			<td width="202" class="rowA title"></td>
			<td align="right" class="title"></td>
			<td class="rowA title" width="200"></td>
		  </tr>
		  <tr>
			<td width="113" align="right" class="title rowB">Последний вход</td>
			<td width="202" class="rowA rowB title">' . $values['last_login'] . '</td>
			<td align="right" class="title rowB"></td>
			<td class="rowA rowB title"></td>
		  </tr>
' : null ) . '
		  </table>
		  </div>

	  <table border="0" cellpadding="0" cellspacing="0" class="list" style="width:692px">
	  <tr>
		<td align="right" class="title">' . ( isset ( $values['id'] ) && $_SESSION['id'] != $values['id'] ? '<input type="button" value="Удалить администратора" onclick="javascript:if(confirm(\'Вы уверены, что действительно хотите удалить администратора?\\n\\Удаление необратимо.\'))window.location.href=\'/' . self::MODULE_NAME . '/?mod=admin&action=delete&hash=' . md5 ( $_SESSION['name'] . $values['id'] ) . '&id=' . $values['id'] . '\'" style="background-color: #FFD4FF; float: left; margin-left: 10px; width: 240px;" />' : null ) . '<input type="submit" value="' . ( isset ( $values['id'] ) ? 'Сохранить' : 'Создать' ) . '" style="width:240px;" /></td>
		<td width="8" align="right" class="title"><br /><br /></td>
	  </tr>
		</table>
	</form>
		';
	}

	public function actionDelete () {
		if ( $_GET['hash'] == md5 ( $_SESSION['name'] . $_GET['id'] ) ) {
			$sql	= "DELETE FROM %s WHERE id = '%d'";
			$sql	= sprintf ( $sql, self::TABLE_ADMINISTRATORS, $_GET['id'] );

			$query	= $this -> mysqlQuery( $sql );
		}

		$this -> redirect ( $this -> root_path . self::MODULE_NAME . '/?mod=admin' );
	}

	public function actionAdd () {
		$this -> page .= $this -> _makeForm ();
	}

	public function actionEdit () {
		if ( intval ( $this -> id ) < 1 )
			$this -> redirect ( $this -> root_path . self::MODULE_NAME . '/?mod=admin' );

		$sql	= "SELECT * FROM `%s` WHERE id = '%d'";
		$sql	= sprintf ( $sql, self::TABLE_ADMINISTRATORS, $this -> id );

		$query	= $this -> mysqlQuery( $sql );
		$result	= mysql_fetch_array ( $query );

		$this -> page .= $this -> _makeForm ( $result );
	}

	private function actionSave () {
		// обновляем инфо имеющегося админа
		if ( intval ( $this -> id ) > 0 ) {
			$sql	= "UPDATE %s SET name = '%s', lang = '%s' WHERE id = '%d'";
			$sql	= sprintf ( $sql, self::TABLE_ADMINISTRATORS, $_POST['name'], $_POST['lang'], $this -> id );

			$query	= $this -> mysqlQuery ( $sql );

			$this -> changePassword ( self::TABLE_ADMINISTRATORS, $this -> id );

			$this -> redirect ( $this -> root_path . self::MODULE_NAME . '/?mod=admin&action=edit&id=' . $this -> id );
		} else
		// создаем нового админа
			$sql	= "INSERT INTO %s SET login = '%s', name = '%s', lang = '%s'";
			$sql	= sprintf ( $sql, self::TABLE_ADMINISTRATORS, $_POST['login'], $_POST['name'], $_POST['lang'] );

			$query	= $this -> mysqlQuery ( $sql );

			$this -> changePassword ( self::TABLE_ADMINISTRATORS, mysql_insert_id () );

			$this -> redirect ( $this -> root_path . self::MODULE_NAME . '/?mod=admin&action=edit&id=' . mysql_insert_id () );
		}
	}


	// ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~