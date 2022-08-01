<?php

namespace Webkul\Admin\Http\Controllers\Setting;

use Illuminate\Support\Facades\Log;
use Webkul\Workflow\Helpers\Entity;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\EmailTemplate\Repositories\EmailTemplateRepository;

class EmailTemplateController extends Controller
{
    /**
     * EmailTemplateRepository object
     *
     * @var \Webkul\EmailTemplate\Repositories\EmailTemplateRepository
     */
    protected $emailTemplateRepository;

    /**
     * Entity object
     *
     * @var \Workflow\Workflow\Repositories\Entity
     */
    protected $workflowEntityHelper;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\EmailTemplate\Repositories\EmailTemplateRepository  $emailTemplateRepository
     * @param  \Workflow\Workflow\Repositories\Entity  $workflowEntityHelper
     * @return void
     */
    public function __construct(
        EmailTemplateRepository $emailTemplateRepository,
        Entity $workflowEntityHelper
    )
    {
        $this->emailTemplateRepository = $emailTemplateRepository;

        $this->workflowEntityHelper = $workflowEntityHelper;
    }

    /**
     * Display a listing of the email template.
     **************************** 不用 *************************
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Webkul\Admin\DataGrids\Setting\EmailTemplateDataGrid::class)->toJson();
        }

        return view('admin::settings.email-templates.index');
    }

    /**
     * Show the form for creating a new resource.
     **************************** 不用 *************************
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $placeholders = $this->workflowEntityHelper->getEmailTemplatePlaceholders();

        return view('admin::settings.email-templates.create', compact('placeholders'));
    }

    /**
     * Store a newly created email templates in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        Log::info(request());
        $this->validate(request(), [
            'name'    => 'required',
            'subject' => 'required',
            'content' => 'required',
        ]);

        Event::dispatch('settings.email_templates.create.before');

        $emailTemplate = $this->emailTemplateRepository->create(request()->all());

        Event::dispatch('settings.email_templates.create.after', $emailTemplate);

        session()->flash('success', trans('admin::app.settings.email-templates.create-success'));

        // return redirect()->route('admin.settings.email_templates.index');
        return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.email-templates.create-success'));
    }

    /**
     * Show the form for editing the specified email template.
     **************************** 不用 *************************
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $emailTemplate = $this->emailTemplateRepository->findOrFail($id);

        $placeholders = $this->workflowEntityHelper->getEmailTemplatePlaceholders();

        return view('admin::settings.email-templates.edit', compact('emailTemplate', 'placeholders'));
    }

    /**
     * Update the specified email template in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        Log::info(request());
        $this->validate(request(), [
            'name'    => 'required',
            'subject' => 'required',
            'content' => 'required',
        ]);

        Event::dispatch('settings.email_templates.update.before', $id);

        $emailTemplate = $this->emailTemplateRepository->update(request()->all(), $id);

        Event::dispatch('settings.email_templates.update.after', $emailTemplate);

        session()->flash('success', trans('admin::app.settings.email-templates.update-success'));

        // return redirect()->route('admin.settings.email_templates.index');
        return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.email-templates.create-success')); 
    }

    /**
     * Remove the specified email template from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::info(request());
        $emailTemplate = $this->emailTemplateRepository->findOrFail($id);

        try {
            Event::dispatch('settings.email_templates.delete.before', $id);

            $this->emailTemplateRepository->delete($id);

            Event::dispatch('settings.email_templates.delete.after', $id);

            // return response()->json([
            //     'message' => trans('admin::app.settings.email-templates.delete-success'),
            // ], 200);

            return $this->ReturnJsonSuccessMsg(trans('admin::app.settings.email-templates.delete-success')); 

        } catch(\Exception $exception) {
            // return response()->json([
            //     'message' => trans('admin::app.settings.email-templates.delete-failed'),
            // ], 400);
            return $this->ReturnJsonFailMsg(trans('admin::app.settings.email-templates.delete-failed')); 
        }

        // return response()->json([
        //     'message' => trans('admin::app.settings.email-templates.delete-failed'),
        // ], 400);
        return $this->ReturnJsonFailMsg(trans('admin::app.settings.email-templates.delete-failed')); 

        
    }
}
