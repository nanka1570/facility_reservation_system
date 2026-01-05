<?php
    include_once 'facility_common.php';

    try {
        $data = [
            'room_number' => $_POST['room_number'] ?? 0,
            'room_name' => $_POST['room_name'] ?? '',
            'category_name' => $_POST['category_name'] ?? '',
            'max_number_of_people' => $_POST['max_number_of_people'] ?? 0,
            'item_number' => $_POST['item_number'] ?? 0,
            'item_name' => $_POST['item_name'] ?? '',
            'total_of_item' => $_POST['total_of_item'] ?? 0,
            'equipment' => $_POST['equipment'] ?? '',
            'time_of_unit_price' => $_POST['time_of_unit_price'] ?? 0,
            'rental_unit_price' => $_POST['rental_unit_price'] ?? 0,
        ];

        // 入力値のバリデーション
        $errors = validateFacilityInput($data);
        if (!empty($errors)) {
            displayErrors($errors);
            exit();
        }

        // 施設変更の実行
        $result = updateFacility($data);

        if ($result['success']) {
            displaySuccess('変更が完了しました。');
            
            // 変更内容の表示
            if (!empty($result['changed_fields'])) {
                echo '<p>変更された項目：</p>';
                echo '<ul>';
                foreach ($result['changed_fields'] as $field) {
                    echo "<li>" . h($field) . "</li>";
                }
                echo '</ul>';
            } else {
                echo '<p>変更された項目はありませんでした。</p>';
            }
        }

    } catch (Exception $e) {
        displayErrors([$e->getMessage()]);
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>施設変更完了</title>
        <link rel="stylesheet" href="admin_basic.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    </head>
    <body>
        <div class="button-group">
            <input type="button" onclick="location.href='facility_edit_top.php'" value="施設一覧に戻る">
        </div>
    </body>
</html>