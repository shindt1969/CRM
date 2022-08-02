<?php

namespace Webkul\Admin\Http\Controllers\Admin;

use Carbon\Carbon;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Helpers\Dashboard as DashboardHelper;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Dashboard object
     *
     * @var \Webkul\Admin\Helpers\Dashboard
     */
    protected $dashboardHelper;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Admin\Helpers\DashboardHelper  $dashboardHelper
     * @return void
     */
    public function __construct(DashboardHelper $dashboardHelper)
    {
        $this->dashboardHelper = $dashboardHelper;

        $this->dashboardHelper->setCards();
        // 建構函式先setcards，

    }

    /**
     * Display a listing of the resource.
     * dashboardHelper=Dashboard ->Dashboard.php->getCards()->setCards()
     * ->dashboard_cards.php->app.php->得到字串
     * dateRange的結束時間
     * dateRange的開始時間
     * Carbon::可以擷取當前時間
     * 利用compact將資料變成   cards=>" "、startDate=>" "、endDate=>" "
     * compact('cards', 'startDate', 'endDate') cards', 'startDate', 'endDate' 
     * 的內容會丟到，index.blade->admin::app.dashboard.cards。但是在index.blade.php找不到使用方式
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cards = $this->dashboardHelper->getCards();
           // $cards是取得 登入後的dashboard的內容
        if ($dateRange = request('date-range')) {
            $dateRange = explode(",", $dateRange);  
            $endDate = $dateRange[1];               
            $startDate = $dateRange[0];             
        } else {
           
            $endDate = Carbon::now()->format('Y-m-d');
            $startDate = Carbon::now()->subMonth()->addDays(1)->format('Y-m-d');
        }
        return view('admin::dashboard.index', compact('cards', 'startDate', 'endDate'));
                         
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function template()
    {
        return view('admin::dashboard.template');
    }

    /**
     * Returns json data for dashboard card.
     */
    public function getCardData()
    {
        $cardData = $this->dashboardHelper->getFormattedCardData(request()->all());

        return response()->json($cardData);
    }

    /**
     * Returns json data for available dashboard cards.
     * 
     * dashboardHelper=Dashboard ->Dashboard.php->getCards()->setCards()
     * setCards() 裡面是 -> dashboard_cards-> admin.products.index(總共有四個view_url)
     * admin.products.index->Route::get('', 'ProductController@index')->
     * return app(\Webkul\Admin\DataGrids\Product\ProductDataGrid::class)
     * 
     * dashboardHelper->getCards() 出來的是 11張卡片 ，的 php 陣列  key => 值
     * 
     * 
     * @return \Illuminate\Http\Response
     */
    public function getCards()
    {
        $response = $this->dashboardHelper->getCards();

        $response = array_map(function ($card) {
            if ($card['view_url'] ?? false) {
                $card['view_url'] = route($card['view_url'], $card['url_params'] ?? []);
            }

            return $card;
        }, $response);

        return response()->json($response);
    }

    /**
     * Returns updated json data for available dashboard cards.
     * 
     * @return \Illuminate\Http\Response
     */
    public function updateCards()
    {

        $data = request()->all();
        Log::info(json_encode($data));
        Log::info("123");

        $requestData = request()->all();
        $cards = $this->dashboardHelper->getCards();

        foreach ($requestData['cards'] as $requestedCardData) {
            foreach ($cards as $cardIndex => $card) {
                if (isset($card['card_id'])
                    && isset($requestedCardData['card_id'])
                    && $card['card_id'] == $requestedCardData['card_id']
                ) {
                    $cards[$cardIndex]['selected'] = $requestedCardData['selected'];
                }
            }
        }

        return response()->json($cards);
    }
}