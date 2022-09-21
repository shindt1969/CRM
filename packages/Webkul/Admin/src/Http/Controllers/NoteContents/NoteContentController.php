<?php

namespace Webkul\Admin\Http\Controllers\NoteContents;

use App\Models\NoteContent;
use App\Models\Content_type;
use Illuminate\Http\Request;
use Webkul\User\Models\User;
use Webkul\Contact\Models\Person;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webkul\Contact\Models\Organization;
use Webkul\Admin\Http\Controllers\Controller;


class NoteContentController extends Controller
{

    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index($page = 0)
    {
        $return_data = [];
        $show_recoreds_number = 10;

        if ($page == 0) {
            $data = NoteContent::select('text', 'owner_id', 'type_id', 'create_by_id', 'created_at')
                ->orderBy('created_at')->get();
        } else {
            $data = NoteContent::select('text', 'owner_id', 'type_id', 'create_by_id', 'created_at')
                ->offset(($page - 1) * $show_recoreds_number)
                ->limit($show_recoreds_number)->orderBy('created_at')->get();
        }

        foreach ($data as $record) {
            $user = User::find($record->create_by_id);
            Log::info($user);

            // 客戶記事
            if ($record->type_id == "1") {
                $table = "persons";
                $name = Person::find($record->create_by_id)->first()->name;
            }
            // 公司記事
            if ($record->type_id == "2") {
                $table = "organizations";
                $name = Organization::find($record->create_by_id)->first()->name;
            }

            // 個人記事
            if ($record->type_id == "3") {
                $table = "users";
                $name = $user->name;
            }

            $return_data[] = array(
                'content_type' => $table,
                'name' => $name,
                'text' => $record->text,
                'create_by_id' => $user->name,
                'created_at' => $record->created_at
            );
        }

        return $this->ReturnJsonSuccessMsg($return_data);
    }

    public function indexById($id)
    {
        $record = NoteContent::find($id);
        return $this->ReturnJsonSuccessMsg($record);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

        $this->validate(request(), [
            'text'           => 'required',
            'owner_id'       => 'required|integer',
            'type_id'        => 'required|integer|exists:content_types,id',
            'create_by_id'   => 'required|integer|exists:users,id',
        ]);

        $income_data = request();

        $content = NoteContent::create([
            'text' => $income_data['text'],
            'owner_id' => $income_data['owner_id'],
            'type_id' => $income_data['type_id'],
            'create_by_id' => $income_data['create_by_id'],
        ]);

        $content->save();
        return $this->ReturnJsonSuccessMsg($content->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'text'           => 'required',
            'owner_id'       => 'required|integer',
            'type_id'        => 'required|integer|exists:content_types,id',
            'create_by_id'   => 'required|integer|exists:users,id',
        ]);

        $income_data = request();

        NoteContent::where('id', $id)
        ->update([
            'text' => $income_data['text'],
            'owner_id' => $income_data['owner_id'],
            'type_id' => $income_data['type_id'],
            'create_by_id' => $income_data['create_by_id'],

        ]);

        return $this->ReturnJsonSuccessMsg($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = NoteContent::where('id', $id)->delete();

        if ($deleted){
            return $this->ReturnJsonSuccessMsg($id);
        }else{
            return $this->ReturnJsonFailMsg($id);
        }
    }

    /**
     * Search product results
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
        $this->validate(request(), [
            'query' => 'required',
        ]);

        $results = $this->productRepository->search([
            ['name', 'like', '%' . urldecode(request()->input('query')) . '%']
        ]);
        return $this->ReturnJsonSuccessMsg($results);
    }

    /**
     * Mass Delete the specified resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        foreach (request('rows') as $productId) {
            Event::dispatch('product.delete.before', $productId);
            $this->productRepository->delete($productId);
            Event::dispatch('product.delete.after', $productId);
        }
        return $this->ReturnJsonSuccessMsg([
            'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.products.title')]),
        ]);
    }
}
