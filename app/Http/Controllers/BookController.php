<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\File;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')
            ->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Book::with('category')
            ->with('author.user')
            ->with('photo')
            ->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $author = $request->user()
            ->authors()
            ->find($data['author_id']);
        if (!$author) {
            return new Response('', 403);
        }

        $file = File::process($data['photo']);
        $file->save();
        $data['photo_id'] = $file->id;

        return Book::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Book::with('category')
            ->with('author.user')
            ->with('photo')
            ->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $data = $request->validate($this->rules('nullable'));

        if (isset($data['author_id'])) {
            $author = $request->user()
                ->authors()
                ->find($data['author_id']);
            if (!$author) {
                return new Response('', 403);
            }
        }

        if (isset($data['photo'])) {
            $old = $book->photo;
            $new = File::process($data['photo']);
            $new->save();
            $book->photo_id = $new->id;
            $book->save();
            $old->delete();
        }

        $book->update($data);
        return $book;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Book $book)
    {
        $user = $request->user();
        if (
            $book->author->user->id !== $user->id
            && !$user->hasRole(User::ADMIN)
        ) {
            return new Response('', 404);
        }
        $book->delete();
        return new Response('', 204);
    }

    /**
     * Make rules for validation.
     * @param string $mode
     * @return array
     */
    protected function rules($mode = 'required')
    {
        return [
            'title' => [$mode, 'string', 'max:255'],
            'description' => [$mode, 'string'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['numeric', 'exists:' . Tag::class . ',id'],
            'category_id' => [$mode, 'numeric', 'exists:' . Category::class . ',id'],
            'author_id' => [$mode, 'numeric', 'exists:' . Author::class . ',id'],
            'photo' => [$mode],
        ];
    }
}
