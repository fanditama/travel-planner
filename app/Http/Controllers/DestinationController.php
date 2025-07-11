<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestinationCreateRequest;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function create(DestinationCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $destination = new Destination($data);
        $destination->save();

        return (new DestinationResource($destination))->response()->setStatusCode(201);
    }

    public function get(int $id): DestinationResource
    {
        $destination = Destination::where('id', $id)->first();

        if (!$destination) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new DestinationResource($destination);
    }
}
