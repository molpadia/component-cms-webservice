<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetOrderRequest;
use App\Http\Requests\PostOrderRequest;
use App\Repositories\OrderRepository;

class OrderController extends Controller
{
    /**
     * The order repository instance.
     *
     * @var \App\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * Create a new order controller instance.
     *
     * @param \App\Repositories\OrderRepository $orderRepository
     *
     * @return void
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get multiple order entities.
     *
     * @param \App\Http\Requests\GetOrderRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show(GetOrderRequest $request)
    {
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        $orders = $this->orderRepository->get($limit, $offset);

        return response($orders, 200);
    }

    /**
     * Create a new order entity.
     *
     * @param \App\Http\Requests\PostOrderRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(PostOrderRequest $request)
    {
        $this->orderRepository->create($request->input());

        return response()->created();
    }
}
