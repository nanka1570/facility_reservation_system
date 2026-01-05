<?php
include_once 'facility_common.php';

$room_number = isset($_GET['room_number']) ? intval($_GET['room_number']) : 0;

try {
    // 施設情報の取得
    $facility_data = getFacilityDetails($room_number);
    
    // 備品情報の取得
    $items = getAllItems();

    // // 施設削除時
    // if (checkAndDisplayValidationErrors($_POST, 'delete')) {
    //     return; // エラーがある場合は処理を中断
    // }
} catch (Exception $e) {
    displayErrors([$e->getMessage()]);
    exit();
}

$room_name = $facility_data['room_name'] ?? '';
$category_name = $facility_data['category_name'] ?? '';
$max_number_of_people = $facility_data['max_number_of_people'] ?? 0;
$equipment = $facility_data['equipment'] ?? '';
$time_of_unit_price = $facility_data['time_of_unit_price'] ?? 0;
$rental_unit_price = $_POST['rental_unit_price'] ?? 0;

// 以下、HTMLの表示部分は変更なし
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>施設削除</title>
    <meta name="facility_delete" content="facility">
    <link rel="stylesheet" href="facility_edit.css">
    <link rel="stylesheet" href="admin_basic.css">
    
</head>
<body>
    <h1>施設削除</h1>
    <form method="post" action="facility_delete_check.php">
        <!-- 施設情報の表示 -->
        <div class="facility-info">
            <h2>部屋の削除情報</h2>
            <?php if (!empty($room_name)): ?>
                <p>部屋名: <?= h($room_name) ?></p>
            <?php endif; ?>

            <?php if (!empty($category_name)): ?>
                <p>分類名: <?= h($category_name) ?></p>
            <?php endif; ?>

            <?php if (!empty($max_number_of_people)): ?>
                <p>最大収容人数: <?= h($max_number_of_people) ?> 人</p>
            <?php endif; ?>

            <?php if (!empty($equipment) && $equipment != ' '): ?>
                <p>設備: <?= h($equipment) ?></p>
            <?php endif; ?>

            <?php if ($time_of_unit_price > 0): ?>
                <p>時間単位あたりの料金: <?= h($time_of_unit_price) ?> 円</p>
            <?php endif; ?>
        </div>

    
    <!-- 削除項目の選択 -->
    <h3>削除方法</h3>
    <div class="delete">
        <div class="delete-option-item" style="margin-left: 10px;">
            <input type="radio" id="full_delete" name="delete_type" value="facility">
            <label for="full_delete">部屋全体を削除</label>
            <!-- <input type="hidden" name="delete_items[]" value="facility"> -->
        </div>
        <?php if ($category_name != "未分類" || !empty($equipment) || $time_of_unit_price > 0): ?>
        <div class="delete-option-item" style="margin-left: 10px;">
            <input type="radio" id="partial_delete" name="delete_type" value="partial">
            <label for="partial_delete">削除する項目を選ぶ</label>
        </div>
        <?php endif; ?>
        
        <div class="delete-options" >
            <div class="checkbox-group" style="margin-left: 50px;">
                <?php if (!empty($category_name) && $category_name != '未分類'): ?>
                <div class="checkbox-item">
                    <input type="checkbox" id="delete_category" name="delete_items[]" value="category">
                    <label for="delete_category">分類名を削除</label>
                </div>
                <?php endif; ?>
                <?php if (!empty($equipment) && $equipment != ' '): ?>
                <div class="checkbox-item">
                    <input type="checkbox" id="delete_equipment" name="delete_items[]" value="equipment">
                    <label for="delete_equipment">設備を削除</label>
                </div>
                <?php endif; ?>
                <?php if ($time_of_unit_price > 0): ?>
                <div class="checkbox-item">
                    <input type="checkbox" id="delete_time_price" name="delete_items[]" value="time_unit_price">
                    <label for="delete_time_price">時間単位あたりの料金を削除</label>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
        

        <!-- 備品の削除選択 -->
        <?php if (!empty($items)): ?>
        <div class="items-section">
            <h3>備品の削除</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;"></th>
                        <th>選択</th>
                        <th>備品名</th>
                        <th>総数</th>
                        <?php if (!empty($item['rental_unit_price'])): ?>
                        <th>貸出単価</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="itemTableBody">
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="delete_items_list[]" 
                                   value="<?= h($item['item_number']) ?>">
                        </td>
                        <td><?= h($item['item_name']) ?></td>
                        <td><?= h($item['total_of_item']) ?>個</td>
                        <td><?= h($item['rental_unit_price']) ?>円</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="warning">
            <p>※削除した内容は元に戻すことができません。</p>
        </div>

        <input type="hidden" name="room_number" value="<?= h($room_number) ?>">

        <div class="button-group">
            <input type="button" onclick="location.href='facility_edit_top.php'" value="戻る">
            <input type="submit" value="OK">
        </div>
    </form>

    <script src="facility.js"></script>
    
</body>
</html>