<?php

namespace Tests\Order\Feature;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    /**
     * Get multiple orders.
     *
     * @return void
     */
    public function testGetDefaultOrders()
    {
        $this->withoutExceptionHandling();
        $response = $this->withHeaders(['Accept' => 'application/json'])
                        ->json('GET', '/api/cms/v1/orders');
        $response->assertStatus(200);
    }

    /**
     * Get multiple orders with limit and offset.
     *
     * @return void
     */
    public function testGetOrdersWithLimitAndOffset()
    {
        $this->withoutExceptionHandling();
        $response = $this->withHeaders(['Accept' => 'application/json'])
                        ->json('GET', '/api/cms/v1/orders', ['limit' => 10, 'offset' => 0]);
        $response->assertStatus(200);
    }

    /**
     * Get multiple orders with invalid limit field.
     *
     * @return void
     */
    public function testGetOrdersWithInvalidLimit()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
                        ->json('GET', '/api/cms/v1/orders', ['limit' => 'undefined']);
        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                'limit' => ['The limit must be an integer.']
            ]
        ]);
    }

    /**
     * Create a order.
     *
     * @return void
     */
    public function testCreateOrder()
    {
        $attributes = [
            'type' => 'pendingPayment',
            'status' => 1,
            'customer_id' => 1,
            'shipping_id' => 1,
            'payment_id' => 1,
            "order_details" => [
                [
                    'quantity' => 1,
                    'discount' => 75,
                    'price' => 150,
                    'product_id' => 1
                ]
            ]
        ];

        $this->withoutExceptionHandling();

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('POST', '/api/cms/v1/orders', $attributes);

        $response->assertStatus(201);
    }

    /**
     * Create a order without required fields.
     *
     * @return void
     */
    public function testCreateOrderWithoutRequiredFields()
    {
        $attributes = [];

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->json('POST', '/api/cms/v1/orders', $attributes);

        $response->assertStatus(422);
    }
}
