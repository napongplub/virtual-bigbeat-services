<?php

namespace App\Exports;

use App\Model\FindAbout;
use App\Model\ReasonForAttending;
use App\Model\Register;
use App\Model\MainCate;
use App\Model\SlotAppointment;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MatchingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = SlotAppointment::with([
            'requestTime' => function ($query) {
                $query->select('id', 'slot_time')->with('slotTime');
            }, 'outGoingRegister', 'inComingRegister', 'outGoingExhibitor', 'inComingExhibitor'
        ])->get();

        $index = 0;

        // echo json_encode($data);
        foreach ($data as $appointment) {

            $requestName = '';
            $requestComapny = '';
            $ownerName = '';
            $ownerCompany = '';
            // echo json_encode($appointmet['']);
            if ($appointment->request_type == 'buyer') {
                $requestComapny =  $appointment->inComingRegister[0]->company;
                $requestName =  $appointment->inComingRegister[0]->name;
            } else {
                $requestComapny =  $appointment->inComingExhibitor[0]->company;
                $requestName =  $appointment->inComingExhibitor[0]->m_name;
            }

            if ($appointment->owner_type == 'buyer') {
                $ownerCompany =  $appointment->outGoingRegister[0]->company;
                $ownerName =  $appointment->outGoingRegister[0]->name;
            } else {
                $ownerCompany =  $appointment->outGoingExhibitor[0]->company;
                $ownerName =  $appointment->outGoingExhibitor[0]->m_name;
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

            $appointment->request_company =  $requestComapny;
            $appointment->request_name =  $requestName;

            $appointment->owner_company = $ownerCompany;
            $appointment->owner_name = $ownerName;

            $appointment->type = $type;
            $appointment->start_date = $appointment->requestTime->slotTime->start_date;
            $appointment->start_time = $appointment->requestTime->slotTime->start_time;
            $appointment->end_time = $appointment->requestTime->slotTime->end_time;
            $appointment->index = $index++;
        }

        return $data;
    }

    public function map($appointmet): array
    {
        return [
            $appointmet->index,
            $appointmet->request_company,
            $appointmet->request_name,
            $appointmet->owner_company,
            $appointmet->owner_name,
            $appointmet->request_type,
            $appointmet->owner_type,
            $appointmet->type,
            $appointmet->status_appointment,
            $appointmet->start_date,
            $appointmet->start_time,
            $appointmet->end_time,
            $appointmet->meeting_id,
            $appointmet->meeting_status,
            $appointmet->rating_1,
            $appointmet->rating_2,

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
