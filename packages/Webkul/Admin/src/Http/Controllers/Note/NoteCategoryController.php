<?php

namespace Webkul\Admin\Http\Controllers\Note;

use Webkul\Admin\Http\Controllers\Controller;
use App\Models\NoteCategories;

use Illuminate\Http\Request;

class NoteCategoryController extends Controller
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
    public function index()
    {
        $data =  NoteCategories::select('id', 'name', 'color')->get();
        return $this->ReturnJsonSuccessMsg($data);
    }

    public function indexById($id)
    {

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
            'name'   => 'required',
            'color'  => 'required',
        ]);

        $income_data = [
            'name' => request('name'),
            'color' => request('color'),
        ];

        $noteCategories = NoteCategories::create($income_data);

        $noteCategories->save();
        return $this->ReturnJsonSuccessMsg($noteCategories->id);
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
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
