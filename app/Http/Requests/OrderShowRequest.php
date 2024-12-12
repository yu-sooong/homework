<?php

namespace App\Http\Requests;

use App\Enums\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'       => ['required|string'],
            'currency' => ['required', Rule::in(Currency::values())], // 使用 Enum 驗證
        ];
    }

    public function prepareForValidation()
    {
        $currency = $this->query('currency');

        $this->merge([
            'currency' => !$currency ? null : $currency, // 讓它成為 null 以便後續驗證
        ]);
    }
}
