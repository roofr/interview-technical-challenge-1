<?php

namespace App\Http\Requests;

use App\Enums\VehicleType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ParkinkSpotRequest extends FormRequest
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
            'vehicleType' => ['required', Rule::in(VehicleType::values())],
            'vehiclePlate' => ['required', 'string', 'size:6'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator,
            response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], Response::HTTP_NOT_ACCEPTABLE)
        );
    }
}
