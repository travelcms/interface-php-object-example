<?php

if (!is_file('config.inc.php'))
  die('Please rename config.inc.php.dist to config.inc.php and set the username and password.');

include "config.inc.php";

if (TCMSINTERFACEUSER == '' || TCMSINTERFACEPASSWORD == '')
  die('Please configure the username and password in file "config.inc.php".');

$object = false;  
$to = $bc = '';
$error = false;
$baseUrl = 'http://'.TCMSINTERFACEUSER.':'.TCMSINTERFACEPASSWORD.'@'.TCMSINTERFACEHOST.'/rest/';

if ($_GET['do'] == 'lookup')
{
  $url = $baseUrl.'catalogobject/'.TCMSINTERFACELANGUAGE.'/lookup.xml?'.
         'TourOperatorCode='.$_POST['to'].'&BookingCode='.$_POST['bc'];  
  $result = simplexml_load_file($url);
  
  if (isset($result->response->object))
  {
    $object = $result->response->object;  
  } else {
  	$error = $result->status;
  }
  $to = $_POST['to'];
  $bc = $_POST['bc'];
}

if ($_GET['do'] == 'getcode')
{
  $url = $baseUrl.'search/'.TCMSINTERFACELANGUAGE.'/clist.xml?PerPage=1';  
  $result = simplexml_load_file($url);

  if (isset($result->response->searchlist->catalogobject))
  {
    $to = $result->response->searchlist->catalogobject->touroperator['code'];
    $bc = $result->response->searchlist->catalogobject->object->bookingcode;
  } else {
    $error = $result->status;
  }  
}

?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <style type="text/css">   
   body {
     font-family: arial;
     font-size: 12px;
     background-color: #eeeeee;
   }   
   
   h1 {
     font-size: 16px;
   }
   
   #search {
     margin: 16px;
     border: 1px solid gray;     
     padding: 8px;
     background-color: white;
   }
   
   
   #object {
     margin: 16px;
     border: 1px solid gray;     
     padding: 8px;
     background-color: white;
   }   
  </style>
</head>
<body>



<div id="search">
  <form action="?do=lookup" method="POST">
    Touropertorcode: <input type="text" name="to" value="<?php echo $to; ?>">
    Bookingcode: <input type="text" name="bc" value="<?php echo $bc; ?>">   
    <input type="submit">
  </form> 
  <a href="?do=getcode">Don't know a touroperatorcode or a bookingcode?</a>    
</div>  
  
  
  
<div id="object">   
<?php if ($object !== false): ?>
  
  
  <h1><?php echo $object->name; ?></h1>
  
  
  <p id="attributes">
  <?php foreach ($object->attributes->attribute as $attribute): ?>
  <img src="http://www.travelcms.de/image/default/attributes/<?php echo str_replace('/', '_', str_replace(' ', '_', $attribute['code'])); ?>.png">    
  <?php endforeach; ?>
  </p>
  
  
  <p id="bookingcodes">
  Bookingcodes: 
  <?php foreach ($object->bookingcodes->bookingcode as $bookingcode): ?>
    <?php echo $bookingcode; ?>
  <?php endforeach; ?>
  </p>


  <p id="text">  
  <?php foreach ($object->texts->textsegment as $textsegment): ?>
    <?php if ($textsegment['title'] != ''): ?>
      <b><?php echo $textsegment['title']; ?></b>:<br>
    <?php endif ?>
    
    <?php echo $textsegment; ?>
  <?php endforeach; ?>
  </p>  
  
  
  <p id="pictures">
  <?php foreach ($object->pictures->picture as $picture): ?>
    <?php if ($picture['size'] == 'middle'): ?>
      <img src="<?php echo $picture; ?>">
    <?php endif ?>
  <?php endforeach; ?>
  </p>       
   
   
<?php endif ?>

<?php if ($error !== false): ?>
  <?php echo $error; ?>
<?php endif ?>

</div>



</body>
</html>