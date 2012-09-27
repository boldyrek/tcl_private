<?php
require('/home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/lib/class.Proto.php');
require('/home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/inc/config.php');

class alert extends Proto {

    var $accPrefix = '';
    var $siteUrl="http://tcl.makmalauto.com";
    var $listCar=array();


    // Пункт 5
    function get2weekAlert()
    {
        $sql = "SELECT id,model FROM ccl_cars WHERE place_id1 IN(0,1,4) AND IF(buy_date='0000-00-00',created < DATE_SUB(NOW(), INTERVAL 14 DAY),buy_date < DATE_SUB(NOW(), INTERVAL 14 DAY)) AND type!='3' AND delivered!='1' ";
        $res = mysql_query($sql);
        if ($res && mysql_num_rows($res)>0)
        {
            while ($arr = mysql_fetch_array($res))
            {
                $this->listCar[]=array('type'=>'mail1','name'=>$this->linkgenerate($arr['id'],$arr['model']));

            }
        }
        else
        echo mysql_error();

    }
    // Пункт 6
    function get1monthAlert()
    {
        $plases = implode(',',$this->place_in_amerika);
        $sql = "SELECT id,model FROM ccl_cars WHERE container='0' AND place_id1 IN({$plases}) AND IF(buy_date='0000-00-00',created < DATE_SUB(NOW(), INTERVAL 1 MONTH),buy_date < DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND type!='3' AND delivered!='1' ";
        $res = mysql_query($sql);
        if ($res && mysql_num_rows($res)>0)
        {
            while ($arr = mysql_fetch_array($res))
            {
                $this->listCar[]=array('type'=>'mail2','name'=>$this->linkgenerate($arr['id'],$arr['model']));

            }
        }
        else
        echo mysql_error();
    }
    // Пункт 7
    function get2weekAlert2()
    {
        $sql = "SELECT id,model FROM ccl_cars WHERE place_id2 IN(0,1,4) AND IF(buy_date='0000-00-00',created < DATE_SUB(NOW(), INTERVAL 14 DAY),buy_date < DATE_SUB(NOW(), INTERVAL 14 DAY)) AND type!='3' AND delivered!='1' ";
        $res = mysql_query($sql);
        if ($res && mysql_num_rows($res)>0)
        {
            while ($arr = mysql_fetch_array($res))
            {
                $this->listCar[]=array('type'=>'mail3','name'=>$this->linkgenerate($arr['id'],$arr['model']));

            }
        }
        else
        echo mysql_error();

    }

    // Пункт 8

    function get2monthAlert()
    {
        $sql = "SELECT id,model FROM ccl_cars WHERE place_id1!='7' AND delivered!='1' AND IF (buy_date='0000-00-00',created < DATE_SUB(DATE_SUB(NOW(), INTERVAL 2 MONTH), INTERVAL 21 DAY),buy_date < DATE_SUB(DATE_SUB(NOW(), INTERVAL 2 MONTH), INTERVAL 21 DAY)) AND type!='3'";
        $res = mysql_query($sql);
        if ($res && mysql_num_rows($res)>0)
        {
            while ($arr = mysql_fetch_array($res))
            {
                $this->listCar[]=array('type'=>'mail4','name'=>$this->linkgenerate($arr['id'],$arr['model']));
            }
        }
        else
        echo mysql_error();
    }
    function _makeReadyForSend($mail_body='')
    {
        $sql = "SELECT email FROM ccl_usrs WHERE email !='' and (`type`='1' or `type`='7')";
        // исправил, чтобы выбирало только админские адреса

        $res = mysql_query($sql);
        if ($res && mysql_num_rows($res)>0)
        {
            $this->mail_template="/home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/template/mail_template.php";
            $mail_body=addslashes($this->make_mail_teplate($mail_body));
            while ($arr = mysql_fetch_array($res))
            {
                $sql = "INSERT INTO `ccl_mail_tmp` (`email`,`mail_body`) VALUES ('{$arr['email']}','$mail_body')";
                mysql_query($sql);
                echo mysql_error();
            }
        }
    }
    function linkgenerate($carId, $name)
    {
        return "<a  href='{$this->siteUrl}?mod=cars&sw=form&car_id={$carId}'>".($name!=''?$name:'БЕЗ НАЗВАНИЯ')."</a>";
    }

    function mailGenerate()
    {
        $text='';
        $this->get2weekAlert();
        $this->get1monthAlert();
        $this->get2weekAlert2();
        $this->get2monthAlert();

        foreach ($this->listCar as $car)
        {
            $listCarByType[$car['type']][]=$car['name'];
        }
        foreach ($listCarByType as $key=>$type)
        {
            $text.=$this->make_value_for_template('cars', $key, array('car_list'=>'<ul><li>'.implode('</li><li>',$type).'</li></ul>'));
        }
        if ($text!='')
        {
            $this->_makeReadyForSend($text);
        }
    }
}

class MailItterator{

    function __construct(){
        $conn = mysql_connect($DBvars['host'], $DBvars['user'], $DBvars['pass']);
        mysql_select_db($DBvars['name'], $conn);
        $alert = new alert();
        $alert->cDB();
        $alert->mailGenerate();


    }
}




?>