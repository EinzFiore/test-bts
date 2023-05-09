<?php

namespace App\Providers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ResponseFactory $factory)
    {
        // create macro api() for structur response return
        $isSuccess = function ($data, $statusCode) {
            if (isset($data['errors'])) {
                return false;
            }

            $success = isset($data['success']) ? (bool)$data['success'] : null;

            if (!is_null($success)) {
                return $success;
            }

            if ($statusCode == 200) {
                return true;
            }

            return false;
        };

        $getMetadata = function ($data) {
            if (isset($data['metadata'])) {
                return $data['metadata'];
            }

            $metadata = [];
            if ($data instanceof LengthAwarePaginator) {
                $pagination = [
                    'per_page'      => intVal($data->perPage()),
                    'total'         => $data->total(),
                    'current_page'  => $data->currentPage(),
                    'last_page'     => $data->lastPage(),
                    'next_page_url' => $data->nextPageUrl(),
                    'prev_page_url' => $data->previousPageUrl(),
                ];

                $metadata['pagination'] = $pagination;
            }

            return $metadata;
        };

        $getErrors = function($data, $statusCode) {
            $errors = $data['errors'] ?? [];
            if (!$errors) {
                return null;
            }

            if ($errors instanceof \Exception) {
                return [
                    'code' => ($errors->getCode() > 0) ? $errors->getCode() : $statusCode,
                    'message' => $errors->getMessage(),
                ];
            }

            return [
                'code' => $errors['code'] ?? $statusCode,
                'message' => $errors['message'] ?? '',
            ];
        };

        $getResult = function ($data, $isSuccess) {
            if (!$isSuccess) {
                return null;
            }

            if ($data instanceof LengthAwarePaginator) {
                return $data->items();
            }

            $results = $data['results'] ?? $data;

            return $results;
        };

        $macro = function (
            $data,
            $statusCode = 200,
            $version = 0
        ) use (
            $factory,
            $isSuccess,
            $getMetadata,
            $getResult,
            $getErrors
        ) {

            $message = $data['message'] ?? '';
            $isSuccess = $isSuccess($data, $statusCode);
            $errors = $getErrors($data, $statusCode);
            $metadata = $getMetadata($data);
            $results = $getResult($data, $isSuccess);

            $customFormat = [
                'message' => $message,
                'success' => $isSuccess,
                'errors' => $errors,
                'metadata' => $metadata,
                'data' => $results,
            ];

            // @phpstan-ignore-next-line
            return $factory->make($customFormat, $statusCode);
        };

        $factory->macro('api', $macro);
    }
}
