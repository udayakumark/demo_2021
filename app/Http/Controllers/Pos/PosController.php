<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\ContactusRequests;
use App\Models\RiceBenefits;
use App\Models\Gallery;
use App\Models\Products;
use App\Models\ProductPrices;
use App\Models\CartProducts;
use App\Models\HomeBanner;
use App\Models\Testimonials;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PosController extends Controller
{
    public function Index()
    {
        return view('pos.dashboard');
    }

    public function bill_add(Request $request){

        dd($request);

    }
}
