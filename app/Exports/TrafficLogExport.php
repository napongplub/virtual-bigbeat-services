<?php

namespace App\Exports;

use App\Model\Exhibitor;
use App\Model\LogVisitBooths;
use App\Model\Register;
use DateTime;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;

class TrafficLogExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    private $ownerId;
    private $countryId;
    private $mainCateId;
    private $typeId;
    private $search;

    public function __construct($ownerId, $countryId, $mainCateId, $typeId, $search)
    {
        $this->ownerId = $ownerId;
        $this->countryId = $countryId;
        $this->mainCateId = $mainCateId;
        $this->typeId = $typeId;
        $this->search = $search;
    }

    public function collection()
    {

        $ownerId = $this->ownerId;
        $countryId = $this->countryId;
        $mainCateId = $this->mainCateId;
        $typeId = $this->typeId;
        $search = $this->search;

        // collect all actor_id interact with this owner_id
        $targetExhibitorId = LogVisitBooths::where("owner_id", $ownerId)
            ->where("actor_type", "exhibitor")
            ->distinct()
            ->pluck("actor_id");

        $targetRegisterId = LogVisitBooths::where("owner_id", $ownerId)
            ->where("actor_type", "visitor")
            ->distinct()
            ->pluck("actor_id");

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
                    "brochureBag" => function ($query) {
                        $query->join("brochure_list", "brochure_bag.brochure_id", "=", "brochure_list.id")
                            ->select(
                                "brochure_bag.*",
                                "brochure_list.info"
                            );
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
                ->where("allow_accept", "=", "Y")
                ->whereIn("id", $targetRegisterId)
                ->with([
                    "country",
                    "mainCateRef",
                    "brochureBag" => function ($query) {
                        $query->join("brochure_list", "brochure_bag.brochure_id", "=", "brochure_list.id")
                            ->select(
                                "brochure_bag.*",
                                "brochure_list.info"
                            );
                    },
                    "natureOfBusinessRef",
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
                "name" => $detail["name"],
                "company" => $detail["company"],
                "position" => $detail["position"],
                "email" => $detail["email"],
                "phone" => $detail["mobile"],
                "website" => $detail["website"],
                "address" => $detail["address"],
                "countryName" => $detail["country"]["name"],
                "cateInterest" => $detail["main_cate_ref"],
                "type" => $detail["type_str"],
                "lastVisitAt" => $lastVisitedTime,
                "brochureDownloadList" => $detail["brochure_bag"],
                "industry" => []
            ]);
            $dataIndex += 1;
        }

        foreach ($register as $detail) {
            $tmp = new DateTime($detail["last_visit_booth"]["created_at"]);
            $tmp->modify("+7 hours");
            $lastVisitedTime = $tmp->format("Y-m-d H:i:s");

            array_push($data, [
                "index" => $dataIndex,
                "name" => $detail["fname"] . " " . $detail["lname"],
                "company" => $detail["company"],
                "position" => $detail["position"],
                "email" => $detail["email"],
                "phone" => $detail["mobile"],
                "website" => $detail["website"],
                "address" => $detail["address"],
                "countryName" => $detail["country"]["name"],
                "cateInterest" => $detail["main_cate_ref"],
                "type" => $detail["type_str"],
                "lastVisitAt" => $lastVisitedTime,
                "brochureDownloadList" => $detail["brochure_bag"],
                "industry" => $detail["nature_of_business_ref"]["id"] != 24 ? $detail["nature_of_business_ref"]["name_en"] : $detail["nature_of_business_other"]
            ]);
            $dataIndex += 1;
        }

        return collect($data);
    }

    public function map($data): array
    {
        $industryName = "";
        if ($data["industry"]) {
            $industryName = $data["industry"];
        }

        return [
            $data["index"],
            $data["name"],
            $data["company"],
            $data["position"],
            $data["email"],
            $data["phone"],
            $data["website"],
            $data["address"],
            $data["countryName"],
            $data["type"],
            $industryName,
            $this->concatItemWord($data["cateInterest"], "name_en"),
            $this->concatItemWord($data["brochureDownloadList"], "info"),
            $data["lastVisitAt"],
        ];
    }

    public function headings(): array
    {
        return [
            [
                "No",
                "Name",
                "Company",
                "Position",
                "Email",
                "Phone",
                "Website",
                "Address",
                "Country Name",
                "Type",
                "Industry (Visitor Only)",
                "Categories/Interest",
                "Brochure Downloaded",
                "Last Visit Time"
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
}
