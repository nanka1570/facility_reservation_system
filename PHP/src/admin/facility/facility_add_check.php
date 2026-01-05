<?php
    $room_name = $_POST['room_name'] ?? '';
    $category_name = $_POST['category_name'] ?? '';
    $max_number_of_people = $_POST['max_number_of_people'] ?? 0;

    $item_name = $_POST['item_name'] ?? '';
    $total_of_item = $_POST['total_of_item'] ?? 0;

    $equipment = $_POST['equipment'] ?? '';
    $time_of_unit_price = $_POST['time_of_unit_price'] ?? 0;
    $rental_unit_price = $_POST['rental_unit_price'] ?? 0;
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>施設追加確認</title>
        <meta name="facility_add_check" content="facility">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="admin_basic.css">

    </head>
    <body>
        <h1>施設追加確認</h1>
        <br />
        <form method="post" action="facility_add_done.php">
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
                <input type="hidden" name="category_name" value="<?= $category_name ?>">
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
                    <input type="hidden" name="equipment" value="<?= $equipment ?>">
                    <?php else: ?>
                    <input type="hidden" name="equipment" value="">
                <?php endif; ?>
                

                <!-- 料金情報 -->
                <?php if(!empty($time_of_unit_price)): ?>
                <div class="confirm-section">
                    <h3>料金情報</h3>
                    <p><label>時間単位あたりの料金(部屋): </label> <?= $time_of_unit_price ?>円 </p>
                    <input type="hidden" name="time_of_unit_price" value="<?= $time_of_unit_price ?>">
                <?php endif; ?>

                <?php if(!empty($rental_unit_price)): ?>
                    <p><label>貸出単価(備品): </label> <?= $rental_unit_price ?>円 </p>
                    <input type="hidden" name="rental_unit_price" value="<?= $rental_unit_price ?>">
                <?php endif; ?>
                </div>
            </div>

            <!-- 戻るボタンとOKボタン -->
            <div class="button-group">
                <input type="button" onclick="history.back()" value="戻る">
                <input type="submit" value="確定">
            </div>
        </form>
    </body>
</html>