<?php

class PmtTableBridgePlugin extends PMPlugin
{
  
  
  public function PmtTableBridgePlugin($sNamespace, $sFilename = null)
  {
    $res = parent::PMPlugin($sNamespace, $sFilename);
    $this->sFriendlyName = "PmtTableBridge Plugin";
    $this->sDescription  = "Autogenerated plugin for class PmtTableBridge";
    $this->sPluginFolder = "PmtTableBridge";
    $this->sSetupPage    = "setup";
    $this->iVersion      = 1;
    //$this->iPMVersion    = 2425;
    $this->aWorkspaces   = null;
    //$this->aWorkspaces = array("os");
    $this->enableRestService(true);
    
    
    return $res;
  }

  public function setup()
  {
    $this->registerMenu("processmaker", "menuPmtTableBridge.php");
    $this->registerPmFunction();
    
    
  }

  public function install()
  {
  }
  
  public function enable()
  {
    $this->enableRestService(true);
  }

  public function disable()
  {
    
  }
  
}

$oPluginRegistry = PMPluginRegistry::getSingleton();
$oPluginRegistry->registerPlugin("PmtTableBridge", __FILE__);
