<?php

namespace App\Http\Controllers;

use App\Models\Checklists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ChecklistController extends Controller
{
    public function listChecklist()
    {
        try {
            $results = Checklists::all();

            return response()->api($results, 200);

        } catch (\Exception $e) {
            $statusCode = ($e->getCode() > 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            $response = [
                'errors' => $e,
            ];

            return response()->api($response, $statusCode);
        }
    }

    public function deleteChecklist(string $id)
    {
        try {
            $checklist = Checklists::find($id);
            if(!$checklist){
                throw new \Exception("Not Found checklist with given id", 400);
            }

            $checklist->delete();

            return response()->api($checklist, 200);

        } catch (\Exception $e) {
            $statusCode = ($e->getCode() > 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            $response = [
                'errors' => $e,
            ];

            return response()->api($response, $statusCode);
        }
    }

    public function newChecklist(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:checklists,name'
            ]);

            $params = $validator->validate();

            $results = Checklists::create($params);

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
