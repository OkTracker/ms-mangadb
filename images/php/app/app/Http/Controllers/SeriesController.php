<?php

namespace App\Http\Controllers;

use App\Http\Resources\SeriesResource;
use App\Models\Series;
use App\Models\Title;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SeriesResource::collection(Series::all());
    }

    /**
     * Search for the resource.
     *
     * @param  string  $query
     * @return \Illuminate\Http\Response
     */
    public function search($query)
    {
        $series = Title::where('title', 'Like', '%' . $query . '%')->with('series')->get()->pluck('series')->unique();
        return SeriesResource::collection($series);
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
            'titles' => 'required|array',
            'languages' => 'required|array',
            'authors' => 'required|string',
            'cover' => 'file'
        ]);

        $series = new Series();
        $series->fill(['authors' => $request->authors]);
        $series->save();

        foreach($request->titles as $key => $val){
            $title = new Title([
                "title" => $val,
                "language_iso_639_1" => $request->languages[$key],
            ]);
            $series->titles()->save($title);
        }

        if ($request->file('cover')) {
            $media = MediaController::uploadToS3($request->file('cover'), $series);
        }

        return SeriesResource::make($series);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return SeriesResource::make(Series::find($id));
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
            'titles' => 'array',
            'languages' => 'array',
            'authors' => 'string',
            'cover' => 'file'
        ]);

        $series = Series::find($id);
        $series->fill(['authors' => $request->authors]);
        $series->save();

        if($request->titles){
            $series->titles()->delete();
            foreach ($request->titles as $key => $val) {
                $title = new Title([
                    "title" => $val,
                    "language_iso_639_1" => $request->languages[$key],
                ]);
                $series->titles()->save($title);
            }
        }

        if ($request->file('cover')) {
            if($series->cover) $series->cover->delete();
            $media = MediaController::uploadToS3($request->file('cover'), $series);
        }

        return SeriesResource::make($series->refresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $series = Series::find($id);
        $series->titles()->delete();
        if ($series->cover) $series->cover->delete();
        $series->delete();
        return $series;
    }
}
