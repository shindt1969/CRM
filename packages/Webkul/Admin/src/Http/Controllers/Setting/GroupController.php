<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Event;

use Webkul\User\Repositories\GroupRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    /**
     * GroupRepository object
     *
     * @var \Webkul\User\Repositories\GroupRepository
     */
    protected $groupRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\User\Repositories\GroupRepository  $groupRepository
     * @return void
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * Display a listing of the resource.
     ************************** 不用 *************************
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // if (request()->ajax()) {
        //     return app(\Webkul\Admin\DataGrids\Setting\GroupDataGrid::class)->toJson();
        // }
        return $this->ReturnJsonSuccessMsg($this->groupRepository->all());
        // return view('admin::settings.groups.index');
    }


    public function indexById($id)
    {
        return $this->ReturnJsonSuccessMsg($this->groupRepository->findOrFail($id));

    }

    /**
     * Show the form for creating a new resource.
     ************************** 不用 *************************
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::settings.groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

        $data = request()->all();
        Log::info(json_encode( $data));

        $this->validate(request(), [
            'name' => 'required|unique:groups,name',
        ]);

        Event::dispatch('settings.group.create.before');

        $group = $this->groupRepository->create(request()->all());

        Event::dispatch('settings.group.create.after', $group);

        session()->flash('success', trans('admin::app.settings.groups.create-success'));

        // return redirect()->route('admin.settings.groups.index');
        return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.groups.create-success'));
    }

    /**
     * Show the form for editing the specified resource.
     ************************** 不用 *************************
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $group = $this->groupRepository->findOrFail($id);

        return view('admin::settings.groups.edit', compact('group'));
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
        Log::info(json_encode( $data));
        $this->validate(request(), [
            'name' => 'required|unique:groups,name,' . $id,
        ]);

        Event::dispatch('settings.group.update.before', $id);

        $group = $this->groupRepository->update(request()->all(), $id);

        Event::dispatch('settings.group.update.after', $group);

        session()->flash('success', trans('admin::app.settings.groups.update-success'));

        // return redirect()->route('admin.settings.groups.index');
        return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.groups.update-success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group = $this->groupRepository->findOrFail($id);

        $data = request()->all();
        Log::info(json_encode( $data));

        try {
            Event::dispatch('settings.group.delete.before', $id);

            $this->groupRepository->delete($id);

            Event::dispatch('settings.group.delete.after', $id);

            // return response()->json([
            //     'message' => trans('admin::app.settings.groups.destroy-success'),
            // ], 200);
            return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.groups.destroy-success'));
        } catch(\Exception $exception) {
            // return response()->json([
            //     'message' => trans('admin::app.settings.groups.delete-failed'),
            // ], 400);
            return $this->ReturnJsonFailMsg(trans('admin::app.settings.groups.delete-failed'));
        }

        // return response()->json([
        //     'message' => trans('admin::app.settings.groups.delete-failed'),
        // ], 400);
        return $this->ReturnJsonFailMsg(trans('admin::app.settings.groups.delete-failed'));
    }
}
