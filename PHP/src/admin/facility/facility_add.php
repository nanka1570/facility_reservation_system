<?php
    include_once 'facility_common.php';

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

        // // 施設追加時
        // if (checkAndDisplayValidationErrors($_POST, 'add')) {
        //     return; // エラーがある場合は処理を中断
        // }
        
    } catch (Exception $e) {
        displayErrors([$e->getMessage()]);
        exit();
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>施設追加</title>
        <meta name="facility_add" content="facility">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="admin_basic.css">

    </head>
    <body>
        <!-- 施設情報をそれぞれ追加する -->
        <h1>施設の追加</h1>
        <br />
        <form id="facility_add" method="post" action="facility_add_check.php">
            <div class="form_content">
                <h3>部屋の追加</h3>
                <p><label for="room_name">部屋名</label><br /><input id="room_name" name="room_name" type="text" 
                placeholder="例: スタジオA" ></p>
                <!-- requiredを消した -->

                <p><label for="category_name">分類名</label><br />
                    <select name="category_name" id="category_name" >
                        <option value="">分類を選択してください</option>
                        <?php foreach ($categories as $cat_num => $cat_name): ?>
                            <option value="<?= h($cat_name) ?>" 
                                    data-category-number="<?= h($cat_num) ?>"
                                    <?= $category_name === $cat_name ? 'selected' : '' ?>>
                                <?= h($cat_num) ?> - <?= h($cat_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                    <input type="hidden" name="category_number" id="selected_category_number" value="<?php echo $category_number; ?>">
                    <input type="hidden" name="original_category_number" value="<?php echo $category_number; ?>">

                <p><label for="max_number_of_people">最大収容人数</label><br /><input id="max_number_of_people" name="max_number_of_people" type="number" 
                    placeholder="例: 40" >人</p>
            </div>

        <!-- 拡張機能のフラグによって表示方法切り替え -->

                <!-- 備品の追加 -->
                <div class="form_content">
                        <h3>備品の追加</h3>
                        <p><label for="item_name">備品名</label><br /><input id="item_name" name="item_name" type="text" 
                        placeholder="例: プロジェクター" ></p>
                        <p><label for="total_of_item">備品総数</label><br /><input id="total_of_item" name="total_of_item" type="number" 
                        placeholder="例: 7" >個</p>
                </div>
            
        <!-- 拡張機能のON/OFFで表示/非表示 -->
         <!-- アコーディオン -->
        <div class="form_content">
            <div style="display: <?php echo getDisplayStyle($use_extension_flag);?>">
                <!-- 設備の追加 -->
                <div class="accordion-item" id="equipment_accordion" style="display: <?php echo getDisplayStyle($equipment_flag); ?>;"> 
                    <div class="accordion-header">
                        <h3>設備の追加(部屋ごと)</h3>
                        <span class="icon">▼</span>
                    </div>
                    <div class="accordion-content">
                        <p><label for="equipment">設備</label><br /><input id="equipment" name="equipment" type="text" 
                        placeholder="例: パイプ椅子[40]、長テーブル(50cm×200cm)[20]" <?php echo $equipment_flag ?>></p>
                    </div>
                </div>

                <!-- 料金設定 -->
                <div class="accordion-item" id="fee_accordion" style="display: <?php echo getDisplayStyle($price_flag) ?>;">
                    <div class="accordion-header">
                        <h3>料金設定</h3>
                        <span class="icon">▼</span>
                    </div>
                    <div class="accordion-content">
                        <div>
                            <p><label for="time_of_unit_price">時間単位あたりの料金(部屋)</label><br /><input id="time_of_unit_price" name="time_of_unit_price" type="number" 
                            placeholder="例: 700" <?php echo $price_flag ?>>円/時</p>
                        </div>
                        <div>
                            <p><label for="rental_unit_price">貸出単価(備品)</label><br /><input id="rental_unit_price" name="rental_unit_price" type="number" 
                            placeholder="例: 10000" <?php echo $price_flag ?>>円/個</p>
                        </div>
                    </div>
                </div>

                </div>
            </div>

            <?php if (!$equipment_flag): ?>
                    <input type="hidden" name="equipment" value="">
            <?php endif; ?>

            <?php if (!$price_flag): ?>
                    <input type="hidden" name="time_of_unit_price" value="0">
                    <input type="hidden" name="rental_unit_price" value="0">
            <?php endif; ?>
            
            <div class="button-group">
                <input type="button" onclick="location.href='facility_edit_top.php'" value="戻る">
                <input type="submit" value="OK">
            </div>
        </form>

        <script src="facility.js"></script>
        
    </body>
</html>