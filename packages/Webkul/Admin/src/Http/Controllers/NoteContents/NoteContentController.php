<?php

namespace Webkul\Admin\Http\Controllers\NoteContents;

use App\Models\Content;
use App\Models\Content_type;
use Illuminate\Http\Request;
use Webkul\User\Models\User;
use Webkul\Contact\Models\Person;
use Illuminate\Support\Facades\DB;
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
    public function index($start=1, $limit=1)
    {
        $return_data = [];
        // $data = DB::select(
        //     "SELECT con.text, con.owner_id, con.type_id, con.create_by_id, con.created_at from contents con order by created_at limit {$start}, {$limit};", [1]
        // );
        
        $data = Content::select('text', 'owner_id', 'type_id', 'create_by_id', 'created_at')
                    ->offset($start)
                    ->limit($limit)->orderBy('created_at')->get(); 

        foreach ($data as $record){
            $user = User::where('id', $record->create_by_id )->first();

            // 客戶記事
            if($record->type_id=="1"){
                $table = "persons";
                $name = Person::where('id', $record->create_by_id )->first()->name;
            }
            // 公司記事
            if($record->type_id=="2"){
                $table = "organizations";
                $name = Organization::where('id', $record->create_by_id )->first()->name;

            }
            // 個人記事
            if($record->type_id=="3"){
                $table = "users";
                $name = $user->name;
            }

            $return_data[] = array(
                'content_type' => $table,
                'name' => $name,
                'text' => $record->text,
                'create_by' => $user->name,
                'created_at' => $record->created_at
            );
        }


        // $data = DB::select(
        //     "SELECT * from contents;", [1]
        // );

        return $this->ReturnJsonSuccessMsg($return_data);
    }

    public function indexById($id)
    {
        $aa = $this->productRepository->findOrFail($id);
        return $this->ReturnJsonSuccessMsg($this->productRepository->findOrFail($id));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $income_data = request();

        $flight = Content::create([
            'type' => $income_data['type'],
            'name' => $income_data['name'],
            'text' => $income_data['text'],
            'create_by' => $income_data['create_by'],
        ]);

        $flight->save();

        return $this->ReturnJsonSuccessMsg(trans('admin::app.NoteContents.create-success'));
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
        $product = $this->productRepository->update(request()->all(), $id);

        return $this->ReturnJsonSuccessMsg(trans('admin::app.NoteContents.update-success'));
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->productRepository->findOrFail($id);

        try {
            Event::dispatch('settings.products.delete.before', $id);

            $this->productRepository->delete($id);

            Event::dispatch('settings.products.delete.after', $id);

            return $this->ReturnJsonSuccessMsg([
                'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.products.product')]),
            ], 200);

        } catch(\Exception $exception) {

            return $this->ReturnJsonFailMsg([
                'message' => trans('admin::app.response.destroy-failed', ['name' => trans('admin::app.products.product')]),
            ], 400);
        }
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
