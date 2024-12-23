<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\Models\Product;
use App\Services\HomeService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    private HomeService $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }
    
    public function index(Request $request)
    {
        $show_thankyou_modal = false;
        if ($request->input('message') == 'thankyou') {
            $show_thankyou_modal = true;
        }
        return Inertia::render('Welcome', [
            'show_thankyou_modal' => $show_thankyou_modal,
            'products' => Product::whereStatus(1)->get(),
        ]);

    }

    public function contact()
    {
        return Inertia::render('Contact');
    }

    public function storeInquiry(StoreInquiryRequest $storeInquiryRequest)
    {
        $this->homeService->storeInquiry($storeInquiryRequest->all()); 
    }

}
