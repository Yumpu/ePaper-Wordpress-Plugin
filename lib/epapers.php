<?php

require "YumpuAPI.php";
require "YumpuEpaper.php";
require "YumpuEpaper_repository.php";

$myAPIToken = $_GET['tokenid'];
$YAPI = new YumpuAPI($myAPIToken);
$result = $YAPI->documents_get(0, 100, 'desc');

$items = array();
if ($result) {
    foreach ($result->documents as $mydata) {
        $items[] = YumpuEpaper_repository::loadByAPI($mydata);
    }
}

$cnt = 0;
$myarray = array();
foreach($items as $ePaper) {

    $myShortcode = "";
    if ($ePaper->getEpaper_id() > 0) {
        $myShortcode = '[YUMPU epaper_id='.$ePaper->getEpaper_id().' width=&quot;512&quot; height=&quot;384&quot;]';
    }

    $myarray[] = array('Cover' => $ePaper->getImage_Small(), 'Title' => $ePaper->getTitle(), 'Shortcode' => $myShortcode, 'State' => $ePaper->getStatus(), 'Visibility' => $ePaper->getPrivacy_Mode(), 'Created' => $ePaper->getCreate_Date(),'ePaperID' => $ePaper->getEpaper_id());
    $cnt = $cnt + 1;
}

echo json_encode(array('epapers' => $myarray))

?>


