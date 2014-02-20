<?php
/**
 * Abstract processor class
 *
 * PHP version 5
 *
 * @category   AppServer
 * @package    Webster\Shop
 * @subpackage Services
 * @author     Florian Sydekum <f.sydekum@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace Webster\Shop\Services;

use TechDivision\ApplicationServer\Interfaces\ApplicationInterface;

/**
 * Webster\Shop\Services\AbstractProcessor
 *
 * Abstract processor class
 *
 * @category   AppServer
 * @package    Webster\Shop
 * @subpackage Services
 * @author     Florian Sydekum <f.sydekum@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class AbstractProcessor
{
    const ELASTIC_INDEX = 'shop';

    /**
     * Datasource name to use.
     *
     * @var string
     */
    protected $datasourceName = 'Webster';

    /**
     * Entity namespace
     *
     * @var array
     */
    protected $entityNamespaces;

    /**
     * The application instance that provides the entity manager.
     *
     * @var Application
     */
    protected $application;

    /**
     * The Elastica client instance
     *
     * @var  $elastica \Elastica\Client
     */
    protected $elastica;

    /**
     * Initializes the session bean with the Application instance.
     *
     * Checks on every start if the database already exists, if not
     * the database will be created immediately.
     *
     * @param Application $application
     *            The application instance
     *
     * @return void
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->entityNamespaces = array(
            'Webster\\Shop\\Entities\\Product'
        );

        // set the application instance and initialize the connection parameters
        $this->setApplication($application);
        $this->initConnectionParameters();

        $this->createIndex();
    }

    /**
     * Return's the entity namespaces
     *
     * @return array
     */
    public function getEntityNamespaces()
    {
        return $this->entityNamespaces;
    }

    /**
     * Return's the datasource name to use.
     *
     * @return string The datasource name
     */
    public function getDatasourceName()
    {
        return $this->datasourceName;
    }

    /**
     * The application instance providing the database connection.
     *
     * @param
     *            \TechDivision\ApplicationServer\Interfaces\ApplicationInterface The application instance
     *
     * @return void
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * The application instance providing the database connection.
     *
     * @return Application The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * The database connection parameters used to connect to Doctrine.
     *
     * @param array $connectionParameters
     *            The Doctrine database connection parameters
     *
     * @return
     *
     */
    public function setConnectionParameters(array $connectionParameters = array())
    {
        $this->connectionParameters = $connectionParameters;
    }

    /**
     * Returns the database connection parameters used to connect to Doctrine.
     *
     * @return array The Doctrine database connection parameters
     */
    public function getConnectionParameters()
    {
        return $this->connectionParameters;
    }

    /**
     * Return's the initial context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->getApplication()->getInitialContext();
    }

    /**
     * Return's the system configuration
     *
     * @return \TechDivision\ApplicationServer\Api\Node\NodeInterface The system configuration
     */
    public function getSystemConfiguration()
    {
        return $this->getInitialContext()->getSystemConfiguration();
    }

    /**
     * Return's the array with the datasources.
     *
     * @return array The array with the datasources
     */
    public function getDatasources()
    {
        return $this->getSystemConfiguration()->getDatasources();
    }

    /**
     * Return's the initialized Elastica client instance
     *
     * @return \Elastica\Client The initialized Elastica client
     */
    public function getElasticaClient()
    {
        require_once '/opt/appserver/webapps/webstershop/vendor/autoload.php';
        if(!$this->elastica){
            $this->elastica = new \Elastica\Client($this->getConnectionParameters());
        }

        return $this->elastica;
    }

    /**
     * Initializes the database connection parameters necessary
     * to connect to the database using Elastica.
     *
     * @return void
     */
    public function initConnectionParameters()
    {
        // iterate over the found database sources
        foreach ($this->getDatasources() as $datasourceNode) {

            // if the datasource is related to the session bean
            if ($datasourceNode->getName() == $this->getDatasourceName()) {

                // initialize the database node
                $databaseNode = $datasourceNode->getDatabase();

                // initialize the connection parameters
                $connectionParameters = array(
                    'host' => $databaseNode->getUser()
                            ->getNodeValue()
                            ->__toString(),
                    'port' => (int) $databaseNode->getPassword()
                            ->getNodeValue()
                            ->__toString()
                );

                // set the connection parameters
                $this->setConnectionParameters($connectionParameters);
            }
        }
    }

    /**
     * Builds up the elastic search index and all entities' mappings
     *
     * @return void
     */
    public function createIndex()
    {
        // Load index
        $elasticaIndex = $this->getElasticaClient()->getIndex(self::ELASTIC_INDEX);

        // Create new index
        $elasticaIndex->create(
            array(
                'number_of_shards' => 4,
                'number_of_replicas' => 1,
                'analysis' => array(
                    'analyzer' => array(
                        'indexAnalyzer' => array(
                            'type' => 'custom',
                            'tokenizer' => 'keyword'
                        ),
                        'searchAnalyzer' => array(
                            'type' => 'custom',
                            'tokenizer' => 'keyword'
                        )
                    )
                )
            ),
            false
        );

        foreach($this->getEntityNamespaces() as $entity){
            call_user_func(array($entity, 'createMapping'), $elasticaIndex);
        }
    }
}