<?
/**
 * @author Nghiep Pham <nghiep.pt96.develop@gmail.com>
 */

namespace Services\Api\PmtTableBridge;
use ProcessMaker\Services\Api;
use Luracast\Restler\RestException;
use Controllers\PmtTableBridge\CustomPmTablesProxy;
use stdClass;

class PmtTableSync extends Api
{
    protected $pmTables;
    protected $pmTablesProxy;

    public function __construct()
    {
        parent::__construct();
        $this->pmTables = new \pmTables();
        $this->pmTablesProxy = new CustomPmTablesProxy();

        $clientDetail = $this->getClientCredentials();
        $_SESSION['USER_LOGGED'] = $clientDetail['usr_uid'];
    }

    private function getDsn()
    {
        list($host, $port) = strpos(DB_HOST, ':') !== false ? explode(':', DB_HOST) : array(DB_HOST, '');
        $port = empty($port) ? '' : ";port=$port";
        $dsn = DB_ADAPTER . ':host=' . $host . ';dbname=' . DB_NAME . $port;

        return array('dsn' => $dsn, 'username' => DB_USER, 'password' => DB_PASS);
    }

    protected function getClientCredentials()
    {
        $oauthQuery = new \ProcessMaker\Services\OAuth2\PmPdo($this->getDsn());
        return $oauthQuery->getClientDetails('UNIQFHKZEVMCRVPJAXRCLXIETZBDUEAO');
    }

    protected function convertArrayToObject($array)
    {
        return json_decode(json_encode($array), false);
    }

    /**
     * @url GET /getPmtTableList
     * 
     * @param mixed $start 
     * @param mixed $limit 
     */
    public function getPmtTableList($start, $limit)
    {
        $httpData = new stdClass();
        $httpData->start = $start;
        $httpData->limit = $limit;

        return $this->pmTablesProxy->getList($httpData);
    }

    /**
     * @param string $rows
     */
    protected function generatePmtFile($rows)
    {
        $httpData = new stdClass();
        $httpData->rows = $rows;

        $pmTablesProxyResponse = $this->pmTablesProxy->export($httpData);

        return $pmTablesProxyResponse;
    }

    /**
     * @url POST /exportPmtTable
     * 
     * @param array $params
     */
    public function exportPmtTable($params)
    {
        $response = $this->generatePmtFile($params['rows']);

        if ($response->success) {
            $httpData = new stdClass();
            $httpData->f = $response->filename;

            return $this->pmTables->streamExported($httpData);
        }

        throw new RestException(Api::STAT_APP_EXCEPTION, 'Export failed');
    }

    /**
     * @url POST /importPmtTable
     */
    public function importPmtTable()
    {
        if (!isset($_FILES['form'])) {
            throw new RestException(Api::STAT_APP_EXCEPTION, 'No file uploaded');
        }

        $_SESSION['PROCESS'] = $_POST['form']['PRO_UID'];

        $tmpFile = $_FILES['form'];

        $_FILES = array();

        $_FILES['form']['name']['FILENAME'] = $tmpFile['name'];
        $_FILES['form']['tmp_name']['FILENAME'] = $tmpFile['tmp_name'];
        $_FILES['form']['error']['FILENAME'] = file_exists($tmpFile['tmp_name']) ? 0 : 1;

        return $this->pmTablesProxy->import(new stdClass());
    }
}