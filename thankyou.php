<?php
@ob_start();
error_reporting(0);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

date_default_timezone_set("Asia/Kolkata"); 


/*==================================== Below variables to review START ========================================*/
	//define('DB_SERVER', '65.0.39.16'); 
	//define('DB_USERNAME','combri_digidend'); 
	//define('DB_PASSWORD','q(aMPu)KKl1_'); 
	//define('DB_DATABASE','combri_dbformdigidend'); 
	
	// do not Edit below
	//$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die(mysqli_error());
	//$database = mysqli_select_db(DB_DATABASE) or die(mysqli_error());
	$type_of_form=ucwords(str_replace("_"," ",$_POST['type_of_form']));
//	$email_to3="forms@compubrain.in";
	
	$enable_SMTP = true;
	$has_attached_file = false;	
	
	$client_name="Compubrain"; //Client Full Name
	$client_email_id="info@compubrain.in"; //Email id, whom to send emails on form submit? // 
		
	$is_microsite = false;
	$name_if_microsite = 'Demo Microsite Name'; // do not matter if --> $is_microsite = false;
	
	$client_website_url="https://compubrain.com/";
	$client_logo_url_in_png="https://compubrain.com/dist/img/CompuBrain.svg";
	
	$client_file_uploads_path='https://digidend.xyz/forms/file_uploads//';
	
	$captcha_is_enabled=false;
	$captcha_privatekey = '';
	
	// do not Edit below
	$name_if_microsite = (!empty($name_if_microsite))?$name_if_microsite:'Microsite'; 


    /*$name_of_project='';
    if(!empty($_POST['name_of_project']))
    {
        $name_of_project = $_POST['name_of_project'];
    }*/
	
	
	$email_to=$client_email_id;
	$email_subject=$type_of_form." | ".$client_name;	
	if($is_microsite) $email_subject=$type_of_form." | ".$name_if_microsite." | ".$client_name;
    // if($name_of_project) $email_subject=$name_of_project." | ".$type_of_form." | ".$client_name;
	
	
/*==================================== Below variables to review END ========================================*/


if($type_of_form=="Career")
$has_attached_file = true;

function getIp() 
{
$ip=$_SERVER['REMOTE_ADDR'];
  if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
   	$ip=$_SERVER['HTTP_CLIENT_IP'];
  } else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    	$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
  return $ip;
}

$name=$_POST['name'];
if (!preg_match("/^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])+(\s{0,1}[a-zA-Z-, ])*$/",$name))
{
	if($type_of_form!="Newsletter")
	$name="";
	else
	$name="User";
}
$email=$_POST['email'];
if(!preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i',$email))
{
  	$email="";
}

// For Check Domain Validation Start
$domain=explode("@",$email);
$domain=end($domain);

if(@checkdnsrr($domain,'ANY')) {

}
else {
	$email="";
}
// For Check Domain Validation End

$contact_no=$_POST['contact_no'];
if(!preg_match("/[0-9+()]|\./",$contact_no)) {
	$contact_no="";
}

$email_message=trim(strip_tags($_POST['message']));
if (strlen($email_message) >= 500)
{
	$email_message="";
}

$ip1=getIp();
if(!empty($_SERVER['HTTP_REFERER'])){
	$ref_page=$_SERVER['HTTP_REFERER'];
}


$brochure_link='';
if(!empty($_POST['brochure_link']))
$brochure_link=$_POST['brochure_link'];


$onscreen_thankyou_msgs=array(
"Contact_Us" => "Hello!<br>We appreciate you contacting us.<br>We will circle back shortly.",
"Career" => "Awesome!<br>We appreciate your interest in working with us.<br>Be patient until our team scans through this and based on the relevance revert back.",
"Product_Inquiry" => "Awesome!<br>Thank you for showing your interest.<br>Our relevant officer shall soon address your qualified query.",
"Request_for_Quote" => "Awesome!<br>Thank you for showing your interest.<br>Our relevant officer shall soon address your qualified query.",
"Registration" => "Thank you for taking the time to register.<br>The details have been added to our database.<br>Looking forward to connect.",
"Quick_Inquiry" => "Awesome!<br>Thank you for your query.<br>Our relevant officer shall soon address your qualified query.",
"Book_Appointment" => "Thank you!<br>Looking forward to see you.",
"Newsletter" => "Awesome!<br>Thank you for signing up for our newsletter.<br>Welcome to our mailing list.",
"Request_for_Call_Back" => "Thank you!<br>Our relevant officer shall soon address your qualified query.",
"Download_Brochure" => "Awesome!<br>Thank you for your interest.<br>The requested brochure would have just landed in the inbox of your registered mail address. Given the quirky mailing algorithm, do check your spam if our mail has missed your inbox.");

$auto_response_thankyou_msgs=array(
"Contact_Us" => "We appreciate you contacting us.<br>Trust that our team shall connect, depending on the nature of your query.<br>Thank you.",
"Career" => "We appreciate your interest in working with us.<br>Be patient until our team scans through the details shared and based on the relevance revert back.<br>We wish you all the best.",
"Product_Inquiry" => "Thank you for showing your interest.<br>Our relevant officer shall soon address your qualified query.",
"Request_for_Quote" => "Thank you for showing your interest.<br>Our relevant officer shall soon address your qualified query.",
"Registration" => "Thank you for taking the time to register.<br>The details have been added to our database.<br>Looking forward to connect.",
"Quick_Inquiry" => "Thank you for your query.<br>Our relevant officer shall soon address your qualified query.",
"Book_Appointment" => "Thank you.<br>Looking forward to see you.<br>Should there be any change in your schedule, kindly let us know at your earliest convenience.",
"Newsletter" => "Thank you for signing up for our newsletter.<br>Welcome to our mailing list.",
"Request_for_Call_Back" => "Thank you.<br>Our relevant officer shall soon address your qualified query.",
"Download_Brochure" => "Awesome!<br>Thank you for your interest.<br><a href='$brochure_link' target='_blank'>Click Here to to refer and download your requested brochure.</a>");

if($captcha_is_enabled && isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
	$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$captcha_privatekey.'&response='.$_POST['g-recaptcha-response']);
	$captcha_response = json_decode($verifyResponse);
}elseif(!$captcha_is_enabled){
	$captcha_response=new stdClass();
	$captcha_response->success=true;
}
if(!$captcha_response->success || (($name=="" || $email=="" || $contact_no=="") && $type_of_form!="Newsletter"))
{   
     echo "<br> CAP:".$captcha_response->success;
    echo "<br>NAME:.".$name." Email: ".$email. "CONTACT: ".$contact_no." TOF: ".$type_of_form;
	$status="2";
}
else
{
	$digidend_table_name=$_POST['type_of_form'];
	if($is_microsite) $digidend_table_name=$digidend_table_name.'_microsite';
	
	
	$create_init="CREATE TABLE IF NOT EXISTS `".$digidend_table_name."` (`id` int(11) unsigned NOT NULL auto_increment, ";
	
	$collective_create_init="CREATE TABLE IF NOT EXISTS `collective_data` (`id` int(11) unsigned NOT NULL auto_increment, `name` varchar(150)	NOT NULL, `email` varchar(150) NOT NULL, `phone` varchar(150)	NOT NULL, `page` text NOT NULL,`ip` varchar(20) NOT NULL,`datetime` datetime NOT NULL, PRIMARY KEY (`ID`), UNIQUE KEY `email` (`email`,`phone`));";
	
	$collective_insert_init="insert into `collective_data` (name,email,phone,page,ip,datetime) values ('".addslashes($_POST['name'])."','".addslashes($_POST['email'])."','".addslashes($_POST['contact_no'])."','".$ref_page."','".$ip1."','".date('Y-m-d H:i:s')."')";	
		
	$insert_init="insert into `".$digidend_table_name."` ";
	$insert_temp1='';
	$insert_temp2='';
	
	

	$email_message="<table cellpadding='15px' cellspacing='0px' style='width: auto; color: #333; padding: 20px; font-family: Open Sans, sans-serif; border: 3px dashed #333;' border='0' bordercolor='#fff' align='center'>
	  <tr align='center'>
		<td colspan='2' bgColor='#f2f2f2'><a href='".$client_website_url."' target='_blank'><img src='".$client_logo_url_in_png."'  alt='".$client_name." Logo' /></a></td>
	  </tr>";
	  foreach($_POST as $key => $value)
		{
			if($key != 'type_of_form' && $key != 'g-recaptcha-response')
			{
				$email_message.="<tr>
					<td align='right' bgColor='#f2f2f2'><strong>".str_replace("_"," ",ucfirst($key)).":</strong></td>
					<td bgColor='#f6f6f6'>".$value."</td>
				</tr>";
				$create_init.="`".ucfirst($key)."` text NOT NULL,";	
				
				$insert_temp1.='`'.ucfirst($key)."`,";	
				$insert_temp2.="'".addslashes($value)."',";
				
			}
		}
	  
	  if(!empty($ref_page)){
		$email_message.="<tr>
		<td align='right' bgColor='#f2f2f2'><strong>URL:</strong></td>
		<td bgColor='#f6f6f6'><a href='".$ref_page."' target='_blank'>".$ref_page."</a></td>
	  	</tr>";
	  }
	$email_message.="<tr>
		<td align='right' bgColor='#f2f2f2'><strong>Visitor IP:</strong></td>
		<td bgColor='#f6f6f6'>".$ip1."</td>
	  </tr>
	</table>";
	
	
	$email_headers="From: ".$name."<".$email.">\r\n";
	$email_headers.="MIME-Version: 1.0\r\n";
	$email_headers.="Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	if($has_attached_file){
		
			//Attachment
			if (!file_exists('file_uploads')) {
				mkdir('file_uploads', 0777, true);
			}
			
			$uploadpaths='';			
			$ticketno=str_replace(' ','_',preg_replace('/[^\w ]/u','_',$client_name.' '.$type_of_form)).'_digidend_uploads_';			
			foreach($_FILES as $userfile)
			{
				$tmp_name = $userfile['tmp_name'];
				$type = $userfile['type'];
				$file_name = $userfile['name'];
				$size = $userfile['size'];
				if (file_exists($tmp_name))
				{
				   if(is_uploaded_file($tmp_name))
				   {
					   	$ext=pathinfo($file_name,PATHINFO_EXTENSION);
					  	$fileuplaodname=$ticketno.date('d_m_Y_H_i_s').rand(0,99999).".".$ext;
					  
						copy($tmp_name,"file_uploads/".$fileuplaodname);
						$uploadpaths.=$client_file_uploads_path.$fileuplaodname.',';
					}
				}
			}		
			$uploadpaths=trim($uploadpaths,",");		
			$create_init.="`uploads` text NOT NULL,";			
			$insert_temp1.="uploads,";
			$insert_temp2.="'".addslashes($uploadpaths)."',";
			
			$alter_add_uploads="ALTER TABLE `".$digidend_table_name."` ADD `uploads` TEXT NOT NULL  AFTER `page`;";
			//mysqli_query($connection,$alter_add_uploads);
			//Attachment END
	}
	
	$create_init.=" `page` text NOT NULL,`ip` varchar(20) NOT NULL,`datetime` datetime NOT NULL, PRIMARY KEY (`ID`));";
	
	//$insert_temp1 = rtrim($insert_temp1,',');
	$insert_temp1 = $insert_temp1.'page,ip,datetime';
	$insert_init = $insert_init."(".$insert_temp1.") "."values (".$insert_temp2."'".$ref_page."','".$ip1."','".date('Y-m-d H:i:s')."')";
	//echo $insert_init;
	// echo 'Yashh';
	// mysqli_query($connection,$create_init);
	// mysqli_query($connection,$insert_init);
	// mysqli_query($connection,$collective_create_init);
	// mysqli_query($connection,$collective_insert_init);
	
	// mysqli_close($connection);

	$status="1";
	$email_to2=$email;
	$email_message2="<table cellpadding='2px' cellspacing='5px' style='width: auto; color: #333; padding: 20px; font-family: Open Sans, sans-serif; border: 3px dashed #333;' border='0' align='center'>
		  <tr align='center'>
			<td colspan='2' bgColor='#f2f2f2'><a href='".$client_website_url."' target='_blank'><img src='".$client_logo_url_in_png."'  alt='".$client_name." Logo' /></a></td>
		  </tr>
		  <tr>
			<td><p align='center'><font size='+2'><strong>Thank you!</strong></font></p>";
			if($type_of_form!="Newsletter"){
			  $email_message2.="Dear <strong>$name</strong>,<br />
			  <br />";
			}
			$email_message2.=$auto_response_thankyou_msgs[$_POST['type_of_form']]."</td>
		  </tr>
		  <tr>
			<td>Regards,<br />
			  <strong>".$client_name."</strong><br />
			  <br />
			  <font size='2'>This is an auto generated email. PLEASE DO NOT REPLY directly to this email.</font></td>
		  </tr>
		</table>";
	
	
}


$Mail_Msg='';
if($status=="1") {
	$Mail_Msg="<div class='alert alert-success'>".$onscreen_thankyou_msgs[$_POST['type_of_form']]."</div>";
}
else if($status=="2") {
	$Mail_Msg="<div class='alert alert-danger'>It seems you are not submitting all the details as they are expected.</div>";
}
else if($status=="0") {
	$Mail_Msg="<div class='alert alert-danger'>Sorry! Some Technical issue occured. Please try again after sometime.</div>";
}



if($status=="1")
{
    include("saritaayunamApiCalls_realtime.php");
}



$dataPass = new stdClass();   // <-- ADD THIS LINE


	$dataPass->clientname = $client_name;
	$dataPass->clientid = $client_email_id;
	$dataPass->mailsubject = $email_subject;
	$dataPass->mail_message = $email_message;
	$dataPass->auto_response_msg = $email_message2;
	$dataPass->isattachment = $has_attached_file;
	if($has_attached_file)
		$dataPass->files = $_FILES;
	$dataPass->name = $name;
	$dataPass->email = $email_to2;
	$dataPass->communication = 'EMAIL';
	$dataPass->status = $status;
	$dataPass->sucessMsg = $Mail_Msg;
	$dataPasss = json_encode($dataPass);
	echo $dataPasss;
?>