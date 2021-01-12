<?php

namespace App\Exports;

use App\Model\Exhibitor;
use App\Model\FindAbout;
use App\Model\ReasonForAttending;
use App\Model\Register;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Throwable;

class WebinarHistoryLogExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    private $exhibitorTargetId;
    private $visitorTargetId;

    public function __construct($exhibitorTargetId, $visitorTargetId)
    {
        $this->exhibitorTargetId = $exhibitorTargetId;
        $this->visitorTargetId = $visitorTargetId;
    }

    public function collection()
    {

        $exhibitorTargetId = $this->exhibitorTargetId;
        $visitorTargetId = $this->visitorTargetId;

        $query = Exhibitor::select(
            "*",
            DB::raw('"Exhibitor" as type_str')
        )
            ->whereIn("id", $exhibitorTargetId)
            ->with([
                "country",
                "mainCateRef"
            ]);
        $exhibitor = $query->get()->toArray();

        $query = Register::select(
            "*",
            DB::raw('"Visitor" as type_str')
        )
            ->whereIn("id", $visitorTargetId)
            ->with([
                "country",
                "mainCateRef",
                "prefixName",
                "natureOfBusinessRef",
                "jobLevelRef",
                "jobFunctionRef",
                "role",
                "numberOfEmployeesRef",
                "budget"
            ]);
        $visitor = $query->get()->toArray();

        // create data mapping
        $reasonForAttMap = [];
        foreach (ReasonForAttending::all() as $tmp) {
            $reasonForAttMap["$tmp->id"] = $tmp->name_en;
        }

        $findOutAbountMap = [];
        foreach (FindAbout::all() as $tmp) {
            $findOutAbountMap["$tmp->id"] = $tmp->name_en;
        }

        // format data
        $data = [];
        foreach ($exhibitor as $detail) {
            array_push($data, [
                "id" => $detail["id"],
                "salutation" => "",
                "name" => $detail["name"],
                "position" => $detail["position"],
                "company" => $detail["company"],
                "address" => $detail["address"],
                "city" => "",
                "province" => "",
                "postalCode" => "",
                "countryName" => $detail["country"]["name"],
                "telephone" => $detail["m_mobile"],
                "mobile" => $detail["mobile"],
                "email" => $detail["email"],
                "website" => $detail["website"],
                "facebook" => $detail["facebook"],
                "youtube" => $detail["youtube"],
                "twitter" => $detail["twitter"],
                "linkedIn" => $detail["linkedin"],
                "companyInsdustry" => "",
                "jobLevel" => "",
                "jobFunction" => "",
                "roleProcess" => "",
                "numberEmp" => "",
                "cateInterest" => $detail["main_cate_ref"],
                "budget" => "",
                "reasonForAtt" => "",
                "findOutAbount" => "",
                "allowBusinessMatching" => "",
                "interestedToJoin" => "",
                "allowAccept" => "",
                "type" => $detail["type_str"]
            ]);
        }

        foreach ($visitor as $detail) {
            $prefixName = $detail["prefix_name"]["id"];
            if ($prefixName == 5) {
                $prefixName = $detail["prefix_name_other"];
            } else {
                $prefixName = $detail["prefix_name"]["name_en"];
            }

            $companyIndustry = $detail["nature_of_business_ref"]["id"];
            if ($companyIndustry == 24) {
                $companyIndustry = $detail["nature_of_business_other"];
            } else {
                $companyIndustry = $detail["nature_of_business_ref"]["name_en"];
            }

            $jobLevel = $detail["job_level_ref"]["id"];
            if ($jobLevel == 8) {
                $jobLevel = $detail["job_level_other"];
            } else {
                $jobLevel = $detail["job_level_ref"]["name_en"];
            }

            $jobFunction = $detail["job_function_ref"]["id"];
            if ($jobFunction == 17) {
                $jobFunction = $detail["job_function_other"];
            } else {
                $jobFunction = $detail["job_function_ref"]["name_en"];
            }

            $reasonForAtt = $this->makeTextFromStringArrayMap($detail["reason_for_attending"], $reasonForAttMap, $detail["reason_for_attending_other"], 9);
            $findOutAbout = $this->makeTextFromStringArrayMap($detail["find_out_about"], $findOutAbountMap, $detail["find_out_about_other"], 13);

            array_push($data, [
                "id" => $detail["id"],
                "salutation" => $prefixName,
                "name" => $detail["fname"] . " " . $detail["lname"],
                "position" => $detail["position"],
                "company" => $detail["company"],
                "address" => $detail["address"],
                "city" => $detail["city"],
                "province" => $detail["province"],
                "postalCode" => $detail["postal_code"],
                "countryName" => $detail["country"]["name"],
                "telephone" => $detail["telephone"],
                "mobile" => $detail["mobile"],
                "email" => $detail["email"],
                "website" => $detail["website"],
                "facebook" => "",
                "youtube" => "",
                "twitter" => "",
                "linkedIn" => "",
                "companyInsdustry" => $companyIndustry,
                "jobLevel" => $jobLevel,
                "jobFunction" => $jobFunction,
                "roleProcess" => $detail["role"]["name_en"],
                "numberEmp" => $detail["number_of_employees_ref"]["name"],
                "cateInterest" => $detail["main_cate_ref"],
                "budget" => $detail["budget"]["name_en"],
                "reasonForAtt" => $reasonForAtt,
                "findOutAbount" => $findOutAbout,
                "allowBusinessMatching" => $detail["allow_matching"],
                "interestedToJoin" => $detail["interested_to_join"],
                "allowAccept" => $detail["allow_accept"],
                "type" => $detail["type_str"]
            ]);
        }

        // add index
        $dataIndex = 1;
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]["index"] = $dataIndex;
            $dataIndex += 1;
        }

        return collect($data);
    }

    public function map($data): array
    {
        return [
            $data["index"],
            $data["salutation"],
            $data["name"],
            $data["position"],
            $data["company"],
            $data["address"],
            $data["city"],
            $data["province"],
            $data["postalCode"],
            $data["countryName"],
            $data["telephone"],
            $data["mobile"],
            $data["email"],
            $data["website"],
            $data["facebook"],
            $data["youtube"],
            $data["twitter"],
            $data["linkedIn"],
            $data["companyInsdustry"],
            $data["jobLevel"],
            $data["jobFunction"],
            $data["roleProcess"],
            $data["numberEmp"],
            $this->concatItemWord($data["cateInterest"], "name_en"),
            $data["budget"],
            $data["reasonForAtt"],
            $data["findOutAbount"],
            $data["allowBusinessMatching"],
            $data["interestedToJoin"],
            $data["allowAccept"],
            $data["type"]
        ];
    }

    public function headings(): array
    {
        return [
            [
                "index",
                "salutation",
                "name",
                "position",
                "company",
                "address",
                "city",
                "province",
                "postalCode",
                "countryName",
                "telephone",
                "mobile",
                "email",
                "website",
                "facebook",
                "youtube",
                "twitter",
                "linkedIn",
                "companyInsdustry",
                "jobLevel",
                "jobFunction",
                "roleProcess",
                "numberEmp",
                "cateInterest",
                "budget",
                "reasonForAtt",
                "findOutAbount",
                "allowBusinessMatching",
                "interestedToJoin",
                "allowAccept",
                "type"
            ]
        ];
    }

    public function concatItemWord($obj, $key)
    {
        $text = "";

        foreach ($obj as $detail) {
            $text .= $detail[$key] . " ,";
        }

        if ($text) {
            $text = substr($text, 0, -2);
        }

        return $text;
    }

    public function makeTextFromStringArrayMap($detail, $textMap, $strOther, $idOther)
    {
        $txt = "";
        if ($detail) {
            $detailToArray = substr($detail, 1, -1);
            $detailToArray = explode(",", $detailToArray);

            foreach ($detailToArray as $id) {
                if ($id == $idOther) {
                    $txt .= $strOther . " ,";
                } else {
                    try {
                        $txt .= $textMap[$id] . " ,";
                    } catch (Throwable $e) {
                    }
                }
            }

            $txt = substr($txt, 0, -2);
        }
        return $txt;
    }
}
