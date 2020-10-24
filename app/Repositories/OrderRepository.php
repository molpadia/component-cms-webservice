<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderRepository extends Repository
{
    /**
     * Create a new order repository instance.
     *
     * @param \App\Models\Order $model
     *
     * @return void
     */
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * Get multiple order entities.
     *
     * @param int $limit
     * @param int $offset
     *
     * @return \Illuminate\Database\Eloquent\Model[]
     */
    public function get($limit, $offset)
    {
        return $this->model->limit($limit)->offset($offset)->get();
    }

    /**
     * Save a new order entity.
     *
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        DB::transaction(function () use ($data) {
            $newOrder = $this->model->create($data);

            foreach ($data['order_details'] as $detail) {
                $newOrder->orderDetails()->create($detail);
            }
        });
    }
}
