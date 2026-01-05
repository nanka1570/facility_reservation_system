<?php
    include_once 'facility_common.php';

    $room_number = $_POST['room_number'] ?? '';
    $item_number = $_POST['item_number'] ?? '';
    $category_number = $_POST['category_number'] ?? '';
    $original_category_number = $_POST['original_category_number'] ?? '';
    // $category_number = isset($_GET['category_number']) ? intval($_GET['category_number']) : null;


    $room_name = $_POST['room_name'] ?? '';
    $category_name = $_POST['category_name'] ?? '';

    $max_number_of_people = $_POST['max_number_of_people'] ?? 0;

    $item_name = $_POST['item_name'] ?? '';
    $total_of_item = $_POST['total_of_item'] ?? 0;

    $equipment = $_POST['equipment'] ?? '';
    $time_of_unit_price = $_POST['time_of_unit_price'] ?? 0;
    $rental_unit_price = $_POST['rental_unit_price'] ?? 0;

    // データベースから分類名を取得
    try {
        $dbh = getDbConnection();
        $categories = getCategoryNumbers($dbh);
    } catch (Exception $e) {
        displayErrors([$e->getMessage()]);
        exit();
    }
 
// $categories = getCategoryNumbers($dbh);
// var_dump($categories);

//     //デバッグ
//     // 更新前の状態を出力
//     $old_category_name = $_POST['old_category_name'] ?? '';
//  echo "Category Number: " . $original_category_number . "<br>";
// echo "Category Number: " . $category_number . "<br>";
// echo "New Category Name: " . $category_name . "<br>";

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>施設変更確認</title>
        <meta name="facility_change_check" content="facility">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="admin_basic.css">
    </head>
    <body>
        <h1>施設変更確認</h1>
        <br />
        <form method="post" action="facility_change_done.php">
            <!-- 確認画面のコンテンツ -->
            <div class="confirm-content">
                <h2>以下の内容で登録してよろしいですか？</h2>
                
                <!-- 基本情報の確認 -->
                <h2>施設情報</h2>
                    <div class="confirm-section">
                        <p><label>部屋名: </label> <?= $room_name ?> </p>
                        <p><label>分類名: </label> <?= $category_name ?> </p>
                        <p><label>最大収容人数: </label> <?= $max_number_of_people ?> 人</p>
                    </div>
                <!-- hiddenフィールドの追加 -->
                <input type="hidden" name="room_name" value="<?= $room_name ?>">
                <input type="hidden" name="category_name" value="<?= h($category_name) ?>">
                <input type="hidden" name="max_number_of_people" value="<?= $max_number_of_people ?>">

                <!-- 備品情報 -->
                <?php if(!empty($item_name) && !empty($total_of_item)): ?>
                    <div class="confirm-section">
                        <h3>備品情報</h3>
                        <p><label>備品名: </label> <?= $item_name ?> </p>
                        <p><label>備品総数: </label> <?= $total_of_item ?>個 </p>
                    </div>
                    <input type="hidden" name="item_name" value="<?= $item_name ?>">
                    <input type="hidden" name="total_of_item" value="<?= $total_of_item ?>">
                <?php endif; ?>

                <!-- 設備情報 -->
                <?php if(!empty($equipment)): ?>
                    <div class="confirm-section">
                        <h3>設備情報</h3>
                        <p><label>設備: </label> <?= $equipment ?> </p>
                    </div>
                    <input type="hidden" name="equipment" value="<?= $equipment ?>">
                <?php endif; ?>

                <!-- 料金情報 -->
                <?php if(!empty($time_of_unit_price) || !empty($rental_unit_price)): ?>
                        <h3>料金情報</h3>
                <?php endif; ?>
                <?php if(!empty($time_of_unit_price)): ?>
                    <div class="confirm-section">
                        <p><label>時間単位あたりの料金(部屋): </label> <?= $time_of_unit_price ?>円 </p>
                        <input type="hidden" name="time_of_unit_price" value="<?= $time_of_unit_price ?>">
                    </div>
                <?php endif; ?>

                <?php if(!empty($rental_unit_price)): ?>
                    <div class="confirm-section">
                        <p><label>貸出単価(備品): </label> <?= $rental_unit_price ?>円 </p>
                        <input type="hidden" name="rental_unit_price" value="<?= $rental_unit_price ?>">
                    </div>
                <?php endif; ?>
                
            </div>

            <!-- 戻るボタンとOKボタン -->
            <!-- <div class="button-group">
                <a href="facility_change.php"><input type="button" value="戻る"></a>
                <input type="submit" value="確定">
            </div> -->
            <!-- 修正 -->
            <div class="button-group">
                <input type="button" onclick="history.back()" value="戻る">
                <input type="submit" value="確定">
            </div>

            <input type="hidden" name="room_number" value="<?= $room_number ?>">
            <input type="hidden" name="item_number" value="<?= $item_number ?>">
            <input type="hidden" name="category_number" value="<?php echo $category_number; ?>">
            <input type="hidden" name="original_category_number" value="<?= $original_category_number ?>">
        </form>
        
    </body>
</html>