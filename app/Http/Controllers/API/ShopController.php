<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Supports\ShopFilter;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(Author $authorModel, Book $bookModel, Category $categoryModel)
    {
        $this->authorModel = $authorModel;
        $this->bookModel = $bookModel;
        $this->categoryModel = $categoryModel;
    }

    public function getProducts(Request $request)
    {
        $limit = $request->limit;
        $per_page = $request->per_page ?? 20;

        $query = $this->bookModel->with('author', 'category', 'availableDiscounts', 'reviews')->selectFinalPrice();

        if ($request->filter_by != '' && $request->filter_value != '') {
            $query = $query->filterBy($request->filter_by, $request->filter_value);
        }

        switch ($request->get('sort_by')) {
            case 'popularity':
                $query = $query
                    ->withCount('reviews')
                    ->orderByDesc('reviews_count')
                    ->orderBy('final_price');
                break;

            case 'asc_price':
                $query = $query
                    ->orderBy('final_price');
                break;

            case 'desc_price':
                $query = $query
                    ->orderByDesc('final_price');
                break;

            default:
                $query = $query
                    ->selectSubPrice()
                    ->orderByDesc('sub_price')
                    ->orderBy('final_price');
        }

        return BookResource::collection($query->paginate($per_page));
    }

    public function getAllFilters(): array
    {
        $author_filter = ShopFilter::getFiltersByAuthor();

        $category_filter = ShopFilter::getFiltersByCategory();

        $rating_filter = ShopFilter::getFiltersByStar();

        return [
            $category_filter,
            $author_filter,
            $rating_filter,
        ];
    }
}
