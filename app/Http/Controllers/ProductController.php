<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::latest()->paginate(15);
        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'                  => ['required', 'string', 'max:255'],
            'subtitle'               => ['nullable', 'string', 'max:255'],
            'description'            => ['nullable', 'string'],
            'type'                   => ['nullable', 'string', 'max:100'],
            'wingspan'               => ['nullable', 'string', 'max:100'],
            'flightEndurance'        => ['nullable', 'string', 'max:100'],
            'flightRange'            => ['nullable', 'string', 'max:100'],
            'flightHeight'           => ['nullable', 'string', 'max:100'],
            'otherDetails'           => ['nullable', 'string'],
            'basePrice'              => ['nullable', 'numeric'],

            'images'                 => ['nullable', 'array'],
            'images.*'               => ['nullable', 'string'],

            'include'                => ['nullable', 'array'],
            'include.*'              => ['nullable', 'string'],

            'packageOptions'         => ['nullable', 'array'],
            'packageOptions.*.name'  => ['required_with:packageOptions', 'string'],
            'packageOptions.*.price' => ['nullable', 'numeric'],
            'packageOptions.*.description' => ['nullable', 'string'],

            'financing'              => ['nullable', 'array'],
            'financing.*'            => ['nullable', 'string'],
        ]);

        $images = $this->processImages($data['images'] ?? []);

        $payload = $this->mapPayload($data);
        $payload['images'] = $images;

        $product = Product::create($payload);

        return response()->json([
            'message' => 'Product created',
            'data'    => $product,
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'title'                  => ['sometimes', 'required', 'string', 'max:255'],
            'subtitle'               => ['sometimes', 'nullable', 'string', 'max:255'],
            'description'            => ['sometimes', 'nullable', 'string'],
            'type'                   => ['sometimes', 'nullable', 'string', 'max:100'],
            'wingspan'               => ['sometimes', 'nullable', 'string', 'max:100'],
            'flightEndurance'        => ['sometimes', 'nullable', 'string', 'max:100'],
            'flightRange'            => ['sometimes', 'nullable', 'string', 'max:100'],
            'flightHeight'           => ['sometimes', 'nullable', 'string', 'max:100'],
            'otherDetails'           => ['sometimes', 'nullable', 'string'],
            'basePrice'              => ['sometimes', 'nullable', 'numeric'],

            'images'                 => ['sometimes', 'array'],
            'images.*'               => ['nullable', 'string'],

            'include'                => ['sometimes', 'array'],
            'include.*'              => ['nullable', 'string'],

            'packageOptions'         => ['sometimes', 'array'],
            'packageOptions.*.name'  => ['required_with:packageOptions', 'string'],
            'packageOptions.*.price' => ['nullable', 'numeric'],
            'packageOptions.*.description' => ['nullable', 'string'],

            'financing'              => ['sometimes', 'array'],
            'financing.*'            => ['nullable', 'string'],
        ]);

        if (array_key_exists('images', $data)) {
            $existing = $product->images ?? [];
            $images   = $this->processImages($data['images'] ?? [], $existing);
            $data['images'] = $images;
        }

        $payload = $this->mapPayload($data);
        $product->update($payload);

        return response()->json([
            'message' => 'Product updated',
            'data'    => $product->fresh(),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->deleteImages($product->images ?? []);
        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }

    private function mapPayload(array $data): array
    {
        $map = [
            'title'           => 'title',
            'subtitle'        => 'subtitle',
            'description'     => 'description',
            'type'            => 'type',
            'wingspan'        => 'wingspan',
            'flightEndurance' => 'flight_endurance',
            'flightRange'     => 'flight_range',
            'flightHeight'    => 'flight_height',
            'otherDetails'    => 'other_details',
            'include'         => 'include',
            'packageOptions'  => 'package_options',
            'financing'       => 'financing',
            'basePrice'       => 'base_price',
            'images'          => 'images',
        ];

        $payload = [];
        foreach ($map as $from => $to) {
            if (array_key_exists($from, $data)) {
                $payload[$to] = $data[$from];
            }
        }
        return $payload;
    }

    private function processImages(array $incoming, array $existing = []): array
    {
        $result = [];

        foreach ($incoming as $img) {
            if (!is_string($img)) continue;

            if (!str_starts_with($img, 'data:image/')) {
                $result[] = $img;
                continue;
            }

            [$meta, $b64] = explode(',', $img, 2);
            $ext = 'png';
            if (preg_match('#^data:image/([a-zA-Z0-9+]+);base64$#', $meta, $m)) {
                $ext = strtolower($m[1]);
            }
            $binary   = base64_decode($b64);
            $filename = 'products/' . now()->format('Ymd_His') . '_' . Str::random(10) . '.' . $ext;

            Storage::disk('public')->put($filename, $binary);

            $result[] = Storage::url($filename); 
        }

        $toDelete = array_diff($existing, $result);
        $this->deleteImages($toDelete);

        return $result;
    }

    private function deleteImages(array $urls): void
    {
        foreach ($urls as $url) {
            if (is_string($url) && str_starts_with($url, '/storage/')) {
                $path = substr($url, strlen('/storage/')); 
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }
    }
}