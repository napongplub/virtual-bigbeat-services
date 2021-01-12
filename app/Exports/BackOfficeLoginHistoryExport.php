<?php

namespace App\Exports;

use App\Model\Countries;
use App\Model\Exhibitor;
use App\Model\ExhibitorLogin;
use App\Model\FindAbout;
use App\Model\ReasonForAttending;
use App\Model\Register;
use App\Model\VisitorLogin;
use DateTime;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Throwable;

class BackOfficeLoginHistoryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    private $countryId;
    private $mainCateId;
    private $typeId;
    private $search;
    private $startDate;
    private $endDate;

    public function __construct($countryId, $mainCateId, $typeId, $search, $startDate, $endDate)
    {
        $this->countryId = $countryId;
        $this->mainCateId = $mainCateId;
        $this->typeId = $typeId;
        $this->search = $search;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {

        $countryId = $this->countryId;
        $mainCateId = $this->mainCateId;
        $typeId = $this->typeId;
        $search = $this->search;
        $startDate = $this->startDate;
        $endDate = $this->endDate;

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
                    "lastLogIn" => function ($query) use($startDate, $endDate) {
                        if ($startDate && $endDate) {
                            $query->whereRaw(DB::raw("Date(created_at) Between '$startDate' and '$endDate'"));
                        }
                        else if ($startDate) {
                            $query->whereRaw(DB::raw("Date(created_at) = '$startDate'"));
                        }
                    }
                ]);

            // filter
            if ($countryId) {
                $query->where("exhibitor_list.country_id", $countryId);
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
            // $exhibitor = $query->limit(1)->get()->toArray();
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
                    "lastLogIn" => function ($query) use($startDate, $endDate) {
                        if ($startDate && $endDate) {
                            $query->whereRaw(DB::raw("Date(created_at) Between '$startDate' and '$endDate'"));
                        }
                        else if ($startDate) {
                            $query->whereRaw(DB::raw("Date(created_at) = '$startDate'"));
                        }
                    },
                    "prefixName",
                    "natureOfBusinessRef",
                    "jobLevelRef",
                    "jobFunctionRef",
                    "role",
                    "numberOfEmployeesRef",
                    "budget"
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
            // $register = $query->limit(10)->get()->toArray();
        }

        $reasonForAttMap = [];
        foreach (ReasonForAttending::all() as $tmp) {
            $reasonForAttMap["$tmp->id"] = $tmp->name_en;
        }

        $findOutAbountMap = [];
        foreach (FindAbout::all() as $tmp) {
            $findOutAbountMap["$tmp->id"] = $tmp->name_en;
        }

        // format data
        foreach ($exhibitor as $detail) {
            $tmp = new DateTime($detail["last_log_in"]["created_at"]);
            $tmp->modify("+7 hours");
            $lastLoginTime = $tmp->format("Y-m-d H:i:s");

            array_push($data, [
                "id" => $detail["id"],
                "channel" => "",
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
                "type" => $detail["type_str"],
                "lastLoginTime" => $lastLoginTime
            ]);
        }

        foreach ($register as $detail) {
            $tmp = new DateTime($detail["last_log_in"]["created_at"]);
            $tmp->modify("+7 hours");
            $lastLoginTime = $tmp->format("Y-m-d H:i:s");

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

            $channel = strtoupper($detail["channel"]);
            if (!$channel) {
                $channel = "CEBIT WEBSITE";
            }

            array_push($data, [
                "id" => $detail["id"],
                "channel" => $channel,
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
                "type" => $detail["type_str"],
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
        for ($i=0; $i < count($data); $i++) {
            $data[$i]["index"] = $dataIndex;
            $dataIndex += 1;
        }

        return collect($data);
    }

    public function map($data): array
    {
        return [
            $data["index"],
            $data["channel"],
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
            $data["type"],
            $data["lastLoginTime"]
        ];
    }

    public function headings(): array
    {
        return [
            [
                "index",
                "channel (register)",
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
                "type",
                "lastLoginTime"
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
