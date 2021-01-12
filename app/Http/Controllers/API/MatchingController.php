<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\SlotAvailable;
use App\Model\SlotTime;
use App\Model\SlotAppointment;
use App\Model\Register;
use App\Model\RegisterMatching;
use App\Model\Interest;
use App\Model\Exhibitor;
use App\Model\ExhibitorMatching;
use App\Model\LogCancel;
use App\Model\MatchingReport;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MatchingExport;
use App\Exports\MatchingExportCancel;
use App\Exports\MatchingExportAll;

use App\Http\Controllers\API\EmailController;


use DB;
use Facade\Ignition\QueryRecorder\Query;
use Illuminate\Http\Request;

class MatchingController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }
    public function getSlot(Request $request)
    {

        $userId = $request->get('id');
        $role = $request->get('role');

        $data = DB::table(
            DB::raw(
                " (select avb.*
                    from matching_enable_slot as avb
                    where avb.owner_id = " . $userId . " and avb.owner_type = '" . $role . "')
                    as temp"
            )
        )->rightJoin('matching_slot as slot', 'slot.id', '=', 'temp.slot_time')
            ->select('slot.id as id', 'slot.start_time', 'slot.end_time', 'slot.start_date', 'temp.id as slot_id', DB::raw('IFNULL(temp.slot_time, 0 ) as slot_time'), 'temp.slot_status', DB::raw('IFNULL(temp.is_available, false ) as is_available'))
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return $data;
    }

    public function updateSlot(Request $request)
    {
        $userId = $request->input('id');
        $role = $request->get('role');

        foreach ($request->input('data') as $key => $value) {

            if ($value['slot_status'] != 2 && $value['slot_status'] != 3) {

                if ($value['slot_id'] != null && $value['slot_id'] != "") {
                    // update slot when data exist
                    $slot = SlotAvailable::find($value['slot_id']);
                    $slot->is_available = $value['is_available'];
                    if ($value['is_available'] == false) {
                        $slot->slot_status = 0;
                    } else {
                        $slot->slot_status = 1;
                    }
                    $slot->save();
                } else if ($value['slot_id'] == null && $value['is_available'] == true) {

                    // if slot id doesn't exist insert new slot status

                    $slot =  new SlotAvailable();
                    $slot->slot_time = $value['id'];
                    $slot->slot_status = 1;
                    $slot->is_available = true;
                    $slot->owner_id = $userId;
                    $slot->owner_type = $role;
                    $slot->save();
                }
            } else {
            }
        }
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
    public function getCountriesBuyerHaving(Request $request)
    {
        $data = DB::table('registers as regis')
            ->join('countries as ctry', 'regis.country', '=', 'ctry.id')
            ->select('ctry.id as id', 'ctry.name as name')
            ->where('regis.type', '=', '1')
            ->groupBy('ctry.id')
            ->get();

        return $data;
    }
    public function getBookingList(Request $request)
    {
        $userId             = $request->input('id');
        $role               = $request->get('role');
        $type               = $request->get('type');
        $search             = $request->get('search');
        $categoryExhibitor  = $request->get('categoryExhibitor');
        $countryExhibitor   = $request->get('countryExhibitor');
        $dateExhibitor      = $request->get('dateExhibitor');
        $interestBuyer      = $request->get('interestBuyer');
        $countryBuyer       = $request->get('countryBuyer');
        $dateBuyer          = $request->get('dateBuyer');
        $forYou             = $request->get('forYou');
        $myCate             = $request->get('myCate');


        if ($type == "exhibitor") {
            // if ($role == "buyer") {
            //  outgoing request

            $exhi_id_collection = array();
            if ($forYou == true) {
                $data_Exhibitor = Interest::select(['exhibitor_id'])->whereIn("main_cate_id", $myCate)->where('type', '1')->groupBy('exhibitor_id')->get()->toArray();
            } else {
                $data_Exhibitor = Interest::select(['exhibitor_id'])->where('main_cate_id', $categoryExhibitor)->where('type', '1')->groupBy('exhibitor_id')->get()->toArray();
            }

            foreach ($data_Exhibitor as $i => $val) {
                array_push($exhi_id_collection, $val["exhibitor_id"]);
            }

            $dataOut = ExhibitorMatching::with(['appointmentOutgoing' => function ($query) use ($userId, $type, $role) {
                $query->select('id', 'slot_time', 'request_id', 'request_type', 'owner_id', 'status_appointment', 'updated_at')
                    ->where('request_id', $userId)
                    ->where('owner_type', $type);
            }, 'appointmentMainCate', 'country', 'haveSlot']);

            if ($search && \trim($search) != "") {
                $dataOut->where("company", "like", "%$search%");
            }
            if ($countryExhibitor && $countryExhibitor != "" && $countryExhibitor != "ALL") {
                $dataOut->where("country_id", "=", $countryExhibitor);
            }
            if ($forYou == true) {
                $dataOut->whereIn("id", $exhi_id_collection);
            } else {
                if ($categoryExhibitor && $categoryExhibitor != "" && $categoryExhibitor != "ALL") {
                    if (sizeof($data_Exhibitor) > 0) {
                        $dataOut->whereIn("id", $exhi_id_collection);
                    }
                }
            }



            $data['dataOut'] = $dataOut->paginate(15);
            $dataIn = ExhibitorMatching::with(['appointmentIncoming' => function ($query) use ($userId, $type, $role) {
                $query->select('id', 'slot_time', 'request_id', 'request_type', 'owner_id', 'status_appointment', 'updated_at')
                    ->where('owner_id', $userId)
                    ->where('request_type', $type);
            }, 'appointmentMainCate', 'country', 'haveSlot']);
            if ($search && \trim($search) != "") {
                $dataIn->where("company", "like", "%$search%");
            }
            if ($countryExhibitor && $countryExhibitor != "" && $countryExhibitor != "ALL") {
                $dataIn->where("country_id", "=", $countryExhibitor);
            }
            if ($forYou == true) {
                $dataIn->whereIn("id", $exhi_id_collection);
            } else {
                if ($categoryExhibitor && $categoryExhibitor != "" && $categoryExhibitor != "ALL") {
                    if (sizeof($data_Exhibitor) > 0) {
                        $dataIn->whereIn("id", $exhi_id_collection);
                    }
                }
            }

            $data['dataIn'] = $dataIn->paginate(15);
        } else {

            $regis_id_collection = array();
            if ($forYou == true) {
                $data_Register = Interest::select(['register_id'])->whereIn("main_cate_id", $myCate)->where('type', '2')->groupBy('register_id')->get()->toArray();
            } else {
                $data_Register = Interest::select(['register_id'])->where('main_cate_id', $interestBuyer)->where('type', '2')->groupBy('register_id')->get()->toArray();
            }

            foreach ($data_Register as $i => $val) {
                array_push($regis_id_collection, $val["register_id"]);
            }

            $dataOut = RegisterMatching::with(['appointmentOutgoing' => function ($query) use ($userId, $type, $role) {
                $query->select('id', 'slot_time', 'request_id', 'request_type', 'owner_id', 'status_appointment', 'updated_at')
                    ->where('request_id', $userId)
                    ->where('owner_type', $type);
            }, 'appointmentMainCate', 'natureOfBusinessRef', 'country', 'haveSlot'])->where('type', 1);

            if ($search && \trim($search) != "") {
                $dataOut->where(function ($query) use ($search) {
                    $query->orWhere('fname', 'like', '%' . $search . '%');
                    $query->orWhere('lname', 'like', '%' . $search . '%');
                    $query->orWhere('company', 'like', '%' . $search . '%');
                });
            }
            if ($countryBuyer && $countryBuyer != "" && $countryBuyer != "ALL") {
                $dataOut->where("country", "=", $countryBuyer);
            }

            if ($forYou == true) {
                $dataOut->whereIn("id", $regis_id_collection);
            } else {
                if ($interestBuyer && $interestBuyer != "" && $interestBuyer != "ALL") {
                    if (sizeof($data_Register) > 0) {
                        $dataOut->whereIn("id", $regis_id_collection);
                    }
                }
            }

            $data['dataOut'] = $dataOut->paginate(15);

            $dataIn = RegisterMatching::with(['appointmentIncoming' => function ($query) use ($userId, $type, $role) {
                $query->select('id', 'slot_time', 'request_id', 'request_type', 'owner_id', 'status_appointment', 'updated_at')
                    ->where('owner_id', $userId)
                    ->where('request_type', $type);
            }, 'appointmentMainCate', 'natureOfBusinessRef', 'country', 'haveSlot'])->where('type', 1);
            if ($search && \trim($search) != "") {
                $dataIn->where(function ($query) use ($search) {
                    $query->orWhere('fname', 'like', '%' . $search . '%');
                    $query->orWhere('lname', 'like', '%' . $search . '%');
                    $query->orWhere('company', 'like', '%' . $search . '%');
                });
            }
            if ($countryBuyer && $countryBuyer != "" && $countryBuyer != "ALL") {
                $dataIn->where("country", "=", $countryBuyer);
            }
            if ($forYou == true) {
                $dataIn->whereIn("id", $regis_id_collection);
            } else {
                if ($interestBuyer && $interestBuyer != "" && $interestBuyer != "ALL") {
                    if (sizeof($data_Register) > 0) {
                        $dataIn->whereIn("id", $regis_id_collection);
                    }
                }
            }

            $data['dataIn'] = $dataIn->paginate(15);
        }
        return $data;
    }

    public function getAppointmentDetail(Request $request)
    {

        $ownerId = $request->input('ownerId');
        $role = $request->get('role');
        $type = $request->get('type');

        $slot = DB::table(
            DB::raw(
                " (select avb.*
                    from matching_enable_slot as avb
                    where avb.owner_id = " . $ownerId . " and avb.owner_type = '" . $type . "')
                    as temp"
            )
        )->rightJoin('matching_slot as slot', 'slot.id', '=', 'temp.slot_time')
            ->select('slot.id as id', 'slot.start_time', 'slot.end_time', 'slot.start_date', 'temp.id as slot_id', DB::raw('IFNULL(temp.slot_time, 0 ) as slot_time'), 'temp.slot_status', DB::raw('IFNULL(temp.is_available, false ) as is_available'))
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        if ($type == "exhibitor") {
            $data    = ExhibitorMatching::with(['mainCate' => function ($query) {
                $query->select('name_en');
            }, 'country'])->findOrFail(intval($ownerId));
            $data_rp["haveSlot"] = SlotAvailable::where("owner_id", $ownerId)->count();
        } else {
            $data    = RegisterMatching::with(['mainCate' => function ($query) {
                $query->select('name_en');
            }, 'country'])->findOrFail(intval($ownerId));
            $data_rp["haveSlot"] = SlotAvailable::where("owner_id", $ownerId)->count();
        }

        $data_rp['user'] = $data;
        $data_rp['slot'] = $slot;

        return $data_rp;
    }

    public function createAppointment(Request $request)
    {
        $requestId = $request->input('id');
        $ownerId = $request->input('appointmentPartnerId');
        $role = $request->get('role');
        $type = $request->get('type');
        $appointment = new SlotAppointment();

        // echo "create app";
        $isDuplicateOut = 'false';
        $isDuplicateIn = 'false';
        // check duplicate appointment IN

        $data = SlotAppointment::with(['requestTime'])
            ->where('request_id', $requestId)->where('request_type', $role)->where('status_appointment', "!=", 'reject')->get();


        $slotTemp = SlotAvailable::find($request->input('slotTime'));

        $res = collect([
            'status' => false,
        ]);
        foreach ($data as $item) {

            if ($item->requestTime->slot_time == $slotTemp->slot_time) {
                $isDuplicateOut = 'true';
                if ($item->owner_type == "exhibitor") {
                    $ownerData = Exhibitor::find($item->owner_id);
                    $ownerName = $ownerData->m_name;
                    $ownerCompany = $ownerData->company;
                } else {
                    $ownerData = Register::find($item->owner_id);
                    $ownerName = $ownerData->full_name;
                    $ownerCompany = $ownerData->company;
                }
            }
        }
        // check duplicate appointment OUT

        $data = SlotAppointment::with(['requestTime'])
            ->where('owner_id', $requestId)->where('owner_type', $role)->where('status_appointment', "=", 'accept')->get();


        $slotTemp = SlotAvailable::find($request->input('slotTime'));

        foreach ($data as $item) {

            if ($item->requestTime->slot_time == $slotTemp->slot_time) {
                $isDuplicateIn = 'true';
                if ($item->request_type == "exhibitor") {
                    $requestData = Exhibitor::find($item->request_id);
                    $requestName = $requestData->m_name;
                    $requestCompany = $requestData->company;
                } else {
                    $requestData = Register::find($item->request_id);
                    $requestName = $requestData->full_name;
                    $requestCompany = $requestData->company;
                }
            }
        }

        if ($isDuplicateOut == 'true') {

            $res = collect([
                'status' => true,
                'type' => 'out',
                'name' =>  $ownerName,
                'company' =>  $ownerCompany,
            ]);
            return $res;
        } elseif ($isDuplicateIn == 'true') {

            $res = collect([
                'status' => true,
                'type' => 'in',
                'name' =>  $requestName,
                'company' =>  $requestCompany,
            ]);
            return $res;
        } elseif ($isDuplicateIn == 'false' && $isDuplicateOut == 'false') {
            if ($role == "buyer" && $type == "exhibitor") {
                $appointment->type = "V2E";
            } else if ($role == "exhibitor" && $type == "buyer") {
                $appointment->type = "E2V";
            } else if ($role == "exhibitor" && $type == "exhibitor") {
                $appointment->type = "E2E";
            }

            $appointment->request_id = $requestId;
            $appointment->owner_id = $ownerId;

            $appointment->request_type = $role;
            $appointment->owner_type = $type;

            $appointment->note = $request->input('note');
            $appointment->request_slot = $request->input('slotTime');
            $appointment->status_appointment = "request";
            $appointment->save();

            if ($role == "exhibitor") {
                $requestData = Exhibitor::find($requestId);
                $requestEmail = $requestData->m_email;
                $requestName = $requestData->m_name;
                $requestComapny = $requestData->company;
            } else {
                $requestData = Register::find($requestId);
                $requestEmail = $requestData->email;
                $requestName = $requestData->full_name;
                $requestComapny = $requestData->company;
            }

            if ($type == "exhibitor") {
                $ownerData = Exhibitor::find($ownerId);
                $ownerEmail = $ownerData->m_email;
                $ownerName = $ownerData->m_name;
                $ownerCompany = $ownerData->company;
            } else {
                $ownerData = Register::find($ownerId);
                $ownerEmail = $ownerData->email;
                $ownerName = $ownerData->full_name;
                $ownerCompany = $ownerData->company;
            }

            $slotData = SlotAvailable::with('slotTime')->find($request->input('slotTime'));

            $emailData = [
                "email_state" => "request", // request, reponse
                "request" => [
                    "id" => $requestId,
                    "email" => $requestEmail,
                    "name" => $requestName . " : " . $requestComapny . ' (' . $role . ')',
                    "type" => $role
                ],
                "response" => [
                    "id" => $ownerId,
                    "email" => $ownerEmail,
                    "name" => $ownerName . " : " . $ownerCompany . ' (' . $type . ')',
                    "type" => $type
                ],
                "appointment" => [
                    "date" => $slotData->slotTime->start_date,
                    "time" => $slotData->slotTime->start_time,
                    "slot" => "1",
                    "duration_time" => "3",
                    "status" => "1"
                ]

            ];
            $controller = new EmailController;
            $controller->requestBusinessMatching($emailData);
            return $res;
        }
    }


    // Re sch

    public function reAppointment(Request $request)
    {
        $requestId = $request->input('id');
        $ownerId = $request->input('appointmentPartnerId');
        $role = $request->get('role');
        $type = $request->get('type');
        $oldAppointmentId = $request->get('oldAppointmentId');

        // clear old slot

        $data =  SlotAppointment::find($oldAppointmentId);

        $slotData = SlotAvailable::with('slotTime')->find($data->request_slot);

        $log =  new LogCancel();
        $log->request_id = $requestId;
        $log->role = $role;
        $log->type = "re-booking";
        $log->appointment_data = json_encode($data);
        $log->slot_data = json_encode($slotData);
        $log->save();

        // partner data
        if ($data->owner_type == "exhibitor") {
            $ownerData = Exhibitor::find($data->owner_id);
            $ownerName = $ownerData->m_name;
            $ownerCompany = $ownerData->company;
            $ownerEmail = $ownerData->m_email;
        } else {
            $ownerData = Register::find($data->owner_id);
            $ownerName = $ownerData->full_name;
            $ownerCompany = $ownerData->company;
            $ownerEmail = $ownerData->email;
        }
        // request data
        if ($data->request_type == "exhibitor") {
            $requestData = Exhibitor::find($data->request_id);
            $requestName = $requestData->m_name;
            $requestCompany = $requestData->company;
            $requestEmail = $requestData->m_email;
        } else {
            $requestData = Register::find($data->request_id);
            $requestName = $requestData->full_name;
            $requestCompany = $requestData->company;
            $requestEmail = $requestData->email;
        }
        // echo $appointmentId;
        SlotAppointment::destroy($oldAppointmentId);
        SlotAvailable::where('appointment_id', '=', $oldAppointmentId)->delete();


        // create new booking

        $appointment = new SlotAppointment();

        // echo "create app";
        $isDuplicateOut = 'false';
        $isDuplicateIn = 'false';
        // check duplicate appointment IN

        $data = SlotAppointment::with(['requestTime'])
            ->where('request_id', $requestId)->where('request_type', $role)->where('status_appointment', "!=", 'reject')->get();


        $slotTemp = SlotAvailable::find($request->input('slotTime'));

        $res = collect([
            'status' => false,
        ]);
        foreach ($data as $item) {

            if ($item->requestTime->slot_time == $slotTemp->slot_time) {
                $isDuplicateOut = 'true';
                if ($item->owner_type == "exhibitor") {
                    $ownerData = Exhibitor::find($item->owner_id);
                    $ownerName = $ownerData->m_name;
                    $ownerCompany = $ownerData->company;
                } else {
                    $ownerData = Register::find($item->owner_id);
                    $ownerName = $ownerData->full_name;
                    $ownerCompany = $ownerData->company;
                }
            }
        }
        // check duplicate appointment OUT

        $data = SlotAppointment::with(['requestTime'])
            ->where('owner_id', $requestId)->where('owner_type', $role)->where('status_appointment', "=", 'accept')->get();


        $slotTemp = SlotAvailable::find($request->input('slotTime'));

        foreach ($data as $item) {

            if ($item->requestTime->slot_time == $slotTemp->slot_time) {
                $isDuplicateIn = 'true';
                if ($item->request_type == "exhibitor") {
                    $requestData = Exhibitor::find($item->request_id);
                    $requestName = $requestData->m_name;
                    $requestCompany = $requestData->company;
                } else {
                    $requestData = Register::find($item->request_id);
                    $requestName = $requestData->full_name;
                    $requestCompany = $requestData->company;
                }
            }
        }

        if ($isDuplicateOut == 'true') {

            $res = collect([
                'status' => true,
                'type' => 'out',
                'name' =>  $ownerName,
                'company' =>  $ownerCompany,
            ]);
            return $res;
        } elseif ($isDuplicateIn == 'true') {

            $res = collect([
                'status' => true,
                'type' => 'in',
                'name' =>  $requestName,
                'company' =>  $requestCompany,
            ]);
            return $res;
        } elseif ($isDuplicateIn == 'false' && $isDuplicateOut == 'false') {
            if ($role == "buyer" && $type == "exhibitor") {
                $appointment->type = "V2E";
            } else if ($role == "exhibitor" && $type == "buyer") {
                $appointment->type = "E2V";
            } else if ($role == "exhibitor" && $type == "exhibitor") {
                $appointment->type = "E2E";
            }

            $appointment->request_id = $requestId;
            $appointment->owner_id = $ownerId;

            $appointment->request_type = $role;
            $appointment->owner_type = $type;

            $appointment->note = $request->input('note');
            $appointment->request_slot = $request->input('slotTime');
            $appointment->status_appointment = "request";
            $appointment->save();

            if ($role == "exhibitor") {
                $requestData = Exhibitor::find($requestId);
                $requestEmail = $requestData->m_email;
                $requestName = $requestData->m_name;
                $requestComapny = $requestData->company;
            } else {
                $requestData = Register::find($requestId);
                $requestEmail = $requestData->email;
                $requestName = $requestData->full_name;
                $requestComapny = $requestData->company;
            }

            if ($type == "exhibitor") {
                $ownerData = Exhibitor::find($ownerId);
                $ownerEmail = $ownerData->m_email;
                $ownerName = $ownerData->m_name;
                $ownerCompany = $ownerData->company;
            } else {
                $ownerData = Register::find($ownerId);
                $ownerEmail = $ownerData->email;
                $ownerName = $ownerData->full_name;
                $ownerCompany = $ownerData->company;
            }

            $slotData = SlotAvailable::with('slotTime')->find($request->input('slotTime'));

            $emailData = [
                "email_state" => "request", // request, reponse
                "request" => [
                    "id" => $requestId,
                    "email" => $requestEmail,
                    "name" => $requestName . " : " . $requestComapny . ' (' . $role . ')',
                    "type" => $role
                ],
                "response" => [
                    "id" => $ownerId,
                    "email" => $ownerEmail,
                    "name" => $ownerName . " : " . $ownerCompany . ' (' . $type . ')',
                    "type" => $type
                ],
                "appointment" => [
                    "date" => $slotData->slotTime->start_date,
                    "time" => $slotData->slotTime->start_time,
                    "slot" => "1",
                    "duration_time" => "3",
                    "status" => "1"
                ]

            ];
            $controller = new EmailController;
            $controller->requestBusinessMatching($emailData);
            return $res;
        }
    }
    // Request Pages

    // cancel request
    public function cancelRequest(Request $request)
    {
        $userId = $request->get('id');
        $role = $request->get('role');

        $appointmentId               = $request->input('appointment_id');
        $data =  SlotAppointment::find($appointmentId);

        $slotData = SlotAvailable::with('slotTime')->find($data->request_slot);

        $log =  new LogCancel();
        $log->request_id = $userId;
        $log->role = $role;
        $log->type = "cancel";
        $log->appointment_data = json_encode($data);
        $log->slot_data = json_encode($slotData);
        $log->save();

        // partner data
        if ($data->owner_type == "exhibitor") {
            $ownerData = Exhibitor::find($data->owner_id);
            $ownerName = $ownerData->m_name;
            $ownerCompany = $ownerData->company;
            $ownerEmail = $ownerData->m_email;
        } else {
            $ownerData = Register::find($data->owner_id);
            $ownerName = $ownerData->full_name;
            $ownerCompany = $ownerData->company;
            $ownerEmail = $ownerData->email;
        }
        // request data
        if ($data->request_type == "exhibitor") {
            $requestData = Exhibitor::find($data->request_id);
            $requestName = $requestData->m_name;
            $requestCompany = $requestData->company;
            $requestEmail = $requestData->m_email;
        } else {
            $requestData = Register::find($data->request_id);
            $requestName = $requestData->full_name;
            $requestCompany = $requestData->company;
            $requestEmail = $requestData->email;
        }

        SlotAppointment::destroy($appointmentId);
        /// send to partner
        $controller = new EmailController;
        $emailData = [
            'ownerName' => $ownerName,
            'requestName' => $requestName,
            'requestCompany' =>  $requestCompany,
            "date" => $slotData->slotTime->start_date,
            "time" => $slotData->slotTime->start_time,
            'email' =>  $ownerEmail,
        ];

        $controller->requestCancelSend($emailData);

        /// send self
        $emailData = [
            'ownerName' => $requestName,
            'requestName' => $ownerName,
            'requestCompany' =>  $ownerCompany,
            "date" => $slotData->slotTime->start_date,
            "time" => $slotData->slotTime->start_time,
            'email' =>  $requestEmail,
        ];
        $controller->requestCancelSend($emailData);
    }

    public function cancelAccept(Request $request)
    {

        $userId = $request->get('id');
        $role = $request->get('role');

        $appointmentId               = $request->input('appointment_id');
        $data =  SlotAppointment::find($appointmentId);

        $slotData = SlotAvailable::with('slotTime')->find($data->request_slot);

        $log =  new LogCancel();
        $log->request_id = $userId;
        $log->role = $role;
        $log->type = "accept";
        $log->appointment_data = json_encode($data);
        $log->slot_data = json_encode($slotData);
        $log->save();

        // partner data
        if ($data->owner_type == "exhibitor") {
            $ownerData = Exhibitor::find($data->owner_id);
            $ownerName = $ownerData->m_name;
            $ownerCompany = $ownerData->company;
            $ownerEmail = $ownerData->m_email;
        } else {
            $ownerData = Register::find($data->owner_id);
            $ownerName = $ownerData->full_name;
            $ownerCompany = $ownerData->company;
            $ownerEmail = $ownerData->email;
        }
        // request data
        if ($data->request_type == "exhibitor") {
            $requestData = Exhibitor::find($data->request_id);
            $requestName = $requestData->m_name;
            $requestCompany = $requestData->company;
            $requestEmail = $requestData->m_email;
        } else {
            $requestData = Register::find($data->request_id);
            $requestName = $requestData->full_name;
            $requestCompany = $requestData->company;
            $requestEmail = $requestData->email;
        }
        // echo $appointmentId;
        SlotAppointment::destroy($appointmentId);
        SlotAvailable::where('appointment_id', '=', $appointmentId)->delete();

        // SlotAvailable::destroy($data->request_slot);

        /// send to partner
        $controller = new EmailController;
        $emailData = [
            'ownerName' => $ownerName,
            'requestName' => $requestName,
            'requestCompany' =>  $requestCompany,
            "date" => $slotData->slotTime->start_date,
            "time" => $slotData->slotTime->start_time,
            'email' =>  $ownerEmail,
        ];

        $controller->requestCancelSend($emailData);

        /// send self
        $emailData = [
            'ownerName' => $requestName,
            'requestName' => $ownerName,
            'requestCompany' =>  $ownerCompany,
            "date" => $slotData->slotTime->start_date,
            "time" => $slotData->slotTime->start_time,
            'email' =>  $requestEmail,
        ];
        $controller->requestCancelSend($emailData);
    }


    public function getRequestList(Request $request)
    {

        $userId                 = $request->input('id');
        $role                   = $request->get('role');
        $search                 = $request->get('search');
        $requested              = $request->get('requested');
        $rejected               = $request->get('rejected');
        $accepted               = $request->get('accepted');

        $inComing = [];
        $outGoing = [];

        if ($role == "buyer") {

            $outGoing = SlotAppointment::with([
                'outGoingExhibitor' => function ($query) {
                    $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
                },
                'requestTime' => function ($query) {
                    $query->select('id', 'slot_time')->with('slotTime');
                },
                'slotTime' => function ($query) {
                    $query->select('id', 'slot_time')->with('slotTime');
                },
            ])->where('request_id', $userId)->where('request_type', $role);
            if ($requested == true) {
                $outGoing->where('status_appointment', 'request');
            }
            if ($rejected == true) {
                $outGoing->where('status_appointment', 'reject');
            }
            if ($accepted == true) {
                $outGoing->where('status_appointment', 'accept');
            }



            $outGoing = $outGoing->get();

            $inComing = SlotAppointment::with([
                'inComingExhibitor' => function ($query) {
                    $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
                },
                'requestTime' => function ($query) {
                    $query->select('id', 'slot_time')->with('slotTime');
                },
                'slotTime' => function ($query) {
                    $query->select('id', 'slot_time')->with('slotTime');
                },
            ])->where('owner_id', $userId)->where('owner_type', $role);
            if ($requested == true) {
                $inComing->where('status_appointment', 'request');
            }
            if ($rejected == true) {
                $inComing->where('status_appointment', 'reject');
            }
            if ($accepted == true) {
                $inComing->where('status_appointment', 'accept');
            }

            $inComing = $inComing->get();
        } else { // exhibitor
            $outGoing = SlotAppointment::with([
                'outGoingExhibitor' => function ($query) {
                    $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
                }, 'outGoingRegister' => function ($query) {
                    $query->select('id', 'fname', 'lname', 'company', 'position', 'website', 'country')->with(['mainCate', 'country']);
                },
                'requestTime' => function ($query) {
                    $query->select('id', 'slot_time')->with('slotTime');
                },
                'slotTime' => function ($query) {
                    $query->select('id', 'slot_time')->with('slotTime');
                },
            ])->where('request_id', $userId)->where('request_type', $role);
            if ($requested == true) {
                $outGoing->where('status_appointment', 'request');
            }
            if ($rejected == true) {
                $outGoing->where('status_appointment', 'reject');
            }
            if ($accepted == true) {
                $outGoing->where('status_appointment', 'accept');
            }



            $outGoing = $outGoing->get();


            $inComing = SlotAppointment::with([
                'inComingExhibitor' => function ($query) {
                    $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
                }, 'inComingRegister' => function ($query) {
                    $query->select('id', 'fname', 'lname', 'company', 'position', 'website', 'country')->with(['mainCate', 'country']);
                },
                'requestTime' => function ($query) {
                    $query->select('id', 'slot_time')->with('slotTime');
                },
                'slotTime' => function ($query) {
                    $query->select('id', 'slot_time')->with('slotTime');
                },
            ])->where('owner_id', $userId)->where('owner_type', $role);
            if ($requested == true) {
                $inComing->where('status_appointment', 'request');
            }
            if ($rejected == true) {
                $inComing->where('status_appointment', 'reject');
            }
            if ($accepted == true) {
                $inComing->where('status_appointment', 'accept');
            }

            $inComing = $inComing->get();
        }

        $data_rp['outGoing'] = $outGoing;
        $data_rp['inComing'] = $inComing;
        $data_rp['serverTime'] = date('Y-m-d H:i:s');

        return $data_rp;
    }


    // public function getRequestList(Request $request)
    // {

    //     $userId                 = $request->input('id');
    //     $role                   = $request->get('role');
    //     $search                 = $request->get('search');
    //     $requested              = $request->get('requested');
    //     $rejected               = $request->get('rejected');
    //     $accepted               = $request->get('accepted');
    //     $inComing = [];
    //     $outGoing = [];

    //     if ($role == "buyer") {

    //         if($requested == true){
    //             $outGoing = SlotAppointment::with([
    //                 'outGoingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('request_id', $userId)
    //                 ->where('request_type', $role)
    //                 ->where('status_appointment', 'request')
    //                 ->get();

    //             $inComing = SlotAppointment::with([
    //                 'inComingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('owner_id', $userId)
    //                 ->where('owner_type', $role)
    //                 ->where('status_appointment', 'request')
    //                 ->get();
    //         }
    //         if($rejected == true){
    //             $outGoing = SlotAppointment::with([
    //                 'outGoingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('request_id', $userId)
    //                 ->where('request_type', $role)
    //                 ->where('status_appointment', 'reject')
    //                 ->get();

    //             $inComing = SlotAppointment::with([
    //                 'inComingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('owner_id', $userId)
    //                 ->where('owner_type', $role)
    //                 ->where('status_appointment', 'reject')
    //                 ->get();
    //         }
    //         if($accepted){
    //             $outGoing = SlotAppointment::with([
    //                 'outGoingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('request_id', $userId)
    //                 ->where('request_type', $role)
    //                 ->where('status_appointment', 'accept')
    //                 ->get();

    //             $inComing = SlotAppointment::with([
    //                 'inComingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('owner_id', $userId)
    //                 ->where('owner_type', $role)
    //                 ->where('status_appointment', 'accept')
    //                 ->get();
    //         }
    //         if($rejected == false && $requested == false && $accepted == false){
    //             $outGoing = SlotAppointment::with([
    //                 'outGoingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('request_id', $userId)
    //                 ->where('request_type', $role)
    //                 ->get();

    //             $inComing = SlotAppointment::with([
    //                 'inComingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('owner_id', $userId)
    //                 ->where('owner_type', $role)
    //                 ->get();
    //         }

    //     } else { // exhibitor
    //         if($requested == true){
    //             $outGoing = SlotAppointment::with([
    //                 'outGoingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 }, 'outGoingRegister' => function ($query) {
    //                     $query->select('id', 'fname', 'lname', 'company', 'position', 'website', 'country')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('request_id', $userId)
    //                 ->where('request_type', $role)
    //                 ->where('status_appointment', 'request')
    //                 ->get();

    //             $inComing = SlotAppointment::with([
    //                 'inComingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 }, 'inComingRegister' => function ($query) {
    //                     $query->select('id', 'fname', 'lname', 'company', 'position', 'website', 'country')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('owner_id', $userId)
    //                 ->where('owner_type', $role)
    //                 ->where('status_appointment', 'request')
    //                 ->get();
    //         }
    //         if($rejected == true){
    //             $outGoing = SlotAppointment::with([
    //                 'outGoingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 }, 'outGoingRegister' => function ($query) {
    //                     $query->select('id', 'fname', 'lname', 'company', 'position', 'website', 'country')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('request_id', $userId)
    //                 ->where('request_type', $role)
    //                 ->where('status_appointment', 'reject')
    //                 ->get();

    //             $inComing = SlotAppointment::with([
    //                 'inComingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 }, 'inComingRegister' => function ($query) {
    //                     $query->select('id', 'fname', 'lname', 'company', 'position', 'website', 'country')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('owner_id', $userId)
    //                 ->where('owner_type', $role)
    //                 ->where('status_appointment', 'reject')
    //                 ->get();
    //         }
    //         if($accepted == true){
    //             $outGoing = SlotAppointment::with([
    //                 'outGoingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 }, 'outGoingRegister' => function ($query) {
    //                     $query->select('id', 'fname', 'lname', 'company', 'position', 'website', 'country')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('request_id', $userId)
    //                 ->where('request_type', $role)
    //                 ->where('status_appointment', 'accept')
    //                 ->get();

    //             $inComing = SlotAppointment::with([
    //                 'inComingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 }, 'inComingRegister' => function ($query) {
    //                     $query->select('id', 'fname', 'lname', 'company', 'position', 'website', 'country')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('owner_id', $userId)
    //                 ->where('owner_type', $role)
    //                 ->where('status_appointment', 'accept')
    //                 ->get();
    //         }
    //         if($rejected == false && $requested == false && $accepted == false){
    //             $outGoing = SlotAppointment::with([
    //                 'outGoingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 }, 'outGoingRegister' => function ($query) {
    //                     $query->select('id', 'fname', 'lname', 'company', 'position', 'website', 'country')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('request_id', $userId)
    //                 ->where('request_type', $role)
    //                 ->get();

    //             $inComing = SlotAppointment::with([
    //                 'inComingExhibitor' => function ($query) {
    //                     $query->select('id', 'company', 'name', 'img_avatar', 'position', 'website', 'country_id')->with(['mainCate', 'country']);
    //                 }, 'inComingRegister' => function ($query) {
    //                     $query->select('id', 'fname', 'lname', 'company', 'position', 'website', 'country')->with(['mainCate', 'country']);
    //                 },
    //                 'requestTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //                 'slotTime' => function ($query) {
    //                     $query->select('id', 'slot_time')->with('slotTime');
    //                 },
    //             ])
    //                 ->where('owner_id', $userId)
    //                 ->where('owner_type', $role)
    //                 ->get();
    //         }

    //     }

    //     $data_rp['outGoing'] = $outGoing;
    //     $data_rp['inComing'] = $inComing;
    //     return $data_rp;
    // }

    // Appointment Pages

    public function getAppointmentList(Request $request)
    {

        $userId = $request->input('id');
        $role = $request->get('role');


        $visitorData = [];
        $exhibitorData = [];

        if ($role == "buyer") {
            $exhibitorData = SlotAppointment::with(['exhibitor' => function ($query) {
                $query->select('id', 'company')->with('mainCate');
            }])
                ->where('request_id', $userId)
                ->where('request_type', $role)
                ->get();
        } else {

            $exhibitorData = SlotAppointment::with(['exhibitor' => function ($query) {
                $query->select('id', 'company')->with('mainCate');
            }])
                ->where('request_id', $userId)
                ->where('request_type', $role)
                ->get();

            $visitorData = SlotAppointment::with(['register' => function ($query) {
                $query->select('id', 'fname', 'lname')->with('mainCate');
            }])
                ->where('request_id', $userId)
                ->where('request_type', $role)
                ->get();
        }
        $data_rp['visitorData'] = $visitorData;
        $data_rp['exhibitorData'] = $exhibitorData;
        return $data_rp;
    }

    public function getAppointmentProfile(Request $request)
    {

        $userId = $request->input('id');
        $role = $request->get('role');
        $appointmentId = $request->get('appointment_id');
        $appointment = SlotAppointment::find($appointmentId);

        $appointmentType = $appointment->type;
        if ($appointmentType == "V2E" || $appointmentType == "E2E") {
            $data = DB::table('matching_appointment as apm')
                ->join('exhibitor_list as exh', 'apm.exhibitor_id', '=', 'exh.id')
                ->join('matching_enable_slot as enable', 'apm.request_slot', '=', 'enable.id')
                ->join('matching_slot as slot', 'enable.slot_time', '=', 'slot.id')
                ->select('exh.company', 'apm.*', 'slot.start_date', 'slot.start_time')
                ->where('apm.id', intval($appointmentId))
                ->first();
        } else {
            $data = DB::table('matching_appointment as apm')
                ->join('registers as regis', 'apm.visitor_id', '=', 'regis.id')
                ->join('matching_enable_slot as enable', 'apm.request_slot', '=', 'enable.id')
                ->join('matching_slot as slot', 'enable.slot_time', '=', 'slot.id')
                ->select('regis.company', 'apm.*', 'slot.start_date', 'slot.start_time')
                ->where('apm.id', intval($appointmentId))
                ->first();
        }
        $data_rp['profile'] = $data;

        return $data_rp;
    }

    public function updateAppointment(Request $request)
    {
        $userId = $request->input('id');
        $role = $request->get('role');
        // update appointment list
        DB::beginTransaction();

        try {

            $appointment = SlotAppointment::find($request->input('appointment_id'));
            $appointment->status_appointment = $request->input('appointment_status');
            $requestId = $appointment->request_id;
            $requestType = $appointment->request_type;
            $ownerId = $appointment->owner_id;
            $ownerType = $appointment->owner_type;
            // // update slot time
            $slotTime = SlotAvailable::find($request->input('slot_time'));
            $emailAppointmentStatus = '0';
            $note = '';
            if ($request->input('appointment_status') == 'accept') {
                $slotTime->slot_status = 2;
                $slotTime->appointment_id = $request->input('appointment_id');
                $appointment->slot_time = $request->input('slot_time');
                $slotTime->save();

                $requestSlot = SlotAvailable::where('owner_id', $requestId)
                    ->where('owner_type', $requestType)
                    ->where('slot_time', $slotTime->slot_time)
                    ->first();

                if ($requestSlot) {
                    $requestSlot->slot_status = 2;
                    $requestSlot->slot_status_type = 'outgoing';
                    $requestSlot->appointment_id = $request->input('appointment_id');
                    $requestSlot->save();
                } else {
                    $requestSlot = new SlotAvailable();
                    $requestSlot->owner_id = $requestId;
                    $requestSlot->owner_type = $requestType;
                    $requestSlot->slot_time = $slotTime->slot_time;
                    $requestSlot->slot_status = 2;
                    $requestSlot->slot_status_type = 'outgoing';
                    $requestSlot->appointment_id = $request->input('appointment_id');

                    $requestSlot->save();
                }
                // send mail response accept
                $emailAppointmentStatus = '1';

                // $appointment->meeting_id = $this->generateMeetingId();
                // $appointment->meeting_code = $this->generateMeetingCode();

                // $appointment->meeting_status = 'create';
            } else {

                $appointment->reject_note = $request->input('note');
                $note = $request->input('note');
                // send mail response accept
                $emailAppointmentStatus = '2';
            }

            // // check request
            if ($requestType == "exhibitor") {
                $requestData = Exhibitor::find($requestId);
                $requestEmail = $requestData->m_email;
                $requestName = $requestData->m_name;
                $requestComapny = $requestData->company;
            } else if ($requestType == "buyer") {

                $requestData = Register::find($requestId);
                $requestEmail = $requestData->email;
                $requestName = $requestData->full_name;
                $requestComapny = $requestData->company;
            }

            // echo "ownerType = " . $ownerType;
            // check owner
            if ($ownerType == "exhibitor") {
                $ownerData = Exhibitor::find($ownerId);
                $ownerEmail = $ownerData->m_email;
                $ownerName = $ownerData->m_name;
                $ownerCompany = $ownerData->company;
            } else if ($ownerType == "buyer") {
                $ownerData = Register::find($ownerId);
                $ownerEmail = $ownerData->email;
                $ownerName = $ownerData->full_name;
                $ownerCompany = $ownerData->company;
            }
            // var_dump($ownerData);

            $slotData = SlotAvailable::with('slotTime')->find($request->input('slot_time'));
            $emailData = [
                "email_state" => "response", // request, reponse
                "request" => [
                    "id"    => $requestId,
                    "email" => $requestEmail,
                    "name"  => $requestName . " : " . $requestComapny . ' (' . $requestType . ')',
                    "type"  => $requestType
                ],
                "response" => [
                    "id"    => $ownerId,
                    "email" => $ownerEmail,
                    "name"  => $ownerName . " : " . $ownerCompany . ' (' . $ownerType . ')',
                    "type"  => $ownerType
                ],
                "appointment" => [
                    "date" => $slotData->slotTime->start_date,
                    "time" => $slotData->slotTime->start_time,
                    "slot" => "1",
                    "duration_time" => "3",
                    "status" => $emailAppointmentStatus,
                    "note" => $note,
                ]
            ];

            $controller = new EmailController;
            $controller->requestBusinessMatching($emailData);
            $appointment->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();

        if ($request->input('appointment_status') == 'accept') {

            $res = collect([
                'meeting_id'        => $appointment->meeting_id,
                'meeting_code'        => $appointment->meeting_id,
                'type' => $request->input('appointment_status')

            ]);
        } else {

            $res = collect([
                'meeting_id'        => '',
                'meeting_code'        => '',
                'type' => $request->input('appointment_status')


            ]);
        }
        return \response($res, 201);
    }

    public function getProfile(Request $request)
    {
        $userId = $request->input('id');
        $role = $request->get('role');
        $type = $request->get('type');

        if ($type == "exhibitor") {
            $data = DB::table('interest_category as interest')
                ->join('exhibitor_list as exh', 'interest.exhibitor_id', '=', 'exh.id')
                ->join('main_category as mcate', 'interest.main_cate_id', '=', 'mcate.id')
                ->join('sub_category as scate', 'interest.sub_cate_id', '=', 'scate.id')
                ->join('countries as ctry', 'exh.country_id', '=', 'ctry.id')
                ->select('exh.id', 'exh.company', 'exh.mobile', 'exh.position', 'exh.email', 'exh.address', 'exh.description', 'exh.website', 'exh.logo', 'ctry.code as countryCode', 'ctry.name as countryName', 'mcate.name_en as mainCate', DB::raw("GROUP_CONCAT(scate.name SEPARATOR ',') AS subCate "))
                ->where('exh.id', intval($userId))
                ->groupBy('exh.id')
                ->first();
        } else {
            $data = DB::table('interest_category as interest')
                ->join('registers as regis', 'interest.register_id', '=', 'regis.id')
                ->join('main_category as mcate', 'interest.main_cate_id', '=', 'mcate.id')
                ->join('countries as ctry', 'regis.country', '=', 'ctry.id')
                ->select('regis.id', 'regis.company', 'regis.mobile', 'regis.position', 'regis.email', 'regis.address',  'regis.website', 'ctry.code as countryCode', 'ctry.name as countryName', 'mcate.name_en as mainCate')
                ->where('regis.id', intval($userId))
                ->groupBy('regis.id')
                ->first();
        }

        $data_rp['profile'] = $data;
        return $data_rp;
    }
    public function testSendMail()
    {
        $emailData = [
            "email_state" => "request", // request, reponse
            "request" => [
                "id" => "1",
                "email" => "napong.p@cmo-group.com",
                "name" => "sermchon",
                // "type" => "exhibitor"
                "type" => "visitor"
            ],
            "response" => [
                "id" => "2",
                "email" => "napong.p@cmo-group.com",
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
        $controller = new EmailController;
        $controller->requestBusinessMatching($emailData);
    }

    public function haveSlot(Request $request, $id)
    {
        $data = [];
        // $id = '1';
        $data["id"]     = $id;
        // $data["type"]   = $type;
        $data["status"] = true;
        $data["data"] = [];

        $slot = SlotAvailable::where("owner_id", $id)->count();
        $data["data"] = $slot;
        if ($slot) {
            $data["status"] = true;
        } else {
            $data["status"] = false;
        }

        return $data;
    }

    public function getExhibitorBackoffice(Request $request)
    {
        $itemsPerPage    = $request->input('itemsPerPage');
        $search           = $request->input('search');
        $country       = $request->input('country');
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
        $data = ExhibitorMatching::selectRaw('*, @row:=@row+1 as row_no')->withCount(['getIncomingExhibitor', 'getIncomingBuyer', 'getOutgoingExhibitor', 'getOutgoingBuyer'])
            ->with(['getIncomingExhibitor', 'getIncomingBuyer', 'getOutgoingExhibitor', 'getOutgoingBuyer', 'country']);

        if ($country && $country != "") {
            $data = $data->where("country_id", "=", $country);
        }
        if ($search && $search != "") {
            $data = $data->where("company", "like", "%{$search}%");
        }

        $data = $data->get();



        $sum = collect([
            'sum_exhibitor_incoming'        => $data->sum('get_incoming_exhibitor_count'),
            'sum_buyer_incoming'         => $data->sum('get_incoming_buyer_count'),
            'sum_exhibitor_outgoing'      => $data->sum('get_outgoing_exhibitor_count'),
            'sum_buyer_outgoing'   => $data->sum('get_outgoing_buyer_count'),

        ]);
        $data_rp["data"] = $data;
        $data_rp["sum"] = $sum;

        return $data_rp;
    }

    public function getRegisterBackoffice(Request $request)
    {
        $search  = $request->input('search');
        $country = $request->input('country');
        $itemsPerPage    = $request->input('itemsPerPage');
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
        $data = RegisterMatching::selectRaw('*, @row:=@row+1 as row_no')->withCount([
            'getOutgoingExhibitor',
            'getIncomingExhibitor',
        ])
            ->with([
                'getOutgoingExhibitor',
                'getIncomingExhibitor',
                'country'
            ]);

        if ($country && $country != "") {
            $data = $data->where("country_id", "=", $country);
        }

        if ($search && $search != "") {
            $data = $data->where("company", "like", "%{$search}%");
        }

        $data = $data->get();

        $sum = collect([
            'sum_exhibitor_outgoing' => $data->sum('get_outgoing_exhibitor_count'),
            'sum_exhibitor_incoming' => $data->sum('get_incoming_exhibitor_count')
        ]);

        $data_rp["data"] = $data;
        $data_rp["sum"] = $sum;

        return $data_rp;
    }

    public function generateMeetingId()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $meeting_id = '';
        for ($i = 0; $i < 10; $i++) {
            $meeting_id .= $characters[rand(0, $charactersLength - 1)];
        }
        return $meeting_id;
    }

    public function generateMeetingCode()
    {
        $characters = '123456789abcdefghjkmnpqrstuvwxyz';
        $charactersLength = strlen($characters);
        $meeting_id = '';
        for ($i = 0; $i < 6; $i++) {
            $meeting_id .= $characters[rand(0, $charactersLength - 1)];
        }
        return $meeting_id;
    }

    public function checkDuplicateRequest(Request $request)
    {

        $userId           = $request->input('id');
        $role       = $request->input('role');
        $appointmentId           = $request->input('appId');

        $isDuplicateOut = 'false';
        $data = SlotAppointment::with(['requestTime'])
            ->where('owner_id', $userId)->where('owner_type', $role)->where('status_appointment', "!=", 'reject')->get();


        $slotTemp = SlotAvailable::find($appointmentId);

        $res = collect([
            'status' => false,
        ]);
        foreach ($data as $item) {

            if ($item->requestTime->slot_time == $slotTemp->slot_time) {
                $isDuplicateOut = 'true';
                if ($item->request_type == "exhibitor") {
                    $requestData = Exhibitor::find($item->request_id);
                    $requestName = $requestData->m_name;
                    $requestCompany = $requestData->company;
                } else {
                    $requestData = Register::find($item->request_id);
                    $requestName = $requestData->full_name;
                    $requestCompany = $requestData->company;
                }

                $res = collect([
                    'status' => true,
                    'name' =>  $requestName,
                    'company' =>  $requestCompany,
                ]);
            }
        }

        return $res;
    }

    public function getAppointmentBackoffice(Request $request)
    {


        $requestStatus          = $request->input("request");
        $cancelStatus          = $request->input("status_cancel");
        $meetingStatus          = $request->input("status_meeting");
        $start_date          = $request->input("start_date");
        $search           = $request->input('search');
        $itemsPerPage    = $request->input('itemsPerPage');
        $page          = $request->input("page");
        $sortBy          = $request->input('sortBy');
        $sortDesc        = $request->input('sortDesc');

        $start         = 0;
        $_itemsPerPage = 0;
        if ($itemsPerPage) {
            $_itemsPerPage = $itemsPerPage;
        }

        if ($page > 1) {
            $start = $_itemsPerPage * ($page - 1);
        }

        DB::statement(DB::raw('set @row=' . $start));
        $data = MatchingReport::selectRaw('*, @row:=@row+1 as row_no');


        // $data = SlotAppointment::with([
        //     'requestTime' => function ($query) {
        //         $query->select('id', 'slot_time')->with('slotTime');
        //     }, 'outGoingRegister', 'inComingRegister', 'outGoingExhibitor', 'inComingExhibitor'
        // ]);

        // $data = SlotAppointment::with([
        //     'requestTime' => function ($query) use ($start_date) {
        //         $query->select('id', 'slot_time')->with(['slotTime' => function ($query) use ($start_date) {
        //             if ($start_date && $start_date != "") {

        //                 $query->select('id', 'start_date', 'start_time', 'end_time')->where('start_date', $start_date);
        //             }
        //         }]);
        //     }, 'outGoingRegister', 'inComingRegister', 'outGoingExhibitor', 'inComingExhibitor'
        // ]);


        if ($search && $search != "") {
            $data = $data->where("request_company", "like", "%{$search}%");
            $data = $data->orWhere("owner_company", "like", "%{$search}%");
        }

        if ($requestStatus && $requestStatus != "") {
            $data = $data->where("status_request", "=", $requestStatus);
        }

        if ($cancelStatus && $cancelStatus != "") {
            $data = $data->where("status_cancel", "=", $cancelStatus);
        }
        if ($meetingStatus && $meetingStatus != "") {
            $data = $data->where("meeting_status", "=", 'create');
        }
        if ($start_date && $start_date != "") {
            $data = $data->where("start_date", "=", $start_date);
        }
        $countAll        = $data->count();
        $countAccept   = MatchingReport::where("status_request", "=", "accept")->count();
        $countRequest = MatchingReport::where("status_request", "=", "request")->count();
        $countDecline = MatchingReport::where("status_request", "=", "reject")->count();
        $countCancel = MatchingReport::where("status_request", "=", "")->count();


        if ($sortBy != "") {
            if ($sortDesc) {
                $data = $data->orderBy($sortBy, "DESC");
            } else {
                $data = $data->orderBy($sortBy, "ASC");
            }
        } else {
            $data = $data->orderBy('start_date', 'ASC');
            $data = $data->orderBy('start_time', 'ASC');
        }


        $data = $data->paginate($itemsPerPage ? $itemsPerPage : 100);

        $sum = collect([
            'count_all'        => $countAll,
            'count_accept'         => $countAccept,
            'count_request'      => $countRequest,
            'count_decline'   => $countDecline,
            'count_cancel' => $countCancel,

        ]);

        // $data_rp["data"] = $data;
        // $data_rp["sum"] = $sum;

        // return $data_rp;
        return $sum->merge($data);
    }

    public function exportExcel()
    {
        $today = date('YmdHis');
        return Excel::download(new MatchingExport, 'business_matching_export_' . $today . '.xlsx');
    }


    public function exportExcelCancel()
    {
        $today = date('YmdHis');
        return Excel::download(new MatchingExportCancel, 'business_matching_cancel_export_' . $today . '.xlsx');
    }

    public function exportExcelAll()
    {
        $today = date('YmdHis');
        return Excel::download(new MatchingExportAll, 'business_matching_export_' . $today . '.xlsx');
    }
}
