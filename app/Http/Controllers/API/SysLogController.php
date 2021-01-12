<?php

namespace App\Http\Controllers\API;

use App\Exports\BackOfficeLoginHistoryExport;
use App\Exports\TrafficLogExport;
use App\Exports\TrafficLogExportV2;
use App\Exports\WebinarHistoryLogExport;
use App\Exports\WebinarHistoryLogExportTest;
use App\Http\Controllers\Controller;
use App\Model\BrochureList;
use App\Model\Countries;
use App\Model\Exhibitor;
use App\Model\ExhibitorLogin;
use App\Model\FindAbout;
use Illuminate\Http\Request;
use App\Model\LogActivity;
use App\Model\LogVisitBooths;
use App\Model\LogVideo;
use App\Model\LogPoster;
use App\Model\LogPromotion;
use App\Model\LogBrochure;
use App\Model\LogCategory;
use App\Model\LogChat;
use App\Model\LogInfo;
use App\Model\MainCate;
use App\Model\ReasonForAttending;
use App\Model\Register;
use App\Model\VisitorLogin;
use DateTime;
use DB;
use Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class SysLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function visitLog(Request $request)
    {
        //console.log(new Date() + "|" + Intl.DateTimeFormat().resolvedOptions().timeZone)
        $status = false;
        $result_status = false;
        $request->validate([
            'ownerId'   => 'required',
            'actorId'   => 'required',
            'actorType' => 'required',
            'log'        => 'required',
        ]);

        $res = [
            "log"   => "log",
            "visit" => $request->input("log"),
            "data"  => $request->all()
        ];

        switch ($request->input("log")) {
            case 'booth':
                $resultStatus = $this->visitBooth($request);
                break;
            case 'video':
                $resultStatus = $this->visitVideo($request);
                break;
            case 'poster':
                $resultStatus = $this->visitPoster($request);
                break;
            case 'promotion':
                $resultStatus = $this->visitPromotion($request);
                break;
            case 'brochure':
                $resultStatus = $this->visitBrochure($request);
                break;
            case 'chat':
                $resultStatus = $this->visitChat($request);
                break;
            case 'category':
                $resultStatus = $this->visitCategory($request);
                break;
        }

        return \response()->json([
            "status" => $resultStatus,
            // "data"   => $res,
        ]);
    }

    public function visitBooth(Request $request)
    {
        //console.log(new Date() + "|" + Intl.DateTimeFormat().resolvedOptions().timeZone)
        $status = false;
        $request->validate([
            'refId' => 'required',
            'ownerId' => 'required',
            'actorId' => 'required',
            'actorType' => 'required',
            'action' => 'required',
            'data' => 'required',
            'description' => 'required',
            'userAgent' => 'required',
            'clientTimezone' => 'required',
        ]);


        $res = [
            "log" => "visitBooth",
            "data" => $request->all()
        ];

        // check

        DB::beginTransaction();

        try {
            $data = new LogVisitBooths();
            $data->ref_id = $request->input('refId');
            $data->owner_id = $request->input('ownerId');
            $data->actor_id = $request->input('actorId');
            $data->actor_type = $request->input('actorType');
            $data->action = $request->input('action');
            $data->data = $request->input('data');
            $data->description = $request->input('description');
            $data->user_agent = $request->input('userAgent');
            $data->client_timezone = $request->input('clientTimezone');
            $data->save();
            $status = true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response()->json([
            "status" => $status,
            "data"   => $res,
        ]);
    }

    public function visitVideo(Request $request)
    {
        //console.log(new Date() + "|" + Intl.DateTimeFormat().resolvedOptions().timeZone)
        $status = false;
        $request->validate([
            'refId' => 'required',
            'ownerId' => 'required',
            'actorId' => 'required',
            'actorType' => 'required',
            'action' => 'required',
            'data' => 'required',
            'description' => 'required',
            'userAgent' => 'required',
            'clientTimezone' => 'required',
        ]);

        $res = [
            "log" => "visitVedio",
            "data" => $request->all()
        ];

        // check

        DB::beginTransaction();

        try {
            $data = new LogVideo();
            $data->ref_id = $request->input('refId');
            $data->owner_id = $request->input('ownerId');
            $data->actor_id = $request->input('actorId');
            $data->actor_type = $request->input('actorType');
            $data->action = $request->input('action');
            $data->data = $request->input('data');
            $data->description = $request->input('description');
            $data->user_agent = $request->input('userAgent');
            $data->client_timezone = $request->input('clientTimezone');
            $data->save();
            $status = true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response()->json([
            "status" => $status,
            "data"   => $res,
        ]);
    }

    public function visitPoster(Request $request)
    {
        //console.log(new Date() + "|" + Intl.DateTimeFormat().resolvedOptions().timeZone)
        $status = false;
        $request->validate([
            'refId' => 'required',
            'ownerId' => 'required',
            'actorId' => 'required',
            'actorType' => 'required',
            'action' => 'required',
            'data' => 'required',
            'description' => 'required',
            'userAgent' => 'required',
            'clientTimezone' => 'required',
        ]);

        $res = [
            "log" => "visitPoster",
            "data" => $request->all()
        ];

        // check

        DB::beginTransaction();

        try {
            $data = new LogPoster();
            $data->ref_id = $request->input('refId');
            $data->owner_id = $request->input('ownerId');
            $data->actor_id = $request->input('actorId');
            $data->actor_type = $request->input('actorType');
            $data->action = $request->input('action');
            $data->data = $request->input('data');
            $data->description = $request->input('description');
            $data->user_agent = $request->input('userAgent');
            $data->client_timezone = $request->input('clientTimezone');
            $data->save();
            $status = true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response()->json([
            "status" => $status,
            "data"   => $res,
        ]);
    }

    public function visitPromotion(Request $request)
    {
        //console.log(new Date() + "|" + Intl.DateTimeFormat().resolvedOptions().timeZone)
        $status = false;
        $request->validate([
            'refId' => 'required',
            'ownerId' => 'required',
            'actorId' => 'required',
            'actorType' => 'required',
            'action' => 'required',
            'data' => 'required',
            'description' => 'required',
            'userAgent' => 'required',
            'clientTimezone' => 'required',
        ]);


        $res = [
            "log" => "visitPromotion",
            "data" => $request->all()
        ];

        // check

        DB::beginTransaction();

        try {
            $data = new LogPromotion();
            $data->ref_id = $request->input('refId');
            $data->owner_id = $request->input('ownerId');
            $data->actor_id = $request->input('actorId');
            $data->actor_type = $request->input('actorType');
            $data->action = $request->input('action');
            $data->data = $request->input('data');
            $data->description = $request->input('description');
            $data->user_agent = $request->input('userAgent');
            $data->client_timezone = $request->input('clientTimezone');
            $data->save();
            $status = true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response()->json([
            "status" => $status,
            "data"   => $res,
        ]);
    }

    public function visitBrochure(Request $request)
    {
        //console.log(new Date() + "|" + Intl.DateTimeFormat().resolvedOptions().timeZone)
        $status = false;
        $request->validate([
            'refId' => 'required',
            'ownerId' => 'required',
            'actorId' => 'required',
            'actorType' => 'required',
            'action' => 'required',
            'data' => 'required',
            'description' => 'required',
            'userAgent' => 'required',
            'clientTimezone' => 'required',
        ]);


        $res = [
            "log" => "visitBrochure",
            "data" => $request->all()
        ];

        // check

        DB::beginTransaction();

        try {
            $data = new LogBrochure();
            $data->ref_id = $request->input('refId');
            $data->owner_id = $request->input('ownerId');
            $data->actor_id = $request->input('actorId');
            $data->actor_type = $request->input('actorType');
            $data->action = $request->input('action');
            $data->data = $request->input('data');
            $data->description = $request->input('description');
            $data->user_agent = $request->input('userAgent');
            $data->client_timezone = $request->input('clientTimezone');
            $data->save();
            $status = true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response()->json([
            "status" => $status,
            "data"   => $res,
        ]);
    }

    public function visitInfo(Request $request)
    {
        $status = false;
        $request->validate([
            'refId' => 'required',
            'ownerId' => 'required',
            'actorId' => 'required',
            'actorType' => 'required',
            'action' => 'required',
            'data' => 'required',
            'description' => 'required',
            'userAgent' => 'required',
            'clientTimezone' => 'required',
        ]);


        $res = [
            "log" => "visitBrochure",
            "data" => $request->all()
        ];

        // check

        DB::beginTransaction();

        try {
            $data = new LogInfo();
            $data->ref_id = $request->input('refId');
            $data->owner_id = $request->input('ownerId');
            $data->actor_id = $request->input('actorId');
            $data->actor_type = $request->input('actorType');
            $data->action = $request->input('action');
            $data->data = $request->input('data');
            $data->description = $request->input('description');
            $data->user_agent = $request->input('userAgent');
            $data->client_timezone = $request->input('clientTimezone');
            $data->save();
            $status = true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response()->json([
            "status" => $status,
            "data"   => $res,
        ]);
    }

    public function visitChat(Request $request)
    {
        //console.log(new Date() + "|" + Intl.DateTimeFormat().resolvedOptions().timeZone)
        $status = false;
        $request->validate([
            'refId' => 'required',
            'ownerId' => 'required',
            'actorId' => 'required',
            'actorType' => 'required',
            'action' => 'required',
            'data' => 'required',
            'description' => 'required',
            'userAgent' => 'required',
            'clientTimezone' => 'required',
        ]);


        $res = [
            "log" => "visitChat",
            "data" => $request->all()
        ];

        // check

        DB::beginTransaction();

        try {
            $data = new LogChat();
            $data->ref_id = $request->input('refId');
            $data->owner_id = $request->input('ownerId');
            $data->actor_id = $request->input('actorId');
            $data->actor_type = $request->input('actorType');
            $data->action = $request->input('action');
            $data->data = $request->input('data');
            $data->description = $request->input('description');
            $data->user_agent = $request->input('userAgent');
            $data->client_timezone = $request->input('clientTimezone');
            $data->save();
            $status = true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response()->json([
            "status" => $status,
            "data"   => $res,
        ]);
    }

    public function visitCategory(Request $request)
    {
        //console.log(new Date() + "|" + Intl.DateTimeFormat().resolvedOptions().timeZone)
        $status = false;
        $request->validate([
            'ownerId' => 'required',
            'actorId' => 'required',
            'actorType' => 'required',
        ]);


        $res = [
            "log" => "visitCategory",
            "data" => $request->all()
        ];

        // check

        DB::beginTransaction();

        try {


            $data = new LogCategory();
            $data->ref_id = $request->input('refId');
            $data->owner_id = $request->input('ownerId');
            $data->actor_id = $request->input('actorId');
            $data->actor_type = $request->input('actorType');
            $data->action = $request->input('action');
            $data->client_timezone = $request->input('clientTimezone');
            $data->save();
            $status = true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        return \response()->json([
            "status" => $status,
            "data"   => $res,
        ]);
    }

    public function getLog(Request $request)
    {

        $request->validate([
            'ownerId'   => 'required',
            'actorId'   => 'nullable',
            'actorType' => 'nullable',
            'logCat'     => 'required', // ex. booth, video or all
            'startDate'  => 'nullable', // ex. 2020-11-30
            'endDate'    => 'nullable'  // ex. 2020-11-30
        ]);

        $startDate = $request->input("startDate", null);
        $endDate = $request->input("endDate", null);

        if (!$startDate) {
            $startDate = date("Y-m-d", strtotime("- 14 day"));
        }

        if (!$endDate) {
            $endDate = date("Y-m-d");
        }

        $startDate = strval($startDate) . " 23:59:59";
        $endDate = strval($endDate) . " 23:59:59";

        // prepare variable and condition
        $logCat = $request->input("logCat", "all");
        $actorId = $request->input("actorId", null);
        $actorType = $request->input("actorType", null);

        $data = [];
        $data["logCat"] = $logCat;
        $data["actorId"] = $actorId;
        $data["actorType"] = $actorType;

        $conditionAll = [
            ["owner_id", "=", $request->input("ownerId")],
        ];

        $conditionScoped = [
            ["owner_id", "=", $request->input("ownerId")],
            ["created_at", ">=", $startDate],
            ["created_at", "<=", $endDate]
        ];

        if ($request->input("actor_id")) {
            array_push($conditionAll, ["actor_id", "=", $actorId]);
            array_push($conditionScoped, ["actor_id", "=", $actorId]);
        }

        if ($request->input("actor_type")) {
            array_push($conditionAll, ["actor_type", "=", $actorType]);
            array_push($conditionScoped, ["actor_type", "=", $actorType]);
        }

        // retrieve data from database
        if ($logCat == "booth" || $logCat == "all") {
            // booth access
            $data["booth"]["stack"] = LogVisitBooths::where($conditionAll)->get()->count();
            $data["booth"]["access"] = LogVisitBooths::where($conditionAll)->groupBy("actor_id")->get()->count();

            $tmp = LogVisitBooths::where($conditionScoped)->groupBy("inDate")
                ->get(array(
                    DB::raw("DATE(created_at) as inDate"),
                    DB::raw("COUNT(*) as n")
                ))->toArray();

            $data["booth"]["dayLabels"] = [];
            $data["booth"]["dayVals"] = [];

            foreach ($tmp as $detail) {
                array_push($data["booth"]["dayLabels"], $detail['inDate']);
                array_push($data["booth"]["dayVals"], $detail['n']);
            }

            array_unshift($data["booth"]["dayLabels"], "All");
            array_unshift($data["booth"]["dayVals"], $data["booth"]["stack"]);
        }

        if ($logCat == "video" || $logCat == "all") {
            // video access
            $data["video"]["stack"] = LogVideo::where($conditionAll)->get()->count();
            $data["video"]["access"] = LogVideo::where($conditionAll)->groupBy('actor_id')->get()->count();

            $tmp = LogVideo::where($conditionScoped)->groupBy("inDate")
                ->get(array(
                    DB::raw("DATE(created_at) as inDate"),
                    DB::raw("COUNT(*) as n")
                ))->toArray();

            $data["video"]["dayLabels"] = [];
            $data["video"]["dayVals"] = [];

            foreach ($tmp as $detail) {
                array_push($data["video"]["dayLabels"], $detail['inDate']);
                array_push($data["video"]["dayVals"], $detail['n']);
            }

            array_unshift($data["video"]["dayLabels"], "All");
            array_unshift($data["video"]["dayVals"], $data["video"]["stack"]);
        }

        if ($logCat == "poster" || $logCat == "all") {
            // poster access
            $data["poster"]["stack"] = LogPoster::where($conditionAll)->get()->count();
            $data["poster"]["access"] = LogPoster::where($conditionAll)->groupBy('actor_id')->get()->count();

            $tmp = LogPoster::where($conditionScoped)->groupBy("inDate")
                ->get(array(
                    DB::raw("DATE(created_at) as inDate"),
                    DB::raw("COUNT(*) as n")
                ))->toArray();

            $data["poster"]["dayLabels"] = [];
            $data["poster"]["dayVals"] = [];

            foreach ($tmp as $detail) {
                array_push($data["poster"]["dayLabels"], $detail['inDate']);
                array_push($data["poster"]["dayVals"], $detail['n']);
            }

            array_unshift($data["poster"]["dayLabels"], "All");
            array_unshift($data["poster"]["dayVals"], $data["poster"]["stack"]);
        }

        if ($logCat == "promotion" || $logCat == "all") {
            // promotion access
            $data["promotion"]["stack"] = LogPromotion::where($conditionAll)->get()->count();
            $data["promotion"]["access"] = LogPromotion::where($conditionAll)->groupBy('actor_id')->get()->count();

            $tmp = LogPromotion::where($conditionScoped)->groupBy("inDate")
                ->get(array(
                    DB::raw("DATE(created_at) as inDate"),
                    DB::raw("COUNT(*) as n")
                ))->toArray();

            $data["promotion"]["dayLabels"] = [];
            $data["promotion"]["dayVals"] = [];

            foreach ($tmp as $detail) {
                array_push($data["promotion"]["dayLabels"], $detail['inDate']);
                array_push($data["promotion"]["dayVals"], $detail['n']);
            }

            array_unshift($data["promotion"]["dayLabels"], "All");
            array_unshift($data["promotion"]["dayVals"], $data["promotion"]["stack"]);
        }

        if ($logCat == "brochure" || $logCat == "all") {
            // brochure access
            $data["brochureView"]["stack"] = LogBrochure::where($conditionAll)->where('action', 'access')->get()->count();
            $data["brochureView"]["access"] = LogBrochure::where($conditionAll)->where('action', 'access')->groupBy('actor_id')->get()->count();

            $tmp = LogBrochure::where($conditionScoped)->where('action', 'access')->groupBy("inDate")
                ->get(array(
                    DB::raw("DATE(created_at) as inDate"),
                    DB::raw("COUNT(*) as n")
                ))->toArray();

            $data["brochureView"]["dayLabels"] = [];
            $data["brochureView"]["dayVals"] = [];

            foreach ($tmp as $detail) {
                array_push($data["brochureView"]["dayLabels"], $detail['inDate']);
                array_push($data["brochureView"]["dayVals"], $detail['n']);
            }

            array_unshift($data["brochureView"]["dayLabels"], "All");
            array_unshift($data["brochureView"]["dayVals"], $data["brochureView"]["stack"]);
        }

        if ($logCat == "brochure_download" || $logCat == "all") {
            // brochure download
            $data["brochureDownload"]["stack"] = LogBrochure::where($conditionAll)->where('action', 'download')->get()->count();
            $data["brochureDownload"]["access"] = LogBrochure::where($conditionAll)->where('action', 'download')->groupBy('actor_id')->get()->count();

            $tmp = LogBrochure::where($conditionScoped)->where('action', 'download')->groupBy("inDate")
                ->get(array(
                    DB::raw("DATE(created_at) as inDate"),
                    DB::raw("COUNT(*) as n")
                ))->toArray();

            $data["brochureDownload"]["dayLabels"] = [];
            $data["brochureDownload"]["dayVals"] = [];

            foreach ($tmp as $detail) {
                array_push($data["brochureDownload"]["dayLabels"], $detail['inDate']);
                array_push($data["brochureDownload"]["dayVals"], $detail['n']);
            }

            array_unshift($data["brochureDownload"]["dayLabels"], "All");
            array_unshift($data["brochureDownload"]["dayVals"], $data["brochureDownload"]["stack"]);
        }

        if ($logCat == "info" || $logCat == "all") {
            // info access
            $data["info"]["stack"] = LogInfo::where($conditionAll)->get()->count();
            $data["info"]["access"] = LogInfo::where($conditionAll)->groupBy('actor_id')->get()->count();

            $tmp = LogInfo::where($conditionScoped)->groupBy("inDate")
                ->get(array(
                    DB::raw("DATE(created_at) as inDate"),
                    DB::raw("COUNT(*) as n")
                ))->toArray();

            $data["info"]["dayLabels"] = [];
            $data["info"]["dayVals"] = [];

            foreach ($tmp as $detail) {
                array_push($data["info"]["dayLabels"], $detail['inDate']);
                array_push($data["info"]["dayVals"], $detail['n']);
            }

            array_unshift($data["info"]["dayLabels"], "All");
            array_unshift($data["info"]["dayVals"], $data["info"]["stack"]);
        }

        if ($logCat == "chat" || $logCat == "all") {
            $data["chat"]["stack"] = LogChat::where($conditionAll)->get()->count();
            $data["chat"]["access"] = LogChat::where($conditionAll)->groupBy('actor_id')->get()->count();

            $tmp = LogChat::where($conditionScoped)->groupBy("inDate")
                ->get(array(
                    DB::raw("DATE(created_at) as inDate"),
                    DB::raw("COUNT(*) as n")
                ))->toArray();

            $data["chat"]["dayLabels"] = [];
            $data["chat"]["dayVals"] = [];

            foreach ($tmp as $detail) {
                array_push($data["chat"]["dayLabels"], $detail['inDate']);
                array_push($data["chat"]["dayVals"], $detail['n']);
            }

            array_unshift($data["chat"]["dayLabels"], "All");
            array_unshift($data["chat"]["dayVals"], $data["chat"]["stack"]);
        }

        return \response()->json([
            $data
        ], 200);
    }

    public function getUserVisitBooth()
    {

        $data = LogVisitBooths::where("owner_id", "=", 1)->get();

        // $data = LogVisitBooths::where("owner_id","=",1)->with(['exhibitor' => function($query){
        //     $query->with(['mainCate']);
        // }])->get();
        // $temp = [];

        foreach ($data as $key => $value) {
            $temp[$key]["data"] = $value;
            if ($value->actor_type === "exhibitor") {
                $temp[$key]["exhibitor"] = $value->exhibitor;
            } else {
                $temp[$key]["visitor"] = $value->visitor;
            }
            // array_push()
            // $value-
        }

        return $data;
    }

    public function getUserActivityLog(Request $request)
    {
        $request->validate([
            "ownerId" => "required",
            "countryId" => "nullable",
            "mainCateId" => "nullable",
            "typeId" => "nullable",
            "search" => "nullable",
        ]);

        $ownerId = $request->input("ownerId");
        $countryId = $request->input("countryId", null);
        $mainCateId = $request->input("mainCateId", null);
        $typeId = $request->input("typeId", null);
        $search = $request->input("search", null);

        // collect all actor_id interact with this owner_id
        $targetExhibitorId = LogVisitBooths::where("owner_id", $ownerId)
            ->where("actor_type", "exhibitor")
            ->distinct()
            ->pluck("actor_id");

        $targetRegisterId = LogVisitBooths::where("owner_id", $ownerId)
            ->where("actor_type", "visitor")
            ->distinct()
            ->pluck("actor_id");

        $exhibitorId = $ownerId;
        $brochureListId = BrochureList::where('exhibitor_id', $exhibitorId)->pluck("id");

        $data = [];
        $exhibitor = [];
        $register = [];
        $dataIndex = 1;
        if ($typeId == 2 || !$typeId) {
            $query = Exhibitor::select(
                "*",
                DB::raw('"Exhibitor" as type_str')
            )
                ->whereIn("id", $targetExhibitorId)
                ->with([
                    "country",
                    "mainCateRef",
                    "brochureBag" => function ($query) use ($brochureListId) {
                        $query
                            ->join("brochure_list", "brochure_list.id", "=", "brochure_bag.brochure_id")
                            ->select(
                                "brochure_bag.id",
                                "acc_id",
                                "brochure_bag.brochure_id",
                                "brochure_list.info as name",
                                "brochure_bag.created_at",
                            )
                            ->whereIn("brochure_id", $brochureListId);
                    },
                    "logVisitBooths" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logVideo" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logPoster" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logPromotion" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logBrochureAccess" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logBrochureDownload" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logInfo" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logChat" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "lastVisitBooth"
                ]);

            // filter
            if ($countryId) {
                $query->where("exhibitor_list.country_id", $countryId);
            }

            if ($mainCateId) {
                $query->whereHas("mainCateRef", function ($query) use ($mainCateId) {
                    $query->where("main_category.id", $mainCateId);
                });
            }

            if ($search) {
                $query
                    ->where(function ($query) use ($search) {
                        $query
                            ->Where("exhibitor_list.name", "LIKE", "%{$search}%")
                            ->orWhereHas("country", function ($query) use ($search) {
                                $query->where("countries.name", "LIKE", "%{$search}%");
                            })
                            ->orWhere("exhibitor_list.company", "LIKE", "%{$search}%")
                            ->orWhere("exhibitor_list.position", "LIKE", "%{$search}%");
                    });
            }

            $exhibitor = $query->get()->toArray();
        }

        if ($typeId == 1 || !$typeId) {
            $query = Register::select(
                "*",
                DB::raw('"Visitor" as type_str')
            )
                ->whereIn("id", $targetRegisterId)
                ->where("allow_accept", "Y")
                ->with([
                    "country",
                    "mainCateRef",
                    "brochureBag" => function ($query) use ($brochureListId) {
                        $query
                            ->join("brochure_list", "brochure_list.id", "=", "brochure_bag.brochure_id")
                            ->select(
                                "brochure_bag.id",
                                "acc_id",
                                "brochure_bag.brochure_id",
                                "brochure_list.info as name",
                                "brochure_bag.created_at",
                            )
                            ->whereIn("brochure_id", $brochureListId);
                    },
                    "logVisitBooths" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logVideo" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logPoster" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logPromotion" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logBrochureAccess" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logBrochureDownload" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logInfo" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "logChat" => function ($query) use ($ownerId) {
                        $query->where("owner_id", $ownerId);
                    },
                    "lastVisitBooth"
                ]);

            // filter
            if ($countryId) {
                $query->where("registers.country", $countryId);
            }

            if ($mainCateId) {
                $query->whereHas("mainCateRef", function ($query) use ($mainCateId) {
                    $query->where("main_category.id", $mainCateId);
                });
            }

            if ($search) {
                $query
                    ->where(function ($query) use ($search) {
                        $query
                            ->orWhere("registers.fname", "LIKE", "%{$search}%")
                            ->orWhere("registers.lname", "LIKE", "%{$search}%")
                            ->orWhereHas("country", function ($query) use ($search) {
                                $query->where("countries.name", "LIKE", "%{$search}%");
                            })
                            ->orWhere("registers.company", "LIKE", "%{$search}%")
                            ->orWhere("registers.position", "LIKE", "%{$search}%");
                    });
            }

            $register = $query->get()->toArray();
        }

        // format data
        foreach ($exhibitor as $detail) {
            $tmp = new DateTime($detail["last_visit_booth"]["created_at"]);
            $tmp->modify("+7 hours");
            $lastVisitedTime = $tmp->format("Y-m-d H:i:s");

            array_push($data, [
                "index" => $dataIndex,
                "id" => $detail["id"],
                "name" => $detail["name"],
                "profileImage" => "",
                "company" => $detail["company"],
                "position" => $detail["position"],
                "cateInterest" => $detail["main_cate_ref"],
                "countryCode" => strtolower($detail["country"]["code"]),
                "countryName" => $detail["country"]["name"],
                "type" => $detail["type_str"],
                "lastVisitAt" => $lastVisitedTime,
                "brochureDownloadList" => $detail["brochure_bag"],
                "activityList" => array_merge(
                    $detail["log_visit_booths"] ? $detail["log_visit_booths"] : [],
                    $detail["log_video"] ? $detail["log_video"] : [],
                    $detail["log_poster"] ? $detail["log_poster"] : [],
                    $detail["log_promotion"] ? $detail["log_promotion"] : [],
                    $detail["log_brochure_access"] ? $detail["log_brochure_access"] : [],
                    $detail["log_brochure_download"] ? $detail["log_brochure_download"] : [],
                    $detail["log_info"] ? $detail["log_info"] : [],
                    $detail["log_chat"] ? $detail["log_chat"] : [],
                )
            ]);

            usort(end($data)["activityList"], function ($obj1, $obj2) {
                if ($obj1["created_at"] == $obj2["created_at"]) {
                    return (0);
                }
                return (($obj1["created_at"] > $obj2["created_at"]) ? -1 : 1);
            });

            $dataIndex += 1;
        }

        // format data
        foreach ($register as $detail) {
            $tmp = new DateTime($detail["last_visit_booth"]["created_at"]);
            $tmp->modify("+7 hours");
            $lastVisitedTime = $tmp->format("Y-m-d H:i:s");

            array_push($data, [
                "index" => $dataIndex,
                "id" => $detail["id"],
                "name" => $detail["fname"] . " " . $detail["lname"],
                "profileImage" => "",
                "company" => $detail["company"],
                "position" => $detail["position"],
                "cateInterest" => $detail["main_cate_ref"],
                "countryCode" => strtolower($detail["country"]["code"]),
                "countryName" => $detail["country"]["name"],
                "type" => $detail["type_str"],
                "lastVisitAt" => $lastVisitedTime,
                "brochureDownloadList" => $detail["brochure_bag"],
                "activityList" => array_merge(
                    $detail["log_visit_booths"] ? $detail["log_visit_booths"] : [],
                    $detail["log_video"] ? $detail["log_video"] : [],
                    $detail["log_poster"] ? $detail["log_poster"] : [],
                    $detail["log_promotion"] ? $detail["log_promotion"] : [],
                    $detail["log_brochure_access"] ? $detail["log_brochure_access"] : [],
                    $detail["log_brochure_download"] ? $detail["log_brochure_download"] : [],
                    $detail["log_info"] ? $detail["log_info"] : [],
                    $detail["log_chat"] ? $detail["log_chat"] : [],
                )
            ]);

            usort(end($data)["activityList"], function ($obj1, $obj2) {
                if ($obj1["created_at"] == $obj2["created_at"]) {
                    return (0);
                }
                return (($obj1["created_at"] > $obj2["created_at"]) ? -1 : 1);
            });
            $dataIndex += 1;
        }

        // sorting
        usort($data, function ($obj1, $obj2) {
            if ($obj1["lastVisitAt"] == $obj2["lastVisitAt"]) {
                return (0);
            }
            return (($obj1["lastVisitAt"] > $obj2["lastVisitAt"]) ? -1 : 1);
        });

        // add index to brochureDownloadList/activityList
        for ($i = 0; $i < count($data); $i++) {
            $tmpIndex = 1;
            for ($k = 0; $k < count($data[$i]["brochureDownloadList"]); $k++) {
                $data[$i]["brochureDownloadList"][$k]["index"] = $tmpIndex;
                $tmpIndex += 1;
            }

            $tmpIndex = 1;
            for ($k = 0; $k < count($data[$i]["activityList"]); $k++) {
                $data[$i]["activityList"][$k]["index"] = $tmpIndex;
                $tmpIndex += 1;
            }
        }

        return response()->json([
            $data
        ], 200);
    }

    public function visitedTrafficLogExport(Request $request)
    {
        $request->validate([
            "ownerId" => "required",
            "countryId" => "nullable",
            "mainCateId" => "nullable",
            "typeId" => "nullable",
            "search" => "nullable",
        ]);

        $ownerId = $request->input("ownerId");
        $countryId = $request->input("countryId", null);
        $mainCateId = $request->input("mainCateId", null);
        $typeId = $request->input("typeId", null);
        $search = $request->input("search", null);

        $today = date('YmdHis');
        // return Excel::download(new TrafficLogExportV2($ownerId), 'traffic_log_' . $today . '.xlsx');
        return Excel::download(new TrafficLogExport(
            $ownerId,
            $countryId,
            $mainCateId,
            $typeId,
            $search
        ), 'traffic_log_' . $today . '.xlsx');
    }

    public function getVisitorLoginLogCount(Request $request)
    {

        $request->validate([
            "unique" => "nullable",
            "orderBy" => "nullable"
        ]);

        $unique = $request->input("unique", false);
        $orderBy = $request->input("orderBy", "ASC");

        $startDate = "2020-11-23";

        if ($unique) {
            $query = "
            select
                inDate,
                COUNT(*) as n
            from
                (
                select
                    DATE(created_at) as inDate, COUNT(*) as n, visitor_id
                from
                    visitor_login_log
                where
                    success = 'Y'
                and
                    visitor_id is not null
                group by
                    inDate, visitor_id ) as tbl1
            group by
                inDate
            having inDate >= '$startDate'
            order by inDate $orderBy
            ";
        } else {
            $query = "
                select
                    DATE(created_at) as inDate,
                    COUNT(visitor_id) as n
                from
                    visitor_login_log
                where
                    success = 'Y'
                and
                    visitor_id is not null
                group by
                    inDate
                having inDate >= '$startDate'
                order by inDate $orderBy
            ";
        }

        $data = DB::select($query);

        // all day
        $countAll = 0;
        foreach ($data as $detail) {
            $countAll += $detail->n;
        }

        // all unique
        $query = "
        select
            count(distinct visitor_id) as n
        from
            visitor_login_log
        where
            success = 'Y'
            and Date(created_at) >= '$startDate'
        ";
        $countAllUnique = DB::select($query);

        // add all day unique
        array_unshift($data, [
            "inDate" => "All Unique",
            "n" => $countAllUnique[0]->n
        ]);

        // add all day
        array_unshift($data, [
            "inDate" => "All",
            "n" => $countAll
        ]);

        return response()->json([
            $data
        ], 200);
    }

    public function getExhibitorLoginLogCount(Request $request)
    {

        $request->validate([
            "unique" => "nullable",
            "orderBy" => "nullable"
        ]);

        $unique = $request->input("unique", false);
        $orderBy = $request->input("orderBy", "ASC");

        $startDate = "2020-11-23";

        if ($unique) {
            $query = "
                select
                    inDate,
                    COUNT(*) as n
                from
                    (
                    select
                        DATE(created_at) as inDate, COUNT(*) as n, exhibitor_id
                    from
                        exhibitor_login_log
                    where
                        success = 'Y'
                    and
                        exhibitor_id is not null
                    group by
                        inDate, exhibitor_id ) as tbl1
                group by
                    inDate
                having inDate >= '$startDate'
                order by inDate $orderBy
            ";
        } else {
            $query = "
                select
                    DATE(created_at) as inDate,
                    COUNT(exhibitor_id) as n
                from
                    exhibitor_login_log
                where
                    success = 'Y'
                and
                    exhibitor_id is not null
                group by
                    inDate
                having inDate >= '$startDate'
                order by inDate $orderBy
            ";
        }


        $data = DB::select($query);

        // all day
        $countAll = 0;
        foreach ($data as $detail) {
            $countAll += $detail->n;
        }

        // all unique
        $query = "
        select
            count(distinct exhibitor_id) as n
        from
            exhibitor_login_log
        where
            success = 'Y'
            and Date(created_at) >= '$startDate'
        ";
        $countAllUnique = DB::select($query);

        // add all day
        array_unshift($data, [
            "inDate" => "All Unique",
            "n" => $countAllUnique[0]->n
        ]);

        // add all day unique
        array_unshift($data, [
            "inDate" => "All",
            "n" => $countAll
        ]);

        return response()->json([
            $data
        ], 200);
    }

    public function getBothLoginLogCount(Request $request)
    {
        $request->validate([
            "unique" => "nullable",
            "orderBy" => "nullable"
        ]);

        $unique = $request->input("unique", false);
        $orderBy = $request->input("orderBy", "ASC");

        $startDate = "2020-11-23";

        if ($unique) {
            $query = "
                select inDate, sum(n) as n
                from
                (
                select
                    inDate,
                    COUNT(*) as n
                from
                    (
                    select
                        DATE(created_at) as inDate, COUNT(*) as n, visitor_id
                    from
                        visitor_login_log
                    where
                        success = 'Y'
                    and
                        visitor_id is not null
                    group by
                        inDate, visitor_id ) as tbl1
                group by
                    inDate
                UNION ALL
                select
                    inDate,
                    COUNT(*) as n
                from
                    (
                    select
                        DATE(created_at) as inDate, COUNT(*) as n, exhibitor_id
                    from
                        exhibitor_login_log
                    where
                        success = 'Y'
                    and
                        exhibitor_id is not null
                    group by
                        inDate, exhibitor_id ) as tbl1
                group by
                    inDate
                ) AS tbl2
                group by
                    inDate
                having inDate >= '$startDate'
                order by inDate $orderBy
            ";
        } else {
            $query = "
                select
                    inDate,
                    SUM(n) as n
                from
                    (
                    select
                        DATE(created_at) as inDate, COUNT(exhibitor_id) as n
                    from
                        exhibitor_login_log
                    where
                        success = 'Y'
                    group by
                        inDate
                UNION ALL (
                    select
                        DATE(created_at) as inDate,
                        COUNT(visitor_id) as n
                    from
                        visitor_login_log
                    where
                        success = 'Y'
                    group by
                        inDate ) ) as tbl2
                group by
                    inDate
                having inDate >= '$startDate'
                order by inDate $orderBy
            ";
        }


        $data = DB::select($query);

        // all day
        $countAll = 0;
        foreach ($data as $detail) {
            $countAll += $detail->n;
        }

        // all unique
        $query = "
        select
            SUM(userType = 'visitor') + SUM(userType = 'exhibitor') as n
        from
            (
            select
                visitor_id, 'visitor' as userType
            from
                visitor_login_log
            where
                success = 'Y'
                and Date(created_at) >= '$startDate'
            group by visitor_id, userType
        UNION ALL
            select
                exhibitor_id, 'exhibitor' as userType
            from
                exhibitor_login_log
            where
                success = 'Y'
                and Date(created_at) >= '$startDate'
            group by exhibitor_id, userType ) tbl1
        ";
        $countAllUnique = DB::select($query);

        // add all day
        array_unshift($data, [
            "inDate" => "All Unique",
            "n" => $countAllUnique[0]->n
        ]);

        // add all day unique
        array_unshift($data, [
            "inDate" => "All",
            "n" => $countAll
        ]);

        return response()->json([
            $data
        ], 200);
    }

    public function getMostVisitBooth(Request $request)
    {
        $startDate = "2020-11-23";

        $query = "
        select
            name,
            company,
            count(*) as n
        from
            log_visit_booths
        join exhibitor_list
        on owner_id = exhibitor_list.id
        where
            Date(log_visit_booths.created_at) >= '$startDate'
        group by
            name,
            company
        order by
            owner_id
        ";

        $queryUnique = "
        select
            name,
            company,
            count(actor_id) as n
        from
            (
            select
                distinct name, company, actor_id, actor_type , owner_id
            from
                log_visit_booths
            inner join exhibitor_list on
                exhibitor_list.id = log_visit_booths.ref_id
                and Date(log_visit_booths.created_at) >= '$startDate') as tmp
        group by
            name,
            company
        order by
            owner_id
        ";


        $countAll = DB::select($query);
        $countUnique = DB::select($queryUnique);

        $data = [];
        for ($i = 0; $i < count($countAll); $i++) {
            array_push($data, [
                "name" => $countAll[$i]->name,
                "company" => $countAll[$i]->company,
                "totalAll" => $countAll[$i]->n,
                "totalUnique" => $countUnique[$i]->n
            ]);
        }

        usort($data, function ($obj1, $obj2) {
            if ($obj1["totalAll"] == $obj2["totalAll"]) {
                return (0);
            }
            return (($obj1["totalAll"] > $obj2["totalAll"]) ? -1 : 1);
        });

        // add index
        $index = 1;
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["index"] = $index;
            $index += 1;
        }

        return response()->json([
            $data
        ], 200);
    }

    public function getMostContentView(Request $request)
    {
        $startDate = "2020-11-23";

        $queryAll = "
        select
            name,
            company,
            info,
            'Video' as content_type,
            count(*) as n
        from
            (
            select
                ref_id, owner_id, actor_id, actor_type
            from
                log_video
            where
                Date(created_at) >= '$startDate' ) tbl1
        join video_list on
            ref_id = video_list.id
        join exhibitor_list on
            exhibitor_list.id = owner_id
        group by
            ref_id
        order by name, company, info
        ";
        $videoLogCountAll = DB::select($queryAll);

        $queryUnique = "
        select
            name,
            company,
            info,
            'Video' as content_type,
            count(*) as n
        from
            (
            select distinct
                ref_id, owner_id, actor_id, actor_type
            from
                log_video
            where
                Date(created_at) >= '$startDate' ) tbl1
        join video_list on
            ref_id = video_list.id
        join exhibitor_list on
            exhibitor_list.id = owner_id
        group by
            ref_id
        order by name, company, info
        ";
        $videoLogCountUnique = DB::select($queryUnique);

        $queryAll = "
        select
            name,
            company,
            info,
            'Poster' as content_type,
            count(*) as n
        from
            (
            select
                ref_id, owner_id, actor_id, actor_type
            from
                log_poster
            where
                Date(created_at) >= '$startDate' ) tbl1
        join eposter_list on
            ref_id = eposter_list.id
        join exhibitor_list on
            exhibitor_list.id = owner_id
        group by
            ref_id
        order by name, company, info
        ";
        $posterLogCountAll = DB::select($queryAll);

        $queryUnique = "
        select
            name,
            company,
            info,
            'Poster' as content_type,
            count(*) as n
        from
            (
            select distinct
                ref_id, owner_id, actor_id, actor_type
            from
                log_poster
            where
                Date(created_at) >= '2020-11-23' ) tbl1
        join eposter_list on
            ref_id = eposter_list.id
        join exhibitor_list on
            exhibitor_list.id = owner_id
        group by
            ref_id
        order by name, company, info
        ";
        $posterLogCountUnique = DB::select($queryUnique);

        $queryAll = "
        select
            name,
            company,
            info,
            'Promotion' as content_type,
            count(*) as n
        from
            (
            select
                ref_id, owner_id, actor_id, actor_type
            from
                log_promotion
            where
                Date(created_at) >= '2020-11-23' ) tbl1
        join promotion_list on
            ref_id = promotion_list.id
        join exhibitor_list on
            exhibitor_list.id = owner_id
        group by
            ref_id
        order by name, company, info
        ";
        $promotionLogCountAll = DB::select($queryAll);

        $queryUnique = "
        select
            name,
            company,
            info,
            'Promotion' as content_type,
            count(*) as n
        from
            (
            select distinct
                ref_id, owner_id, actor_id, actor_type
            from
                log_promotion
            where
                Date(created_at) >= '2020-11-23' ) tbl1
        join promotion_list on
            ref_id = promotion_list.id
        join exhibitor_list on
            exhibitor_list.id = owner_id
        group by
            ref_id
        order by name, company, info
        ";
        $promotionLogCountUnique = DB::select($queryUnique);

        $queryAll = "
        select
            name,
            company,
            info,
            'Brochure' as content_type,
            count(*) as n
        from
            (
            select
                ref_id, owner_id, actor_id, actor_type
            from
                log_brochure
            where
                Date(created_at) >= '$startDate' ) tbl1
        join brochure_list on
            ref_id = brochure_list.id
        join exhibitor_list on
            exhibitor_list.id = owner_id
        group by
            ref_id
        order by name, company, info
        ";
        $brochureLogCountAll = DB::select($queryAll);

        $queryUnique = "
        select
            name,
            company,
            info,
            'Brochure' as content_type,
            count(*) as n
        from
            (
            select distinct
                ref_id, owner_id, actor_id, actor_type
            from
                log_brochure
            where
                Date(created_at) >= '$startDate' ) tbl1
        join brochure_list on
            ref_id = brochure_list.id
        join exhibitor_list on
            exhibitor_list.id = owner_id
        group by
            ref_id
        order by name, company, info
        ";

        $brochureLogCountUnique = DB::select($queryUnique);

        // format data
        $data = [];
        for ($i = 0; $i < count($videoLogCountAll); $i++) {
            array_push($data, [
                "name" => $videoLogCountAll[$i]->name,
                "company" => $videoLogCountAll[$i]->company,
                "info" => $videoLogCountAll[$i]->info,
                "contentType" => $videoLogCountAll[$i]->content_type,
                "totalAll" => $videoLogCountAll[$i]->n,
                "totalUnique" => $videoLogCountUnique[$i]->n
            ]);
        }

        for ($i = 0; $i < count($posterLogCountAll); $i++) {
            array_push($data, [
                "name" => $posterLogCountAll[$i]->name,
                "company" => $posterLogCountAll[$i]->company,
                "info" => $posterLogCountAll[$i]->info,
                "contentType" => $posterLogCountAll[$i]->content_type,
                "totalAll" => $posterLogCountAll[$i]->n,
                "totalUnique" => $posterLogCountUnique[$i]->n
            ]);
        }

        for ($i = 0; $i < count($promotionLogCountAll); $i++) {
            array_push($data, [
                "name" => $promotionLogCountAll[$i]->name,
                "company" => $promotionLogCountAll[$i]->company,
                "info" => $promotionLogCountAll[$i]->info,
                "contentType" => $promotionLogCountAll[$i]->content_type,
                "totalAll" => $promotionLogCountAll[$i]->n,
                "totalUnique" => $promotionLogCountUnique[$i]->n
            ]);
        }

        for ($i = 0; $i < count($brochureLogCountAll); $i++) {
            array_push($data, [
                "name" => $brochureLogCountAll[$i]->name,
                "company" => $brochureLogCountAll[$i]->company,
                "info" => $brochureLogCountAll[$i]->info,
                "contentType" => $brochureLogCountAll[$i]->content_type,
                "totalAll" => $brochureLogCountAll[$i]->n,
                "totalUnique" => $brochureLogCountUnique[$i]->n
            ]);
        }

        usort($data, function ($obj1, $obj2) {
            if ($obj1["totalAll"] == $obj2["totalAll"]) {
                return (0);
            }
            return (($obj1["totalAll"] > $obj2["totalAll"]) ? -1 : 1);
        });

        // add index
        $index = 1;
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["index"] = $index;
            $index += 1;
        }

        return response()->json([
            $data
        ], 200);
    }

    public function getBackOfficeLoginLog(Request $request)
    {

        $request->validate([
            "countryId" => "nullable",
            "mainCateId" => "nullable",
            "typeId" => "nullable",
            "search" => "nullable",
            "startDate" => "nullable",
            "endDate" => "nullable"
        ]);

        $countryId = $request->input("countryId", null);
        $mainCateId = $request->input("mainCateId", null);
        $typeId = $request->input("typeId", null);
        $search = $request->input("search", null);
        $startDate = $request->input("startDate", null);
        $endDate = $request->input("endDate", null);

        if ($countryId == -99) {
            $countryId = Countries::where("id", "!=", 217)->pluck("id");
        } else if ($countryId == 217) {
            $countryId = [$countryId];
        }

        $query = VisitorLogin::where("success", "Y");
        if ($startDate && $endDate) {
            $query = $query->whereRaw(DB::raw("Date(created_at) Between '$startDate' and '$endDate'"));
        } else if ($startDate) {
            $query = $query->whereRaw(DB::raw("Date(created_at) = '$startDate'"));
        }

        $targetVisitorId = $query->select("visitor_id")
            ->distinct()
            ->pluck("visitor_id");

        $query = ExhibitorLogin::where("success", "Y");
        if ($startDate && $endDate) {
            $query = $query->whereRaw(DB::raw("Date(created_at) Between '$startDate' and '$endDate'"));
        } else if ($startDate) {
            $query = $query->whereRaw(DB::raw("Date(created_at) = '$startDate'"));
        }

        $targetExhibitorId = $query->select("exhibitor_id")
            ->distinct()
            ->pluck("exhibitor_id");

        $data = [];
        $exhibitor = [];
        $register = [];

        if ($typeId == 2 || !$typeId) {
            $query = Exhibitor::select(
                "*",
                DB::raw('"Exhibitor" as type_str')
            )
                ->whereIn("id", $targetExhibitorId)
                ->with([
                    "country",
                    "mainCateRef",
                    "lastLogIn" => function ($query) use ($startDate, $endDate) {
                        if ($startDate && $endDate) {
                            $query->whereRaw(DB::raw("Date(created_at) Between '$startDate' and '$endDate'"));
                        } else if ($startDate) {
                            $query->whereRaw(DB::raw("Date(created_at) = '$startDate'"));
                        }
                    }
                ]);

            // filter
            if ($countryId) {
                $query->whereIn("exhibitor_list.country_id", $countryId);
            }

            if ($mainCateId) {
                $query->whereHas("mainCateRef", function ($query) use ($mainCateId) {
                    $query->where("main_cate_id", $mainCateId);
                });
            }

            if ($search) {
                $query
                    ->where(function ($query) use ($search) {
                        $query
                            ->Where("exhibitor_list.name", "LIKE", "%{$search}%")
                            ->orWhereHas("country", function ($query) use ($search) {
                                $query->where("countries.name", "LIKE", "%{$search}%");
                            })
                            ->orWhere("exhibitor_list.company", "LIKE", "%{$search}%")
                            ->orWhere("exhibitor_list.position", "LIKE", "%{$search}%");
                    });
            }

            $exhibitor = $query->get()->toArray();
        }

        if ($typeId == 1 || !$typeId) {
            $query = Register::select(
                "*",
                DB::raw('"Visitor" as type_str')
            )
                ->whereIn("id", $targetVisitorId)
                ->with([
                    "country",
                    "mainCateRef",
                    "lastLogIn" => function ($query) use ($startDate, $endDate) {
                        if ($startDate && $endDate) {
                            $query->whereRaw(DB::raw("Date(created_at) Between '$startDate' and '$endDate'"));
                        } else if ($startDate) {
                            $query->whereRaw(DB::raw("Date(created_at) = '$startDate'"));
                        }
                    }
                ]);

            // filter
            if ($countryId) {
                $query->where("registers.country", $countryId);
            }

            if ($mainCateId) {
                $query->whereHas("mainCateRef", function ($query) use ($mainCateId) {
                    $query->where("main_cate_id", $mainCateId);
                });
            }

            if ($search) {
                $query
                    ->where(function ($query) use ($search) {
                        $query
                            ->orWhere("registers.fname", "LIKE", "%{$search}%")
                            ->orWhere("registers.lname", "LIKE", "%{$search}%")
                            ->orWhereHas("country", function ($query) use ($search) {
                                $query->where("countries.name", "LIKE", "%{$search}%");
                            })
                            ->orWhere("registers.company", "LIKE", "%{$search}%")
                            ->orWhere("registers.position", "LIKE", "%{$search}%");
                    });
            }

            $register = $query->get()->toArray();
        }

        // format data
        foreach ($exhibitor as $detail) {
            $tmp = new DateTime($detail["last_log_in"]["created_at"]);
            $tmp->modify("+7 hours");
            $lastLoginTime = $tmp->format("Y-m-d H:i:s");

            array_push($data, [
                "id" => $detail["id"],
                "profileImage" => "",
                "name" => $detail["name"],
                "company" => $detail["company"],
                "position" => $detail["position"],
                "cateInterest" => $detail["main_cate_ref"],
                "countryName" => $detail["country"]["name"],
                "countryCode" => strtolower($detail["country"]["code"]),
                "type" => $detail["type_str"],
                "telephone" => $detail["m_mobile"],
                "mobile" => $detail["mobile"],
                "address" => $detail["address"],
                "website" => $detail["website"],
                "email" => $detail["email"],
                "lastLoginTime" => $lastLoginTime
            ]);
        }

        foreach ($register as $detail) {
            $tmp = new DateTime($detail["last_log_in"]["created_at"]);
            $tmp->modify("+7 hours");
            $lastLoginTime = $tmp->format("Y-m-d H:i:s");

            array_push($data, [
                "id" => $detail["id"],
                "profileImage" => "",
                "name" => $detail["fname"] . " " . $detail["lname"],
                "company" => $detail["company"],
                "position" => $detail["position"],
                "cateInterest" => $detail["main_cate_ref"],
                "countryName" => $detail["country"]["name"],
                "countryCode" => strtolower($detail["country"]["code"]),
                "type" => $detail["type_str"],
                "telephone" => $detail["telephone"],
                "mobile" => $detail["mobile"],
                "address" => $detail["address"],
                "website" => $detail["website"],
                "email" => $detail["email"],
                "lastLoginTime" => $lastLoginTime
            ]);
        }

        usort($data, function ($obj1, $obj2) {
            if ($obj1["lastLoginTime"] == $obj2["lastLoginTime"]) {
                return (0);
            }
            return (($obj1["lastLoginTime"] > $obj2["lastLoginTime"]) ? -1 : 1);
        });

        // add index
        $dataIndex = 1;
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["index"] = $dataIndex;
            $dataIndex += 1;
        }

        return response()->json([
            $data,
            $mainCateId,
            $typeId,
            $search
        ], 200);
    }

    public function backOfficeLoginLogExport(Request $request)
    {
        $request->validate([
            "countryId" => "nullable",
            "mainCateId" => "nullable",
            "typeId" => "nullable",
            "search" => "nullable",
            "startDate" => "nullable",
            "endDate" => "nullable"
        ]);

        $countryId = $request->input("countryId", null);
        $mainCateId = $request->input("mainCateId", null);
        $typeId = $request->input("typeId", null);
        $search = $request->input("search", null);
        $startDate = $request->input("startDate", null);
        $endDate = $request->input("endDate", null);

        $today = date('YmdHis');
        return Excel::download(new BackOfficeLoginHistoryExport(
            $countryId,
            $mainCateId,
            $typeId,
            $search,
            $startDate,
            $endDate
        ), 'backoffice_login_log_' . $today . '.xlsx');
    }

    public function getLoginCountEachCountry(Request $request)
    {
        $request->validate([
            "orderBy" => "nullable"
        ]);

        $orderBy = $request->input("orderBy", "ASC");

        $startDate = "2020-11-23";

        // // with exhibitor
        // $query = "
        // select
        //     inDate,
        //     sum(count_all) as count_all,
        //     sum(count_local) as count_local,
        //     sum(count_inter) as count_inter
        // from
        //     (
        //     select
        //         inDate, count(*) as count_all, SUM(country = 217) as count_local, SUM(country != 217) as count_inter
        //     from
        //         (
        //         select
        //             registers.*, success, visitor_id , Date(visitor_login_log.created_at) as inDate
        //         from
        //             visitor_login_log
        //         inner join registers on
        //             visitor_id = registers.id
        //         group by
        //             visitor_id, indate
        //         having
        //             success = 'Y') as tbl1
        //     group by
        //         inDate
        // UNION ALL
        //     select
        //         inDate, count(*) as count_all, SUM(country_id = 217) as count_local, SUM(country_id != 217) as count_inter
        //     from
        //         (
        //         select
        //             exhibitor_list.*, success, exhibitor_id , Date(exhibitor_login_log.created_at) as inDate
        //         from
        //             exhibitor_login_log
        //         inner join exhibitor_list on
        //             exhibitor_id = exhibitor_list.id
        //         group by
        //             exhibitor_id, indate
        //         having
        //             success = 'Y') as tbl1
        //     group by
        //         inDate ) as tmp
        // group by
        //     inDate
        // having inDate >= '$startDate'
        // order by inDate $orderBy
        // ";

        // without exhibitor
        $query = "
        select
            inDate,
            sum(count_all) as count_all,
            sum(count_local) as count_local,
            sum(count_inter) as count_inter
        from
            (
            select
                inDate, count(*) as count_all, SUM(country = 217) as count_local, SUM(country != 217) as count_inter
            from
                (
                select
                    registers.*, success, visitor_id , Date(visitor_login_log.created_at) as inDate
                from
                    visitor_login_log
                inner join registers on
                    visitor_id = registers.id
                group by
                    visitor_id, indate
                having
                    success = 'Y') as tbl1
            group by
                inDate ) as tmp
        group by
            inDate
        having inDate >= '$startDate'
        order by inDate $orderBy
        ";

        $data = DB::select($query);

        // all day
        $countLocal = 0;
        $countInter = 0;
        foreach ($data as $detail) {
            $countLocal += intval($detail->count_local);
            $countInter += intval($detail->count_inter);
        }

        // all unique
        // // with exhibitor
        // $query = "
        // select
        //     count(*) as count_all,
        //     sum(country_id = 217) as count_local,
        //     sum(country_id != 217) as count_inter
        // from
        //     (
        //     select
        //         visitor_id as target_id, 'visitor' as userType, registers.country as country_id
        //     from
        //         visitor_login_log
        //     inner join registers on
        //         visitor_id = registers.id
        //     where
        //         success = 'Y'
        //     and Date(visitor_login_log.created_at) >= '$startDate'
        //     group by
        //         visitor_id, userType
        // UNION ALL
        //     select
        //         exhibitor_id as target_id, 'exhibitor' as userType, exhibitor_list.country_id
        //     from
        //         exhibitor_login_log
        //     inner join exhibitor_list on
        //         exhibitor_id = exhibitor_list.id
        //     where
        //         success = 'Y'
        //     and Date(exhibitor_login_log.created_at) >= '$startDate'
        //     group by
        //         exhibitor_id, userType
        //     ) tbl
        // ";

        // without exhibitor
        $query = "
        select
            count(*) as count_all,
            sum(country_id = 217) as count_local,
            sum(country_id != 217) as count_inter
        from
            (
            select
                visitor_id as target_id, 'visitor' as userType, registers.country as country_id
            from
                visitor_login_log
            inner join registers on
                visitor_id = registers.id
            where
                success = 'Y'
            and Date(visitor_login_log.created_at) >= '$startDate'
            group by
                visitor_id, userType ) tbl
        ";
        $countAllUniqueDetail = DB::select($query)[0];

        // add all day unique
        array_unshift($data, [
            "inDate" => "All Unique",
            "count_all" => $countAllUniqueDetail->count_all,
            "count_local" => $countAllUniqueDetail->count_local,
            "count_inter" => $countAllUniqueDetail->count_inter
        ]);

        // add all day
        array_unshift($data, [
            "inDate" => "All",
            "count_all" => $countLocal + $countInter,
            "count_local" => $countLocal,
            "count_inter" => $countInter
        ]);

        return response()->json([
            $data
        ], 200);
    }

    public function webinarVisitedListExport(Request $request)
    {
        $request->validate([
            "visitorId" => "nullable",
            "exhibitorId" => "nullable"
        ]);

        $visitorTargetId = explode(",", $request->input("visitorId"));
        $exhibitorTargetId = explode(",", $request->input("exhibitorId"));

        $today = date('YmdHis');
        return Excel::download(new WebinarHistoryLogExport(
            $exhibitorTargetId,
            $visitorTargetId
        ), 'webinar_visited_list_export_' . $today . '.xlsx');
    }
}
