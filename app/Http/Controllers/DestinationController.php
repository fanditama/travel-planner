<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestinationCreateRequest;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
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
}
