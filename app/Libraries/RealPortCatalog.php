<?php

namespace App\Libraries;

/**
 * Curated starter catalogue of real international seaports.
 *
 * Coordinates are representative operational map points intended for software
 * demos and data entry assistance. They are not nautical navigation data.
 */
final class RealPortCatalog
{
    /**
     * @return array<int, array<string, int|float|string>>
     */
    public static function all(): array
    {
        return [
            self::port('CNSHA', 'Port of Shanghai', 'ميناء شنغهاي', 'CN', 'China', 'الصين', 'Shanghai', 'شنغهاي', 31.230400, 121.473700, 'Asia/Shanghai'),
            self::port('SGSIN', 'Port of Singapore', 'ميناء سنغافورة', 'SG', 'Singapore', 'سنغافورة', 'Singapore', 'سنغافورة', 1.264400, 103.840000, 'Asia/Singapore'),
            self::port('CNNGB', 'Port of Ningbo-Zhoushan', 'ميناء نينغبو تشوشان', 'CN', 'China', 'الصين', 'Ningbo', 'نينغبو', 29.868300, 121.544000, 'Asia/Shanghai'),
            self::port('CNSZX', 'Port of Shenzhen', 'ميناء شينزن', 'CN', 'China', 'الصين', 'Shenzhen', 'شينزن', 22.543100, 114.057900, 'Asia/Shanghai'),
            self::port('CNQDG', 'Port of Qingdao', 'ميناء تشينغداو', 'CN', 'China', 'الصين', 'Qingdao', 'تشينغداو', 36.067100, 120.382600, 'Asia/Shanghai'),
            self::port('KRPUS', 'Port of Busan', 'ميناء بوسان', 'KR', 'South Korea', 'كوريا الجنوبية', 'Busan', 'بوسان', 35.102800, 129.040300, 'Asia/Seoul'),
            self::port('HKHKG', 'Port of Hong Kong', 'ميناء هونغ كونغ', 'HK', 'Hong Kong', 'هونغ كونغ', 'Hong Kong', 'هونغ كونغ', 22.319300, 114.169400, 'Asia/Hong_Kong'),
            self::port('MYPKG', 'Port Klang', 'ميناء كلانغ', 'MY', 'Malaysia', 'ماليزيا', 'Klang', 'كلانغ', 3.000000, 101.400000, 'Asia/Kuala_Lumpur'),
            self::port('MYTPP', 'Port of Tanjung Pelepas', 'ميناء تانجونغ بيليباس', 'MY', 'Malaysia', 'ماليزيا', 'Gelang Patah', 'جيلانغ باتاه', 1.362000, 103.550000, 'Asia/Kuala_Lumpur'),
            self::port('LKCMB', 'Port of Colombo', 'ميناء كولومبو', 'LK', 'Sri Lanka', 'سريلانكا', 'Colombo', 'كولومبو', 6.950000, 79.840000, 'Asia/Colombo'),
            self::port('JPTYO', 'Port of Tokyo', 'ميناء طوكيو', 'JP', 'Japan', 'اليابان', 'Tokyo', 'طوكيو', 35.620000, 139.780000, 'Asia/Tokyo'),
            self::port('JPYOK', 'Port of Yokohama', 'ميناء يوكوهاما', 'JP', 'Japan', 'اليابان', 'Yokohama', 'يوكوهاما', 35.450000, 139.640000, 'Asia/Tokyo'),
            self::port('AEJEA', 'Jebel Ali Port', 'ميناء جبل علي', 'AE', 'United Arab Emirates', 'الإمارات العربية المتحدة', 'Dubai', 'دبي', 25.011000, 55.061000, 'Asia/Dubai'),
            self::port('AEPRA', 'Port Rashid', 'ميناء راشد', 'AE', 'United Arab Emirates', 'الإمارات العربية المتحدة', 'Dubai', 'دبي', 25.275000, 55.281000, 'Asia/Dubai'),
            self::port('OMSLL', 'Port of Salalah', 'ميناء صلالة', 'OM', 'Oman', 'عُمان', 'Salalah', 'صلالة', 16.940000, 54.000000, 'Asia/Muscat'),
            self::port('OMSOH', 'Port of Sohar', 'ميناء صحار', 'OM', 'Oman', 'عُمان', 'Sohar', 'صحار', 24.500000, 56.630000, 'Asia/Muscat'),
            self::port('SAJED', 'Jeddah Islamic Port', 'ميناء جدة الإسلامي', 'SA', 'Saudi Arabia', 'المملكة العربية السعودية', 'Jeddah', 'جدة', 21.490000, 39.170000, 'Asia/Riyadh'),
            self::port('SADMM', 'King Abdulaziz Port', 'ميناء الملك عبدالعزيز', 'SA', 'Saudi Arabia', 'المملكة العربية السعودية', 'Dammam', 'الدمام', 26.430000, 50.100000, 'Asia/Riyadh'),
            self::port('QADOH', 'Hamad Port', 'ميناء حمد', 'QA', 'Qatar', 'قطر', 'Doha', 'الدوحة', 25.020000, 51.610000, 'Asia/Qatar'),
            self::port('BHKBS', 'Khalifa Bin Salman Port', 'ميناء خليفة بن سلمان', 'BH', 'Bahrain', 'البحرين', 'Hidd', 'الحد', 26.200000, 50.700000, 'Asia/Bahrain'),
            self::port('KWSAA', 'Shuwaikh Port', 'ميناء الشويخ', 'KW', 'Kuwait', 'الكويت', 'Kuwait City', 'مدينة الكويت', 29.350000, 47.920000, 'Asia/Kuwait'),
            self::port('EGPSD', 'Port Said', 'ميناء بورسعيد', 'EG', 'Egypt', 'مصر', 'Port Said', 'بورسعيد', 31.265300, 32.301900, 'Africa/Cairo'),
            self::port('EGALY', 'Port of Alexandria', 'ميناء الإسكندرية', 'EG', 'Egypt', 'مصر', 'Alexandria', 'الإسكندرية', 31.190000, 29.880000, 'Africa/Cairo'),
            self::port('JOAQJ', 'Port of Aqaba', 'ميناء العقبة', 'JO', 'Jordan', 'الأردن', 'Aqaba', 'العقبة', 29.526700, 35.007800, 'Asia/Amman'),
            self::port('LBBEY', 'Port of Beirut', 'مرفأ بيروت', 'LB', 'Lebanon', 'لبنان', 'Beirut', 'بيروت', 33.900000, 35.520000, 'Asia/Beirut'),
            self::port('SYLTK', 'Port of Latakia', 'ميناء اللاذقية', 'SY', 'Syria', 'سوريا', 'Latakia', 'اللاذقية', 35.520000, 35.770000, 'Asia/Damascus'),
            self::port('TRAMB', 'Port of Ambarli', 'ميناء أمبارلي', 'TR', 'Türkiye', 'تركيا', 'Istanbul', 'إسطنبول', 40.960000, 28.680000, 'Europe/Istanbul'),
            self::port('TRMER', 'Port of Mersin', 'ميناء مرسين', 'TR', 'Türkiye', 'تركيا', 'Mersin', 'مرسين', 36.800000, 34.640000, 'Europe/Istanbul'),
            self::port('GRPIR', 'Port of Piraeus', 'ميناء بيرايوس', 'GR', 'Greece', 'اليونان', 'Piraeus', 'بيرايوس', 37.942000, 23.646000, 'Europe/Athens'),
            self::port('ITGOA', 'Port of Genoa', 'ميناء جنوة', 'IT', 'Italy', 'إيطاليا', 'Genoa', 'جنوة', 44.405600, 8.946300, 'Europe/Rome'),
            self::port('ESVLC', 'Port of Valencia', 'ميناء فالنسيا', 'ES', 'Spain', 'إسبانيا', 'Valencia', 'فالنسيا', 39.448000, -0.316000, 'Europe/Madrid'),
            self::port('ESALG', 'Port of Algeciras', 'ميناء الجزيرة الخضراء', 'ES', 'Spain', 'إسبانيا', 'Algeciras', 'الجزيرة الخضراء', 36.130000, -5.440000, 'Europe/Madrid'),
            self::port('FRLEH', 'Port of Le Havre', 'ميناء لوهافر', 'FR', 'France', 'فرنسا', 'Le Havre', 'لوهافر', 49.480000, 0.100000, 'Europe/Paris'),
            self::port('NLRTM', 'Port of Rotterdam', 'ميناء روتردام', 'NL', 'Netherlands', 'هولندا', 'Rotterdam', 'روتردام', 51.950000, 4.140000, 'Europe/Amsterdam'),
            self::port('BEANR', 'Port of Antwerp-Bruges', 'ميناء أنتويرب بروج', 'BE', 'Belgium', 'بلجيكا', 'Antwerp', 'أنتويرب', 51.264000, 4.401000, 'Europe/Brussels'),
            self::port('DEHAM', 'Port of Hamburg', 'ميناء هامبورغ', 'DE', 'Germany', 'ألمانيا', 'Hamburg', 'هامبورغ', 53.546100, 9.966100, 'Europe/Berlin'),
            self::port('GBFXT', 'Port of Felixstowe', 'ميناء فيليكسستو', 'GB', 'United Kingdom', 'المملكة المتحدة', 'Felixstowe', 'فيليكسستو', 51.954000, 1.312000, 'Europe/London'),
            self::port('USNYC', 'Port of New York and New Jersey', 'ميناء نيويورك ونيوجيرسي', 'US', 'United States', 'الولايات المتحدة', 'New York', 'نيويورك', 40.680000, -74.050000, 'America/New_York'),
            self::port('USLAX', 'Port of Los Angeles', 'ميناء لوس أنجلوس', 'US', 'United States', 'الولايات المتحدة', 'Los Angeles', 'لوس أنجلوس', 33.736000, -118.264000, 'America/Los_Angeles'),
            self::port('USLGB', 'Port of Long Beach', 'ميناء لونغ بيتش', 'US', 'United States', 'الولايات المتحدة', 'Long Beach', 'لونغ بيتش', 33.754000, -118.216000, 'America/Los_Angeles'),
            self::port('USSAV', 'Port of Savannah', 'ميناء سافانا', 'US', 'United States', 'الولايات المتحدة', 'Savannah', 'سافانا', 32.080000, -81.090000, 'America/New_York'),
            self::port('BRSSZ', 'Port of Santos', 'ميناء سانتوس', 'BR', 'Brazil', 'البرازيل', 'Santos', 'سانتوس', -23.960000, -46.300000, 'America/Sao_Paulo'),
            self::port('ZADUR', 'Port of Durban', 'ميناء ديربان', 'ZA', 'South Africa', 'جنوب أفريقيا', 'Durban', 'ديربان', -29.880000, 31.050000, 'Africa/Johannesburg'),
            self::port('ZACPT', 'Port of Cape Town', 'ميناء كيب تاون', 'ZA', 'South Africa', 'جنوب أفريقيا', 'Cape Town', 'كيب تاون', -33.910000, 18.430000, 'Africa/Johannesburg'),
            self::port('KEMBA', 'Port of Mombasa', 'ميناء مومباسا', 'KE', 'Kenya', 'كينيا', 'Mombasa', 'مومباسا', -4.040000, 39.670000, 'Africa/Nairobi'),
            self::port('DJJIB', 'Port of Djibouti', 'ميناء جيبوتي', 'DJ', 'Djibouti', 'جيبوتي', 'Djibouti', 'جيبوتي', 11.590000, 43.140000, 'Africa/Djibouti'),
            self::port('INNSA', 'Jawaharlal Nehru Port', 'ميناء جواهر لال نهرو', 'IN', 'India', 'الهند', 'Navi Mumbai', 'نافي مومباي', 18.950000, 72.950000, 'Asia/Kolkata'),
            self::port('INMAA', 'Port of Chennai', 'ميناء تشيناي', 'IN', 'India', 'الهند', 'Chennai', 'تشيناي', 13.090000, 80.290000, 'Asia/Kolkata'),
            self::port('PKKHI', 'Port of Karachi', 'ميناء كراتشي', 'PK', 'Pakistan', 'باكستان', 'Karachi', 'كراتشي', 24.840000, 66.990000, 'Asia/Karachi'),
            self::port('AUSYD', 'Port Botany', 'ميناء بوتاني', 'AU', 'Australia', 'أستراليا', 'Sydney', 'سيدني', -33.970000, 151.220000, 'Australia/Sydney'),
            self::port('AUMEL', 'Port of Melbourne', 'ميناء ملبورن', 'AU', 'Australia', 'أستراليا', 'Melbourne', 'ملبورن', -37.830000, 144.930000, 'Australia/Melbourne'),
        ];
    }

    /**
     * @return array<string, int|float|string>
     */
    private static function port(
        string $code,
        string $nameEn,
        string $nameAr,
        string $countryCode,
        string $countryEn,
        string $countryAr,
        string $cityEn,
        string $cityAr,
        float $latitude,
        float $longitude,
        string $timezone
    ): array {
        return [
            'code' => $code,
            'name' => $nameEn,
            'name_en' => $nameEn,
            'name_ar' => $nameAr,
            'country' => $countryEn,
            'country_code' => $countryCode,
            'country_en' => $countryEn,
            'country_ar' => $countryAr,
            'city_en' => $cityEn,
            'city_ar' => $cityAr,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'type' => 'port',
            'timezone' => $timezone,
            'status' => 'active',
            'is_seeded' => 1,
        ];
    }
}
