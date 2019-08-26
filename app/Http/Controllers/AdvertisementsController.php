<?php

namespace App\Http\Controllers;

use App\Advertisement;
use App\Event;
use App\Http\Resources\AdvertisementResource;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Class AdvertisementsController
 * @package App\Http\Controllers
 */
class AdvertisementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Event $event
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event) {
        $advertisements = $event->advertisements()->paginate();

        return view("admin.events.advertisements.index",
            compact('advertisements', 'event'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Event $event
     * @return \Illuminate\Http\Response
     */
    public function create(Event $event) {

        return view("admin.events.advertisements.create",
            compact('event'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Event               $event
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Event $event) {

        $validatedData = $this->validate($request, Advertisement::StoreRules());

        DB::beginTransaction();

        try {

            /** @var \App\Advertisement $ad */
            $ad = $event->advertisements()->create($validatedData);

            if ($request->hasFile('logo')) {
                $ad->addMediaFromRequest('logo')->toMediaCollection('logo');
            }

            $this->addMediaToCollection($ad, 'banners', $request->banners);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


        return redirect()->route('events.advertisements.index', $event);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Event         $event
     * @param \App\Advertisement $advertisement
     * @return void
     */
    public function show(Event $event, Advertisement $advertisement) {

        if (!$advertisement->event->is($event)) {
            abort(403);
        }

        return view("admin.events.advertisements.show",
            compact('advertisement', 'event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Event         $event
     * @param \App\Advertisement $advertisement
     * @return void
     */
    public function edit(
        Event $event, Advertisement $advertisement
    ) {
        if (!$advertisement->event->is($event)) {
            abort(403);
        }

        return view("admin.events.advertisements.edit",
            compact('advertisement', 'event'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Event               $event
     * @param \App\Advertisement       $advertisement
     * @return void
     */
    public function update(
        Request $request, Event $event, Advertisement $advertisement
    ) {
        if (!$advertisement->event->is($event)) {
            abort(403);
        }
        $validatedData = $this->validate($request, Advertisement::StoreRules());

        DB::beginTransaction();

        try {

            /** @var \App\Advertisement $ad */
            $advertisement->update($validatedData);

            if ($request->hasFile('logo')) {
                $advertisement->addMediaFromRequest('logo')
                              ->toMediaCollection('logo');
            }

            $this->addMediaToCollection($advertisement, 'banners',
                $request->banners);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


        return redirect()->route('events.advertisements.index', $event)
                         ->withStatus('Advertisement updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Event         $event
     * @param \App\Advertisement $advertisement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event, Advertisement $advertisement) {
        if (!$advertisement->event->is($event)) {
            abort(403);
        }

        $advertisement->delete();

        return redirect()->route("events.advertisements.index",
            compact('event'));
    }

    /**
     * @param \App\Advertisement $ad
     * @param string             $collection
     * @param array              $files
     */
    private function addMediaToCollection(
        Advertisement $ad, string $collection, array $files = null
    ): void {
        if (!is_null($files)) {
            collect($files)
                ->filter(null)
                ->map(function (UploadedFile $file) {
                    return $file->move(storage_path('app'),
                        $file->getClientOriginalName());
                })->each(function (string $path) use ($ad, $collection) {
                    $ad->addMedia($path)->toMediaCollection($collection);
                });
        }
    }

    public function deleteImage(
        Event $event, Advertisement $advertisement, int $image_id
    ) {
        if (!$advertisement->event->is($event)) {
            abort(403);
        }
        if ($item = $advertisement->media()->find($image_id)) {
            $item->delete();
        }

        return request()->ajax() ? response()->json(['status' => 'completed']) :
            redirect()->back()->withStatus('Image deleted!');
    }

    public function apiAdvertisements(Event $event) {
        return AdvertisementResource::collection($event->advertisements);
    }
}
