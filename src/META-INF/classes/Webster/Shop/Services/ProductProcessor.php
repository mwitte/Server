<?php
/**
 * Product processor class
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

use Webster\Shop\Services\AbstractProcessor;
use Webster\Shop\Entities\Product;

/**
 * Webster\Shop\Services\ProductProcessor
 *
 * Product processor class
 *
 * @category   AppServer
 * @package    Webster\Shop
 * @subpackage Services
 * @author     Florian Sydekum <f.sydekum@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 * @Singleton
 */
class ProductProcessor extends AbstractProcessor
{
    const ELASTIC_TYPE = 'product';

    /**
     * Persists the passed entity.
     *
     * @param Product $product The entity to persist
     * @return Product The persisted entity
     */
    public function persist(Product $product)
    {
        require_once '/opt/appserver/webapps/webstershop/vendor/autoload.php';

        // load the elastica client, index and type
        $elastica = $this->getElasticaClient();
        $index = $elastica->getIndex(self::ELASTIC_INDEX);
        $type = $index->getType(self::ELASTIC_TYPE);


        // create a document
        $productDocument = new \Elastica\Document('', $product->toArray());
        $type->addDocument($productDocument);

        // Refresh Index
        $type->getIndex()->refresh();

        return $product;
    }
}