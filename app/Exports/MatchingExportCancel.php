<?php

namespace App\Exports;

use App\Model\FindAbout;
use App\Model\ReasonForAttending;
use App\Model\Register;
use App\Model\Exhibitor;

use App\Model\MainCate;
use App\Model\SlotAppointment;
use App\Model\LogCancel;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MatchingExportCancel implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = LogCancel::all();


        $index = 0;

        // echo json_encode($data);
        foreach ($data as $log) {

            $appointment = json_decode($log->appointment_data);
            $slot =  json_decode($log->slot_data);
            //    echo $appointment->request_id;

            $requestName = '';
            $requestComapny = '';
            $ownerName = '';
            $ownerCompany = '';
            // echo json_encode($appointment['']);
            if ($appointment->request_type == 'buyer') {
                $request = Register::find($appointment->request_id);
                $requestComapny = $request->company;
                $requestName =  $request->full_name;
            } else {
                $request = Exhibitor::find($appointment->request_id);
                $requestComapny = $request->company;
                $requestName =  $request->m_name;
            }

            if ($appointment->owner_type == 'buyer') {
                $owner = Register::find($appointment->owner_id);
                $ownerCompany =  $owner->company;
                $ownerName =  $owner->full_name;
            } else {
                $owner = Exhibitor::find($appointment->owner_id);
                $ownerCompany =  $owner->company;
                $ownerName = $owner->m_name;
            }

            // type
            $type = '';
            if ($appointment->type == 'E2E') {
                $type = 'exhibitor to exhibitor';
            } else if ($appointment->type == 'E2V') {
                $type = 'exhibitor to buyer';
            } else  if ($appointment->type == 'V2E') {
                $type = 'buyer to exhibitor';
            }

            $log->request_company =  $requestComapny;
            $log->request_name =  $requestName;

            $log->owner_company = $ownerCompany;
            $log->owner_name = $ownerName;

            $log->status_cancel = $log->type;

            $log->type = $type; // e2v v2e
            $log->start_date = $slot->slot_time->start_date;
            $log->start_time = $slot->slot_time->start_time;
            $log->end_time = $slot->slot_time->end_time;
            $log->index = $index++;

            $log->request_type = $appointment->request_type;
            $log->owner_type = $appointment->owner_type;

            $cancel_by = '';
            if ($log->role == 'buyer') {
                $cancel =  Register::find($log->request_id);
            } else {
                $cancel =  Exhibitor::find($log->request_id);
            }

            $log->cancel_by = $cancel->company;
        }

        return $data;
    }

    public function map($log): array
    {
        return [
            $log->index,
            $log->request_company,
            $log->request_name,
            $log->owner_company,
            $log->owner_name,
            $log->request_type,
            $log->owner_type,
            $log->type,
            $log->status_appointment,
            $log->status_cancel,
            $log->cancel_by,
            $log->start_date,
            $log->start_time,
            $log->end_time,
            $log->meeting_id,
            $log->meeting_status,
            $log->rating_1,
            $log->rating_2,

        ];
    }

    public function headings(): array
    {
        return [
            [
                'No.',
                'request_company',
                'request_name',
                'owner_company',
                'owner_name',
                'request_type',
                'owner_type',
                'type',
                'status_request',
                'status_cancel',
                'cancel_by',
                'start_date',
                'start_time',
                'end_time',
                'meeting_room',
                'meeting_status',
                'request_rating',
                'owner_rating'

            ],
        ];
    }
}
