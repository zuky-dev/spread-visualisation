<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderBookLogIndexRequest extends FormRequest
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
     * We either want a date or null (so either sync since a date or last 10 seconds)
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'since' => 'nullable|date'
        ];
    }
}
