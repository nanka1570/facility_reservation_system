<?php
include_once "../common/DB_switch.php";
include_once "../common/session.php";
$user_Id = $_SESSION['login'];
date_default_timezone_set('Asia/Tokyo');
$dbh = $conn;

// データベース接続とエラーハンドリングの共通関数
function getDbConnection() {
    global $conn;
    try {
        return $conn;
    } catch (PDOException $e) {
        throw new Exception('データベースに接続できません。');
    }
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 拡張機能関連の処理をまとめた関数
// function updateExtensionStatus($dbh, $use_extension, $rental_flag = false, $price_flag = false, $equipment_flag = false) {
//     try {
//         $current_time = date('Y-m-d H:i:s');
        
//         $extension_status = $use_extension ? 'Y' : 'N';
//         $rental_status = $rental_flag ? 'R' : '';
//         $price_status = $price_flag ? 'P' : '';
//         $equipment_status = $equipment_flag ? 'E' : '';
        
//         if ($extension_status === 'N') {
//             $rental_status = '';
//             $price_status = '';
//             $equipment_status = '';
//         }

//         $sql = "INSERT INTO extension_table 
//                 (change_extension_date, use_extension, rental_flag, price_flag, equipment_flag) 
//                 VALUES (?, ?, ?, ?, ?)";
//         $stmt = $dbh->prepare($sql);
//         $stmt->execute([
//             $current_time,
//             $extension_status,
//             $rental_status,
//             $price_status,
//             $equipment_status
//         ]);
        
//         return ['success' => true];
//     } catch (Exception $e) {
//         return ['error' => 'データベースエラー: ' . $e->getMessage()];
//     }
// }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 時間延長機能を追加する場合の関数
function updateExtensionStatus($dbh, $use_extension, $rental_flag = false, $price_flag = false, $equipment_flag = false, $time_extension_flag = false) {
    try {
        $current_time = date('Y-m-d H:i:s');
        
        $extension_status = $use_extension ? 'Y' : 'N';
        $rental_status = $rental_flag ? 'R' : '';
        $price_status = $price_flag ? 'P' : '';
        $equipment_status = $equipment_flag ? 'E' : '';
        $time_extension_status = $time_extension_flag ? 'T' : '';
        
        if ($extension_status === 'N') {
            $rental_status = '';
            $price_status = '';
            $equipment_status = '';
            $time_extension_status = '';
        }

        $sql = "INSERT INTO extension_table 
                (change_extension_date, use_extension, rental_flag, price_flag, equipment_flag, time_extension_flag) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            $current_time,
            $extension_status,
            $rental_status,
            $price_status,
            $equipment_status,
            $time_extension_status
        ]);
        
        return ['success' => true];
    } catch (Exception $e) {
        return ['error' => 'データベースエラー: ' . $e->getMessage()];
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// POST処理
//旧ver
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_extension'])) {
//     $use_extension = isset($_POST['extension']) && $_POST['extension'] === '1';
//     $rental_flag = isset($_POST['rental_flag']) && $_POST['rental_flag'] === '1';
//     $price_flag = isset($_POST['price_flag']) && $_POST['price_flag'] === '1';
//     $equipment_flag = isset($_POST['equipment_flag']) && $_POST['equipment_flag'] === '1';
    
//     $result = updateExtensionStatus($dbh, $use_extension, $rental_flag, $price_flag, $equipment_flag);
//     echo json_encode($result);
//     exit;
// }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//時間延長機能　追加
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_extension'])) {
    $use_extension = isset($_POST['extension']) && $_POST['extension'] === '1';
    $rental_flag = isset($_POST['rental_flag']) && $_POST['rental_flag'] === '1';
    $price_flag = isset($_POST['price_flag']) && $_POST['price_flag'] === '1';
    $equipment_flag = isset($_POST['equipment_flag']) && $_POST['equipment_flag'] === '1';
    $time_extension_flag = isset($_POST['time_extension_flag']) && $_POST['time_extension_flag'] === '1';
    
    $result = updateExtensionStatus($dbh, $use_extension, $rental_flag, $price_flag, $equipment_flag, $time_extension_flag);
    echo json_encode($result);
    exit;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ポータル</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container">
        <header class="admin-header">
            <h1>管理者ポータル</h1>
        </header>

        <main class="admin-content">
            <section class="menu-section">
                <div class="menu-grid">
                    <button onclick="location.href='../admin/facility/facility_edit_top.php'" class="menu-button">
                        <span class="button-text">施設情報の管理</span>
                    </button>
                    <button onclick="location.href='../admin/reservation/admin_room.php'" class="menu-button">
                        <span class="button-text">新規予約</span>
                    </button>
                    <button onclick="location.href='../admin/reservation/admin_reservation_del_list.php'" class="menu-button">
                        <span class="button-text">予約のキャンセル</span>
                    </button>
                    <button onclick="location.href='../admin/reservation/admin_reservation_edit_list.php'" class="menu-button">
                        <span class="button-text">予約の変更</span>
                    </button>
                    <button onclick="location.href='../inquily/getquestion2.php'" class="menu-button">
                        <span class="button-text">問合せ・FAQ</span>
                    </button>
                    <button onclick="location.href='../admin/gamenn/gamennseni.php'" class="menu-button">
                        <span class="button-text">画面表示</span>
                    </button>
                </div>
            </section>

            <section class="extension-section">
                <div class="extension-toggle">
                    <input type="checkbox" id="extension-toggle" name="extension">
                    <label for="extension-toggle">拡張機能</label>
                </div>

                <div id="extension-menu" class="extension-menu hidden">
                    <form id="features-form" class="features-form">
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="rental-flag" name="rental_flag" value="rental" class="extension">
                                <label for="rental-flag">貸出機能</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="price-flag" name="price_flag" value="price" class="extension">
                                <label for="price-flag">料金計算機能</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="equipment-flag" name="equipment_flag" value="equipment" class="extension">
                                <label for="equipment-flag">設備機能</label>
                            </div>
                            <!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                            <!-- 新拡張機能(予定)時間延長機能 -->
                            <div class="checkbox-item">
                                <input type="checkbox" id="time-extension-flag" name="time_extension_flag" value="time-extension" class="extension">
                                <label for="time-extension">時間延長機能</label>
                            </div>
                            <!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                        </div>
                        <button type="submit" class="save-button">保存</button>
                    </form>
                </div>
            </section>

            <footer class="admin-footer">
                <button onclick="location.href='logout_check.php'" class="logout-button">
                    <span class="button-text">ログアウト</span>
                </button>
            </footer>
        </main>
    </div>

    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const extensionToggle = document.getElementById('extension-toggle');
            const extensionMenu = document.getElementById('extension-menu');
            const featuresForm = document.getElementById('features-form');

            extensionToggle.addEventListener('change', function() {
                const isChecked = this.checked;
                extensionMenu.classList.toggle('hidden', !isChecked);
                
                if (!isChecked) {
                    const checkboxes = extensionMenu.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
            });

            featuresForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                formData.append('update_extension', '1');
                formData.append('extension', extensionToggle.checked ? '1' : '');
                
                if (extensionToggle.checked) {
                    const rentalFlag = document.getElementById('rental-flag');
                    const priceFlag = document.getElementById('price-flag');
                    const equipmentFlag = document.getElementById('equipment-flag');
                    
                    formData.append('rental_flag', rentalFlag.checked ? '1' : '');
                    formData.append('price_flag', priceFlag.checked ? '1' : '');
                    formData.append('equipment_flag', equipmentFlag.checked ? '1' : '');
                }

                fetch('admin_home.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    alert('設定を保存しました');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('エラーが発生しました');
                });
            });
        });
    </script> -->

<!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
    <!-- 時間延長機能の処理追加 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const extensionToggle = document.getElementById('extension-toggle');
            const extensionMenu = document.getElementById('extension-menu');
            const featuresForm = document.getElementById('features-form');

            extensionToggle.addEventListener('change', function() {
                const isChecked = this.checked;
                extensionMenu.classList.toggle('hidden', !isChecked);
                
                if (!isChecked) {
                    const checkboxes = extensionMenu.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
            });

            featuresForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                formData.append('update_extension', '1');
                formData.append('extension', extensionToggle.checked ? '1' : '');
                
                if (extensionToggle.checked) {
                    const rentalFlag = document.getElementById('rental-flag');
                    const priceFlag = document.getElementById('price-flag');
                    const equipmentFlag = document.getElementById('equipment-flag');
                    // 新しく追加
                    const timeextensionFlag = document.getElementById('time-extension-flag');
                    
                    formData.append('rental_flag', rentalFlag.checked ? '1' : '');
                    formData.append('price_flag', priceFlag.checked ? '1' : '');
                    formData.append('equipment_flag', equipmentFlag.checked ? '1' : '');
                    // 新しく追加
                    formData.append('time_extension_flag', timeextensionFlag.checked ? '1' : '');
                }

                fetch('admin_home.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    alert('設定を保存しました');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('エラーが発生しました');
                });
            });
        });
    </script>
<!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
</body>
</html>