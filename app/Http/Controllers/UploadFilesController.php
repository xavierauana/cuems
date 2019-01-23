<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Requests\StoreUploadFileRequest;
use App\Http\Requests\UpdateUploadFileRequest;
use App\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadFilesController extends Controller
{
    /**
     * @var \App\UploadFile
     */
    private $model;

    private $paginateNumber = null;

    /**
     * UploadFilesControllers constructor.
     * @param \App\UploadFile $uploadFile
     */
    public function __construct(UploadFile $uploadFile) {
        $this->model = $uploadFile;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Event              $event
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Event $event) {

        $query = $this->model;

        if ($keyword = $request->query("keyword")) {
            $query = $query->where('name', 'like', "%{$keyword}%");
        }

        $uploadFiles = $query->paginate($this->paginateNumber);

        return view("admin.events.uploadFiles.index",
            compact('event', 'uploadFiles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Event $event
     * @return \Illuminate\Http\Response
     */
    public function create(Event $event) {

        return view("admin.events.uploadFiles.create", compact("event"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUploadFileRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUploadFileRequest $request, Event $event) {
        $disk = "local";
        $file = $request->file('file');
        $path = $file->storeAs(
            'events/' . $event->id . '/uploadFiles',
            $file->getClientOriginalName(), $disk
        );

        $fileName = $request->file->getClientOriginalName();

        $data = [
            'name' => $fileName,
            'path' => $path,
            'disk' => $disk
        ];

        $event->uploadFiles()->create($data);

        return redirect()->route('events.uploadFiles.index', compact("event"))
                         ->withStatus('New upload file created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event      $event
     * @param  \App\UploadFile $uploadFile
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event, UploadFile $uploadFile) {
        $path = storage_path("app/" . $uploadFile->path);


        return response()->file($path);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Event       $event
     * @param  \App\UploadFile $uploadFile
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event, UploadFile $uploadFile) {

        return view("admin.events.uploadFiles.edit",
            compact('event', 'uploadFile'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUploadFileRequest $request $request
     * @param  \App\UploadFile                            $uploadFile
     * @param  \App\Event                                 $event
     * @return \Illuminate\Http\Response
     */
    public function update(
        UpdateUploadFileRequest $request, Event $event, UploadFile $uploadFile
    ) {
        $validatedData = $request->validated();

        $uploadFile->update($validatedData);

        return redirect()->route('events.uploadFiles.index', compact('event'))
                         ->withStatus('UploadFile updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \App\UploadFile         $uploadFile
     * @param  \App\Event              $event
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(
        Request $request, Event $event, UploadFile $uploadFile
    ) {
        Storage::disk($uploadFile->disk)->delete($uploadFile->path);

        $uploadFile->delete();

        return $request->ajax() ?
            response()->json(['status' => 'completed']) :
            redirect()->back()->withStatus('UploadFile deleted!');
    }
}
