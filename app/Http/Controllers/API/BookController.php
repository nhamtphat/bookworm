<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Supports\ShopFilter;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BookController extends Controller
{
    public function __construct(Book $bookModel)
    {
        $this->bookModel = $bookModel;
    }

    public function index(Request $request)
    {
        $query = $this->bookModel
            ->with('author', 'category', 'availableDiscounts', 'reviews')
            ->withCount('reviews')
            ->selectAvgStar()
            ->selectFinalPrice()
            ->selectSubPrice();

        $query = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::exact('category', 'category_id'),
                AllowedFilter::exact('author', 'author_id'),
                AllowedFilter::scope('star')
            ])
            ->defaultSort('-sub_price')
            ->allowedSorts([
                'sub_price',
                'final_price',
                'avg_star',
                'reviews_count'
            ]);

        if($request->has('limit') && $request->limit != '') {
            $books_collection = $query->limit($request->limit)->get();
        } else {
            $books_collection = $query->paginate($request->per_page ?? 20);
        }

        return BookResource::collection($books_collection);
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
