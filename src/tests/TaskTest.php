<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

use App\DataProviders\DataProvider;
use App\Enums\UserState;

class TaskTest extends TestCase
{
    /**
     * A basic test route.
     *
     * @return void
     */
    public function testRoute()
    {
        $this->get('/api/v1/users');

        $this->assertEquals(
            200, $this->response->getStatusCode()
        );
    }

    /**
     * A basic test for list all users.
     *
     * @return void
     */
    public function testAllUsers()
    {
        $dataProvider = new DataProvider;
        $this->get('/api/v1/users');

        $providers = [
            'DataProviderX',
            'DataProviderY'
        ];

        $collection = collect([]);
        foreach( $providers as $filename )
        {
            $data = $dataProvider->getData( $filename );
            $collection = $collection->merge($data);
        }

        $payload = ['users' => array_values($collection->all())];
        
        $this->seeJsonEquals(
            $payload, $this->response->getContent()
        );
    }

    /**
     * A basic test for provider filter.
     *
     * @return void
     */
    public function testProviderFilter()
    {
        $providers = [
            'DataProviderX',
            'DataProviderY'
        ];

        foreach( $providers as $filename )
        {
            $dataProvider = new DataProvider;
            $this->get('/api/v1/users?provider=' . $filename);


            $data = $dataProvider->getData( $filename );
            $payload = ['users' => array_values($data)];

            $this->seeJsonEquals(
                $payload, $this->response->getContent()
            );
        }
    }

    /**
     * A basic test for all status.
     *
     * @return void
     */
    public function testStatusCodeFilter()
    {
        $dataProvider = new DataProvider;

        $providers = [
            'DataProviderX',
            'DataProviderY'
        ];

        $collection = collect([]);
        foreach( $providers as $filename )
        {
            $data = $dataProvider->getData( $filename );
            $collection = $collection->merge($data);
        }
        
        $status = [
            UserState::AUTHORISED,
            UserState::DECLINE,
            UserState::REFUNDED,
        ];

        foreach( $status as $state )
        {
            $this->get('/api/v1/users?statusCode=' . $state );

            $filtered = $collection->filter( function ( $value ) use( $state ) {
                return $value->status == $state;
            });

            $payload = ['users' => array_values($filtered->all())];

            $this->seeJsonEquals(
                $payload, $this->response->getContent()
            );
        }
    }

    /**
     * A basic test for amount range filter.
     *
     * @return void
     */
    public function testBalanceMinBalanceMaxFilter()
    {
        $dataProvider = new DataProvider;

        $providers = [
            'DataProviderX',
            'DataProviderY'
        ];

        $collection = collect([]);
        foreach( $providers as $filename )
        {
            $data = $dataProvider->getData( $filename );
            $collection = $collection->merge($data);
        }

        $balanceMin = 0;
        $balanceMax = 500;
        
        $this->get('/api/v1/users?balanceMin=' . $balanceMin . '&balanceMax=' . $balanceMax );

        $filtered = $collection->filter( function ( $value ) use( $balanceMin, $balanceMax ) {
            return $value->balance >= $balanceMin && $value->balance <= $balanceMax;
        });
        
        $payload = ['users' => array_values($filtered->all())];

        $this->seeJsonEquals(
            $payload, $this->response->getContent()
        );
    }

    /**
     * A basic test for all currencies.
     *
     * @return void
     */
    public function testCurrenciesFilter()
    {
        $dataProvider = new DataProvider;

        $providers = [
            'DataProviderX',
            'DataProviderY'
        ];

        $collection = collect([]);
        foreach( $providers as $filename )
        {
            $data = $dataProvider->getData( $filename );
            $collection = $collection->merge($data);
        }
        
        $currencies = [
            "EUR",
            "USD",
            "EGP",
            "AED",
        ];

        foreach( $currencies as $currency )
        {
            $this->get('/api/v1/users?currency=' . $currency );

            $filtered = $collection->filter( function ( $value ) use( $currency ) {
                return $value->currency == $currency;
            });
            
            $payload = ['users' => array_values($filtered->all())];

            $this->seeJsonEquals(
                $payload, $this->response->getContent()
            );
        }
    }

    /**
     * A basic test for all filters.
     *
     * @return void
     */
    public function testAllFilter()
    {
        $dataProvider = new DataProvider;

        $filters = [
            'provider' => 'DataProviderY',
            'statusCode' => UserState::REFUNDED,
            'balanceMin' => 10,
            'balanceMax' => 500,
            'currency' => 'USD',
        ];

        $collection = collect([]);
        $data = $dataProvider->getData( $filters['provider'] );
        $collection = $collection->merge($data);

        $queryString = http_build_query($filters);

        $this->get('/api/v1/users?' . $queryString );

        $filtered = $collection->filter( function ( $value ) use( $filters ) {
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
        });
        
        $payload = ['users' => array_values($filtered->all())];

        $this->seeJsonEquals(
            $payload, $this->response->getContent()
        );
    }
}
