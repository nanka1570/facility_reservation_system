<?php
    include_once 'facility_common.php';

    $room_number = isset($_GET['room_number']) ? intval($_GET['room_number']) : 0;
    $category_number = isset($_GET['category_number']) ? intval($_GET['category_number']) : 0;
    $category_name = $_POST['category_name'] ?? '';

    $max_number_of_people = $_POST['max_number_of_people'] ?? 0;

    $item_name = $_POST['item_name'] ?? '';
    $total_of_item = $_POST['total_of_item'] ?? 0;

    $equipment = $_POST['equipment'] ?? '';
    $time_of_unit_price = $_POST['time_of_unit_price'] ?? 0;
    $rental_unit_price = $_POST['rental_unit_price'] ?? 0;


    try {
        $dbh = getDbConnection();
        $dbh = $conn;
        $categories = getCategoryNumbers($dbh);

        // 施設テーブルから部屋情報を取得
        $sql_facility = "SELECT f.room_number, f.room_name, f.max_number_of_people, 
                                f.equipment, f.category_number, c.category_name,
                                f.time_of_unit_price
                         FROM facility_table f
                         JOIN category_table c ON f.category_number = c.category_number 
                         WHERE room_number = :room_number";
    
        $stmt_facility = $dbh->prepare($sql_facility);
        $stmt_facility->bindParam(':room_number', $room_number);
        $stmt_facility->execute();
        $facility_data = $stmt_facility->fetch(PDO::FETCH_ASSOC);
    
        if (!$facility_data) {
            displayErrors(['指定された部屋が見つかりません。']);
            exit;
        }
    //     // 施設変更時
    // $original_data = getFacilityDetails($_POST['room_number']);
    // if (checkAndDisplayValidationErrors($_POST, 'change', $original_data)) {
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
        <title>施設変更</title>
        <meta name="facility_change" content="facility">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="admin_basic.css">
        <link rel="stylesheet" href="facility_edit.css">
    </head>
    <body>
        <!-- 施設情報をそれぞれ変更する -->
        <h1>施設の変更</h1>
        <h3>部屋の変更</h3>
        <form id="facility_change" method="post" action="facility_change_check.php">
            <div class="form_content">
                <p><label for="room_name">部屋名</label><br />
                <input id="room_name" name="room_name" type="text" required
                value="<?php echo $facility_data['room_name'] ?? ''; ?>"></p>
            </div>

                <!-- 既存の分類番号に対する処理 -->
                

                <div class="form_content">
                    <p><label for="category_name">分類名</label><br />
                    <select name="category_name" id="category_name" required>
                        <option value="">分類を選択してください</option>
                        <?php foreach ($categories as $cat_num => $cat_name): ?>
                            <option value="<?= h($cat_name) ?>" 
                                    data-category-number="<?= h($cat_num) ?>"
                                    <?= ($facility_data['category_number'] == $cat_num) ? 'selected' : '' ?>>
                                <?= h($cat_num) ?> - <?= h($cat_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    </p>
                    <input type="hidden" name="category_number" id="selected_category_number" value="<?php echo $facility_data['category_number']; ?>">
                    <input type="hidden" name="original_category_number" value="<?php echo $facility_data['category_number']; ?>">
                </div>

            <div class="form_content">
                <p><label for="max_number_of_people">最大収容人数</label><br />
                <input id="max_number_of_people" name="max_number_of_people" type="number" required
                value="<?php echo $facility_data['max_number_of_people'] ?? ''; ?>"></p>
            </div>   

            <!-- 備品の変更 -->
            <div class="form_content" id="item_change_form">
                <h3>備品の変更</h3>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 30px;"></th>
                            <th>備品番号</th>
                            <th>備品名</th>
                            <th>備品総数</th>
                            <?php
                            if(getDisplayStyle($use_extension_flag)==='block' && getDisplayStyle($rental_flag)==='block'){
                                echo "<th>貸出単価</th>";
                            } 
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_item = "SELECT item_number, item_name, total_of_item, rental_unit_price 
                                    FROM item_table";
                        $stmt_item = $dbh->prepare($sql_item);
                        $stmt_item->execute();
                        while($item_data = $stmt_item->fetch(PDO::FETCH_ASSOC)){
                            echo "<tr>";
                            echo "<td><input type='radio' name='selected_item' value='" . h($item_data['item_number']) . "'></td>";
                            echo "<td>" . h($item_data['item_number']) . "</td>";
                            echo "<td>" . h($item_data['item_name']) . "</td>";
                            echo "<td>" . h($item_data['total_of_item']) . '個' . "</td>";
                            if(getDisplayStyle($use_extension_flag)==='block' && getDisplayStyle($rental_flag)==='block'){
                                echo "<td>" . h($item_data['rental_unit_price']) . '円' ."</td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="form_content">
                    <div class="reference-button-container">
                        <button type="button" id="reference_button">参照</button>
                    </div>

                    <p><label for="item_name">備品名</label><br />
                    <input id="item_name" name="item_name" type="text"></p>
                    
                    <p><label for="total_of_item">備品総数</label><br />
                    <input id="total_of_item" name="total_of_item" type="number"></p>
                    
                </div>
            </div>
        <!-- オプション -->    
        <!-- 拡張機能のON/OFFで表示/非表示 -->

            <div style="display: <?php echo getDisplayStyle($use_extension_flag);?>">
                <!-- 設備の変更 -->
                <div class="accordion-item" id="equipment_accordion" style="display: <?php echo getDisplayStyle($equipment_flag); ?>;"> 
                    <div class="accordion-header">
                        <h3>設備の変更(部屋ごと)</h3>
                        <span class="icon">▼</span>
                    </div>
                    <div class="accordion-content">
                        <p><label for="equipment">設備</label><br />
                        <input id="equipment" name="equipment" type="text" 
                        value="<?php echo $facility_data['equipment'] ?? ''; ?>"></p>
                    </div>
                </div>

                <!-- 料金設定 -->
                <?php if(getDisplayStyle($price_flag)==='block' || getDisplayStyle($rental_flag)==='block'): ?>
                <div class="accordion-item" id="fee_accordion">
                    <div class="accordion-header">
                        <h3>料金設定</h3>
                        <span class="icon">▼</span>
                    </div>
                    <div class="accordion-content">
                        <div style="display: <?php echo getDisplayStyle($price_flag) ?>;">
                            <p><label for="time_of_unit_price">時間単位あたりの料金(部屋)</label><br />
                            <input id="time_of_unit_price" name="time_of_unit_price" type="number" 
                            value="<?php echo $facility_data['time_of_unit_price'] ?? ''; ?>"></p>
                        </div>

                        <div style="display: <?php echo getDisplayStyle($rental_flag) ?>;">
                            <p><label for="rental_unit_price">貸出単価(備品)</label><br />
                            <input id="rental_unit_price" name="rental_unit_price" type="number" 
                            value="<?php echo $item_data['rental_unit_price'] ?? ''; ?>"></p>
                        </div>
                    
                    </div>
                </div>
                <?php endif ?>

                

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
                <input name="OK" type="submit" value="OK">

            
            </div>

            <input type="hidden" name="room_number" value="<?php echo $room_number; ?>">
            <input type="hidden" name="item_number" id="item_number_for_js" value="<?php echo isset($item_data['item_number']) ? $item_data['item_number'] : ''; ?>">
            
            <!-- <input type="hidden" name="new_category_number" value=""> -->
            
        </form>

        <script src="facility.js"></script>
        
    </body>
</html>