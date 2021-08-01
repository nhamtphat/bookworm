<?php

namespace App\Supports;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Support\Collection;

class ShopFilter
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public static function getFiltersByAuthor()
    {
        $authors = Author::orderBy('author_name')->get(['id', 'author_name'])->map(function ($author) {
            return [
                'name' => $author->author_name,
                'value' => $author->id
            ];
        });

        return [
            'title' => 'Author',
            'query_key' => 'author',
            'data' => $authors
        ];
    }

    public static function getFiltersByCategory()
    {
        $categories = Category::orderBy('category_name')->get(['id', 'category_name'])->map(function ($category) {
            return [
                'name' => $category->category_name,
                'value' => $category->id
            ];
        });

        return [
            'title' => 'Category',
            'query_key' => 'category',
            'data' => $categories
        ];
    }

    public static function getFiltersByStar()
    {
        $ratings = collect([1, 2, 3, 4, 5])->map(function ($star) {
            return [
                'name' => "$star star",
                'value' => $star
            ];
        });

        return [
            'title' => 'Rating',
            'query_key' => 'star',
            'data' => $ratings
        ];
    }

    public static function getFiltersOfBook(Book $book)
    {
        if (isset($book)) {
            $reviews_count = $book->reviews()->selectRaw('rating_start, count(*)')->groupBy('rating_start')->get();
        } else {
            $reviews_count = Review::selectRaw('rating_start, count(*)')->groupBy('rating_start')->get();
        }

        $ratings = collect([1, 2, 3, 4, 5])->map(function ($star) use ($reviews_count) {
            $count = $reviews_count->firstWhere('rating_start', $star)->count ?? 0;

            return [
                'name' => "$star star ($count)",
                'value' => $star
            ];
        });

        return [
            'title' => 'Rating',
            'query_key' => 'star',
            'data' => $ratings
        ];
    }
}
