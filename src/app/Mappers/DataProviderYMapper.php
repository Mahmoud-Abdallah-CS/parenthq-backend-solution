<?php

namespace App\Mappers;

use AutoMapperPlus\CustomMapper\CustomMapper;
use App\Enums\UserState;

class DataProviderYMapper extends CustomMapper
{
    public function mapToObject($source, $destination)
    {
        $destination->balance = $source['balance'];
        $destination->currency = $source['currency'];
        $destination->email = $source['email'];
        $destination->status = $this->getStatus( $source['status'] );
        $destination->created_at = $source['created_at'];
        $destination->id = $source['id'];
        
        return $destination;
    }
    
    protected function getStatus( $code )
    {
        switch( $code )
        {
            case 100:
                return UserState::AUTHORISED;
            break;

            case 200:
                return UserState::DECLINE;
            break;

            case 300:
                return UserState::REFUNDED;
            break;
        }
    }
}