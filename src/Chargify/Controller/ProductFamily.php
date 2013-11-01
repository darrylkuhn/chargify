<?php

namespace Chargify\Controller;

use \Chargify\Resource\ProductFamilyResource as Resource;

class ProductFamily extends AbstractController 
{
    /**
     * Return all product families.
     *
     * @return array of \Chargify\Resource\ProductFamilyResource
     */
    public function getAll() 
    {
        $productsFamilies = array();

        // Get the raw data from Chargify.
        $response = $this->request('product_families');

        // Convert the raw data into resource objects.
        foreach ( $response as $data ) 
        {
            $productsFamilies[] = new Resource($data['product_family']);
        }

        return $productsFamilies;
    }

    /**
     * I couldn't find this equivelent call documented anywhere. So simulate it
     * with a get all and then a filer
     * 
     * @param string $handle particular membership we're looking for
     * @return mixed \Chargify\Resource\ProductFamilyResource or null
     */
    public function getByHandle( $handle ) 
    {
        $productFamilies = $this->getAll();

        foreach ( $productFamilies as $productFamily )
        {
            if ( $productFamily->handle == $handle )
            {
                return $productFamily;
            }
        }

        return null;
    }
}