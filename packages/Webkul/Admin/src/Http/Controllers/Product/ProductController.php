<?php

namespace Webkul\Admin\Http\Controllers\Product;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Attribute\Http\Requests\AttributeForm;
use Webkul\Product\Repositories\ProductRepository;
class ProductController extends Controller
{
    /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Product\Repositories\ProductRepository  $productRepository
     *
     * @return void
     */

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        // $this->ResponseJsonController = $ResponseJsonController;
        request()->request->add(['entity_type' => 'products']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->ReturnJsonSuccessMsg($this->productRepository->all());

        // if (request()->ajax()) {
        //     return app(\Webkul\Admin\DataGrids\Product\ProductDataGrid::class)->toJson();
        // }

        // return view('admin::products.index');
    }

    public function indexById($id)
    {
        return $this->ReturnJsonSuccessMsg($this->productRepository->findOrFail($id));

    }






    /**
     * Show the form for creating a new resource.
     *
     *************************** 不用 **********************************
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(AttributeForm $request)
    {
        Event::dispatch('product.create.before');

        $product = $this->productRepository->create(request()->all());

        Event::dispatch('product.create.after', $product);

        session()->flash('success', trans('admin::app.products.create-success'));

        // return redirect()->route('admin.products.index');
        return response()->json([
            'status' => "OK",
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * *************************** 不用 **********************************
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $product = $this->productRepository->findOrFail($id);

        return view('admin::products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Webkul\Attribute\Http\Requests\AttributeForm $request
     * @param int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeForm $request, $id)
    {
        Event::dispatch('product.update.before', $id);

        $product = $this->productRepository->update(request()->all(), $id);

        Event::dispatch('product.update.after', $product);

        session()->flash('success', trans('admin::app.products.update-success'));

        // return redirect()->route('admin.products.index');
        return $this->ReturnJsonSuccessMsg('OK');
    }

    /**
     * Search product results
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {

        // $this->validate(request(), [
        //     'query' => 'required'
        // ]);
        Log::info("test");
        $results = $this->productRepository->findWhere([
            ['name', 'like', '%' . urldecode(request()->input('query')) . '%']
        ]);
        return $this->ReturnJsonSuccessMsg($results);
        // return response()->json($results);
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

            // return response()->json([
            //     'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.products.product')]),
            // ], 200);
        } catch(\Exception $exception) {

            return $this->ReturnJsonFailMsg([
                'message' => trans('admin::app.response.destroy-failed', ['name' => trans('admin::app.products.product')]),
            ], 400);

            // return response()->json([
            //     'message' => trans('admin::app.response.destroy-failed', ['name' => trans('admin::app.products.product')]),
            // ], 400);
        }
    }

    /**
     * Mass Delete the specified resources.
     *
     * 
     * 在postman當中 傳 'rows':["2","3"]，表示能夠集體刪除
     * 
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        Log::info(request());
        Log::info(request('rows'));
        foreach (request('rows') as $productId) {
            Event::dispatch('product.delete.before', $productId);
            $this->productRepository->delete($productId);
            Event::dispatch('product.delete.after', $productId);
        }
        return $this->ReturnJsonSuccessMsg([
            'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.products.title')]),
        ]);
        // return response()->json([
        //     'message' => trans('admin::app.response.destroy-success', ['name' => trans('admin::app.products.title')]),
        // ]);
    }
}
