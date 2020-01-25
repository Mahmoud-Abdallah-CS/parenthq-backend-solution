<?php

namespace App\Mappers;

use AutoMapperPlus\CustomMapper\CustomMapper;
use App\Enums\UserState;

class DataProviderXMapper extends CustomMapper
{
    public function mapToObject($source, $destination)
    {
        $destination->balance = $source['parentAmount'];
        $destination->currency = $source['Currency'];
        $destination->email = $source['parentEmail'];
        $destination->status = $this->getStatus( $source['statusCode'] );
        $destination->created_at = $source['registerationDate'];
        $destination->id = $source['parentIdentification'];
        
        return $destination;
    }
    
    protected function getStatus( $code )
    {
        switch( $code )
        {
            case 1:
                return UserState::AUTHORISED;
            break;

            case 2:
                return UserState::DECLINE;
            break;

            case 3:
                return UserState::REFUNDED;
            break;
        }
    }
}