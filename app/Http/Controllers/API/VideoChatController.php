<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\Exhibitor;
use Illuminate\Http\Request;
use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\Role;
use App\Model\Register;
use App\Model\SlotAppointment;
use App\Model\SlotAvailable;
use App\Model\LogJoin;

use App\Model\RegisterMatching;
use App\Model\ExhibitorMatching;


class VideoChatController extends Controller
{
    private $apiKey;
    private $secretKey;

    public function __construct()
    {
        $this->apiKey = config("videoCall.OPENTOK_API_KRY");
        $this->secretKey = config("videoCall.OPENTOK_SECRET_KEY");
    }



    function createSession()
    {

        // return response()->json([
        //     "session_id" => "2_MX40Njk5NjY3NH5-MTYwNjAzMDEyMzQyMn5CejJITkJKc2lxYVBtVFRJbFR1NGJMTzd-fg",
        //     "token" => "T1==cGFydG5lcl9pZD00Njk5NjY3NCZzaWc9Yjg1NTYwMmMzYjYwYjkxNDAwYjYyMzFkMTBjMDUwZDY1NTYxNDVjNTpzZXNzaW9uX2lkPTJfTVg0ME5qazVOalkzTkg1LU1UWXdOakF6TURFeU16UXlNbjVDZWpKSVRrSktjMmx4WVZCdFZGUkpiRlIxTkdKTVR6ZC1mZyZjcmVhdGVfdGltZT0xNjA2MDMwMTIzJnJvbGU9cHVibGlzaGVyJm5vbmNlPTE2MDYwMzAxMjMuNzA1MTE1ODYwODIyNyZleHBpcmVfdGltZT0xNjA2MDQwOTIzJmluaXRpYWxfbGF5b3V0X2NsYXNzX2xpc3Q9"
        // ]);

        try {
            $openTok = new OpenTok($this->apiKey, $this->secretKey);
            $session = $openTok->createSession(array('mediaMode' => MediaMode::ROUTED));
            $sessionId = $session->getSessionId();

            $token = $openTok->generateToken($sessionId, [
                'role' => Role::PUBLISHER,
                'expireTime' => time() + (3600 * 3)
            ]);

            return response()->json([
                "session_id" => $sessionId,
                "token" => $token
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getVisitorById($id)
    {
        $data = Register::select(["id", "fname", "lname", "company"])->findOrFail($id);

        return $data;
    }

    public function createVideoMatchingRoom(Request $request)
    {


        $appointmentId               = $request->get('appointmentId');
        $userId = $request->get('id');
        $role = $request->get('role');
        $meeting_status = "";
        $meeting_room_log = "";

        $appointment =  SlotAppointment::find($appointmentId);
        $slotData = SlotAvailable::with('slotTime')->find($appointment->slot_time);

        if ($appointment->meeting_status == '' ||  null) {
            $openTok = new OpenTok($this->apiKey, $this->secretKey);
            $session = $openTok->createSession(array('mediaMode' => MediaMode::ROUTED));
            $sessionId = $session->getSessionId();

            $token = $openTok->generateToken($sessionId, [
                'role' => Role::PUBLISHER,
                'expireTime' => time() + (3600 * 3)
            ]);
            // $sessionId = "2_MX40Njk5NjY3NH5-MTYwNjAzMDEyMzQyMn5CejJITkJKc2lxYVBtVFRJbFR1NGJMTzd-fg";
            // $token = "T1==cGFydG5lcl9pZD00Njk5NjY3NCZzaWc9Yjg1NTYwMmMzYjYwYjkxNDAwYjYyMzFkMTBjMDUwZDY1NTYxNDVjNTpzZXNzaW9uX2lkPTJfTVg0ME5qazVOalkzTkg1LU1UWXdOakF6TURFeU16UXlNbjVDZWpKSVRrSktjMmx4WVZCdFZGUkpiRlIxTkdKTVR6ZC1mZyZjcmVhdGVfdGltZT0xNjA2MDMwMTIzJnJvbGU9cHVibGlzaGVyJm5vbmNlPTE2MDYwMzAxMjMuNzA1MTE1ODYwODIyNyZleHBpcmVfdGltZT0xNjA2MDQwOTIzJmluaXRpYWxfbGF5b3V0X2NsYXNzX2xpc3Q9";

            $roomId = $this->generateMeetingId();
            $roomCode = $this->generateMeetingCode();
            $appointment->meeting_id = $roomId;
            $appointment->meeting_code = $roomCode;
            $appointment->meeting_status = 'create';
            $appointment->create_room_at =  date('Y-m-d H:i:s');

            $appointment->save();

            $url = 'https://firestore.googleapis.com/v1/projects/virtual-cebit'
                . '/databases/(default)/documents/video-chat?documentId=' .  $roomId;


            if ($appointment->type == "V2E" || $appointment->type == "E2V") {

                if ($appointment->request_type == "buyer") {
                    $visitor_id = $appointment->request_id;
                    $exhibitor_id = $appointment->owner_id;
                } else if ($appointment->request_type == "exhibitor") {
                    $exhibitor_id = $appointment->request_id;
                    $visitor_id = $appointment->owner_id;
                }

                $data = array("fields" => (object)array(
                    "session_id" => array("stringValue" => $sessionId),
                    "token" => array("stringValue" =>  $token),
                    "exhibitor_id" => array("integerValue" => $exhibitor_id),
                    "visitor_id" => array("integerValue" =>   $visitor_id),
                    "type" => array("stringValue" =>   'V_TO_E'),
                    "code" => array("stringValue" =>   $roomCode),
                    "createdAt" => array("timestampValue" =>   now()),
                    "time_start" => array("stringValue" =>   $slotData->slotTime->start_time),
                    "time_end" => array("stringValue" =>   $slotData->slotTime->end_time),
                    "date_start" => array("stringValue" =>   $slotData->slotTime->start_date),
                    "appointment_id" => array("integerValue" =>   $appointmentId),

                ));
            } else if ($appointment->type == "E2E") {

                $data = array("fields" => (object)array(
                    "session_id" => array("stringValue" => $sessionId),
                    "token" => array("stringValue" =>  $token),
                    "exhibitor_receive_id" => array("integerValue" =>  $appointment->owner_id),
                    "exhibitor_sender_id" => array("integerValue" =>    $appointment->request_id),
                    "type" => array("stringValue" =>   'E_TO_E'),
                    "code" => array("stringValue" =>   $roomCode),
                    "createdAt" => array("timestampValue" =>   now()),
                    "time_start" => array("stringValue" =>   $slotData->slotTime->start_time),
                    "time_end" => array("stringValue" =>   $slotData->slotTime->end_time),
                    "date_start" => array("stringValue" =>   $slotData->slotTime->start_date),
                    "appointment_id" => array("integerValue" =>   $appointmentId),


                ));
            }
            $payload = json_encode($data, JSON_UNESCAPED_SLASHES);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLINFO_HEADER_OUT, FALSE);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "Content-Length: " . strlen($payload),
                "X-HTTP-Method-Override: POST"
            ));

            $response = curl_exec($ch);
            curl_close($ch);
            // echo $response;
            $res = collect([
                'status' => 'create',
                'roomId' =>  $roomId,
            ]);

            $meeting_status = "create";
            $meeting_room_log = $roomId;

            $newAppointment =  SlotAppointment::find($appointmentId);

            $log =  new LogJoin();
            $log->join_id = $userId;
            $log->role = $role;
            $log->appointment_id = $appointmentId;
            $log->appointment_data = json_encode($newAppointment);
            $log->slot_data = json_encode($slotData);
            $log->meeting_status = $meeting_status;
            $log->meeting_id = $meeting_room_log;

            $log->save();

            return $res;
        } else {
            $res = collect([
                'status' => 'start',
                'roomId' =>  $appointment->meeting_id,
            ]);

            $meeting_status = "join";
            $meeting_room_log = $appointment->meeting_id;
            $newAppointment =  SlotAppointment::find($appointmentId);

            $log =  new LogJoin();
            $log->join_id = $userId;
            $log->role = $role;
            $log->appointment_id = $appointmentId;
            $log->appointment_data = json_encode($newAppointment);
            $log->slot_data = json_encode($slotData);
            $log->meeting_status = $meeting_status;
            $log->meeting_id = $meeting_room_log;

            $log->save();
            return $res;
        }
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

    public function createRating(Request $request)
    {

        $appointmentId               = $request->get('appointmentId');
        $rating               = $request->get('rating');
        $isExhibitor            = $request->get('isExhibitor');


        $appointment =  SlotAppointment::find($appointmentId);

        if ($appointment->type == "V2E" || $appointment->type == "E2V") {
            if ($isExhibitor) {
                if ($appointment->request_type == "exhibitor") {
                    $appointment->rating_1 = $rating;
                    $appointment->rating_1_at =  date('Y-m-d H:i:s');
                } else if ($appointment->owner_type == "exhibitor") {
                    $appointment->rating_2 = $rating;
                    $appointment->rating_2_at =  date('Y-m-d H:i:s');
                }
            } else {
                if ($appointment->request_type == "buyer") {
                    $appointment->rating_1 = $rating;
                    $appointment->rating_1_at =  date('Y-m-d H:i:s');
                } else if ($appointment->owner_type == "buyer") {
                    $appointment->rating_2 = $rating;
                    $appointment->rating_2_at =  date('Y-m-d H:i:s');
                }
            }
        }
        if ($appointment->type == "E2E") {
            if ($appointment->rating_1  == "" || $appointment->rating_1 == null) {
                $appointment->rating_1 = $rating;
                $appointment->rating_1_at =  date('Y-m-d H:i:s');
            } else {
                $appointment->rating_2 = $rating;
                $appointment->rating_2_at =  date('Y-m-d H:i:s');
            }
        }
        $appointment->save();
    }

    public function createRatingBack(Request $request)
    {

        $appointmentId               = $request->get('appointmentId');
        $rating               = $request->get('rating');
        $isExhibitor            = $request->get('isExhibitor');
        $userId            = $request->get('userId');
        $role            = $request->get('role');


        $appointment =  SlotAppointment::find($appointmentId);

        if ($appointment->type == "V2E" || $appointment->type == "E2V") {
            if ($isExhibitor) {
                if ($appointment->request_type == "exhibitor") {
                    $appointment->rating_1 = $rating;
                    $appointment->rating_1_at =  date('Y-m-d H:i:s');
                } else if ($appointment->owner_type == "exhibitor") {
                    $appointment->rating_2 = $rating;
                    $appointment->rating_2_at =  date('Y-m-d H:i:s');
                }
            } else {
                if ($appointment->request_type == "buyer") {
                    $appointment->rating_1 = $rating;
                    $appointment->rating_1_at =  date('Y-m-d H:i:s');
                } else if ($appointment->owner_type == "buyer") {
                    $appointment->rating_2 = $rating;
                    $appointment->rating_2_at =  date('Y-m-d H:i:s');
                }
            }
        }
        if ($appointment->type == "E2E") {
            if ($userId == $appointment->request_id && $role == "exhibitor") {
                $appointment->rating_1 = $rating;
                $appointment->rating_1_at =  date('Y-m-d H:i:s');
            } else if ($userId == $appointment->owner_id && $role == "exhibitor") {
                $appointment->rating_2 = $rating;
                $appointment->rating_2_at =  date('Y-m-d H:i:s');
            }
            // if ($appointment->rating_1  == "" || $appointment->rating_1 == null) {
            //     $appointment->rating_1 = $rating;
            //     $appointment->rating_1_at =  date('Y-m-d H:i:s');
            // } else {
            //     $appointment->rating_2 = $rating;
            //     $appointment->rating_2_at =  date('Y-m-d H:i:s');
            // }
        }
        $appointment->save();
    }

    public function getBoothVideoCallList(Request $request)
    {
        $data               = $request->get('data');
        $data_rp = array();
        foreach ($data as $key => $value) {

            $time = $value['createdAt']['seconds'];
            $exhibitorId =  $value['exhibitor_id'];
            $registerId =  $value['visitor_id'];

            $exhibitor = Exhibitor::find($exhibitorId);
            $register = Register::find($registerId);

            $res = collect([
                'exhibitorName' => $exhibitor->name,
                'exhibitorCompany' =>  $exhibitor->company,
                'visitorName' =>  $register->full_name,
                'visitorCompany' =>  $register->company,
                'callTime' => date('Y-m-d H:i:s', $time)
            ]);

            $data_rp[$key] =  $res;
        }
        // echo json_encode($data);
        return $data_rp;
    }
}
