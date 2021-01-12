<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// Route::get('/', function () {
//     return view('welcome');
// });

Route::view('/email','email.notifyunreadexhibitor');

// Route::group(['prefix' => 'email'], function () {
//     Route::view('/','email.confirmation');

//     $data = [
//         "email_state" => "", // request, reponse
//         "request" => [
//             "id" => "1",
//             "email" => "sermchon.y@cmo-group.com",
//             "name" => "sermchon",
//             // "type" => "exhibitor"
//             "type" => "visitor"
//         ],
//         "response" => [
//             "id" => "2",
//             "email" => "sermchon.y@cmo-group.com",
//             "name" => "yanyarat",
//             // "type" => "visitor"
//             "type" => "exhibitor"
//         ],
//         "appointment" => [
//             "date" => "2020-11-11",
//             "time" => "11:11",
//             "slot" => "1",
//             "duration_time" => "3",
//             "status" => "1"
//         ]
//     ];
//     $data["email_state"] = "request";
//     $data["appointment"]["status"] = "0";

//     $data["email_state"] = "response";
//     $data["appointment"]["status"] = "2"; // test accept

//     $data["email_state"] = "response";
//     $data["appointment"]["status"] = "2"; // test deny

//     $data["request"]["type"] = "exhbitor";
//     $data["response"]["type"] = "visitor";

//     // $data["request"]["type"] = "visitor";
//     // $data["response"]["type"] = "exhbitor";

//     $data["type"]  = $data["request"]["type"];
//     $data["email"] = $data["request"]["email"];
//     $data["name"]  = $data["request"]["name"];
//     $data["date"]  = $data["appointment"]["date"];
//     $data["time"]  = $data["appointment"]["time"];

//     Route::view('/invite/visitor-to-buyer','email.VisitorToBuyer');
//     Route::view('/req/req-a','email.ReqA', $data);
//     Route::view('/req/res-a','email.ResA', $data);
//     Route::view('/req/req-b-ok','email.ReqBaccept', $data);
//     Route::view('/req/res-b-ok','email.ResBaccept', $data);
//     Route::view('/req/req-b-no','email.ReqBdeny', $data);
//     Route::view('/req/res-b-no','email.ResBdeny', $data);
// });
// Route::get('/hash',function(){
//     return Hash::make("se4128");
// });

// Route::get('/test', function () {

//     $data = DB::table('interest_category as interest')
//     ->join('exhibitor_list as exh', 'interest.exhibitor_id', '=', 'exh.id')
//     ->join('main_category as mcate', 'interest.main_cate_id', '=', 'mcate.id')
//     ->join('countries as ctry', 'exh.country_id', '=', 'ctry.id')
//     ->select('exh.id', 'exh.company', 'exh.logo', 'exh.name', 'ctry.code as countryCode', 'ctry.name as countryName', 'mcate.name_en as mainCate')
//     ->groupBy('exh.id');
//     $data = $data->orderBy("exh.company");
//     $data = $data->orderBy("exh.name");
//     $search = '';
//     $cate   = "3,4,5,1,2";

//     if ($cate) {
//         $cate = array_map('intval', \explode(",", $cate));
//         var_dump($cate);
//         $data = $data->whereIn('mcate.id', $cate);
//     }

//     if ($search) {
//         $search = \trim($search);
//         if ($search != "") {
//             $data = $data->where(function ($query) use ($search) {
//                 $query->orWhere('exh.name', 'like', '%' . $search . '%');
//                 $query->orWhere('exh.company', 'like', '%' . $search . '%');
//             });
//         }
//     }
//     $data->paginate(15);

//     dd(DB::getQueryLog());
// });

