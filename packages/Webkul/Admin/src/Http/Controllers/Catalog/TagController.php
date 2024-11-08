<?php

namespace Webkul\Admin\Http\Controllers\Catalog;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Webkul\Category\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\DataGrids\Catalog\TagDataGrid;
use Webkul\Category\Repositories\TagRepository;

class TagController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(TagDataGrid::class)->toJson();
        }

        return view('admin::catalog.tags.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $tag = new Tag();

        return view('admin::catalog.tags.create', compact('tag'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Event::dispatch('catalog.tag.create.before');

        $data = $request->validate([
            'name' => ['required', 'max:255', Rule::unique('tag_translations', 'name')],
        ]);


        $tag = Tag::create($data);


        Event::dispatch('catalog.tag.create.after', $tag);

        session()->flash('success', trans('admin::app.catalog.tags.create-success'));

        return redirect()->route('admin.catalog.tags.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $tag = Tag::findOrFail($id);

        return view('admin::catalog.tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        Event::dispatch('catalog.tag.update.before', $id);

        $tag = Tag::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'max:255', Rule::unique('tag_translations', 'name')->ignore($tag->id, 'tag_id')],
        ]);

        $tag->update($data);

        Event::dispatch('catalog.tag.update.after', $tag);

        session()->flash('success', trans('admin::app.catalog.tags.update-success'));

        return redirect()->route('admin.catalog.tags.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        Event::dispatch('catalog.tag.delete.before');

        $tag = Tag::findOrFail($id);
        $tag->delete();

        Event::dispatch('catalog.tag.delete.after', $tag);

        return response()->json([
            'message' => trans('admin::app.catalog.tags.delete-success'),
        ]);
    }
}
