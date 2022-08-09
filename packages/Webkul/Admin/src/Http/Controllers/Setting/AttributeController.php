<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Core\Contracts\Validations\Code;

class AttributeController extends Controller
{
    /**
     * AttributeRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\AttributeRepository  $attributeRepository
     * @return void
     */
    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Display a listing of the resource.
     ************************** 不用 *************************
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // if (request()->ajax()) {
        //     return app(\Webkul\Admin\DataGrids\Setting\AttributeDataGrid::class)->toJson();
        // }

        // return view('admin::settings.attributes.index');

        return $this->ReturnJsonSuccessMsg($this->attributeRepository->all());

    }


    public function indexById($id)
    {
        return $this->ReturnJsonSuccessMsg($this->attributeRepository->findOrFail($id));

    }

    /**
     * Show the form for creating a new resource.
     ************************** 不用 *************************
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::settings.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $data = request()->all();
        Log::info(json_encode($data));

        $this->validate(request(), [
            'code' => ['required', 'unique:attributes,code,NULL,NULL,entity_type,' . request('entity_type'), new Code],
            'name' => 'required',
            'type' => 'required',
        ]);
        Event::dispatch('settings.attribute.create.before');

        request()->request->add(['quick_add' => 1]);

        $attribute = $this->attributeRepository->create(request()->all());

        Event::dispatch('settings.attribute.create.after', $attribute);

        session()->flash('success', trans('admin::app.settings.attributes.create-success'));

        // return redirect()->route('admin.settings.attributes.index');
        return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.attributes.create-success'));
    }

    /**
     * Show the form for editing the specified resource.
     ************************** 不用 *************************
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $attribute = $this->attributeRepository->findOrFail($id);

        return view('admin::settings.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $data = request()->all();
        Log::info(json_encode($data));


        $this->validate(request(), [
            'code' => ['required', 'unique:attributes,code,NULL,NULL,entity_type,' . $id, new Code],
            'name' => 'required',
            'type' => 'required',
        ]);

        Event::dispatch('settings.attribute.update.before', $id);

        $attribute = $this->attributeRepository->update(request()->all(), $id);

        Event::dispatch('settings.attribute.update.after', $attribute);

        session()->flash('success', trans('admin::app.settings.attributes.update-success'));

        // return redirect()->route('admin.settings.attributes.index');
        return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.attributes.update-success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attribute = $this->attributeRepository->findOrFail($id);

        if (! $attribute->is_user_defined) {
            // return response()->json([
            //     'message' => trans('admin::app.settings.attributes.user-define-error'),
            // ], 400);
            
            return $this->ReturnJsonFailMsg(trans('admin::app.settings.attributes.update-success'));
        }

        try {
            Event::dispatch('settings.attribute.delete.before', $id);

            $this->attributeRepository->delete($id);

            Event::dispatch('settings.attribute.delete.after', $id);

            // return response()->json([
            //     'status'  => true,
            //     'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.settings.attributes.attribute')]),
            // ], 200);
            return $this->ReturnJsonSuccessMsg(trans('admin::app.response.destroy-success', ['name' => trans('admin::app.settings.attributes.attribute')]));
        } catch(\Exception $exception) {
            // return response()->json([
            //     'message' => trans('admin::app.settings.attributes.delete-failed'),
            // ], 400);
            return $this->ReturnJsonFailMsg(trans('admin::app.settings.attributes.delete-failed'));
        }
    }

    /**
     * Search attribute lookup results
     *
     * @param  string  $lookup
     * @return \Illuminate\Http\Response
     */
    public function lookup($lookup=null)
    {
        $results = $this->attributeRepository->getLookUpOptions($lookup, request()->input('query'));

        // return response()->json($results);
        Log::info("------------------------------------");

        Log::info($results);
        return $this->ReturnJsonSuccessMsg($results);
    }

    /**
     * Mass Delete the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {

        $data = request()->all();
        Log::info(json_encode($data));

        $count = 0;
        
        foreach (request('rows') as $attributeId) {
            $attribute = $this->attributeRepository->find($attributeId);

            if (! $attribute->is_user_defined) {
                continue;
            }

            Event::dispatch('settings.attribute.delete.before', $attributeId);

            $this->attributeRepository->delete($attributeId);

            Event::dispatch('settings.attribute.delete.after', $attributeId);

            $count++;
        }

        if (! $count) {
            // return response()->json([
            //     'message' => trans('admin::app.settings.attributes.mass-delete-failed'),
            // ], 400);
            return $this->ReturnJsonFailMsg(trans('admin::app.settings.attributes.mass-delete-failed'));
        }

        // return response()->json([
        //     'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.settings.attributes.title')]),
        // ]);
        return $this->ReturnJsonSuccessMsg(trans('admin::app.response.destroy-success', ['name' => trans('admin::app.settings.attributes.title')]));
    }

    /**
     * Download image or file
     ************************** 不用 *************************
     * @return \Illuminate\Http\Response
     */
    public function download()
    {
        return Storage::download(request('path'));
    }
}
