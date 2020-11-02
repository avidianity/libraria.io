<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Author::with('user')
            ->with('books.category')
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
        $data['user_id'] = $request->user()->id;
        return Author::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Author::with('user')
            ->with('books.category')
            ->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Author $author)
    {
        $data = $request->validate($this->rules('nullable'));
        $author->update($data);
        return $author;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\Models\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Author $author)
    {
        $user = $request->user();
        if (
            $author->user_id === $user->id
            && !$user->hasRole(User::ADMIN)
        ) {
            return new Response('', 403);
        }
        $author->delete();
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
            'website' => [$mode, 'url', 'string', 'max:255'],
            'address' => [$mode, 'string', 'max:255'],
            'email' => [$mode, 'email', 'string', 'max:255'],
        ];
    }
}
