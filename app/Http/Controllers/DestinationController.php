<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestinationCreateRequest;
use App\Http\Requests\DestinationUpdateRequest;
use App\Http\Resources\DestinationCollection;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use Illuminate\Contracts\Database\Eloquent\Builder;
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

    public function update(int $id, DestinationUpdateRequest $request): DestinationResource
    {
        $destination = Destination::where('id', $id)->first();

        if (!$destination) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();
        $destination->fill($data);
        $destination->save();

        return new DestinationResource($destination);
    }

    public function delete(int $id): jsonResponse
    {
        $destination = Destination::where('id', $id)->first();

        if (!$destination) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $destination->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function search(Request $request): DestinationCollection
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $destinations = Destination::where(function (Builder $builder) use ($request) {
            $name = $request->input('name');

            // jika user input name
            if ($name) {
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orWhere('name', 'like', '%' . $name . '%');
                    $builder->orWhere('location', 'like', '%' . $name . '%');
                });
            }

            $category = $request->input('category');

            // jika user input category
            if ($category) {
                $builder->where(function (Builder $builder) use ($category) {
                    $builder->orWhere('category', 'like', '%' . $category . '%');
                    $builder->orWhere('image_url', 'like', '%' . $category . '%');
                });
            }
        });

        $destinations = $destinations->paginate(perPage: $size, page: $page);

        return new DestinationCollection($destinations);
    }
}
