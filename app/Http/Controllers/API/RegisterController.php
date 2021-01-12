<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\Confirmation;
use App\Mail\ForgetPassword;
use App\Mail\NotifyVisitorMessage;
use App\Mail\InviteRegisterToBuyer;
use App\Model\BrochureBag;
use App\Model\Countries;
use App\Model\FindAbout;
use App\Model\JobFunction;
use App\Model\JobLevel;
use App\Model\MainCate;
use App\Model\SubCate;
use App\Model\NatureOfBusiness;
use App\Model\NumberOfEmployees;
use App\Model\ReasonForAttending;
use App\Model\Register;
use App\Model\Interest;
use App\Model\RoleProcess;
use App\Model\PrefixName;
use App\Model\Budget;
use App\Model\InterestCategoryByVisitorId;
use Illuminate\Support\Facades\Crypt;
use App\Exports\RegisterExport;
use App\Exports\RemindExport;
use App\Exports\BuyerExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\API\EmailController;



use DB;
use Hash;
use Illuminate\Http\Request;
use Mail;
use Storage;
use File;


class RegisterController extends Controller
{

    public function __construct()
    {
        config(['auth.defaults.guard' => 'register']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        $itemsPerPage    = $request->input('itemsPerPage');
        $sortBy          = $request->input('sortBy');
        $sortDesc        = $request->input('sortDesc');
        $matching        = $request->input('matching');
        $conference      = $request->input('conference');
        $buyer           = $request->input('buyer');
        $accept           = $request->input('accept');
        $search           = $request->input('search');
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
        $data            = Register::selectRaw('*, @row:=@row+1 as row_no')->with(["country", "job_function", "role_process"]);
        $countTH         = Register::where("country", "=", 217)->count();
        $countInter      = Register::where("country", "!=", 217)->count();
        $countAll        = $data->count();
        $countMatching   = Register::where("allow_matching", "=", "Y")->count();
        $countConference = Register::where("interested_to_join", "=", "Y")->count();
        $country       = $request->input('country');
        $international = $request->input('international');


        if ($search && $search != "") {
            $data = $data->where("fname", "like", "%{$search}%");
            $data = $data->orWhere("lname", "like", "%{$search}%");
            $data = $data->orWhere("company", "like", "%{$search}%");
            $data = $data->orWhere("email", "like", "%{$search}%");
        }
        if ($matching && $matching != "") {
            $data = $data->where("allow_matching", "=", $matching);
        }

        if ($conference && $conference != "") {
            $data = $data->where("interested_to_join", "=", $conference);
        }
        if ($accept && $accept != "") {
            $data = $data->where("allow_accept", "=", $accept);
        }


        if ($country && $country != "") {
            $data = $data->where("country", "=", $country);
        }

        if ($international && $international != "") {
            if ($international == "Y") {
                $data = $data->where("country", "!=", 217);
            } else {
                $data = $data->where("country", "=", 217);
            }
        }
        if ($buyer && $buyer != "") {
            $data = $data->where("type", "=", 1);
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
            'count_all'        => $countAll,
            'count_th'         => $countTH,
            'count_inter'      => $countInter,
            'count_matching'   => $countMatching,
            'count_conference' => $countConference,

        ]);

        return $custom->merge($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBuyer(Request $request)
    {

        $itemsPerPage    = $request->input('itemsPerPage');
        $sortBy          = $request->input('sortBy');
        $sortDesc        = $request->input('sortDesc');
        $matching        = $request->input('matching');
        $conference      = $request->input('conference');
        $buyer           = $request->input('buyer');
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
        $data            = Register::selectRaw('*, @row:=@row+1 as row_no')->with(["country", "job_function", "role_process"]);
        $countTH         = Register::where("country", "=", 217)->where("type", "=", 1)->count();
        $countInter      = Register::where("country", "!=", 217)->where("type", "=", 1)->count();
        $countAll        = $data->count();
        $countMatching   = Register::where("allow_matching", "=", "Y")->where("type", "=", 1)->count();
        $countConference = Register::where("interested_to_join", "=", "Y")->where("type", "=", 1)->count();
        $country       = $request->input('country');
        $international = $request->input('international');
        $data = $data->whereRaw("type = 1");
        if ($search && $search != "") {
            $data = $data->whereRaw("(fname REGEXP '{$search}' OR lname REGEXP '{$search}' OR company REGEXP '{$search}' OR email REGEXP '{$search}')");
        }
        if ($matching && $matching != "") {
            $data = $data->where("allow_matching", "=", $matching);
        }

        if ($conference && $conference != "") {
            $data = $data->where("interested_to_join", "=", $conference);
        }

        if ($country && $country != "") {
            $data = $data->where("country", "=", $country);
        }

        if ($international && $international != "") {
            if ($international == "Y") {
                $data = $data->where("country", "!=", 217);
            } else {
                $data = $data->where("country", "=", 217);
            }
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
            'count_all'        => $countAll,
            'count_th'         => $countTH,
            'count_inter'      => $countInter,
            'count_matching'   => $countMatching,
            'count_conference' => $countConference,

        ]);

        return $custom->merge($data);
    }
    public function store(Request $request)
    {
        $request->validate([
            "fname"                => "required",
            "lname"                => "required",
            "company"              => "required",
            "position"              => "required",
            "address"              => "required",
            "city"                 => "required",
            "province"             => "required",
            "postal_code"          => "required",
            "country"              => "required",
            "telephone"            => "required",
            "mobile"               => "required",
            // "fax"                  => "required",
            "email"                => "required|email|unique:registers",
            // "website"              => "required",
            "password"             => "required|confirmed|min:6",
            "nature_of_business"   => "required",
            "job_level"            => "required",
            "job_function"         => "required",
            "role_process"         => "required",
            "number_of_employees"  => "required",
            "allow_matching"       => "required",
            // "cate_id"              => "required",
            "reason_for_attending" => "required",
            "find_out_about"       => "required",
            "budget"       => "required",

            // "join_conference" => "required",
        ]);

        DB::beginTransaction();

        try {
            $register = new Register();
            $register->fill($request->except(['find_out_about', 'reason_for_attending', 'password', 'cate_id', 'channel']));
            $register->reason_for_attending = \json_encode($request->input('reason_for_attending'));
            $register->find_out_about       = \json_encode($request->input('find_out_about'));
            $register->password             = Hash::make($request->input("password"));
            $register->p_hash = Crypt::encryptString($request->input("password"));
            $register->channel             = strtolower($request->input("channel"));

            $register->save();
            if ($register->id) {


                foreach ($request->input('cate_id') as $key => $value) {

                    $interested = new InterestCategoryByVisitorId();
                    $interested->register_id = $register->id;
                    $interested->main_cate_id = $value;
                    $interested->type = 2;
                    $interested->save();
                }
            }

            Mail::send(new Confirmation($register));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response(null, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Register  $register
     * @return \Illuminate\Http\Response
     */
    public function show(Register $register)
    {
        return $register;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Register  $register
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Register $register)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Register  $register
     * @return \Illuminate\Http\Response
     */
    public function destroy(Register $register)
    {
        //
    }

    public function getCountries()
    {
        return Countries::all();
    }

    public function getMainCate()
    {
        return MainCate::all();
    }

    public function getSubCate()
    {
        return SubCate::all();
    }

    public function getNatureOfBusiness()
    {
        return NatureOfBusiness::all();
    }

    public function getJobLevel()
    {
        return JobLevel::all();
    }

    public function getJobFunction()
    {
        return JobFunction::all();
    }

    public function getRoleProcess()
    {
        return RoleProcess::all();
    }

    public function getNumberOfEmployees()
    {
        return NumberOfEmployees::all();
    }

    public function getReasonForAttending()
    {
        return ReasonForAttending::all();
    }

    public function getFindOutAbout()
    {
        return FindAbout::all();
    }
    public function getPrefix()
    {
        return PrefixName::all();
    }
    public function getBudget()
    {
        return Budget::all();
    }

    public function getInitForm()
    {
        $data = [
            'Countries'          => $this->getCountries(),
            'NatureOfBusiness'   => $this->getNatureOfBusiness(),
            'JobLevel'           => $this->getJobLevel(),
            'JobFunction'        => $this->getJobFunction(),
            'RoleProcess'        => $this->getRoleProcess(),
            'NumberOfEmployees'  => $this->getNumberOfEmployees(),
            'ReasonForAttending' => $this->getReasonForAttending(),
            'FindOutAbout'       => $this->getFindOutAbout(),
            "MainCate"           => $this->getMainCate(),
            'SubCate'            => $this->getSubCate(),
            'PrefixName'            => $this->getPrefix(),
            'Budget'            => $this->getBudget(),

        ];

        return $data;
    }

    public function testSendEmail()
    {
        $data = Register::findOrFail(8);
        return Mail::send(new Confirmation($data));
    }

    public function sendReminder(Request $request)
    {
        $email          = $request->input('email');
        $regis_id             = $request->input('id');
        Mail::send(new InviteRegisterToBuyer($email));
        $this->updateRegister($regis_id);
    }

    public function allIdVisitor(){
        $data = DB::table('registers')
            ->select('id')
            ->get();
        return $data;
    }

    public function sendNotifyVisitor(Request $request)
    {
        $register_raw = Register::select(["email", "p_hash", "fname", "lname"])->where("id", $request->input('id'))->get();
        $data["p_hash"]         = $register_raw[0]->p_hash;
        $data['email']          = $register_raw[0]->email;
        $data['noRead']         = $request->input('noRead');
        $data['name']           = $register_raw[0]->full_name;
        $id                     = $register_raw[0]->id;
        Mail::send(new NotifyVisitorMessage($data));
        $rp["status"] = $this->updateRegisterNotify($request->input('id'));
        return $rp;

    }

    public function updateRegisterNotify($regis_id)
    {
        $register = Register::find($regis_id);
        $register->status_email_notify = 'sent';
        $register->status_email_notify_at = date('Y-m-d H:i:s');
        $register->save();
        return true;
    }


    public function forgetPasswordMethod1(Request $request)
    {
        $email          = $request->input('email');
        if (Register::where('email', '=', $email)->exists()) {
            $register_raw = Register::select(["email", "p_hash", "fname", "lname"])->where("email", $email)->get();
            $data["email"] = $register_raw[0]->email;
            $data["p_hash"] = $register_raw[0]->p_hash;
            $mail_rp = Mail::send(new ForgetPassword($data));
            $data_rp["status"] =  true;
        } else {
            $data_rp["status"] = false;
        }
        return $data_rp;
    }

    public function updateRegister($regis_id)
    {
        $register = Register::find($regis_id);
        $register->status_email_remind = 'sent';
        $register->status_email_remind_at = date('Y-m-d H:i:s');
        $register->save();
        return true;
    }
    public function networkLounge(Request $request)
    {
        $data = DB::table('registers as reg')
            ->join('countries as ctry', 'reg.country', '=', 'ctry.id')
            ->join('nature_of_business as nob', 'reg.nature_of_business', '=', 'nob.id')
            ->join('interest_category as interest', 'reg.id', '=', 'interest.register_id')
            ->select('reg.id', 'reg.fname', 'reg.lname', 'reg.company', 'reg.type', 'reg.position', 'reg.nature_of_business', 'reg.nature_of_business_other', 'reg.country', 'nob.name_en', 'nob.name_th', 'ctry.code as countryCode', 'ctry.name as countryName');

        $data = $data->where("reg.allow_accept", "=", "Y");
        // $data = $data->orderBy("reg.company");
        // $data = $data->orderBy("reg.fname");
        $data = $data->orderBy("reg.id",'desc');
        $search = $request->input('search');
        $cate   = $request->input('cate');

        if ($request->user()) {
            $userId = $request->user()->id;

            $data = $data->where("reg.id", "!=", $userId);
        }

        if ($cate) {
            $cate = array_map('intval', \explode(",", $cate));
            $data = $data->whereIn('interest.main_cate_id', $cate);
        }

        if ($search) {
            $search = \trim($search);
            if ($search != "") {
                $data = $data->where(function ($query) use ($search) {
                    $query->orWhere('reg.fname', 'like', '%' . $search . '%');
                    $query->orWhere('reg.lname', 'like', '%' . $search . '%');
                    $query->orWhere('reg.company', 'like', '%' . $search . '%');
                });
            }
        }

        $data = $data->groupBy("reg.id");

        // return $data->toSql();
        // return $data->get();
        return $data->paginate(15);
    }

    public function getReceiverNetworkLounge($id)
    {
        $data  = Register::findOrFail($id);

        return $data;
    }
    public function saveBrochure(Request $request)
    {
        DB::beginTransaction();

        $account_id   = $request->input('acc_id');
        $type  = $request->input('type');
        $brochure_id  = $request->input('brochure_id');

        $existing = $this->checkBrochureExisting($account_id, $type, $brochure_id);
        if ($existing) {
            return \response()->json([
                "status" => false,
            ]);
        } else {
            try {
                $brochureBag                = new BrochureBag();
                $brochureBag->acc_id        = $request->input('acc_id');
                $brochureBag->type  = $request->input('type');
                $brochureBag->brochure_id   = $request->input('brochure_id');
                $brochureBag->created_at    = $request->input('created_at');
                $brochureBag->save();
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }
            DB::commit();

            return \response()->json([
                "status" => true,
            ]);
        }
    }

    public function getallregister(Request $request){
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
        $data = DB::table('registers')
            ->selectRaw('*, @row:=@row+1 as row_no')
            ->get();
        return $data;
    }

    public function checkBrochureExisting($account_id, $type, $brochure_id)
    {
        $data = DB::table('brochure_bag as bag')
            ->where('bag.type', '=', $type)
            ->where('bag.acc_id', '=', $account_id)
            ->where('bag.brochure_id', '=', $brochure_id)
            ->get();

        return count($data) > 0 ? true : false;
    }
    // public function getBrochureBag(Request $request)
    // {

    //     $register_id = $request->get('id');
    //     $data = DB::table('brochure_bag as bag')
    //         ->join('brochure_list as brch', 'bag.brochure_id', '=', 'brch.id')
    //         ->join('exhibitor_list as exh', 'bag.exhibitor_id', '=', 'exh.id')
    //         ->select('bag.id', 'bag.visitor_id', 'brch.link', 'brch.info', 'exh.company', 'exh.logo')
    //         ->where('bag.visitor_id', intval($register_id))
    //         ->get();

    //     $data_rp['brochure'] = $data;

    //     return $data_rp;
    // }
    public function makeApproveRegister(Request $request)
    {
        $regis_id = $request->get('id');
        $register = Register::find($regis_id);
        $register->type = 1;
        $register->approve_at = date('Y-m-d H:i:s');
        $register->save();
        // $emailCtrl = new EmailController;
        // $emailCtrl->sendInviteRegisterToBuyer($regis_id);
        return $regis_id;
    }
    public function getBrochureBag(Request $request)
    {

        $account_id = $request->get('id');
        $type       = $request->get('type');
        $data       = DB::table('brochure_bag as bag')
            ->join('brochure_list as brch', 'bag.brochure_id', '=', 'brch.id')
            ->join('exhibitor_list as exh', 'brch.exhibitor_id', '=', 'exh.id')
            ->select('bag.id', 'bag.acc_id', 'brch.link', 'brch.info', 'exh.company', 'exh.logo', 'brch.type')
            ->where('bag.type', intval($type))
            ->where('bag.acc_id', intval($account_id))
            ->get();

        $data_rp['brochure'] = $data;

        return $data_rp;
    }
    public function decrypt_str(Request $request)
    {
        $encryptedValue = $request->get('hashing');
        $data = Crypt::decryptString($encryptedValue);
        return $data;
    }

    public function getHash(Request $request)
    {
        $encryptedValue = $request->get('hashing');
        $data = Hash::make($encryptedValue);
        return $data;
    }

    public function exportExcel()
    {
        $today = date('YmdHis');
        return Excel::download(new RegisterExport, 'register_export_' . $today . '.xlsx');
    }
    public function exportExcelBuyer()
    {
        $today = date('YmdHis');
        return Excel::download(new BuyerExport, 'buyer_export_' . $today . '.xlsx');
    }
    public function exportExcel_remind()
    {
        $today = date('YmdHis');
        return Excel::download(new RemindExport, 'remind_export_' . $today . '.xlsx');
    }

    public function updateInformation(Request $request)
    {
        $user   = $request->user();
        $userId = $user->id;


        DB::beginTransaction();

        try {
            $register = Register::find($userId);
            $register->fill($request->except(['mainCate','find_out_about', 'reason_for_attending','email','profile_image','updated_at','created_at','status_email_at', 'status_email','website','channel','time_zone','type','status_email_remind','status_email_remind_at','website']));
            $register->reason_for_attending = \json_encode($request->input('reason_for_attending'));
            $register->find_out_about       = \json_encode($request->input('find_out_about'));
            $register->updated_at           = date('Y-m-d H:i:s');
            $register->save();

            Interest::where('register_id', '=', $userId)->delete();

            if ($request->has('mainCate')) {
                foreach ($request->get('mainCate') as $key => $value) {
                    $interest               = new Interest();
                    $interest->type         = 2;
                    $interest->register_id = $userId;
                    $interest->main_cate_id = $value;
                    $interest->save();
                }
            }
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
            'profile_image' => 'required|mimes:jpeg,png,jpg|max:2048',
        ]);
        $reponse  = array();

        $userData = $request->user();
        $userId   = $userData->id;
        $isDone   = false;
        $data     = Register::findOrFail($userId);

        $pathProfileImage = Storage::disk('register')->put("/avatars", $request->file('profile_image'));
        if ($pathProfileImage) {
            DB::beginTransaction();

            try {
                $oldImage         = $data->original_profile_image;
                $data->profile_image = $pathProfileImage;
                $data->save();
                $isDone = true;
                $reponse["link"] = url('/') . "/public/uploads/register/" . $pathProfileImage;
            } catch (\Throwable $th) {
                Storage::disk("register")->delete($pathProfileImage);
                $isDone = false;
                DB::rollBack();
                throw $th;
            }

            if ($isDone && $oldImage != null) {
                Storage::disk("register")->delete($oldImage);
            }

            DB::commit();
        }

        return response($reponse, 201);
    }


    public function getInterests(Request $request) {
        $id = $request->input('register_id');
        $register = Register::find($id);
        $query = InterestCategoryByVisitorId::with('main_cate')
            ->where('register_id', $id)
            ->get();
        $results = $query->map(function($item) {
            return $item->main_cate->name_th;
        });

        return response(["id" => $id, "data" => $register, "interests" => $results], 200);
    }
}
