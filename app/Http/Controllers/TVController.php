<?php

namespace App\Http\Controllers;

use App\Http\Resources\TVResource;
use App\Models\Category;
use App\Models\TvModel;
use Illuminate\Http\JsonResponse;

class TVController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(int|string $categoryID = null): JsonResponse
    {

        $category = Category::when($categoryID, static function ($query, $categoryID) {
                return $query->where('id', $categoryID);
            })
            ->when($categoryID === null, static function ($query) {
                return $query->where('name', 'tv');
            })
            ->first();

        $tvs = TvModel::with('brand')
            ->where('category_id', $category->id)
            ->paginate(20);

        return response()->json([
            'tvs' => TVResource::collection($tvs),
            'pagination' => [
                'total' => $tvs->total(),
                'per_page' => $tvs->perPage(),
                'current_page' => $tvs->currentPage(),
                'last_page' => $tvs->lastPage(),
                'from' => $tvs->firstItem(),
                'to' => $tvs->lastItem(),
            ],
        ]);
    }


}
