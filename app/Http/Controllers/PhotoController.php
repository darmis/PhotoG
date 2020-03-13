<?php

namespace App\Http\Controllers;

use App\Photo;
use App\Tag;
use App\Phototags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $years = range(1901,date("Y"));
        $tags = Tag::whereIn('tag', $years);

        return view('albums')->with('tags', $tags);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $files = $request->file('files');
        $tag = Tag::firstOrCreate(['tag' => $request->year]);
        if($request->specialTag != ''){
            $sTag = Tag::firstOrCreate(['tag' => $request->specialTag]);
        }

        foreach($files as $file){
            $photo = new Photo;

            $photo->path = '2019';
            $photo->year = $request->year;
            $photo->save();
            $filePath = '/photos/'.$request->year.'/';
            $fileName = $request->year.'-'.$photo->id.'.'.$file->getClientOriginalExtension();
            $photo->path = $filePath.$fileName;
            $photo->save();

            $ph = new Phototags;
            $ph->photo_id = $photo->id;
            $ph->tag_id = $tag->id;
            $ph->save();

            if($sTag){
                $sph = new Phototags;
                $sph->photo_id = $photo->id;
                $sph->tag_id = $sTag->id;
                $sph->save();
            }

            Storage::disk('public')->putFileAs($filePath, $file, $fileName);
        }

        return response()->json(array('success' => 'Successfully uploaded images!'));
    }

    /**
     * Display the specified resource.
     *

     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $tag = Tag::where('tag', $request->tag)->first();
        if(!$tag){
            $photos = false;

            return view('show')
                ->with('photos', $photos)
                ->with('tagRequested', "'".$request->tag."'");
        } else {
            $photos = true;

            return view('show')
                ->with('photos', $photos)
                ->with('tagRequested', "'".$request->tag."'");
        }

// return view('show')->with('tagRequested', "'".$request->tag."'");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function edit(Photo $photo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Photo $photo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Photo $photo)
    {
        //
    }

    public function loadPhotos(Request $request)
    {
        //get tag and page and give that page photos
        $tag = Tag::where('tag', $request->tag)->first();
        $photos = $tag->photos()->paginate(20);
        return $photos;
    }

    public function getAllPhotos(Request $request)
    {
        //get tag and give all its photos
        $tag = Tag::where('tag', $request->tag)->first();
        $photos = $tag->photos()->get();
        return $photos;
    }
}
