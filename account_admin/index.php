<?

	session_start ();

	require_once ( $_SERVER['DOCUMENT_ROOT'] . '/account_admin/lib/class.AdministratorsAbstract.php' );


	if ( isset ( $_GET['mod'] ) and $_GET['mod'] != '' ) {
		$file	= $_SERVER['DOCUMENT_ROOT'] . '/' . AdministratorsAbstract::MODULE_NAME . '/lib/class.Administrators' . ucfirst ( $_GET['mod'] ) . '.php';
		if ( file_exists ( $file ) )
			require_once ( $file );
		else { echo $file;
			AdministratorsAbstract::redirect ( '/' . AdministratorsAbstract::MODULE_NAME );}

	} else {
		$default	= $_SERVER['DOCUMENT_ROOT'].'/' . AdministratorsAbstract::MODULE_NAME . '/lib/class.AdministratorsAdmin.php';
		require_once ( $default );
	}

	switch ( $_GET['mod'] ) {
		case 'account':
			$page	= new AdministratorsAccount ();
			break;
		default:
			$page	= new AdministratorsAdmin ();
			break;
	}

	if ( $page ) {
		$page -> makePage ();
	}

	// ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~