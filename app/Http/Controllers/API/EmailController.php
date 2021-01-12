<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\Cancel;
use Illuminate\Http\Request;

use App\Mail\InviteRegisterToBuyer;
use App\Mail\RequestReqA;
use App\Mail\RequestResA;

use App\Mail\RequestReqBaccept;
use App\Mail\RequestReqBdeny;

use App\Mail\RequestResBaccept;
use App\Mail\RequestResBdeny;

use App\Model\Exhibitor;
use App\Model\Register;
use Mail;

class EmailController extends Controller
{

    public $data_gb;

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

    public function inviteRegisterToBuyer(Request $request)
    {
        // echo "test";
        // var_dump($request->all());
        $data = Register::find($request->input("id"));
        // var_dump($data);
        return Mail::send(new InviteRegisterToBuyer($data));
    }

    public function sendInviteRegisterToBuyer($id)
    {
        // echo "test";
        // var_dump($request->all());
        $data = Register::find($id);
        // var_dump($data);
        return Mail::send(new InviteRegisterToBuyer($data));
    }

    public function requestCancelSend($data)
    {
        // $data = [
        //     'ownerName' => 'Napong',
        //     'requestName' => 'CEBIT NAME',
        //     'requestCompany' => 'IMPACT',
        //     'date' => '2020-11-23',
        //     'time' => '10:00 - 10:30',
        //     'email' => 'napong.p@cmo-group.com',
        // ];

        $result["status"] = Mail::send(new Cancel($data));
        $status = true;
        return \response()->json([
            "status" => $status,
            "data"   => $result,
        ]);
    }

    public function requestBusinessMatchingTest(Request $request)
    {
        $data = [
            "email_state" => "", // request, reponse
            "request" => [
                "id" => "1",
                "email" => "sermchon.y@cmo-group.com",
                "name" => "sermchon",
                // "type" => "exhibitor"
                "type" => "visitor"
            ],
            "response" => [
                "id" => "2",
                "email" => "sermchon.y@cmo-group.com",
                "name" => "yanyarat",
                // "type" => "visitor"
                "type" => "exhibitor"
            ],
            "appointment" => [
                "date" => "2020-11-11",
                "time" => "11:11",
                "slot" => "1",
                "duration_time" => "3",
                "status" => "1"
            ]
        ];

        $data["email_state"] = "request";
        $data["appointment"]["status"] = "0";

        $data["email_state"] = "response";
        $data["appointment"]["status"] = "2"; // test accept 

        $data["email_state"] = "response";
        $data["appointment"]["status"] = "2"; // test deny

        $this->data_gb = $data;
        // echo " data_gb request = " . $this->data_gb["request"]["email"];
        // echo " data_gb response = " . $this->data_gb["request"]["email"];

        $this->requestBusinessMatching($data);
    }

    public function requestBusinessMatching($data)
    {
        $status = false;
        $result = [];
        $result["data"] = $data;
        switch ($data["email_state"]) {
            case 'request': // send request Matching
                // send notify Requester
                $result["status"]["req_a"] = Mail::send(new RequestReqA($data));

                // send to Respondent 
                $result["status"]["res_a"] = Mail::send(new RequestResA($data));

                break;
            case 'response': // accept or decline
                if ($data["appointment"]["status"] == 1) {
                    // send notify Requester after Respondent
                    $result["status"]["req_b"] = Mail::send(new RequestReqBaccept($data));
                    // send to Respondent 
                    $result["status"]["req_b"] = Mail::send(new RequestResBaccept($data));
                } else {
                    // send notify Requester after Respondent
                    $result["status"]["req_b"] = Mail::send(new RequestReqBdeny($data));
                    // send to Respondent 
                    $result["status"]["req_b"] = Mail::send(new RequestResBdeny($data));
                }
                break;
        }

        $status = true;
        return \response()->json([
            "status" => $status,
            "data"   => $result,
        ]);
    }


    public function requestReqA(Request $request, $data)
    {

        // $data = Register::findOrFail($request->input("id"));
        // // var_dump($data);
        // return Mail::send(new inviteRegisterToBuyer($data));
    }

    public function requestReqB(Request $request, $data)
    {
    }

    public function requestResA(Request $request, $data)
    {
    }

    public function requestResB(Request $request, $data)
    {
    }
}
