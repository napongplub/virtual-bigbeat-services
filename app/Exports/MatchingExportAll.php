<?php

namespace App\Exports;

use App\Model\FindAbout;
use App\Model\ReasonForAttending;
use App\Model\Register;
use App\Model\MainCate;
use App\Model\SlotAppointment;
use App\Model\MatchingReport;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MatchingExportAll implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = MatchingReport::all();

        $index = 0;
        // echo json_encode($data);
        // echo json_encode($data);
        foreach ($data as $appointment) {



            $requst_join = '';
            $owner_join = '';
            if($appointment->meeting_request_join == 1){
                $requst_join = $appointment->request_company;
            }
            if($appointment->meeting_owner_join == 1){
                $owner_join = $appointment->owner_company;
            }

            $appointment->meeting_request_join = $requst_join;
            $appointment->meeting_owner_join = $owner_join;
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
            $appointmet->request_type,
            $appointmet->owner_company,
            $appointmet->owner_name,
            $appointmet->owner_type,
            $appointmet->type,
            $appointmet->status_request,
            $appointmet->status_cancel,
            $appointmet->cancel_by,
            $appointmet->meeting_status,
            $appointmet->start_date,
            $appointmet->start_time,
            $appointmet->end_time,
            $appointmet->request_rating,
            $appointmet->owner_rating,
            $appointmet->meeting_request_join,
            $appointmet->meeting_owner_join,
            $appointmet->request_join_time,

        ];
    }

    public function headings(): array
    {
        return [
            [
                'No.',
                'Request Name',
                'Request company',
                'Request Type',
                'Owner Name',
                'Owner Company ',
                'Owner Type',
                'type',
                'Status Request',
                'Status Cancel',
                'Cancel by',
                'Status Meeting',
                'Start Date',
                'Start Time',
                'End Time',
                'Request Rating',
                'Owner Rating',
                'Request Join Company',
                'Owner Join Company',
                'Meeting Duration Time',



            ],
        ];
    }
}
