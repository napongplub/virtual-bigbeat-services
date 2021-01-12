<?php

namespace App\Exports;

use App\Model\Exhibitor;
use App\Model\LogVisitBooths;
use App\Model\Register;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use DB;
use Throwable;

class TrafficLogExportV2 implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    private $ownerId;

    public function __construct($ownerId)
    {
        $this->ownerId = $ownerId;
    }

    public function collection()
    {
        $data = LogVisitBooths::where("owner_id", "=", $this->ownerId)->groupBy("actor_id")->get();

        foreach ($data as $key => $value) {
            $temp[$key]["data"] = $value;
            if ($value->actor_type === "exhibitor") {
                $temp[$key]["exhibitor"] = $value->exhibitor;
            } else {
                $data = $value->visitor->toArray();
                // var_dump($data[0]["allow_accept"]);
                // exit;
                // if($data[0]["allow_accept"] == "Y") {

                    $temp[$key]["visitor"] = $value->visitor;
                // }
                // else {
                //     $temp[$key]["visitor"] = [
                //         "fname" => '',
                //         "lname" => '',
                //         "company" => '',
                //         "position" => '',
                //         "email" => '',
                //         "mobile" => '',
                //         "website" => '',
                //         "address" => '',
                //         "mainCate" => [],
                //     ]);
                // }
            }
        }
        return collect($temp);
    }

    public function map($data): array
    {
        // $data["exhibitor"]->toArray()["country"]["name"]
        try {
            $data = [
                isset($data["exhibitor"]) ? $data["exhibitor"][0]->name : $data["visitor"][0]->fname . ' ' . $data["visitor"][0]->lname,
                isset($data["exhibitor"]) ? $data["exhibitor"][0]->company : $data["visitor"][0]->company,
                isset($data["exhibitor"]) ? $data["exhibitor"][0]->position : $data["visitor"][0]->position,
                isset($data["exhibitor"]) ? $data["exhibitor"][0]->email : $data["visitor"][0]->email,
                isset($data["exhibitor"]) ? $data["exhibitor"][0]->mobile : $data["visitor"][0]->mobile,
                isset($data["exhibitor"]) ? $data["exhibitor"][0]->website : $data["visitor"][0]->website,
                isset($data["exhibitor"]) ? $data["exhibitor"][0]->address : $data["visitor"][0]->address,
                isset($data["exhibitor"]) ? $this->concatItemWord($data["exhibitor"][0]->mainCate,"name_en") :$this->concatItemWord($data["visitor"][0]->mainCate,"name_en"),
                isset($data["exhibitor"]) ? "Exhibitor" : "Visitor",
                // isset($data["exhibitor"]) ? $data["exhibitor"]->toArray()["country"]["name"] : $data["visitor"]->toArray()["country"]["name"],
                substr($data["data"]->created_at, 0, -3),
            ];
        }
        catch (Throwable $e) {
            $data = [];
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            [
                "Name",
                "Company",
                "Position",
                "Email",
                "Phone",
                "Website",
                "Address",
                "Main Categories Interest",
                "Type",
                // "Country Name",
                "Visit Time",
            ]
        ];
    }

    public function concatItemWord($obj, $key)
    {
        $text = "";

        foreach ($obj as $detail) {
            $text .= $detail[$key] . " ,";
        }

        return $text;
    }
}
