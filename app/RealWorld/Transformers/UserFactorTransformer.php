<?php

namespace App\RealWorld\Transformers;

class UserFactorTransformer extends Transformer
{
    protected $resourceName = 'factor';

    public function transform($data)
    {
        return [
            'factor_id' => $data['id'],
            'product_id' => $data['purchasable_id'],
            'product_type' => class_basename($data['purchasable_type']),
            'price' => $data['transaction']['amount'],
        ];
    }
}