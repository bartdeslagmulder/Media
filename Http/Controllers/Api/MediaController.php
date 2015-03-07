<?php namespace Modules\Media\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Modules\Media\Http\Requests\UploadMediaRequest;
use Modules\Media\Image\Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;

class MediaController extends Controller
{
    /**
     * @var FileService
     */
    private $fileService;
    /**
     * @var FileRepository
     */
    private $file;
    /**
     * @var Imagy
     */
    private $imagy;

    public function __construct(FileService $fileService, FileRepository $file, Imagy $imagy)
    {
        $this->fileService = $fileService;
        $this->file = $file;
        $this->imagy = $imagy;
    }

    /**
     * Store a newly created resource in storage.
     * @param  UploadMediaRequest $request
     * @return Response
     */
    public function store(UploadMediaRequest $request)
    {
        $savedFile = $this->fileService->store($request->file('file'));

        if (is_string($savedFile)) {
            return Response::json(['error' => $savedFile], 409);
        }

        return Response::json($savedFile->toArray());
    }

    /**
     * Link the given entity with a media file
     * @param Request $request
     */
    public function linkMedia(Request $request)
    {
        $mediaId = $request->get('mediaId');
        $entityClass = $request->get('entityClass');
        $entityId = $request->get('entityId');

        $entity = $entityClass::find($entityId);
        $zone = $request->get('zone');
        $entity->files()->attach($mediaId, ['imageable_type' => $entityClass, 'zone' => $zone]);
        $imageable = DB::table('media__imageables')->whereFileId($mediaId)->whereZone($zone)->whereImageableType($entityClass)->first();
        $file = $this->file->find($imageable->file_id);

        $thumbnailPath = $this->imagy->getThumbnail($file->path, 'smallThumb');

        return Response::json(['error' => false, 'message' => 'The link has been added.', 'result' => ['path' => $thumbnailPath, 'imageableId' => $imageable->id]]);
    }

    /**
     * Remove the record in the media__imageables table for the given id
     * @param Request $request
     */
    public function unlinkMedia(Request $request)
    {
        $imageableId = $request->get('imageableId');
        $deleted = DB::table('media__imageables')->whereId($imageableId)->delete();
        if (! $deleted) {
            return Response::json(['error' => true, 'message' => 'The file was not found.']);
        }

        return Response::json(['error' => false, 'message' => 'The link has been removed.']);
    }
}
