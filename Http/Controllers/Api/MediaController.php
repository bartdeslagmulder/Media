<?php namespace Modules\Media\Http\Controllers\Api;

use Illuminate\Support\Str;
use Modules\Media\Http\Requests\UploadMediaRequest;
use Modules\Media\Repositories\FileRepository;

class MediaController
{
    /**
     * @var FileRepository
     */
    private $file;

    public function __construct(FileRepository $file)
    {
        $this->file = $file;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return \View::make('collection.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return \View::make('collection.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UploadMediaRequest $request
     * @return Response
     */
    public function store(UploadMediaRequest $request)
    {
        $file = $request->file('file');
        $fileName = Str::slug($file->getClientOriginalName());

        // Move the uploaded file to /public/assets/media/
        $file->move(public_path() . '/assets/media', $fileName);

        // Save the file info to db
        $savedFile = $this->file->createFromFile($file);

        // Return json response about those
        dd($request->file('file'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        return \View::make('collection.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        return \View::make('collection.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}