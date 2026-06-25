<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code', 'client_id', 'plan_type', 'amount', 'status', 'payment_method'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
