<?php
/**
 * class.PmtTableBridge.php
 *  
 */

  class PmtTableBridgeClass extends PMPlugin {
    function __construct() {
      set_include_path(
        PATH_PLUGINS . 'PmtTableBridge' . PATH_SEPARATOR .
        get_include_path()
      );
    }

    function setup()
    {
    }

    function getFieldsForPageSetup()
    {
    }

    function updateFieldsForPageSetup()
    {
    }

  }
?>