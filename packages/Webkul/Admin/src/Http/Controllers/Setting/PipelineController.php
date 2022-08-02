<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Requests\PipelineForm;
use Webkul\Lead\Repositories\PipelineRepository;
use Illuminate\Support\Facades\Log;

class PipelineController extends Controller
{
    /**
     * PipelineRepository object
     *
     * @var \Webkul\Lead\Repositories\PipelineRepository
     */
    protected $pipelineRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Lead\Repositories\PipelineRepository  $pipelineRepository
     * @return void
     */
    public function __construct(PipelineRepository $pipelineRepository)
    {
        $this->pipelineRepository = $pipelineRepository;
    }

    /**
     * Display a listing of the resource.
     ************************* 不用 *************************
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Webkul\Admin\DataGrids\Setting\PipelineDataGrid::class)->toJson();
        }

        return view('admin::settings.pipelines.index');
    }

    /**
     * Show the form for creating a new resource.
     ************************* 不用 *************************
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::settings.pipelines.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(PipelineForm $request)
    {

        $data = request()->all();
        Log::info(json_encode($data));


        $request->validated();

        $request->merge([
            'is_default' => request()->has('is_default') ? 1 : 0,
        ]);

        Event::dispatch('settings.pipeline.create.before');

        $pipeline = $this->pipelineRepository->create($request->all());

        Event::dispatch('settings.pipeline.create.after', $pipeline);

        session()->flash('success', trans('admin::app.settings.pipelines.create-success'));

        // return redirect()->route('admin.settings.pipelines.index');
        return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.pipelines.create-success'));

    }

    /**
     * Show the form for editing the specified resource.
     ************************* 不用 *************************
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    
    {

        
        $pipeline = $this->pipelineRepository->findOrFail($id);

        return view('admin::settings.pipelines.edit', compact('pipeline'));
    }

    /**
     * Update the specified resource in storage.
     ************************* 不用 *************************
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PipelineForm $request, $id)
    {
        $data = request()->all();
        Log::info(json_encode($data));


        $request->validated();

        $request->merge([
            'is_default' => request()->has('is_default') ? 1 : 0,
        ]);

        Event::dispatch('settings.pipeline.update.before', $id);

        $pipeline = $this->pipelineRepository->update($request->all(), $id);

        Event::dispatch('settings.pipeline.update.after', $pipeline);

        session()->flash('success', trans('admin::app.settings.pipelines.update-success'));

        // return redirect()->route('admin.settings.pipelines.index');
        return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.pipelines.update-success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pipeline = $this->pipelineRepository->findOrFail($id);

        if ($pipeline->is_default) {
            // return response()->json([
            //     'message' => trans('admin::app.settings.pipelines.default-delete-error'),
            // ], 400);
            return $this->ReturnJsonFailMsg(trans('admin::app.settings.pipelines.default-delete-error'));
        } else {
            $defaultPipeline = $this->pipelineRepository->getDefaultPipeline();

            $pipeline->leads()->update([
                'lead_pipeline_id'       => $defaultPipeline->id,
                'lead_pipeline_stage_id' => $defaultPipeline->stages()->first()->id,
            ]);
        }

        try {
            Event::dispatch('settings.pipeline.delete.before', $id);

            $this->pipelineRepository->delete($id);

            Event::dispatch('settings.pipeline.delete.after', $id);

            // return response()->json([
            //     'message' => trans('admin::app.settings.pipelines.delete-success'),
            // ], 200);
            return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.pipelines.delete-success'));
        } catch(\Exception $exception) {
            // return response()->json([
            //     'message' => trans('admin::app.settings.pipelines.delete-failed'),
            // ], 400);
            return $this->ReturnJsonFailMsg(trans('admin::app.settings.pipelines.delete-failed'));
        }
        // return response()->json([
        //     'message' => trans('admin::app.settings.pipelines.delete-failed'),
        // ], 400);
        return $this->ReturnJsonFailMsg(trans('admin::app.settings.pipelines.delete-failed'));
    }
}
