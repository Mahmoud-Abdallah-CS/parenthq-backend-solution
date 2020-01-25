<?php

namespace App\Repositories;

use App\DataProviders\DataProvider;

class UserRepository implements UserRepositoryInterface
{
    protected $datastore = [];

    /**
     * UserRepository constructor.
     * @param DataProvider $dataProvider
     */
    public function __construct( DataProvider $dataProvider )
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * UserRepository getBy.
     * @param array $filters
     */
    public function getBy(array $filters = []) : array
    {
        // filter by provider
        $defaultProviders = [
            'DataProviderX',
            'DataProviderY'
        ];

        if( isset( $filters['provider'] ) )
        {
            $filterProvider = $filters['provider'];
            $defaultProviders = array_intersect($defaultProviders, [
                $filterProvider
            ]);
        }

        // map to one collection
        $collection = collect();

        foreach( $defaultProviders as $provider )
        {
            $data = $this->dataProvider->getData($provider);
            $collection = $collection->merge( $data );
        }

        if( empty($filters) )
        {
            return $collection->all();
        }

        // apply filters
        $filtered = $collection->filter( function ($value ) use ( $filters ) {
            if( 
                isset( $filters['statusCode'] ) &&
                $value->status != $filters['statusCode']
            ) { return false; }

            if( 
                (
                    isset( $filters['balanceMin'] ) &&
                    isset( $filters['balanceMax'] )
                ) &&
                !(
                    $value->balance >= $filters['balanceMin'] &&
                    $value->balance <= $filters['balanceMax']
                )
            ) { return false; }

            if( 
                isset( $filters['currency'] ) &&
                $value->currency != $filters['currency']
            ) { return false; }
            
            return true;
        } );

        return $filtered->all();
    }
}