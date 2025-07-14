<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestinationCreateRequest;
use App\Http\Requests\DestinationUpdateRequest;
use App\Http\Resources\DestinationCollection;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use App\Models\Tag;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function create(DestinationCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Pisahkan data tag dari data destinasi
        $tagName = null;
        if (isset($data['tag'])) {
            $tagName = $data['tag'];
            // Hapus tag dari data destinasi karena akan disimpan di tabel terpisah
            unset($data['tag']);
        }
        
        $destination = new Destination($data);
        $destination->save();
        
        // Jika ada tag, buat record tag baru
        if ($tagName) {
            $tag = new Tag([
                'destination_id' => $destination->id,
                'tag_name' => $tagName
            ]);
            $tag->save();
        }
        
        // Load relasi tag agar tersedia di resource
        $destination->load('tags');
        
        return (new DestinationResource($destination))->response()->setStatusCode(201);
    }

    public function get(int $id): DestinationResource
    {
        $destination = Destination::with('tags')->where('id', $id)->first();

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
        $destination = Destination::with('tags')->where('id', $id)->first();

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
        
        // Handle tag jika tersedia
        $tagName = null;
        if (isset($data['tag'])) {
            $tagName = $data['tag'];
            unset($data['tag']);
        }
        
        // Update data destinasi
        $destination->fill($data);
        $destination->save();
        
        // Update atau buat tag jika tersedia
        if ($tagName) {
            // Cek jika destinasi sudah ada tag
            if ($destination->tags->count() > 0) {
                $tag = $destination->tags->first();
                $tag->tag_name = $tagName;
                $tag->save();
            } else {
                $tag = new Tag([
                    'destination_id' => $destination->id,
                    'tag_name' => $tagName
                ]);
                $tag->save();
            }
        }
        
        // Reload destinasi dengan tag yang sudah di update
        $destination->load('tags');
        
        return new DestinationResource($destination);
    }

    public function delete(int $id): JsonResponse
    {
        $destination = Destination::with('tags')->where('id', $id)->first();

        if (!$destination) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        // Hapus tag yang terkait terlebih dahulu
        if ($destination->tags->count() > 0) {
            foreach ($destination->tags as $tag) {
                $tag->delete();
            }
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
