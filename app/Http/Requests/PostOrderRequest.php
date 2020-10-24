<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class PostOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type'          => 'required|in:pendingReview,pendingPayment,completed,canceled',
            'status'        => 'required|integer',
            'order_details' => 'required|array|min:1|max:5',
            'customer_id'   => 'required|integer',
            'payment_id'    => 'required|integer',
            'shipping_id'   => 'required|integer'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'type.required' => 'The type is required',
            'status.required' => 'The status is required',
            'order_details.required' => 'The order details are required',
            'customer_id.required' => 'The customer id is required',
            'payment_id.required' => 'The payment id is required',
            'shipping_id.required' => 'The shipping id is required'
        ];
    }
}
