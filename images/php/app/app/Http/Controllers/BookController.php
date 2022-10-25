<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Jobs\S3UploadJob;
use App\Models\Book;
use App\Models\Media;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BookResource::collection(Book::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'series_id' => 'required|integer',
            'isbn_10' => 'string',
            'isbn_13' => 'string',
            'cover' => 'file'
        ]);
        $book = new Book();
        $book->fill($request->all());
        $book->save();

        if($request->file('cover')){
            $local_storage = Storage::disk('local');
            $path = $local_storage->put('temp/'.$book->id, $request->file('cover'));
            dispatch(new S3UploadJob($path, $book->id));
        }
        
        return BookResource::make($book);
    }

    /**
     * Search for the resource.
     *
     * @param  string  $query
     * @return \Illuminate\Http\Response
     */
    public function search($query)
    {
        $books = Book::where('title', 'Like', '%'.$query.'%')->get();
        return BookResource::collection($books);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return BookResource::make(Book::find($id));
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $isbn
     * @return \Illuminate\Http\Response
     */
    public function isbn($isbn)
    {
        return BookResource::make(Book::where('isbn_10', $isbn)->orWhere('isbn_13', $isbn)->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'string',
            'series_id' => 'integer',
            'isbn_10' => 'string',
            'isbn_13' => 'string',
            'cover' => 'file'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
