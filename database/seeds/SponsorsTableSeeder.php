<?php

use Illuminate\Database\Seeder;

class SponsorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $sponsors = [
            "The Chinese University of Hong Kong (Department of Medicine & Therapeutics)",
            "The Chinese University of Hong Kong (University Health Service)",
            "Shatin Hospital (Department of Medicine & Geriatrics)",
            "Alice Ho Miu Ling Nethersole Hospital (Department of Medicine & Geriatrics)",
            "North District Hospital (Department of Medicine & Geriatrics)",
            "Pok Oi Hospital (Department of Medicine & Geriatrics)",
            "Tuen Mun Hospital (Department of Medicine & Geriatrics)",
            "Princess Margaret Hospital (Department of Medicine & Geriatrics, Department of Clinical Oncology)",
            "Yan Chai Hospital (Department of Medicine & Geriatrics)",
            "Kwong Wah Hospital (Department of Medicine & Geriatrics)",
            "Our Lady of Maryknoll Hospital (Department of Medicine & Geriatrics)",
            "Queen Elizabeth Hospital (Department of Medicine)",
            "United Christian Hospital (Department of Medicine & Geriatrics)",
            "Tseung Kwan O Hospital (Department of Medicine & Geriatrics)",
            "Caritas Medical Centre (Department of Medicine & Geriatrics)",
            "TWGHs Wong Tai Sin Hospital (Department of Rehabilitation and Extended Care, Department of Tuberculosis and Chest)",
            "Kowloon Hospital (Department of Medicine)",
            "Ruttonjee Hospital (Department of Medicine)",
            "A. Menarini Hong Kong Limited",
            "Abbott Laboratories Ltd.",
            "AbbVie Limited",
            "Allergan Asia Limited",
            "Amgen",
            "Astellas Pharma Hong Kong Co., Ltd",
            "AstraZeneca Hong Kong Limited",
            "Bayer HealthCare Ltd.",
            "Boehringer Ingelheim (HK) Ltd.",
            "Bristol-Myers Squibb (HK) Ltd.",
            "Celki Medical Company",
            "Celltrion Healthcare Co. Ltd",
            "Chong Lap (HK) Co. Ltd.",
            "Daiichi Sankyo Hong Kong Limited",
            "DKSH Hong Kong Limited",
            "Eisai (HK) Co. Ltd",
            "Eli Lilly Asia, Inc.",
            "Elsevier (Singapore) Ptd. Ltd.",
            "Ferring Pharmaceuticals Ltd.",
            "Fresenius Medical Care Hong Kong Ltd.",
            "Galderma Hong Kong Limited",
            "Gilead Sciences Hong Kong Limited",
            "Given Imaging (Asia) Company Limited",
            "GlaxoSmithKline Limited",
            "Hovid Limited ",
            "Invida (Hong Kong) Limited",
            "Jacobson Medical (HK) Ltd.",
            "Janssen, a division of Johnson & Johnson (HK) Ltd",
            "Janssen, Johnson & Johnson (HK) Ltd.",
            "Kyowa Hakko Kirin (Hong Kong) Co. Ltd.",
            "Lumenis (HK) Ltd.",
            "Lundbeck Export A/S",
            "McBarron Book Co. Ltd.",
            "Merck Pharmaceutical (HK) Limited",
            "Merck Sharp & Dohme (Asia) Limited",
            "Mundipharma (Hong Kong) Limited",
            "National Australia Bank",
            "Novartis Pharmaceuticals (HK) Limited",
            "Novartis Pharmaceuticals (HK) Ltd.",
            "Novo Nordisk Hong Kong Limited",
            "Nutricia Clinical (HK) Limited",
            "OrbusNeich ",
            "Orient Europharma Co., Ltd",
            "Pfizer Corporation HK Limited",
            "Prenetics Limited",
            "Reckitt Benckiser (RB)",
            "ResMed Hong Kong Limited",
            "Roche (Hong Kong) Ltd.",
            "Sanofi-aventis Hong Kong Limited",
            "Servier Hong Kong Ltd.",
            "Springer Asia Limited",
            "Takeda Pharmaceuticals (H.K.) Ltd.",
            "The Homecare Medical Limited",
            "TRB Chemedica HK Ltd.",
            "Others",
        ];

        $event = \App\Event::first();

        foreach ($sponsors as $sponsor) {
            $event->sponsors()->create(['name' => $sponsor]);
        }
    }
}
