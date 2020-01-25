<?php

namespace App\DataProviders;

use AutoMapperPlus\Configuration\AutoMapperConfig;
use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\DataType;
use App\Mappers\DataProviderXMapper;
use App\Mappers\DataProviderYMapper;
use App\UserDataTransferObject;
use AutoMapperPlus\CustomMapper\CustomMapper;


class DataProvider
{
    protected function loadData( $filename )
    {
        $path = storage_path() . "/json/${filename}.json";
        return json_decode(file_get_contents($path), true); 
    }

    // --
    protected function getDataProviderMapper( string $name )
    {
        switch($name)
        {
            case 'DataProviderX':
                return new DataProviderXMapper;
            break;

            case 'DataProviderY':
                return new DataProviderYMapper;
            break;
        }

        return null;
    }

    /**
     * DataProvider getData.
     * @param string $filename
     */
    public function getData( string $filename )
    {
        $data = $this->loadData( $filename );

        $config = new AutoMapperConfig();
        $mapping = $config->registerMapping(DataType::ARRAY, UserDataTransferObject::class);

        $customMapper = $this->getDataProviderMapper( $filename );
        if( $customMapper )
        {
            $mapping->useCustomMapper( $customMapper );
        }

        $mapper = new AutoMapper($config);

        return $mapper->mapMultiple($data['users'], UserDataTransferObject::class);
    }
}