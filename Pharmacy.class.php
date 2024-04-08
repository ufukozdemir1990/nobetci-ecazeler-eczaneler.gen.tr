<?php

/**
 * @author: Ufuk OZDEMIR
 * @creatAt: 07.04.2024 19:46
 * @web: https://www.ufukozdemir.website/
 * @email: ufuk.ozdemir1990@gmail.com
 * @phone: +90 (532) 131 73 07
 */

class Pharmacy
{
    /**
     * City
     * @var string
     */
    private $city;
    /**
     * District
     * @var string
     */
    private $district;
    /**
     * JSON Data
     * @var array
     */
    private $jsonData = [];
    /**
     * Return Data
     * @var array
     */
    private $returnData = [];

    /**
     * Pharmacy constructor.
     * @param $city
     * @param $district
     */
    public function __construct($city, $district)
    {
        $this->city = $city;
        $this->district = $district;
    }

    /**
     * Explode Data
     * @return void
     */
    private function getExplodeData()
    {
        $curlData = $this->fetchDataWithCurl('https://www.eczaneler.gen.tr/nobetci-' . $this->getConvert($this->city) . '-' . $this->getConvert($this->district));
        if ($curlData) {
            preg_match_all('@<table class="table table-striped mt-2" style="font-size:14px;">(.*?)</table>@si', $curlData, $currentData);
            preg_match_all('@<div class="d-flex alert alert-warning text-center rounded shadow-sm">(.*?)</div>@si', $currentData[1][1] ?? '', $date);
            preg_match_all('@<span class="isim">(.*?)</span>@si', $currentData[1][1] ?? '', $name);
            preg_match_all("@<div class='col-lg-6'>(.*?)<@si", $currentData[1][1] ?? '', $address);
            preg_match_all("@<div class='col-lg-3 py-lg-2'>(.*?)</div>@si", $currentData[1][1] ?? '', $phone);

            $total = count($name[1]);
            if ($total > 0) {

                $this->jsonData = [
                    'createdAt' => time(),
                    'city' => $this->city,
                    'district' => $this->district,
                    'descriptionDate' => trim(strip_tags($date[1][0] ?? '')),
                    'pharmacyList' => []
                ];

                for ($i = 0; $i < $total; $i++) {
                    $pharmacyName = $this->ucwordsTR(trim(strip_tags(html_entity_decode($name[1][$i]) ?? '')));
                    $this->jsonData['pharmacyList'][] = [
                        'name' => $pharmacyName,
                        'address' => trim(strip_tags(html_entity_decode($this->ucwordsTR($address[1][$i] ?? '')))),
                        'phone' => trim(strip_tags(html_entity_decode(str_replace('-', ' ', $phone[1][$i] ?? '')))),
                        'maps' => $pharmacyName ? 'https://maps.google.com/maps?q=' . urlencode($pharmacyName) : ''
                    ];
                }
                $this->successDataResult($this->jsonData);

            } else {
                $this->errorDataResult($this->jsonData, 'Nöbetçi Eczane Bulunamadı!');
            }
        } else {
            $this->errorDataResult($this->jsonData, 'Nöbetçi Eczane Sistemine Erişim Sağlanamadı!');
        }
    }

    /**
     * Get Pharmacies
     * @param int $time
     * @return array
     */
    public function getPharmacies($time = 60)
    {
        $cacheName = $this->getCacheName();
        $cacheAge = $this->getCacheAge($time);

        if (!file_exists($cacheName) || (time() - filemtime($cacheName)) > $cacheAge) {
            $this->getExplodeData();
            file_put_contents($cacheName, json_encode($this->returnData));
        } else {
            $cachedData = json_decode(file_get_contents($cacheName), true);

            $city = $cachedData['data']['city'] ?? null;
            $district = $cachedData['data']['district'] ?? null;

            if ($city !== $this->city || $district !== $this->district) {
                $this->getExplodeData();
                file_put_contents($cacheName, json_encode($this->returnData));
            } else {
                $this->returnData = $cachedData;
            }
        }

        return $this->returnData;
    }

    /**
     * Caching Name
     * @return string
     */
    private function getCacheName()
    {
        return 'pharmacy.json';
    }

    /**
     * Caching Age
     * @param $time
     * @return mixed
     */
    private function getCacheAge($time)
    {
        return $time * 60;
    }

    /**
     * Success Data Result
     * @param $data
     * @param string $message
     */
    private function successDataResult($data, $message = '')
    {
        $this->returnData = [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * Error Data Result
     * @param $data
     * @param string $message
     */
    private function errorDataResult($data, $message = '')
    {
        $this->returnData = [
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * String Convert
     * @param $string
     * @return string
     */
    private function getConvert($string)
    {
        $tr = array('ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'Ç', 'ç');
        $en = array('s', 's', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c');
        $string = str_replace($tr, $en, $string);
        $string = strtolower($string);
        $string = preg_replace('/&.+?;/', '', $string);
        $string = preg_replace('/[^%a-z0-9 _-]/', '', $string);
        $string = preg_replace('/\s+/', '-', $string);
        $string = preg_replace('|-+|', '-', $string);
        return trim($string, '-');
    }

    /**
     * Turkish ucwords
     * @param string $string
     * @return string
     */
    private function ucwordsTR($string)
    {
        $words = explode(" ", $string);
        $result = array_map(function ($word) {

            $parts = explode('/', $word);
            $transformedParts = array_map(function ($part) {
                $first_character = mb_substr($part, 0, 1, 'UTF-8');
                $rest_of_word = mb_substr($part, 1, null, 'UTF-8');

                $first_character = $this->upperCaseTR($first_character);
                $rest_of_word = $this->createLower($rest_of_word);

                return $first_character . $rest_of_word;
            }, $parts);

            return implode('/', $transformedParts);
        }, $words);

        $resultString = implode(' ', $result);

        if (strpos($resultString, ' / ') !== false) {
            $resultString = str_replace(' / ', ' - ', $resultString);
        }

        return $this->abbreviateAddressTerms($resultString);
    }

    /**
     * Turkish Character Uppercase
     * @param string $char
     * @return string
     */
    private function upperCaseTR($char)
    {
        $tr = ['ç', 'ğ', 'ı', 'i', 'ö', 'ş', 'ü'];
        $tr_upper = ['Ç', 'Ğ', 'I', 'İ', 'Ö', 'Ş', 'Ü'];

        $index = array_search(mb_strtolower($char, 'UTF-8'), $tr);
        if ($index !== false) {
            return $tr_upper[$index];
        }

        return mb_strtoupper($char, 'UTF-8');
    }

    /**
     * Turkish Character Lowercase
     * @param string $string
     * @return string
     */
    private function createLower($string)
    {
        return mb_strtolower($string, 'UTF-8');
    }

    /**
     * Address Terms Abbreviation
     * @param string $string
     * @return string
     */
    private function abbreviateAddressTerms($string)
    {
        $abbreviations = [
            'Mahallesi' => 'Mh.',
            'Bulvarı' => 'Blv.',
            'Sokak' => 'Sk.',
            'Sokağı' => 'Sk.',
            'Caddesi' => 'Cd.',
            'Meydan' => 'Meyd.',
            'Apartman' => 'Apt.',
            'Daire' => 'D.',
            'Kat' => 'Kt.',
            'Numara' => 'No',
            'Sitesi' => 'Sit.',
            'İş Merkezi' => 'İş Mrk.',
            'Plaza' => 'Plz.',
            'Lojman' => 'Loj.',
            'Köy' => 'Köy',
            'Bina' => 'Bina No',
            'Yolu' => 'Yolu',
            'Sanayi' => 'San.',
            'Organize Sanayi Bölgesi' => 'OSB',
            'Bölge' => 'Blg.',
            'İstasyonu' => 'İst.'
        ];

        foreach ($abbreviations as $fullTerm => $abbreviation) {
            $string = str_replace($fullTerm, $abbreviation, $string);
        }

        return $string;
    }

    /**
     * Data Extraction with Curl
     * @param $link
     * @return bool|string
     */
    private function fetchDataWithCurl($link)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $source = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log('CURL error (' . curl_errno($ch) . '): ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);
        return $source;
    }

}
