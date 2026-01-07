<?php
// include_once '../DB/connect.php';
// include_once '../../common/connect_myhouse.php';
include_once '../../common/DB_switch.php';
include_once '../../common/sanitize.php';
include_once 'flag.php';



// データベース接続とエラーハンドリングの共通関数
function getDbConnection() {
    global $conn;
    try {
        $dbh = $conn;
        return $dbh;
    } catch (PDOException $e) {
        logError('データベース接続エラー: ' . $e->getMessage());
        throw new Exception('データベースに接続できません。');
    }
}

// カテゴリー取得の共通関数
function getCategoryNumbers($dbh) {
    try {
        $sql_category = "SELECT category_number, category_name 
                        FROM category_table 
                        GROUP BY category_number, category_name 
                        ORDER BY category_number";

        $stmt = $dbh->prepare($sql_category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        return [];
    }
}

// 入力値バリデーションの共通関数
function validateFacilityInput($data) {
    $errors = [];
    
    // if (empty($data['room_name']) && ) {
    //     $errors[] = '部屋名を入力してください。';
    // }
    
    // if (empty($data['category_name'])) {
    //     $errors[] = '分類名を選択してください。';
    // }
    
    if (!empty($data['max_number_of_people'])) {
        if (!is_numeric($data['max_number_of_people']) || $data['max_number_of_people'] < 0) {
            $errors[] = '最大収容人数は0以上の数値を入力してください。';
        }
    }
    
    if (!empty($data['total_of_item'])) {
        if (!is_numeric($data['total_of_item']) || $data['total_of_item'] < 0) {
            $errors[] = '備品総数は0以上の数値を入力してください。';
        }
    }
    
    if (!empty($data['time_of_unit_price'])) {
        if (!is_numeric($data['time_of_unit_price']) || $data['time_of_unit_price'] < 0) {
            $errors[] = '時間単位あたりの料金は0以上の数値を入力してください。';
        }
    }
    
    if (!empty($data['rental_unit_price'])) {
        if (!is_numeric($data['rental_unit_price']) || $data['rental_unit_price'] < 0) {
            $errors[] = '貸出単価は0以上の数値を入力してください。';
        }
    }
    
    return $errors;
}

// トランザクション処理の共通関数
function executeTransaction($dbh, callable $callback) {
    try {
        $dbh->beginTransaction();
        $result = $callback($dbh);
        $dbh->commit();
        return $result;
    } catch (Exception $e) {
        $dbh->rollBack();
        throw $e;
    }
}

// エラー表示の共通関数
function displayErrors($errors) {
    if (!empty($errors)) {
        echo '<div class="error-messages">';
        foreach ($errors as $error) {
            echo '<p class="error">' . h($error) . '</p>';
        }
        echo '</div>';
    }
}

// 成功メッセージ表示の共通関数
function displaySuccess($message) {
    echo '<div class="success-message">';
    echo '<p>' . h($message) . '</p>';
    echo '</div>';
}

// 施設追加の共通処理
function addFacility($data) {
    $dbh = getDbConnection();
    
    return executeTransaction($dbh, function($dbh) use ($data) {
        // 重複チェック
        if (!empty($data['room_name'])) {
            $stmt_duplicate = $dbh->prepare("SELECT COUNT(*) FROM facility_table WHERE room_name = ?");
            $stmt_duplicate->execute([$data['room_name']]);
            $duplicate_count = $stmt_duplicate->fetchColumn();
            
            if ($duplicate_count > 0) {
                throw new Exception("「{$data['room_name']}」という部屋名は既に存在します。別の部屋名を入力してください。");
            }
        }

        // カテゴリ番号の取得または新規採番
        $category_number = getCategoryNumber($dbh, $data['category_name']);

        // 施設の追加
        if (!empty($data['room_name'])) {
            $sql_facility = 'INSERT INTO facility_table (
                        room_number,
                        room_name, 
                        max_number_of_people,
                        usable_category,
                        equipment,
                        time_of_unit_price,
                        category_number
                    ) VALUES ((SELECT COALESCE(MAX(room_number)+1,0)FROM facility_table), ?, ?, ?, ?, ?, ?)';

            $stmt_facility = $dbh->prepare($sql_facility);
            $stmt_facility->execute([
                $data['room_name'],
                $data['max_number_of_people'],
                'Y', // usable_category
                $data['equipment'] ?? '',
                $data['time_of_unit_price'] ?? 0,
                $category_number
            ]);
        }

        // 備品の追加
        if (!empty($data['item_name'])) {
            // 同様に備品の重複チェックを追加できます
            $stmt_duplicate_item = $dbh->prepare("SELECT COUNT(*) FROM item_table WHERE item_name = ?");
            $stmt_duplicate_item->execute([$data['item_name']]);
            $duplicate_item_count = $stmt_duplicate_item->fetchColumn();
            
            if ($duplicate_item_count > 0) {
                throw new Exception("「{$data['item_name']}」という備品名は既に存在します。別の備品名を入力してください。");
            }

            $sql_item = 'INSERT INTO item_table (
                        item_number, 
                        item_name, 
                        total_of_item, 
                        rental_unit_price
                    ) VALUES ((SELECT COALESCE(MAX(item_number)+1,0)FROM item_table), ?, ?, ?)';

            $stmt_item = $dbh->prepare($sql_item);
            $stmt_item->execute([
                $data['item_name'],
                $data['total_of_item'],
                $data['rental_unit_price'] ?? 0
            ]);
        }

        return [
            'category_number' => $category_number,
            'success' => true
        ];
    });
}

// 施設変更の共通処理
function updateFacility($data) {
    $dbh = getDbConnection();
    
    return executeTransaction($dbh, function($dbh) use ($data) {
        $changed_fields = [];
        
        // 現在の施設情報を取得
        $stmt = $dbh->prepare("SELECT * FROM facility_table WHERE room_number = ?");
        $stmt->execute([$data['room_number']]);
        $current_facility = $stmt->fetch(PDO::FETCH_ASSOC);

        // 分類名から分類番号を取得
        if (!empty($data['category_name'])) {
            $stmt = $dbh->prepare("SELECT category_number FROM category_table WHERE category_name = ?");
            $stmt->execute([$data['category_name']]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_category_number = $category['category_number'];

            // 分類番号が変更された場合のみ更新
            if ($current_facility['category_number'] != $new_category_number) {
                $stmt = $dbh->prepare("UPDATE facility_table SET category_number = ? WHERE room_number = ?");
                $stmt->execute([$new_category_number, $data['room_number']]);
                $changed_fields[] = "分類名: {$data['category_name']}";
            }
        }

        // 施設情報の更新
        if (!empty($data['room_number'])) {
            $facility_updates = [];
            $params = [':room_number' => $data['room_number']];
            
            $update_fields = [
                'room_name' => '部屋名',
                'max_number_of_people' => '最大収容人数',
                'equipment' => '設備',
                'time_of_unit_price' => '時間単位料金'
            ];

            foreach ($update_fields as $field => $label) {
                if (isset($data[$field]) && $data[$field] !== '' && 
                    $current_facility[$field] != $data[$field]) {
                    $facility_updates[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                    $changed_fields[] = "$label: {$data[$field]}";
                }
            }

            if (!empty($facility_updates)) {
                $sql = "UPDATE facility_table SET " . implode(', ', $facility_updates) . 
                       " WHERE room_number = :room_number";
                $stmt = $dbh->prepare($sql);
                $stmt->execute($params);
            }
        }

        // 備品情報の更新
        if (!empty($data['item_number'])) {
            // 現在の備品情報を取得
            $stmt = $dbh->prepare("SELECT * FROM item_table WHERE item_number = ?");
            $stmt->execute([$data['item_number']]);
            $current_item = $stmt->fetch(PDO::FETCH_ASSOC);

            $item_updates = [];
            $params = [':item_number' => $data['item_number']];
            
            $update_fields = [
                'item_name' => '備品名',
                'total_of_item' => '備品総数',
                'rental_unit_price' => '貸出単価'
            ];

            foreach ($update_fields as $field => $label) {
                if (isset($data[$field]) && $data[$field] !== '' && 
                    $current_item[$field] != $data[$field]) {
                    $item_updates[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                    $changed_fields[] = "$label: {$data[$field]}";
                }
            }

            if (!empty($item_updates)) {
                $sql = "UPDATE item_table SET " . implode(', ', $item_updates) . 
                       " WHERE item_number = :item_number";
                $stmt = $dbh->prepare($sql);
                $stmt->execute($params);
            }
        }

        return [
            'changed_fields' => $changed_fields,
            'success' => true
        ];
    });
}

// カテゴリ番号取得または新規採番
function getCategoryNumber($dbh, $category_name) {
    // カテゴリ名が空の場合、未選択カテゴリを使用
    if (empty($category_name)) {
        // 未選択カテゴリの存在確認
        $sql_check_default = 'SELECT category_number FROM category_table WHERE category_number = 0';
        $stmt_check_default = $dbh->prepare($sql_check_default);
        $stmt_check_default->execute();
        $default_category = $stmt_check_default->fetch(PDO::FETCH_ASSOC);

        // 未選択カテゴリが存在しない場合は作成
        if (!$default_category) {
            $sql_insert_default = 'INSERT INTO category_table (category_number, category_name) VALUES (0, ?)';
            $stmt_insert_default = $dbh->prepare($sql_insert_default);
            $stmt_insert_default->execute(['未選択']);
        }

        return 0; // 未選択カテゴリの番号として0を返す
    }

    // 既存の処理（カテゴリ名が指定されている場合）
    $sql_check = 'SELECT category_number FROM category_table WHERE category_name = ?';
    $stmt_check = $dbh->prepare($sql_check);
    $stmt_check->execute([$category_name]);
    $existing_category = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($existing_category) {
        return $existing_category['category_number'];
    }

    // 新規カテゴリの採番（0以外の番号を使用）
    $sql_max = 'SELECT COALESCE(MAX(CASE WHEN category_number > 0 THEN category_number END) + 1, 1) as next_number FROM category_table';
    $category_number = $dbh->query($sql_max)->fetchColumn();

    // 新規カテゴリの登録
    $sql_insert = 'INSERT INTO category_table (category_number, category_name) VALUES (?, ?)';
    $stmt_insert = $dbh->prepare($sql_insert);
    $stmt_insert->execute([$category_number, $category_name]);

    return $category_number;
}

// 施設情報取得の共通関数
function getFacilityDetails($room_number) {
    $dbh = getDbConnection();
    
    try {
        $sql_facility = "SELECT f.room_number, f.room_name, f.max_number_of_people, 
                                f.equipment, f.category_number, c.category_name,
                                f.time_of_unit_price
                         FROM facility_table f
                         JOIN category_table c ON f.category_number = c.category_number 
                         WHERE room_number = :room_number";

        $stmt_facility = $dbh->prepare($sql_facility);
        $stmt_facility->bindParam(':room_number', $room_number);
        $stmt_facility->execute();
        return $stmt_facility->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        throw $e;
    }
}

// 備品一覧取得の共通関数
function getAllItems() {
    $dbh = getDbConnection();
    
    try {
        $sql_items = "SELECT item_number, item_name, total_of_item, rental_unit_price 
                     FROM item_table";
        $stmt_items = $dbh->prepare($sql_items);
        $stmt_items->execute();
        return $stmt_items->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        throw $e;
    }
}


// 施設と備品の詳細情報取得の共通関数
function getFacilityAndItemsForDelete($room_number, $delete_items_list = []) {
    $dbh = getDbConnection();
    
    try {
        // 施設情報の取得（カテゴリ情報も含む）
        $sql_facility = "SELECT 
            f.*,
            c.category_name,
            c.category_number as current_category_number
            FROM facility_table f
            LEFT JOIN category_table c ON f.category_number = c.category_number
            WHERE f.room_number = :room_number";
        $stmt_facility = $dbh->prepare($sql_facility);
        $stmt_facility->bindParam(':room_number', $room_number);
        $stmt_facility->execute();
        $facility_data = $stmt_facility->fetch(PDO::FETCH_ASSOC);

        // 選択された備品情報の取得
        $items_data = [];
        if (!empty($delete_items_list)) {
            $placeholders = str_repeat('?,', count($delete_items_list) - 1) . '?';
            $sql_items = "SELECT 
                item_number,
                item_name,
                total_of_item,
                rental_unit_price
                FROM item_table 
                WHERE item_number IN ($placeholders)";
            $stmt_items = $dbh->prepare($sql_items);
            $stmt_items->execute($delete_items_list);
            $items_data = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
        }

        return [
            'facility' => $facility_data,
            'items' => $items_data
        ];
    } catch (Exception $e) {
        throw $e;
    }
}

// 削除項目のラベル取得関数
function getDeleteItemLabel($item) {
    $labels = [
        'category' => '分類',
        'equipment' => '設備',
        'time_unit_price' => '時間単位あたりの料金'
    ];
    return $labels[$item] ?? $item;
}

// 削除実行の共通関数
function executeDelete($data) {
    $dbh = getDbConnection();
    
    return executeTransaction($dbh, function($dbh) use ($data) {
        $room_number = $data['room_number'];
        $delete_type = $data['delete_type'] ?? 'partial';
        $delete_items = $data['delete_items'] ?? [];
        $delete_items_list = $data['delete_items_list'] ?? [];

        // 施設全体の削除
        if ($delete_type === 'facility' || in_array('facility', $delete_items)) {
            // 予約チェック
            $sql_check = "SELECT COUNT(*) FROM reservation_table WHERE room_number = :room_number";
            $stmt_check = $dbh->prepare($sql_check);
            $stmt_check->bindValue(':room_number', $room_number, PDO::PARAM_INT);
            $stmt_check->execute();
            
            if ($stmt_check->fetchColumn() > 0) {
                throw new Exception("この施設には予約が存在するため削除できません。");
            }

            $sql_delete = "DELETE FROM facility_table WHERE room_number = :room_number";
            $stmt_delete = $dbh->prepare($sql_delete);
            $stmt_delete->bindValue(':room_number', $room_number, PDO::PARAM_INT);
            $stmt_delete->execute();
            
            return ['success' => true, 'message' => '施設を完全に削除しました'];
        }

        // 部分的な削除
        $updates = [];
        $params = [':room_number' => $room_number];

        // 分類名の削除
        if (in_array('category', $delete_items)) {
            $updates[] = "category_number = :default_category_number";
            $params[':default_category_number'] = 0; // 未分類カテゴリに設定
        }

        // 設備の削除
        if (in_array('equipment', $delete_items)) {
            $updates[] = "equipment = ''";
        }

        // 時間単位料金の削除
        if (in_array('time_unit_price', $delete_items)) {
            $updates[] = "time_of_unit_price = 0";
        }

        // 施設情報の更新
        if (!empty($updates)) {
            $sql_update = "UPDATE facility_table 
                          SET " . implode(", ", $updates) . " 
                          WHERE room_number = :room_number";
            $stmt_update = $dbh->prepare($sql_update);
            $stmt_update->execute($params);
        }

        // 備品の削除
        if (!empty($delete_items_list)) {
            foreach ($delete_items_list as $item_id) {
                $sql_delete_item = "DELETE FROM item_table WHERE item_number = :item_number";
                $stmt_delete_item = $dbh->prepare($sql_delete_item);
                $stmt_delete_item->bindValue(':item_number', $item_id, PDO::PARAM_INT);
                $stmt_delete_item->execute();
            }
        }

        return [
            'success' => true,
            'message' => "選択した項目を削除しました"
        ];
    });
}


?>


