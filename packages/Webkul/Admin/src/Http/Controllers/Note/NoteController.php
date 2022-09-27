<?php

namespace Webkul\Admin\Http\Controllers\Note;

use App\Models\Note;
use App\Models\Content_type;
use Illuminate\Http\Request;
use Webkul\User\Models\User;
use Illuminate\Support\Carbon;
use Webkul\Contact\Models\Person;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webkul\Contact\Models\Organization;
use Webkul\Admin\Http\Controllers\Controller;


class NoteController extends Controller
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

        $columns = Note::select('text', 'target_id', 'target_type_id', 'create_by_id', 'created_at');

        if ($page == 0) {
            $data = $columns->orderBy('created_at')->get();
        } else {
            $data = $columns
                ->offset(($page - 1) * $show_recoreds_number)
                ->limit($show_recoreds_number)
                ->orderBy('created_at')->get();
        }

        foreach ($data as $record) {
            $user = User::find($record->create_by_id);

            // 客戶記事
            if ($record->target_type_id == "1") {
                $table = "persons";
                $name = Person::find($record->target_id)->first()->name;
            }
            // 公司記事
            if ($record->target_type_id == "2") {
                $table = "organizations";
                $name = Organization::find($record->target_id)->first()->name;
            }
            // 個人記事
            if ($record->target_type_id == "3") {
                $table = "users";
                $name = $user->name;
            }

            $created_at = (new Carbon($record->created_at))->format('Y/m/d h:m');

            $return_data[] = array(
                'content_type' => $table,
                'target_name' => $name,
                'text' => $record->text,
                'create_by_name' => $user->name,
                'created_at' => $created_at
            );
        }

        return $this->ReturnJsonSuccessMsg($return_data);
    }

    public function indexById($id)
    {
        $record = Note::find($id);
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
            'text'            => 'required',
            'target_id'       => 'required|integer',
            'target_type_id'  => 'required|integer|in:1,2,3',
            'create_by_id'    => 'required|integer|exists:users,id',
        ]);

        $income_data = request();

        $content = Note::create([
            'text' => $income_data['text'],
            'target_id' => $income_data['target_id'],
            'target_type_id' => $income_data['target_type_id'],
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
            'text'            => 'required',
            'target_id'       => 'required|integer',
            'target_type_id'  => 'required|integer|in:1,2,3',
            'create_by_id'    => 'required|integer|exists:users,id',
        ]);

        $income_data = request();

        Note::where('id', $id)
            ->update([
                'text' => $income_data['text'],
                'target_id' => $income_data['owner_id'],
                'target_type_id' => $income_data['type_id'],
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
        // 刪除，用一個 flag 
        $deleted = Note::where('id', $id)->delete();

        if ($deleted) {
            return $this->ReturnJsonSuccessMsg($id);
        } else {
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
