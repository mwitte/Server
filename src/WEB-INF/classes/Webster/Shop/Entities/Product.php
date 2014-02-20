<?php
/**
 * A product entity
 *
 * PHP version 5
 *
 * @category   AppServer
 * @package    Webster\Shop
 * @subpackage Entities
 * @author     Florian Sydekum <f.sydekum@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace Webster\Shop\Entities;

use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Webster\Shop\Entities\Product
 *
 * A product entity
 *
 * @category   AppServer
 * @package    Webster\Shop
 * @subpackage Entities
 * @author     Florian Sydekum <f.sydekum@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php
 *             Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class Product
{
    const ELASTIC_TYPE = 'product';

    private $_id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "2",
     *      max = "50",
     *      minMessage = "The name must be at least {{ limit }} characters length",
     *      maxMessage = "The name cannot be longer than {{ limit }} characters length"
     * )
     */
    private $_name;

    /**
     * @Assert\NotBlank()
     */
    private $_price;

    private $_inventory;
    private $_description;
    private $_image;
    private $_categories;

    public function __construct(/* Message $message */)
    {
        //TODO: Constructor for socket message
    }

    public function getElasticType($index)
    {
        return $index->getType(self::ELASTIC_TYPE);
    }

    public static function createMapping($elasticaIndex)
    {
        require_once '/opt/appserver/webapps/webstershop/vendor/autoload.php';

        //Create a type
        $elasticaType = $elasticaIndex->getType(self::ELASTIC_TYPE);

        // Define mapping
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($elasticaType);
        $mapping->setParam('index_analyzer', 'indexAnalyzer');
        $mapping->setParam('search_analyzer', 'searchAnalyzer');

        // Send mapping to type
        $mapping->send();
    }

    /**
     * Returns the product data as array
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'inventory' => $this->getInventory(),
            'image' => $this->getImage(),
            'categories' => $this->getCategories()
        );
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories)
    {
        $this->_categories = $categories;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->_categories;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->_image = $image;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->_image;
    }

    /**
     * @param int $inventory
     */
    public function setInventory($inventory)
    {
        $this->_inventory = $inventory;
    }

    /**
     * @return int
     */
    public function getInventory()
    {
        return $this->_inventory;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->_price = $price;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->_price;
    }
}