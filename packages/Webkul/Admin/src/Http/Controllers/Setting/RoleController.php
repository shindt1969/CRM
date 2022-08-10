<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Webkul\User\Repositories\RoleRepository;
use Webkul\Admin\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class RoleController extends Controller
{
    /**
     * Role repository instance.
     *
     * @var \Webkul\User\Repositories\RoleRepository
     */
    protected $roleRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\User\Repositories\RoleRepository  $roleRepository
     * @return void
     */
    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     ************************** 不用 *************************
     * @return \Illuminate\View\View
     */
    public function index()
    {
        
        // return response()->json(auth()->user());
        return $this->ReturnJsonSuccessMsg($this->roleRepository->all());

    }

    public function indexById($id)
    {
        return $this->ReturnJsonSuccessMsg($this->roleRepository->findOrFail($id));
    }

    /**
     * Show the form for creating a new resource.
     ************************** 不用 *************************
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $acl = app('acl');

        return view('admin::settings.roles.create', compact('acl'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name'            => 'required',
            'permission_type' => 'required',
        ]);

        Event::dispatch('settings.role.create.before');

        $roleData = request()->all();

        if ($roleData['permission_type'] == 'custom') {
            if (! isset($roleData['permissions'])) {
                $roleData['permissions'] = [];
            }
        }

        $role = $this->roleRepository->create($roleData);

        Event::dispatch('settings.role.create.after', $role);

        // session()->flash('success', trans('admin::app.settings.roles.create-success'));

        // return redirect()->route('admin.settings.roles.index');
        return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.roles.create-success'));
    }

    /**
     * Show the form for editing the specified resource.
     ************************** 不用 *************************
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $role = $this->roleRepository->findOrFail($id);

        $acl = app('acl');

        return view('admin::settings.roles.edit', compact('role', 'acl'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'name'            => 'required',
            'permission_type' => 'required',
        ]);


        Event::dispatch('settings.role.update.before', $id);

        $roleData = request()->all();

        if ($roleData['permission_type'] == 'custom') {
            if (! isset($roleData['permissions'])) {
                $roleData['permissions'] = [];
            }
        }
        $role = $this->roleRepository->update($roleData, $id);
        Event::dispatch('settings.role.update.after', $role);

        // session()->flash('success', trans('admin::app.settings.roles.update-success'));

        // return redirect()->route('admin.settings.roles.index');
        return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.roles.update-success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = [
            'responseCode' => 400,
        ];

        $role = $this->roleRepository->findOrFail($id);

        if ($role->admins && $role->admins->count() >= 1) {
            $response['message'] = trans('admin::app.settings.roles.being-used');

            // session()->flash('error', $response['message']);
        } else if ($this->roleRepository->count() == 1) {
            $response['message'] = trans('admin::app.settings.roles.last-delete-error');

            // session()->flash('error', $response['message']);
        } else {
            try {
                Event::dispatch('settings.role.delete.before', $id);

                if (auth()->guard('user')->user()->role_id == $id) {
                    $response['message'] = trans('admin::app.settings.roles.current-role-delete-error');
                } else {
                    $this->roleRepository->delete($id);

                    Event::dispatch('settings.role.delete.after', $id);

                    $message = trans('admin::app.settings.roles.delete-success');

                    $response = [
                        'responseCode' => 200,
                        'message'      => $message,
                    ];

                    // session()->flash('success', $message);
                }
            } catch (\Exception $exception) {
                $message = $exception->getMessage();

                $response['message'] = $message;

                // session()->flash('error', $message);
            }
        }

        // return response()->json($response, $response['responseCode']);
        return $this->ReturnJsonSuccessMsg($response);
    }
}
