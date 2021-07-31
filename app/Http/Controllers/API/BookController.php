<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Supports\ShopFilter;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(Book $bookModel)
    {
        $this->bookModel = $bookModel;
    }

    public function index(Request $request)
    {
        $per_page = $request->per_page ?? 20;

        $query = $this->bookModel->with('author', 'category', 'availableDiscounts', 'reviews')->selectFinalPrice();

        if ($request->filter_by != '' && $request->filter_value != '') {
            $query = $query->filterBy($request->filter_by, $request->filter_value);
        }

        switch ($request->get('sort_by')) {
            case 'on-sale':
                $query = $query
                    ->selectSubPrice()
                    ->orderByDesc('sub_price')
                    ->orderBy('final_price');
                break;

            case 'recommended':
                $query = $query
                    ->selectAvgStar()
                    ->orderByDesc('avg_star')
                    ->orderBy('final_price');
                break;

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
                break;
        }

        if($request->has('limit') && $request->limit != '') {
            $books_colelction = $query->limit($request->limit)->get();
        } else {
            $books_colelction = $query->paginate($per_page);
        }

        return BookResource::collection($books_colelction);
    }

    public function show(Book $book)
    {
        return new BookResource($book);
    }

    public function filters(): array
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
