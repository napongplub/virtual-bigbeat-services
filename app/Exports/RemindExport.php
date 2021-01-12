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
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Illuminate\Support\Facades\Crypt;

class RemindExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithCustomValueBinder, WithMapping, ShouldAutoSize
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Register::get();

        $index = 0;

        foreach ($data as $register) {
            // echo json_encode($data);

            $register->fullname = $register->fname . ' ' . $register->lname;

        }

        return $data;
    }

    public function map($register): array
    {
        return [
            $register->fullname,
            $register->email,
            $this->decrypt_str($register->p_hash),
            $register->type == 1 ? "Buyer" : "Visitor",
        ];
    }
    public function bindValue(Cell $cell, $value)
    {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
    }
    public function headings(): array
    {
        return [
            [
                'Name',
                'Email',
                'Password',
                'Type',

            ],
        ];
    }
    public function decrypt_str($str)
    {
        try {

            $data = Crypt::decryptString($str);
            return $data;
        } catch (\Throwable $th) {
            return '';
        }

    }
}
