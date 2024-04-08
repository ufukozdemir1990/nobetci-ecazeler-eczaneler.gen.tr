# Nöbetci Eczaneler (eczaneler.gen.tr)

Php ile günlük nöbetçi eczane listesini veren Php sınıfı

Eczanelere ait veriler <a href="https://www.eczaneler.gen.tr/" target="_blank">eczaneler.gen.tr</a> sitesinden alınmıştır.

Eczaneler.gen.tr sitesinden çekilen veriler, json olarak tutulabilir. Bu sayede her seferinde Eczaneler.gen.tr sitesinden çekilmeyeceği için performans artışı sağlanabilir.

<h2>Çalışma Mantığı</h2>

Öncelikle Php Sınıfımızı Sayfaya Dahil Edelim.

```php
require_once("Pharmacy.class.php");
```

Daha Sonra Sınıfımızı Başlatalım.

```php
// Hangi İl ve İlçeyi İstiyorsak Parametre Verileri Gönderlim
$pharmacy = new Pharmacy('İzmir', 'Konak');
```

Daha Sonra Nöbetçi Eczanelerimizi Çekelim.

```php
// Verilerimizi Çekebiliriz.
$pharmaciesData = $pharmacy->getPharmacies();
print_r($pharmaciesData);
```

Genel Olarak Tam Kodumuz Şöyle.

```php
header("Content-type:application/json");

// Sınıfımızı Sayfamıza Dahil Ettik
require_once("Pharmacy.class.php");

// Sınıfı Başlattık
$pharmacy = new Pharmacy('İzmir', 'Konak');

// Nöbetçi Eczanelerimizi JSON Olarak Çektik
$pharmaciesData = $pharmacy->getPharmacies();
print_r($pharmaciesData);
```
