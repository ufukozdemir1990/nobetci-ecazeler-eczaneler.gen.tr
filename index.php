<?php
/**
 * @author: Ufuk OZDEMIR
 * @creatAt: 07.04.2024 19:46
 * @web: https://www.ufukozdemir.website/
 * @email: ufuk.ozdemir1990@gmail.com
 * @phone: +90 (532) 131 73 07
 */

require_once (__DIR__ . "/Pharmacy.class.php");
$pharmacy = new Pharmacy('İzmir', 'Konak');
$pharmaciesData = $pharmacy->getPharmacies();
?>
<!doctype html>
<html lang="tr" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pharmacy on Duty</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>

<body class="align-items-center d-flex h-100 justify-content-center">
    <div class="container">
        <div class="card shadow-lg">
            <div class="bg-warning card-header py-3">
                <h5 class="mb-0 text-center">
                    <?php if ($pharmaciesData['status'] == 'success')
                        echo $pharmaciesData['data']['descriptionDate'] ?>
                    </h5>
                </div>
                <div class="card-body px-5">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Pharmacy Name</th>
                                <th>Address</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($pharmaciesData['status'] == 'success' && isset($pharmaciesData['data']['pharmacyList'])): ?>
                            <?php foreach ($pharmaciesData['data']['pharmacyList'] as $pharmacy): ?>
                                <tr>
                                    <td>
                                        <?php echo $pharmacy['name']; ?>
                                    </td>
                                    <td><a href="<?php echo $pharmacy['maps']; ?>" target="_blank"
                                            class="text-decoration-none text-body"><i class="bi bi-link-45deg"></i>
                                            <?php echo $pharmacy['address']; ?>
                                        </a></td>
                                    <td><a href="tel:<?php echo $pharmacy['phone']; ?>" target="_blank"
                                            class="text-decoration-none text-body"><i class="bi bi-telephone"></i>
                                            <?php echo $pharmacy['phone']; ?>
                                        </a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    <?php echo $pharmaciesData['message']; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="bg-secondary card-header py-3 text-white">
                <h6 class="mb-0 text-center">
                    <?php if ($pharmaciesData['status'] == 'success')
                        echo $pharmaciesData['data']['totalPharmacy'] ?>
                    </h6>
                </div>
            </div>
        </div>
    </body>

    </html>