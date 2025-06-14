<?php

namespace App\Services;

class NationalIdValidator
{
    public static function validate(string $nid): array
    {
        $isValidLength = preg_match('/^\d{14}$/', $nid);

        if (!$isValidLength) {
            return [
                'is_valid' => false,
                'message' => 'National ID must be 14 digits.'
            ];
        }

        $century = substr($nid, 0, 1);
        $year = substr($nid, 1, 2);
        $month = substr($nid, 3, 2);
        $day = substr($nid, 5, 2);
        $governorateCode = substr($nid, 7, 2);
        $genderDigit = substr($nid, 12, 1);

        $fullYear = match ($century) {
            '2' => '19' . $year,
            '3' => '20' . $year,
            default => null,
        };

        if (!$fullYear || !checkdate((int)$month, (int)$day, (int)$fullYear)) {
            return [
                'is_valid' => false,
                'message' => 'Invalid birthdate in National ID.'
            ];
        }

        $birthDate = \Carbon\Carbon::createFromDate($fullYear, $month, $day);
        $age = $birthDate->age;

        $gender = ((int)$genderDigit % 2 === 0) ? 'أنثى' : 'ذكر';

        $governorates = self::governorateCodes();
        $governorate = $governorates[$governorateCode] ?? 'غير معروفة';

        return [
            'is_valid' => true,
            'birthdate' => $birthDate->toDateString(),
            'age' => $age,
            'gender' => $gender,
            'governorate' => $governorate,
        ];
    }

    private static function governorateCodes(): array
    {
        return [
            '01' => 'القاهرة',
            '02' => 'الإسكندرية',
            '03' => 'بورسعيد',
            '04' => 'السويس',
            '11' => 'دمياط',
            '12' => 'الدقهلية',
            '13' => 'الشرقية',
            '14' => 'القليوبية',
            '15' => 'كفر الشيخ',
            '16' => 'الغربية',
            '17' => 'المنوفية',
            '18' => 'البحيرة',
            '19' => 'الإسماعيلية',
            '21' => 'الجيزة',
            '22' => 'بني سويف',
            '23' => 'الفيوم',
            '24' => 'المنيا',
            '25' => 'أسيوط',
            '26' => 'سوهاج',
            '27' => 'قنا',
            '28' => 'أسوان',
            '29' => 'الأقصر',
            '31' => 'مطروح',
            '32' => 'البحر الأحمر',
            '33' => 'الوادى الجديد',
            '34' => 'شمال سيناء',
            '35' => 'جنوب سيناء',
            '88' => 'خارج الجمهورية'
        ];
    }
}
