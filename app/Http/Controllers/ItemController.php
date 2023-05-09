<?php

namespace App\Http\Controllers;

use App\Models\MItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    public function listItem()
    {
        try {
            $results = MItems::all();

            return response()->api($results, 200);

        } catch (\Exception $e) {
            $statusCode = ($e->getCode() > 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            $response = [
                'errors' => $e,
            ];

            return response()->api($response, $statusCode);
        }
    }

    public function newItem(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:checklists,name'
            ]);

            $params = $validator->validate();

            $results = MItems::create($params);

            return response()->api($results, 200);

        } catch (ValidationException $e) {
            $response = [
                'errors' => [
                    'code' => 422,
                    'message' => $e->errors(),
                ]
            ];
            return response()->api($response, 422);
        } catch (\Exception $e) {
            $statusCode = ($e->getCode() > 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            $response = [
                'errors' => $e,
            ];

            return response()->api($response, $statusCode);
        }
    }
}
