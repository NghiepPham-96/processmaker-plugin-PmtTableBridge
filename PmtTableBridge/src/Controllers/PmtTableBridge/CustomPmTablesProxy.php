<?php
/**
 * CustomPmTablesProxy
 *
 * @author Nghiep Pham <nghiep.pt96.develop@gmail.com>
 * @inherits pmTablesProxy
 * @access public
 */

namespace Controllers\PmtTableBridge;

use AdditionalTables;
use Configurations;
use Exception;
use G;
use PmTable;
use ProcessMaker\Core\System;
use ProcessMap;
use Propel;
use ResultSet;
use RoutePeer;

require_once 'classes/model/AdditionalTables.php';

class CustomPmTablesProxy extends \pmTablesProxy
{
    /**
     * get pmtables list
     *
     * @param string $httpData->start
     * @param string $httpData->limit
     * @param string $httpData->textFilter
     */
    public function getList ($httpData)
    {
        $configurations = new Configurations();
        $processMap = new ProcessMap();

        // setting parameters
        $config = $configurations->getConfiguration( 'additionalTablesList', 'pageSize', '', $_SESSION['USER_LOGGED'] );
        $env = $configurations->getConfiguration( 'ENVIRONMENT_SETTINGS', '' );
        $limit_size = isset( $config->pageSize ) ? $config['pageSize'] : 20;
        $start = isset( $httpData->start ) ? $httpData->start : 0;
        $limit = isset( $httpData->limit ) ? $httpData->limit : $limit_size;
        $filter = isset( $httpData->textFilter ) ? $httpData->textFilter : '';
        $pro_uid = isset( $httpData->pro_uid ) ? $httpData->pro_uid : null;

        if ($pro_uid !== null) {
            $process = $pro_uid == '' ? array ('not_equal' => $pro_uid
            ) : array ('equal' => $pro_uid);
            $addTables = AdditionalTables::getAll( false, false, $filter, $process );

            $c = $processMap->getReportTablesCriteria( $pro_uid );
            $oDataset = RoutePeer::doSelectRS( $c );
            $oDataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $reportTablesOldList = array ();
            while ($oDataset->next()) {
                $reportTablesOldList[] = $oDataset->getRow();
            }
            foreach ($reportTablesOldList as $i => $oldRepTab) {
            	if($filter != ''){
            		if((stripos($oldRepTab['REP_TAB_NAME'], $filter) !== false) || (stripos($oldRepTab['REP_TAB_TITLE'], $filter) !== false)){
            			$addTables['rows'][] = array ('ADD_TAB_UID' => $oldRepTab['REP_TAB_UID'],'PRO_UID' => $oldRepTab['PRO_UID'],'DBS_UID' => ($oldRepTab['REP_TAB_CONNECTION'] == 'wf' ? 'workflow' : 'rp'),'ADD_TAB_DESCRIPTION' => $oldRepTab['REP_TAB_TITLE'],'ADD_TAB_NAME' => $oldRepTab['REP_TAB_NAME'],'ADD_TAB_TYPE' => $oldRepTab['REP_TAB_TYPE'],'TYPE' => 'CLASSIC' );
            		}
            	} else {
            		$addTables['rows'][] = array ('ADD_TAB_UID' => $oldRepTab['REP_TAB_UID'],'PRO_UID' => $oldRepTab['PRO_UID'],'DBS_UID' => ($oldRepTab['REP_TAB_CONNECTION'] == 'wf' ? 'workflow' : 'rp'),'ADD_TAB_DESCRIPTION' => $oldRepTab['REP_TAB_TITLE'],'ADD_TAB_NAME' => $oldRepTab['REP_TAB_NAME'],'ADD_TAB_TYPE' => $oldRepTab['REP_TAB_TYPE'],'TYPE' => 'CLASSIC' );
            	}
            }
            $addTables['count'] = count($addTables['rows']);
            if($start != 0){
           	    $addTables['rows'] = array_splice($addTables['rows'], $start);
            }
            $addTables['rows'] = array_splice($addTables['rows'], 0, $limit);
        } else {
            $addTables = AdditionalTables::getAll( $start, $limit, $filter );
        }

        foreach ($addTables['rows'] as $i => $table) {
            try {
                $con = Propel::getConnection( PmTable::resolveDbSource( $table['DBS_UID'] ) );
                $stmt = $con->createStatement();
                $rs = $stmt->executeQuery( 'SELECT COUNT(*) AS NUM_ROWS from ' . $table['ADD_TAB_NAME'] );
                if ($rs->next()) {
                    $r = $rs->getRow();
                    $addTables['rows'][$i]['NUM_ROWS'] = $r['NUM_ROWS'];
                } else {
                    $addTables['rows'][$i]['NUM_ROWS'] = 0;
                }

                //removing the prefix "PMT" to allow alphabetical order (just in view)
                if (substr( $addTables['rows'][$i]['ADD_TAB_NAME'], 0, 4 ) == 'PMT_') {
                    $addTables['rows'][$i]['ADD_TAB_NAME'] = substr( $addTables['rows'][$i]['ADD_TAB_NAME'], 4 );
                }
            } catch (Exception $e) {
                $addTables['rows'][$i]['NUM_ROWS'] = G::LoadTranslation( 'ID_TABLE_NOT_FOUND' );
            }
        }

        return $addTables;
    }
}
