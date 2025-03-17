<?php
/**
 * welcome.php for plugin PmtTableBridge
 *
 *
 */

try {
  /* Render page */
  $oHeadPublisher = headPublisher::getSingleton();
  
  $G_MAIN_MENU        = "processmaker";
  $G_ID_MENU_SELECTED = "ID_PMTTABLEBRIDGE_MNU_01";
  //$G_SUB_MENU             = "setup";
  //$G_ID_SUB_MENU_SELECTED = "ID_PMTTABLEBRIDGE_02";

  $config = array();
  $config["pageSize"] = 15;
  $config["message"] = "Hello world!";

  $oHeadPublisher->addContent("PmtTableBridge/PmtTableBridgeApplication"); //Adding a html file .html
  $oHeadPublisher->addExtJsScript("PmtTableBridge/PmtTableBridgeApplication", false); //Adding a javascript file .js
  $oHeadPublisher->assign("CONFIG", $config);

  G::RenderPage("publish", "extJs");
} catch (Exception $e) {
  $G_PUBLISH = new Publisher;
  
  $aMessage["MESSAGE"] = $e->getMessage();
  $G_PUBLISH->AddContent("xmlform", "xmlform", "PmtTableBridge/messageShow", "", $aMessage);
  G::RenderPage("publish", "blank");
}
?>