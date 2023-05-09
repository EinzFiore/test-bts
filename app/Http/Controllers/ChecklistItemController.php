<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChecklistItems;
use App\Models\Checklists;
use App\Models\MItems;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ChecklistItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(string $checklistId)
    {
        try {
            $results = Checklists::with('items')->findOrFail($checklistId);

            return response()->api($results, 200);

        } catch (\Exception $e) {
            $statusCode = ($e->getCode() > 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            $response = [
                'errors' => $e,
            ];

            return response()->api($response, $statusCode);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $checklistId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|uuid|exists:m_items,id',
            ]);

            $params = $validator->validate();

            $checkList = ChecklistItems::whereChecklistId($checklistId)
                ->whereItemId($params['item_id'])
                ->first();

            if($checkList){
                throw new \Exception("already exist item in checklist", 400);
            }

            $params['checklist_id'] = $checklistId;
            $results = ChecklistItems::create($params);

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

    /**
     * Display the specified resource.
     */
    public function show(string $checklistId, string $itemId)
    {
        try {
            $results = ChecklistItems::with('item')->byChecklist($checklistId)
                ->whereItemId($itemId)
                ->first();

            if(!$results){
                throw new \Exception("not found item in checklist", 400);
            }

            return response()->api($results, 200);

        } catch (\Exception $e) {
            $statusCode = ($e->getCode() > 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            $response = [
                'errors' => $e,
            ];

            return response()->api($response, $statusCode);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $checklistId, string $itemId)
    {
        try {
            $results = ChecklistItems::byChecklist($checklistId)
                ->whereItemId($itemId)
                ->first();

            if(!$results){
                throw new \Exception("not found item in checklist", 400);
            }

            $status = $results->status ? 0 : 1;

            $results->update([
                'status' => $status
            ]);

            return response()->api($results, 200);

        } catch (\Exception $e) {
            $statusCode = ($e->getCode() > 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            $response = [
                'errors' => $e,
            ];

            return response()->api($response, $statusCode);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $checklistId, string $itemId)
    {
        try {
            $results = ChecklistItems::byChecklist($checklistId)
                ->whereItemId($itemId)
                ->first();

            if(!$results){
                throw new \Exception("not found item in checklist", 400);
            }

            $results->delete();

            return response()->api($results, 200);

        } catch (\Exception $e) {
            $statusCode = ($e->getCode() > 100 && $e->getCode() < 600) ? $e->getCode() : 500;
            $response = [
                'errors' => $e,
            ];

            return response()->api($response, $statusCode);
        }
    }

    public function renameItem(Request $request, string $checklistId, string $itemId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
            ]);

            $params = $validator->validate();

            $results = ChecklistItems::byChecklist($checklistId)
                ->whereItemId($itemId)
                ->first();

            if(!$results){
                throw new \Exception("not found item in checklist", 400);
            }

            $item = MItems::findOrFail($itemId);
            $item->update(['name' => $params['name']]);

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
