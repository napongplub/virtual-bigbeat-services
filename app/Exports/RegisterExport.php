<?php

namespace App\Exports;

use App\Model\FindAbout;
use App\Model\ReasonForAttending;
use App\Model\Register;
use App\Model\MainCate;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RegisterExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Register::with([
            'countryRef',
            'natureOfBusinessRef',
            'jobLevelRef',
            'jobFunctionRef',
            'role',
            'numberOfEmployeesRef',
            'category',
            'prefix'
        ])->get();

        $index = 1;

        foreach ($data as $register) {
            // echo json_encode($data);
            $channel = "";
            if ($register->channel == "") {
                $channel = "cebit website";
            } else {
                $channel = $register->channel;
            }
            $reason_for_attending  = json_decode($register->reason_for_attending);
            $_reason_for_attending = [];

            foreach ($reason_for_attending as $key => $value) {
                $temp = ReasonForAttending::find($value);
                if ($temp) {
                    $text = $temp->name_en . ' (' . $temp->name_th . ')';
                    array_push($_reason_for_attending, $text);
                }
            }

            $find_out_about  = json_decode($register->find_out_about);
            $_find_out_about = [];
            foreach ($find_out_about as $key => $value) {
                $temp = FindAbout::find($value);
                if ($temp) {
                    $text = $temp->name_en . ' (' . $temp->name_th . ')';
                    array_push($_find_out_about, $text);
                }
            }
            $interested_category = json_decode($register->category);
            $_interested_category = [];
            foreach ($interested_category as $key => $value) {

                $temp = MainCate::find($value->main_cate_id);

                if ($temp) {
                    $text = $temp->name_en . ' (' . $temp->name_th . ')';
                    array_push($_interested_category, $text);
                }
                // echo json_encode($value->main_cate_id);
            }

            $register->reason_for_attending = \join(" / ", $_reason_for_attending);
            $register->find_out_about       = \join(" / ", $_find_out_about);
            $register->category       = \join(" / ", $_interested_category);
            $register->channel = $channel;
            $register->index = $index++;
        }

        return $data;
    }

    public function map($register): array
    {
        return [
            $register->index,
            $register->channel,
            $register->prefix->name_en . ' / ' . $register->prefix->name_th,
            $register->fname,
            $register->lname,
            $register->position,
            $register->company,
            $register->address,
            $register->city,
            $register->province,
            $register->postal_code,
            $register->countryRef->name,
            $register->telephone,
            $register->mobile,
            $register->email,
            $register->website,
            $register->natureOfBusinessRef->name_en . ' / ' . $register->natureOfBusinessRef->name_th,
            $register->nature_of_business_other,
            $register->jobLevelRef->name_en . ' / ' . $register->jobLevelRef->name_th,
            $register->job_level_other,
            $register->jobFunctionRef->name_en . ' / ' . $register->jobFunctionRef->name_th,
            $register->job_function_other,
            $register->role->name_en . ' / ' . $register->role->name_th,
            $register->numberOfEmployeesRef->name,
            $register->allow_matching,
            $register->category,
            // $register->category->name_en . ' / ' . $register->category->name_th,
            $register->reason_for_attending,
            $register->reason_for_attending_other,
            $register->find_out_about,
            $register->find_out_about_other,
            $register->interested_to_join,
            $register->allow_accept,
            $register->created_at,
        ];
    }

    public function headings(): array
    {
        return [
            [
                'No',
                'Channel',
                'Salutation',
                'First Name',
                'Last Name',
                'Job Title',
                'Organization',
                'Address',
                'City',
                'Province',
                'Postal Code',
                'Country',
                'Telephone',
                'Mobile',
                'Email',
                'Website',
                'Nature of Business',
                'Nature of Business Other',
                'Job Level',
                'Job Level Other',
                'Job Function',
                'Job Function Other',
                'Role',
                'Number of employees',
                'Matching',
                'Center of Interest',
                'Reason for Attending',
                'Reason for Attending Other',
                'Find out about',
                'find out about other',
                'Conference',
                'Privacy Terms',
                'Register Time',
            ],
        ];
    }
}
