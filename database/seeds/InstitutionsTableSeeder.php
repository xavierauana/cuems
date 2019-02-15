<?php

use Illuminate\Database\Seeder;

class InstitutionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $hospitals = [
            "Alice Ho Miu Ling Nethersole Hospital",
            "Bradbury Hospice",
            "Canossa Hospital",
            "Caritas Medical Centre",
            "Castle Peak Hospital",
            "Cheshire Home, Chung Hom Kok",
            "Cheshire Home, Shatin",
            "Department of Health",
            "Evangel Hospital",
            "Gleneagles Hong Kong Hospital",
            "Grantham Hospital",
            "Haven of Hope Hospital",
            "Hong Kong Adventist Hospital - Stubbs Road",
            "Hong Kong Adventist Hospital - Tsuen Wan",
            "Hong Kong Baptist Hospital",
            "Hong Kong Baptist University",
            "Hong Kong Buddhist Hospital",
            "Hong Kong Children's Hospital",
            "Hong Kong Eye Hospital",
            "Hong Kong Red Cross Blood Transfusion Service",
            "Hong Kong Sanatorium & Hospital",
            "Kowloon Hospital",
            "Kwai Chung Hospital",
            "Kwong Wah Hospital",
            "MacLehose Medical Rehabilitation Centre",
            "Matilda International Hospital",
            "North District Hospital",
            "North Lantau Hospital",
            "Our Lady of Maryknoll Hospital",
            "Pamela Youde Nethersole Eastern Hospital",
            "Pok Oi Hospital",
            "Precious Blood Hospital",
            "Prince of Wales Hospital",
            "Princess Margaret Hospital",
            "Queen Elizabeth Hospital",
            "Queen Mary Hospital",
            "Ruttonjee Hospital",
            "Shatin Hospital",
            "Siu Lam Hospital",
            "St. John Hospital",
            "St. Paul's Hospital",
            "St. Teresa's Hospital",
            "Tai Po Hospital",
            "Tang Shiu Kin Hospital",
            "The Chinese University of Hong Kong",
            "The Duchess of Kent Children's Hospital at Sandy Bay",
            "The University of Hong Kong ",
            "Tin Shui Wai Hospital",
            "Tsan Yuk Hospital",
            "Tseung Kwan O Hospital",
            "Tuen Mun Hospital",
            "Tung Wah Eastern Hospital",
            "Tung Wah Group of Hospitals Fung Yiu King Hospital",
            "Tung Wah Hospital",
            "TWGHs Wong Tai Sin Hospital",
            "Union Hospital",
            "United Christian Hospital",
            "Wong Chuk Hang Hospital",
            "Yan Chai Hospital",
            "Others",
        ];

        foreach ($hospitals as $hospital) {
            \App\Institution::create([
                'name' => $hospital
            ]);
        }
    }
}
