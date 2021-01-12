<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\NotifyExhibitorMessage;
use App\Model\Exhibitor;
use App\Model\Register;
use App\Model\MainCate;
use App\Model\SubCate;
use App\Model\Interest;
use App\Model\InterestCategoryByExhibitorId;
use App\Model\BrochureList;
use App\Model\BrochureBag;
use App\Model\EposterList;
use App\Model\VideoList;
use App\Model\PromotionList;

use DB;
use Hash;
use Illuminate\Http\Request;
use Mail;
use Storage;
use File;


class ExhibitorController extends Controller
{

    public function __construct()
    {
        config(['auth.defaults.guard' => 'exhibitor']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage');
        $search       = $request->input("search");
        $exhibitor    = Exhibitor::with(['category' => function ($query) {
            $query->select('id', 'name');
        }]);

        if (!empty($search)) {
            $search    = '%' . \strtolower($search) . '%';
            $exhibitor = $exhibitor->orWhere("company", 'like', $search)->orWhere("email", 'like', $search);
        }

        return $exhibitor->paginate($itemsPerPage ? $itemsPerPage : 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Exhibitor  $exhibitor
     * @return \Illuminate\Http\Response
     */
    public function show(Exhibitor $exhibitor)
    {
        return $exhibitor;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Exhibitor  $exhibitor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = Exhibitor::findOrFail($id);
        $data->fill($request->all());
        $data->saveOrFail();

        // check file
        return \response($data);
        // return $exhibitor;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Exhibitor  $exhibitor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exhibitor $exhibitor)
    {
        //
    }

    public function exhibitor_tb(Request $request)
    {


        $itemsPerPage    = $request->input('itemsPerPage');
        $sortBy          = $request->input('sortBy');
        $sortDesc        = $request->input('sortDesc');
        $search          = $request->input('search');
        $page          = $request->input("page");
        $start         = 0;
        $_itemsPerPage = 0;
        if ($itemsPerPage) {
            $_itemsPerPage = $itemsPerPage;
        }

        if ($page > 1) {
            $start = $_itemsPerPage * ($page - 1);
        }
        DB::statement(DB::raw('set @row=' . $start));
        $data            = Exhibitor::selectRaw('*, @row:=@row+1 as row_no')->with(["country"]);
        $countAll        = $data->count();


        if ($search && $search != "") {
            $data = $data->where("name", "like", "%{$search}%");
            $data = $data->orWhere("company", "like", "%{$search}%");
            $data = $data->orWhere("email", "like", "%{$search}%");
        }


        if ($sortBy) {
            if ($sortBy == "country.name") {
                $data = $data->orderBy("country", $sortDesc ? "desc" : "asc");
            } else {
                $data = $data->orderBy($sortBy, $sortDesc ? "desc" : "asc");
            }
        } else {
            $data = $data->orderBy("created_at", "desc");
        }

        $data = $data->paginate($itemsPerPage ? $itemsPerPage : 15);

        $custom = collect([
            'count_all'        => $countAll
        ]);

        return $custom->merge($data);
    }

    public function importData(Request $request)
    {
        $request->validate([
            "data" => "required",
        ]);

        $data = $request->input('data');

        DB::beginTransaction();
        try {
            if (\count($data) > 0) {

                foreach ($data as $key => $value) {
                    $exhibitor = new Exhibitor();
                    $exhibitor->fill($value);
                    $exhibitor->password = Hash::make($value['mobile']);
                    $exhibitor->save();
                }

                DB::commit();

                return \response()->json([
                    "status"  => true,
                    "message" => "",
                ]);
            } else {
                return \response()->json([
                    "status"  => false,
                    "message" => "empty data",
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function allIdExhibitor(){
        $data = DB::table('exhibitor_list')
            ->select('id')
            ->get();
        return $data;
    }

    public function sendNotifyExhibitor(Request $request)
    {
        $register_raw = Exhibitor::select(["id","email", "password", "name"])->where("id", $request->input('id'))->get();

        $data['email']          = $register_raw[0]->email;
        $data['noRead']         = $request->input('noRead');
        $data['name']           = $register_raw[0]->name;
        $id                     = $register_raw[0]->id;
        Mail::send(new NotifyExhibitorMessage($data));
        $rp["status"] = $this->updateExhibitorNotify($request->input('id'));
        return $rp;

    }

    public function updateExhibitorNotify($regis_id)
    {
        $register = Exhibitor::find($regis_id);
        $register->status_email_notify = 'sent';
        $register->status_email_notify_at = date('Y-m-d H:i:s');
        $register->save();
        return true;
    }

    public function getExhibitorListSideHall(Request $request)
    {
        $data = Exhibitor::with(["country" => function ($query) {
            $query->select("id", "name", "code");
        }, "mainCate" => function ($query) {
            $query->selectRaw("GROUP_CONCAT(name_en SEPARATOR ',') as name")->groupBy('exhibitor_id');
        }])->select([
            "company",
            "country_id",
            "id",
            "logo",
        ])->orderBy("company", "asc")
            ->get();

        return $data;
    }

    public function getExhibitorList(Request $request)
    {
        // $data = DB::table('exhibitor_list as exh')
        //     ->join('sub_category as cate', 'exh.category', '=', 'cate.id')
        //     ->select('exh.id', 'exh.name', 'exh.company', 'exh.category', 'exh.logo','cate.name as cateName')
        //     ->get();
        $data = DB::table('interest_category as interest')
            ->join('exhibitor_list as exh', 'interest.exhibitor_id', '=', 'exh.id')
            ->join('main_category as mcate', 'interest.main_cate_id', '=', 'mcate.id')
            ->join('sub_category as scate', 'interest.sub_cate_id', '=', 'scate.id')
            ->join('countries as ctry', 'exh.country_id', '=', 'ctry.id')
            ->select('exh.id', 'exh.company', 'exh.logo', 'ctry.code as countryCode', 'ctry.name as countryName', 'mcate.name_en as mainCate', DB::raw("GROUP_CONCAT(scate.name SEPARATOR ',') AS subCate "))
            ->groupBy('exh.id')
            ->get();
        // $data_rp['exhibitor'] = $data;

        return $data;
    }

    public function getExhibitorListDirectory(Request $request)
    {
        $cate = $request->input("cate");
        $country = $request->input("country");

        $data = DB::table('interest_category as interest')
            ->select('exh.id', 'exh.company', 'exh.logo', 'ctry.code as countryCode', 'ctry.name as countryName', 'mcate.name_en as mainCate', DB::raw("SUBSTRING(exh.company, 1, 1) as first_char"), DB::raw("group_concat(mcate.name_en) as group_cate"))
            ->join('exhibitor_list as exh', 'interest.exhibitor_id', '=', 'exh.id')
            ->join('main_category as mcate', 'interest.main_cate_id', '=', 'mcate.id')
            ->join('countries as ctry', 'exh.country_id', '=', 'ctry.id')
            ->where("interest.type", "=", 1);

        if ($cate && $cate != 'All') {
            $data = $data->where("interest.main_cate_id", "=", $cate);
        }

        if ($country && $country != 'All') {
            $data = $data->where("exh.country_id", "=", $country);
        }

        $data =  $data->groupBy('exh.id')
            ->orderBy('exh.company')->get();
        // $data_rp['exhibitor'] = $data;

        return $data;
    }

    public function getCategoryList(Request $request)
    {
        $data = DB::table('main_category')->get();

        return $data;
    }

    public function getCategoryListByMainCate(Request $request)
    {
        $data = SubCate::where('main_cate', '=', $request->input('main_cate'))->get();
        return $data;
    }

    public function getExhibitorbyMainCategory(Request $request)
    {


        $categoryId = $request->get('id');
        // $category_id = base64_decode($request->get('id'));
        $data = DB::table('exhibitor_list as exh')
            ->join('sub_category as cate', 'exh.category', '=', 'cate.id')
            ->join('main_category as main_cate', 'exh.main_category', '=', 'main_cate.id')
            ->select('exh.company', 'exh.id', 'exh.company', 'exh.name', 'cate.name as cateName', 'main_cate.name_en as mainCateName')
            ->where('exh.main_category', intval($categoryId))->get();
        $data_rp['exhibitor'] = $data;

        return $data_rp;
    }

    public function getSubCategory(Request $request)
    {

        $inputData      = $request->all();
        $mainCategoryId = $request->get('id');
        // $category_id = base64_decode($request->get('id'));
        $data = DB::table('sub_category as sub')
            ->join('main_category as m_cate', 'sub.main_cate', '=', 'm_cate.id')
            ->select('sub.*', 'm_cate.name_en as mainName')
            ->where('sub.main_cate', intval($mainCategoryId))->get();
        $data_rp['exhibitor'] = $data;

        return $data_rp;
    }

    public function getSubCategoryList(Request $request)
    {
        // ex 1
        // $data = SubCate::with('main_categery')->get();

        // ex 2
        $data = DB::table('sub_category as sub')
            ->join('main_category as m_cate', 'sub.main_cate', '=', 'm_cate.id')
            ->select('sub.*', 'm_cate.name_en as mainName')
            ->get();
        $data_rp['exhibitor'] = $data;
        return $data;
    }

    public function getSubCategoryBySubCategoryId(Request $request)
    {

        $inputData     = $request->all();
        $subCategoryId = $request->get('id');
        // $category_id = base64_decode($request->get('id'));
        $data = DB::table('sub_category as sub')
            ->join('main_category as m_cate', 'sub.main_cate', '=', 'm_cate.id')
            ->select('sub.*', 'm_cate.name_en as mainName')
            ->where('sub.id', intval($subCategoryId))->get();
        $data_rp['exhibitor'] = $data;

        return $data_rp;
    }

    public function getExhibitorByCategoryId(Request $request)
    {

        $inputData  = $request->all();
        $categoryId = $request->get('id');
        // $category_id = base64_decode($request->get('id'));
        $data = DB::table('exhibitor_list as exh')
            ->join('sub_category as cate', 'exh.category', '=', 'cate.id')
            ->select('exh.id', 'exh.company', 'exh.name', 'cate.name as cateName')
            ->where('category', intval($categoryId))->get();
        $data_rp['exhibitor'] = $data;

        return $data_rp;
    }

    public function getExhibitorById(Request $request)
    {
        $inputData = $request->all();
        $boothId   = $request->get('id');

        $exhibitor = Exhibitor::with(['country' => function ($query) {
            $query->select('id', 'code');
        }, 'eposterList', 'brochureList', 'promotionList', 'videoList' => function ($query) {
            $query->where("active", "=", 1);
        }])->findOrFail(intval($boothId));

        return $exhibitor;
    }



    // public function getExhibitorById(Request $request)
    // {

    //     $inputData = $request->all();
    //     $boothId   = $request->get('id');
    //     // $category_id = base64_decode($request->get('id'));
    //     $data                 = DB::table('exhibitor_list')
    //         ->select('id', 'company', 'name', 'logo')
    //         ->where('id', intval($boothId))->first();
    //     $data_rp['exhibitor'] = $data;

    //     $brochureData = DB::table('brochure_list')
    //         ->where('exhibitor_id', intval($boothId))->get();
    //     $data_rp['brochure'] = $brochureData;

    //     $posterData = DB::table('eposter_list')
    //         ->where('exhibitor_id', intval($boothId))
    //         ->where('active', 1)
    //         ->first();
    //     $data_rp['poster'] = $posterData;

    //     $promotionData = DB::table('promotion_list')
    //         ->where('exhibitor_id', intval($boothId))
    //         ->where('active', 1)
    //         ->first();
    //     $data_rp['promotion'] = $promotionData;

    //     $videoData = DB::table('video_list')
    //         ->where('exhibitor_id', intval($boothId))
    //         ->where('active', 1)
    //         ->first();
    //     $data_rp['video'] = $videoData;

    //     return $data_rp;
    // }

    public function getInterestCateByExhibitorId(Request $request)
    {

        // $inputData = $request->all();
        $exhibitor_id   = $request->get('id'); // exhibitor_id


        // $data = Interest::where("exhibitor_id", "=", $boothId)->get();

        // // // $data = Exhibitor::find($boothId)->interest_cate->all();

        // // // $data = Exhibitor::find($boothId)->interest_cate->all();

        // front-end request
        // main_catetory_id: string;
        // main_category_th: string;
        // main_category_en: string;
        // sub_category_id: string;
        // sub_category: string;
        // sub_category_th: string;
        // sub_category_en: string;
        // // // $data = DB::table('interest_category as int')
        // // // ->select(
        // // //     'int.*',
        // // //     'm_cate.name_en as main_category_en',
        // // //     'm_cate.name_th as main_category_th',
        // // //     's_cate.name as sub_category',
        // // //     's_cate.name as sub_category_en')
        // // // ->join('main_category as m_cate', 'm_cate.id', '=', 'int.main_cate_id')
        // // // ->join('sub_category as s_cate', 's_cate.id', '=', 'int.sub_cate_id')
        // // // ->where('int.exhibitor_id', intval($exhibitor_id))
        // // // ->grouBy('int.main_cate_id')->get();
        // // // $data_rp["main_cate"] = $data;

        // // // $data = DB::table('interest_category as int')
        // // //     ->select(
        // // //         'int.*',
        // // //         'm_cate.name_en as main_category_en',
        // // //         'm_cate.name_th as main_category_th',
        // // //         's_cate.name as sub_category',
        // // //         's_cate.name as sub_category_en')
        // // //     ->join('main_category as m_cate', 'm_cate.id', '=', 'int.main_cate_id')
        // // //     ->join('sub_category as s_cate', 's_cate.id', '=', 'int.sub_cate_id')
        // // //     ->where('int.exhibitor_id', intval($exhibitor_id))->get();
        // // // $data_rp["sub_cate"] = $data;


        $data = DB::table('interest_category as int')
            ->select('int.main_cate_id')
            ->join('main_category as m_cate', 'm_cate.id', '=', 'int.main_cate_id')
            ->join('sub_category as s_cate', 's_cate.id', '=', 'int.sub_cate_id')
            ->where('int.exhibitor_id', intval($exhibitor_id))
            ->groupBy('int.main_cate_id')->get();
        $data_rp["main_cate"] = $data;

        $data = DB::table('interest_category as int')
            ->select('int.sub_cate_id')
            ->join('main_category as m_cate', 'm_cate.id', '=', 'int.main_cate_id')
            ->join('sub_category as s_cate', 's_cate.id', '=', 'int.sub_cate_id')
            ->where('int.exhibitor_id', intval($exhibitor_id))->get();
        $data_rp["sub_cate"] = $data;

        return $data_rp;
    }

    public function getSubCategoryListByMainCateList(Request $request)
    {
        $main_cate_list = $request->get('main_cate_list');
        $main_cate_list = explode(',', $main_cate_list);
        $data = DB::table('sub_category as sub')
            ->join('main_category as m_cate', 'sub.main_cate', '=', 'm_cate.id')
            ->select('sub.*', 'm_cate.name_en as mainName')
            ->whereIn('sub.main_cate', $main_cate_list)->get();


        return $data;
    }

    public function getExhibitorByMultiCategory(Request $request)
    {

        $categoryId = $request->get('id');
        $data = DB::table('interest_category as interest')
            ->join('exhibitor_list as exh', 'interest.exhibitor_id', '=', 'exh.id')
            ->join('main_category as mcate', 'interest.main_cate_id', '=', 'mcate.id')
            ->join('sub_category as scate', 'interest.sub_cate_id', '=', 'scate.id')
            ->join('countries as ctry', 'exh.country_id', '=', 'ctry.id')
            ->select('exh.id', 'exh.company', 'exh.logo', 'ctry.code as countryCode', 'ctry.name as countryName', 'mcate.name_en as mainCate', DB::raw("GROUP_CONCAT(scate.name SEPARATOR ',') AS subCate "))
            ->where('interest.main_cate_id', intval($categoryId))
            ->groupBy('exh.id')
            ->get();
        $data_rp['exhibitor'] = $data;

        return $data_rp;
    }

    public function getExhibitorByCategoryPage(Request $request)
    {
        $categoryId = $request->get('cat');
        $size = $request->get('size');

        $data = DB::table('ordering as ord')
            ->join('exhibitor_list as exh', 'ord.exhibition_id', '=', 'exh.id')
            ->join('main_category as mcate', 'ord.m_cate_id', '=', 'mcate.id')
            ->join('countries as ctry', 'exh.country_id', '=', 'ctry.id')
            ->select('exh.id', 'ord.ordering', 'exh.company', 'exh.logo', 'ctry.code as countryCode', 'ctry.name as countryName', 'mcate.name_en as mainCate')
            ->where('ord.m_cate_id', intval($categoryId))
            ->groupBy('exh.id')
            ->orderBy('ord.ordering', 'asc')
            ->paginate($size); // page automaticaly use on paginate method.

        // echo json_encode($data);
        // $data_rp['data'] = $data;
        return $data;
    }

    public function getCountriesExhibitorHaving(Request $request)
    {
        $data = DB::table('exhibitor_list as exh')
            ->join('countries as ctry', 'exh.country_id', '=', 'ctry.id')
            ->select('ctry.id', 'ctry.name')
            ->groupBy('ctry.id')
            ->get();

        return $data;
    }

    public function networkLounge(Request $request)
    {
        // $data = DB::table('registers as reg')
        // ->join('countries as ctry', 'reg.country', '=', 'ctry.id')
        // ->join('nature_of_business as nob', 'reg.nature_of_business', '=', 'nob.id')
        // ->select('reg.id', 'reg.fname', 'reg.lname', 'reg.company', 'reg.type', 'reg.position', 'reg.nature_of_business', 'reg.nature_of_business_other', 'reg.country', 'nob.name_en', 'nob.name_th', 'ctry.code as countryCode','ctry.name as countryName');

        $data = DB::table('interest_category as interest')
            ->join('exhibitor_list as exh', 'interest.exhibitor_id', '=', 'exh.id')
            ->join('main_category as mcate', 'interest.main_cate_id', '=', 'mcate.id')
            ->join('countries as ctry', 'exh.country_id', '=', 'ctry.id')
            ->select('exh.id', 'exh.company', 'exh.logo', 'exh.name', 'ctry.code as countryCode', 'ctry.name as countryName', 'mcate.name_en as mainCate')
            ->groupBy('exh.id');

        $data = $data->orderBy("exh.company");
        $data = $data->orderBy("exh.name");
        $search = $request->input('search');
        $cate   = $request->input('cate');

        if ($request->user()) {
            $userId = $request->user()->id;

            $data = $data->where("exh.id", "!=", $userId);
        }

        if ($cate) {
            $cate = array_map('intval', \explode(",", $cate));
            $data = $data->whereIn('mcate.id', $cate);
        }

        if ($search) {
            $search = \trim($search);
            if ($search != "") {
                $data = $data->where(function ($query) use ($search) {
                    $query->orWhere('exh.name', 'like', '%' . $search . '%');
                    $query->orWhere('exh.company', 'like', '%' . $search . '%');
                });
            }
        }

        // return $data->toSql();
        // return $data->get();
        return $data->paginate(15);
    }

    public function networkLounge2(Request $request) {
        $data = DB::table('interest_category as interest')
        ->join('exhibitor_list as exh', 'interest.exhibitor_id', '=', 'exh.id')
        ->join('main_category as mcate', 'interest.main_cate_id', '=', 'mcate.id')
        ->join('countries as ctry', 'exh.country_id', '=', 'ctry.id')
        ->select('exh.id', 'exh.company', 'exh.logo', 'exh.name', 'ctry.code as countryCode', 'ctry.name as countryName', 'mcate.name_en as mainCate')
        ->groupBy('exh.id');
        $data = $data->orderBy("exh.company");
        $data = $data->orderBy("exh.name");
        $search = '';
        $cate   = "1,2,3";

        if ($cate) {
            $cate = array_map('intval', \explode(",", $cate));
            $data = $data->whereIn('cate_id', $cate);
        }

        if ($search) {
            $search = \trim($search);
            if ($search != "") {
                $data = $data->where(function ($query) use ($search) {
                    $query->orWhere('exh.name', 'like', '%' . $search . '%');
                    $query->orWhere('exh.company', 'like', '%' . $search . '%');
                });
            }
        }
        $data->paginate(15);

        dd(DB::getQueryLog());
    }

    public function getExhibitorProfile(Request $request)
    {
        $user = $request->user();
        if ($user) {
            return \response(null, 401);
        }

        $userId = $user->id;

        return $userId;
    }


    // brochure actions

    public function getBrochure(Request $request)
    {
        $userData = $request->user();

        $brochure = BrochureList::where("exhibitor_id", "=", $userData->id)->get();

        return \response()->json($brochure);
    }
    public function getBrochureById(Request $request, $id)
    {
        $data = BrochureList::findOrFail($id);
        $user = $request->user();

        if ($data->exhibitor_id != $user->id) {
            return \response(null, 403);
        }

        $data["original_link"] = $data->original_link;
        $data["original_link_thumbnail"] = $data->original_link_thumbnail;

        return $data;
    }

    public function uploadBrochure(Request $request)
    {
        if ($request->input('type') == "file") {
            $request->validate([
                'brochure'  => 'required|mimes:pdf,jpeg,png,jpg|max:13312',
                'thumbnail' => 'mimes:jpeg,png,jpg|max:2048',
            ]);

            $user           = $request->user();
            $brochure       = null;
            $link_thumbnail = null;

            $fileType = $request->file('brochure')->getClientOriginalExtension();

            DB::beginTransaction();

            try {
                $path1 = Storage::disk('exhibitor')->put("/brochures", $request->file('brochure'));
                if ($request->hasFile('thumbnail')) {
                    $link_thumbnail = Storage::disk('exhibitor')->put("/brochures", $request->file('thumbnail'));
                }

                if ($path1) {

                    $brochure               = new BrochureList();
                    $brochure->exhibitor_id = $user->id;
                    $brochure->link         = $path1;
                    $brochure->info = $request->input('info');

                    if ($link_thumbnail) {
                        $brochure->link_thumbnail = $link_thumbnail;
                    }

                    // $brochure->type      = $fileType == "pdf" ? "pdf" : "image";
                    // $brochure->file_type = $fileType == "pdf" ? "application/pdf" : "image/" . $fileType;


                    $brochure->type      = ($fileType == "application/pdf" || $fileType == "pdf") ? "pdf" : "image";
                    $brochure->file_type = ($fileType == "application/pdf" || $fileType == "pdf") ? "application/pdf" : "image/" . $fileType;
                    $brochure->save();
                } else {
                    return \response()->json([
                        "status" => false,
                    ]);
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }

            DB::commit();

            return \response()->json([
                "status" => true,
                "data"   => $brochure,
            ]);
        } else {

            $request->validate([
                'thumbnail' => 'mimes:jpeg,png,jpg|max:2048',
            ]);

            $user           = $request->user();
            $brochure       = null;
            $link_thumbnail = null;

            DB::beginTransaction();

            try {


                if ($request->hasFile('thumbnail')) {
                    $link_thumbnail = Storage::disk('exhibitor')->put("/brochures", $request->file('thumbnail'));
                }

                $brochure               = new BrochureList();
                $brochure->exhibitor_id = $user->id;
                $brochure->link         = $request->input('brochure');
                $brochure->info = $request->input('info');
                $brochure->type = $request->input('type');

                if ($link_thumbnail) {
                    $brochure->link_thumbnail = $link_thumbnail;
                }
                $brochure->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }

            DB::commit();

            return \response()->json([
                "status" => true,
                "data"   => $brochure,
            ]);
        }
    }

    public function deleteBrochure(Request $request, $id)
    {
        $user       = $request->user();
        $updateDone = false;

        $brochure = BrochureList::findOrFail($id);

        if ($brochure->exhibitor_id != $user->id) {
            return \response(null, 403);
        }

        DB::beginTransaction();

        try {
            $brochure->delete();
            DB::table('brochure_bag')->where('brochure_id', '=', $id)->delete();
            $updateDone = true;
        } catch (\Throwable $th) {
            $updateDone = false;
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        if ($updateDone) {
            Storage::disk("exhibitor")->delete($brochure->original_link);
            Storage::disk("exhibitor")->delete($brochure->original_link_thumbnail);
        }

        return \response()->json([
            "status" => true,
        ]);
    }

    public function updateBrochure(Request $request, $id)
    {
        $status   = false;
        $state    = '';
        $brochure = BrochureList::findOrFail($id);
        $user     = $request->user();
        $fileType = '';
        // echo "id = " . $id;
        // var_dump($brochure);

        if ($request->input('type') == "file") {
            $state = "file";
            $request->validate([
                'brochure'  => 'mimetypes:application/pdf,image/jpeg,image/png,image/jpg|max:13312',
                'thumbnail' => 'mimes:jpeg,png,jpg|max:2048',
            ]);

            $path1       = null;
            $link_thumbnail = null;


            DB::beginTransaction();

            try {

                if ($request->hasFile('brochure')) {
                    $fileType = $request->file('brochure')->getClientOriginalExtension();
                    $path1 = Storage::disk('exhibitor')->put("/brochures", $request->file('brochure'));
                } else {
                    $fileType = $brochure->file_type;
                }

                if ($request->hasFile('thumbnail')) {
                    $link_thumbnail = Storage::disk('exhibitor')->put("/brochures", $request->file('thumbnail'));
                }

                $brochure->info = $request->input('info');
                if ($path1) {
                    $brochure->link = $path1;
                }
                if ($link_thumbnail) {
                    $brochure->link_thumbnail = $link_thumbnail;
                }
                // $brochure->type      = $fileType == "pdf" ? "pdf" : "image";
                // $brochure->file_type = $fileType == "pdf" ? "application/pdf" : "image/" . $fileType;

                $brochure->type      = ($fileType == "application/pdf" || $fileType == "pdf") ? "pdf" : "image";
                $brochure->file_type = ($fileType == "application/pdf" || $fileType == "pdf") ? "application/pdf" : "image/" . $fileType;
                $brochure->save();
                $status = true;
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }

            DB::commit();

            $brochure["original_link"] = $brochure->original_link;
            $brochure["original_link_thumbnail"] = $brochure->original_link_thumbnail;

            $status = true;
            // return \response()->json([
            //     "status" => $status,
            //     "data"   => $brochure,
            // ]);
        }

        if ($request->input('type') == "link") {

            $state = "link";
            $request->validate([
                'thumbnail' => 'mimes:jpeg,png,jpg|max:2048',
            ]);

            $path1       = null;
            $link_thumbnail = null;

            DB::beginTransaction();

            try {

                if ($request->hasFile('thumbnail')) {
                    $link_thumbnail = Storage::disk('exhibitor')->put("/brochures", $request->file('thumbnail'));
                }
                $brochure->link = $request->input('brochure');
                $brochure->info = $request->input('info');
                $brochure->type = $request->input('type');
                if ($link_thumbnail) {
                    $brochure->link_thumbnail = $link_thumbnail;
                }
                $brochure->save();
                $status = true;
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }

            DB::commit();

            $status = true;
        }


        return \response()->json([
            "state"  => $state,
            "status" => $status,
            "data"   => $brochure,
        ]);
    }
    // end brochure actions

    // end brochure actions

    // promotion actions

    public function getPromotion(Request $request)
    {
        $userData = $request->user();

        $promotion = PromotionList::where("exhibitor_id", "=", $userData->id)->get();

        return \response()->json($promotion);
    }

    public function getPromotionById(Request $request, $id)
    {
        $data = PromotionList::findOrFail($id);
        $user = $request->user();

        if ($data->exhibitor_id != $user->id) {
            return \response(null, 403);
        }

        return $data;
    }


    public function uploadPromotion(Request $request)
    {
        $request->validate([
            'promotion' => 'required|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user      = $request->user();
        $promotion = null;

        DB::beginTransaction();

        try {
            $path1 = Storage::disk('exhibitor')->put("/promotions", $request->file('promotion'));

            if ($path1) {

                $promotion               = new PromotionList();
                $promotion->exhibitor_id = $user->id;
                $promotion->link         = $path1;
                $promotion->info        = $request->input("info");
                $promotion->description = $request->input("description");


                $promotion->save();
            } else {
                if ($path1) {
                    Storage::disk("exhibitor")->delete($path1);
                }

                return \response()->json([
                    "status" => false,
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response()->json([
            "status" => true,
            "data"   => $promotion,
        ]);
    }

    public function deletePromotion(Request $request, $id)
    {
        $user       = $request->user();
        $updateDone = false;

        $promotion = PromotionList::findOrFail($id);

        if ($promotion->exhibitor_id != $user->id) {
            return \response(null, 403);
        }

        DB::beginTransaction();

        try {
            $promotion->delete();
            $updateDone = true;
        } catch (\Throwable $th) {
            $updateDone = false;
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        if ($updateDone) {
            Storage::disk("exhibitor")->delete($promotion->original_link);
        }

        return \response()->json([
            "status" => true,
        ]);
    }

    public function updatePromotion(Request $request, $id)
    {
        $promotion = PromotionList::findOrFail($id);
        $user   = $request->user();
        $status = false;

        DB::beginTransaction();

        try {

            if ($request->file('promotion') != '') {
                $path1 = Storage::disk('exhibitor')->put("/promotions", $request->file('promotion'));
                if ($path1) {
                    $promotion->exhibitor_id = $user->id;
                    $promotion->link         = $path1;
                    $promotion->info        = $request->input("info");
                    $promotion->description = $request->input("description");
                    $promotion->save();
                    $status = true;
                } else {
                    if ($path1) {
                        Storage::disk("exhibitor")->delete($path1);
                    }
                    $status = false;
                }
            } else {
                $promotion->exhibitor_id = $user->id;
                $promotion->info         = $request->input("info");
                $promotion->description  = $request->input("description");
                $promotion->save();
                $status = true;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response()->json([
            "status" => $status,
            "data"   => $promotion,
        ]);
    }

    // end promotion actions

    // poster actions

    public function getPoster(Request $request)
    {
        $userData = $request->user();

        $poster = EposterList::where("exhibitor_id", "=", $userData->id)->get();

        return \response()->json($poster);
    }

    public function getPosterById(Request $request, $id)
    {
        $data = EposterList::findOrFail($id);
        $user = $request->user();

        if ($data->exhibitor_id != $user->id) {
            return \response(null, 403);
        }


        return $data;
    }

    public function uploadPoster(Request $request)
    {
        $request->validate([
            'poster' => 'required|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user   = $request->user();
        $poster = null;

        DB::beginTransaction();

        try {
            $path1 = Storage::disk('exhibitor')->put("/posters", $request->file('poster'));

            if ($path1) {

                $poster               = new EposterList();
                $poster->exhibitor_id = $user->id;
                $poster->link         = $path1;
                $poster->info        = $request->input("info");
                $poster->description = $request->input("description");

                $poster->save();
            } else {
                if ($path1) {
                    Storage::disk("exhibitor")->delete($path1);
                }

                return \response()->json([
                    "status" => false,
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response()->json([
            "status" => true,
            "data"   => $poster,
        ]);
    }

    public function deletePoster(Request $request, $id)
    {
        $user       = $request->user();
        $updateDone = false;

        $poster = EposterList::findOrFail($id);

        if ($poster->exhibitor_id != $user->id) {
            return \response(null, 403);
        }

        DB::beginTransaction();

        try {
            $poster->delete();
            $updateDone = true;
        } catch (\Throwable $th) {
            $updateDone = false;
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        if ($updateDone) {
            Storage::disk("exhibitor")->delete($poster->original_link);
        }

        return \response()->json([
            "status" => true,
        ]);
    }

    public function updatePoster(Request $request, $id)
    {
        $poster = EposterList::findOrFail($id);
        $user   = $request->user();
        $status = false;

        DB::beginTransaction();

        try {

            if ($request->file('poster') != '') {
                $path1 = Storage::disk('exhibitor')->put("/posters", $request->file('poster'));
                if ($path1) {
                    $poster->exhibitor_id = $user->id;
                    $poster->link         = $path1;
                    $poster->info        = $request->input("info");
                    $poster->description = $request->input("description");
                    $poster->save();
                    $status = true;
                } else {
                    if ($path1) {
                        Storage::disk("exhibitor")->delete($path1);
                    }
                    $status = false;
                }
            } else {
                $poster->exhibitor_id = $user->id;
                $poster->info         = $request->input("info");
                $poster->description  = $request->input("description");
                $poster->save();
                $status = true;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response()->json([
            "status" => $status,
            "data"   => $poster,
        ]);
    }

    // end poster actions

    // video actions

    public function getVideo(Request $request)
    {
        $userData = $request->user();

        $video = VideoList::where("exhibitor_id", "=", $userData->id)->get();

        return \response()->json($video);
    }

    public function getVideoById(Request $request, $id)
    {
        $video = VideoList::findOrFail($id);
        $user  = $request->user();

        if ($video->exhibitor_id != $user->id) {
            return \response(null, 403);
        }

        return $video;
    }

    public function activeVideo(Request $request, $id)
    {
        $video = VideoList::findOrFail($id);
        $user  = $request->user();

        if ($video->exhibitor_id != $user->id) {
            return \response(null, 403);
        }

        DB::beginTransaction();

        try {
            VideoList::where('active', '=', '1')->where('exhibitor_id', '=', $user->id)->update(['active' => '0']);
            $video->active = 1;
            $video->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response(null, 201);
    }

    public function addVideo(Request $request)
    {
        $user               = $request->user();
        $data               = new VideoList;
        $data->info         = $request->info;
        $data->link         = $request->link;
        $data->description  = $request->description;
        $data->type         = $request->type;
        $data->exhibitor_id = $user->id;
        $data->save();

        return \response($data);
    }

    public function deleteVideo(Request $request, $id)
    {
        $data = VideoList::findOrFail($id);
        $user = $request->user();

        if ($data->exhibitor_id != $user->id) {
            return \response(null, 403);
        }

        $data->delete();

        return \response(null, 201);
    }

    public function updateVideo(Request $request, $id)
    {
        $video = VideoList::findOrFail($id);
        $user  = $request->user();

        if ($video->exhibitor_id != $user->id) {
            return \response(null, 403);
        }

        $video->fill($request->all());
        $video->save();

        return \response($video);
    }

    // end video actions

    public function updateInformation(Request $request)
    {
        $user   = $request->user();
        $userId = $user->id;
        $data   = Exhibitor::findOrFail($userId);

        DB::beginTransaction();

        try {
            $data->fill($request->except(['mainCate']));

            $data->save();

            // Interest::where('exhibitor_id', '=', $userId)->delete();

            // if ($request->has('mainCate')) {
            //     foreach ($request->get('mainCate') as $key => $value) {
            //         $interest               = new Interest();
            //         $interest->type         = 1;
            //         $interest->exhibitor_id = $userId;
            //         $interest->main_cate_id = $value;
            //         $interest->save();
            //     }
            // }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response(null, 201);
    }

    public function updateImageProfile(Request $request)
    {
        $request->validate([
            'avatar' => 'required|mimes:jpeg,png,jpg|max:2048',
        ]);
        $reponse  = array();

        $userData = $request->user();
        $userId   = $userData->id;
        $isDone   = false;
        $data     = Exhibitor::findOrFail($userId);

        $pathImageAvatar = Storage::disk('exhibitor')->put("/avatars", $request->file('avatar'));
        if ($pathImageAvatar) {
            DB::beginTransaction();

            try {
                $oldImage         = $data->original_img_avatar;
                $data->img_avatar = $pathImageAvatar;
                $data->save();
                $isDone = true;
                $reponse["link"] = url('/') . "/public/uploads/exhibitor/" . $pathImageAvatar;
            } catch (\Throwable $th) {
                Storage::disk("exhibitor")->delete($pathImageAvatar);
                $isDone = false;
                DB::rollBack();
                throw $th;
            }

            if ($isDone && $oldImage != null) {
                Storage::disk("exhibitor")->delete($oldImage);
            }

            DB::commit();
        }

        return response($reponse, 201);
    }

    public function updateBoothBanner(Request $request)
    {
        $request->validate([
            'banner' => 'required|mimes:jpeg,png,jpg|max:2048',
        ]);
        $reponse  = array();
        $userData = $request->user();
        $userId   = $userData->id;
        $isDone   = false;
        $data     = Exhibitor::findOrFail($userId);

        $pathImageBanner = Storage::disk('exhibitor')->put("/banners", $request->file('banner'));
        if ($pathImageBanner) {
            DB::beginTransaction();

            try {
                $oldImage     = $data->original_banner;
                $data->banner = $pathImageBanner;
                $data->save();
                $isDone = true;
                $reponse["link"] = url('/') . "/public/uploads/exhibitor/" . $pathImageBanner;
            } catch (\Throwable $th) {
                Storage::disk("exhibitor")->delete($pathImageBanner);
                $isDone = false;
                DB::rollBack();
                throw $th;
            }

            if ($isDone && $oldImage != null) {
                Storage::disk("exhibitor")->delete($oldImage);
            }

            DB::commit();
        }

        return response($reponse, 201);
    }

    public function updateBoothLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|mimes:jpeg,png,jpg|max:2048',
        ]);

        $reponse       = array();
        $userData      = $request->user();
        $userId        = $userData->id;
        $isDone        = false;
        $data          = Exhibitor::findOrFail($userId);
        $pathImageLogo = Storage::disk('exhibitor')->put("/logo", $request->file('logo'));

        if ($pathImageLogo) {
            DB::beginTransaction();

            try {
                $oldImage   = $data->original_logo;
                $data->logo = $pathImageLogo;
                $data->save();
                $isDone = true;
                $reponse["link"] = url('/') . "/public/uploads/exhibitor/" . $pathImageLogo;
            } catch (\Throwable $th) {
                Storage::disk("exhibitor")->delete($pathImageLogo);
                $isDone = false;
                DB::rollBack();
                throw $th;
            }

            if ($isDone && $oldImage != null) {
                Storage::disk("exhibitor")->delete($oldImage);
            }

            DB::commit();
        }

        return response($reponse, 201);
    }

    // v2

    public function getExhibitorList_v2(Request $request)
    {
        $data = Exhibitor::with(["country" => function ($query) {
            $query->select("id", "name", "code");
        }])->select([
            "company",
            "country_id",
            "id",
            "logo",
        ])->get();

        return $data;
    }
    public function getData()
    {

        $data = [
            '85925e',
            'varpevent2020',


        ];

        foreach ($data as $item) {

            $phash = Hash::make($item);
            echo $phash . "\n";
        }
    }

    public function getDataAccount (Request $request, $id, $type) {

        $data = [];
        $data["id"] = $id;
        $data["type"] = $type;

        try
        {
            if($type == "exhibitor") {
                $data = Exhibitor::find($id);
            } else if ($type == "buyer") {
                $data = Register::find($id);
            } else if ($type == "visitor") {
                $data = Register::find($id);
            }
        } catch (ModelNotFoundException $e) {
        }

        if (!$data) {
            $data = false;
        }

        return \response($data);
    }


    public function getInterests(Request $request) {
        $id = $request->input('exhibitor_id');
        $exhibitor = Exhibitor::find($id);
        $query = InterestCategoryByExhibitorId::with('main_cate')
            ->where('exhibitor_id', $id)
            ->get();
        $results = $query->map(function($item) {
            return $item->main_cate->name_th;
        });

        return response(["id" => $id, "data" => $exhibitor, "interests" => $results], 200);
    }
}
