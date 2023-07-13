<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\JsonApiResponse;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('user')->where('user_id', Auth::id())->paginate(10);
        return JsonApiResponse::success($books);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'isbn' => 'required|unique:books,isbn',
            'title' => 'required',
            'subtitle' => 'required',
            'author' => 'required',
            'published' => 'required|date',
            'publisher' => 'required',
            'pages' => 'required|integer',
            'description' => 'required',
            'website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return JsonApiResponse::error($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $book = new Book([
            'user_id' => Auth::id(),
            'isbn' => $request->isbn,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'author' => $request->author,
            'published' => $request->published,
            'publisher' => $request->publisher,
            'pages' => $request->pages,
            'description' => $request->description,
            'website' => $request->website,
        ]);
        $book->save();

        return JsonApiResponse::success($book, Response::HTTP_CREATED);
    }

    public function show(string $id)
    {
        $book = Book::with('user')->where('user_id', Auth::id())->find($id);

        if (!$book) {
            return JsonApiResponse::error("book not found", Response::HTTP_NOT_FOUND);
        }

        return JsonApiResponse::success($book);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'subtitle' => 'required',
            'author' => 'required',
            'published' => 'required|date',
            'publisher' => 'required',
            'pages' => 'required|integer',
            'description' => 'required',
            'website' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return JsonApiResponse::error($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $book = Book::where('user_id', Auth::id())->find($id);

        if (!$book) {
            return JsonApiResponse::error("book not found", Response::HTTP_NOT_FOUND);
        }

        $book->user_id = Auth::id();
        $book->isbn = $request->isbn;
        $book->title = $request->title;
        $book->subtitle = $request->subtitle;
        $book->author = $request->author;
        $book->published = $request->published;
        $book->publisher = $request->publisher;
        $book->pages = $request->pages;
        $book->description = $request->description;
        $book->website = $request->website;
        $book->save();

        return JsonApiResponse::success($book, "Book Update Successfully");
    }

    public function destroy(string $id)
    {
        $book = Book::where('user_id', Auth::id())->find($id);

        if (!$book) {
            return JsonApiResponse::error("book not found", Response::HTTP_NOT_FOUND);
        }

        $book->delete();

        return JsonApiResponse::success($book, "Book Delete Successfully");
    }
}
