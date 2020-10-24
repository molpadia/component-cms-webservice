<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'status', 'customer_id', 'payment_id', 'shipping_id'
    ];

    /**
     * Get the customer record associated with the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Get the payment record associated with the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the shipping record associated with the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shipping()
    {
        return $this->hasOne(Shipping::class);
    }

    /**
     * Get the order details associated with the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}
