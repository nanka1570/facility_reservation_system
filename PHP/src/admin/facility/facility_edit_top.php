<?php
include_once 'facility_common.php';

// PHPファイルの先頭付近に追加
function logDebug($message, $data = null) {
    $logFile = 'debug_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";

    if ($data !== null) {
        $logMessage .= print_r($data, true) . "\n";
    }

    file_put_contents($logFile, $logMessage . "\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    logDebug("POST リクエスト受信", $_POST);

    if (isset($_POST['delete_items'])) {
        logDebug("削除処理開始");
        if (isset($_POST['item_numbers'])) {
            logDebug("削除対象の備品番号", $_POST['item_numbers']);
        }
    }

    // 既存の処理の中でも適宜ログを追加
    try {
        if (isset($_POST['delete_items']) && isset($_POST['item_numbers'])) {
            foreach ($_POST['item_numbers'] as $itemNumber) {
                logDebug("備品削除実行", $itemNumber);
                executeQuery($conn,
                    "DELETE FROM item_table WHERE item_number = ?",
                    [(int)$itemNumber]
                );
                logDebug("備品削除成功", $itemNumber);
            }
        }
    } catch (Exception $e) {
        logDebug("エラー発生", $e->getMessage());
    }
}

// デバッグログ設定
ini_set('display_errors', 1);
error_reporting(E_ALL);

// リクエストの詳細をログに記録
function debugLog($message, $data = null) {
    $logFile = 'debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    
    if ($data !== null) {
        $logMessage .= print_r($data, true) . "\n";
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// POSTリクエストの内容をログに記録
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debugLog('POST リクエスト受信', [
        'POST データ' => $_POST,
        'Content-Type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD']
    ]);
}



// 定数
const RESULTS_PER_PAGE = 10;

// 基本的なDB操作をまとめた関数
function executeQuery($conn, $sql, $params = []) {
    try {
        $stmt = $conn->prepare($sql);
        foreach ($params as $key => $value) {
            // 数値型パラメータは明示的にPDO::PARAM_INTを指定
            if (is_int($value)) {
                $stmt->bindValue($key + 1, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key + 1, $value);
            }
        }
        $stmt->execute();
        return $stmt;
    } catch (PDOException $e) {
        error_log("DB Error: " . $e->getMessage());
        throw $e;
    }
}

// カテゴリー操作の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // カテゴリーの追加
    if (isset($_POST['add_category']) && !empty($_POST['new_category_name'])) {
        $stmt = executeQuery($conn, 
            "SELECT ISNULL(MAX(category_number), 0) + 1 AS next_id FROM category_table"
        );
        $nextId = $stmt->fetch()['next_id'];
        
        executeQuery($conn, 
            "INSERT INTO category_table (category_number, category_name) VALUES (?, ?)",
            [$nextId, $_POST['new_category_name']]
        );
    }
    // カテゴリーの一括更新
    elseif (isset($_POST['update_categories']) && 
            isset($_POST['category_numbers']) && 
            isset($_POST['new_names'])) {
        
        $categoryNumbers = $_POST['category_numbers'];
        $newNames = $_POST['new_names'];
        
        for ($i = 0; $i < count($categoryNumbers); $i++) {
            executeQuery($conn,
                "UPDATE category_table SET category_name = ? WHERE category_number = ?",
                [$newNames[$i], (int)$categoryNumbers[$i]]
            );
        }
    }
    // カテゴリーの一括削除
    elseif (isset($_POST['delete_categories']) && isset($_POST['category_numbers'])) {
        $categoryNumbers = $_POST['category_numbers'];
        
        foreach ($categoryNumbers as $categoryNumber) {
            try {
                // トランザクション開始
                $conn->beginTransaction();
                
                // 削除対象のカテゴリーを使用している施設を未分類（カテゴリー番号0）に更新
                executeQuery($conn,
                    "UPDATE facility_table SET category_number = 0 
                    WHERE category_number = ?",
                    [(int)$categoryNumber]
                );
                
                // カテゴリーの削除
                executeQuery($conn,
                    "DELETE FROM category_table WHERE category_number = ?",
                    [(int)$categoryNumber]
                );
                
                // トランザクション確定
                $conn->commit();
                
            } catch (PDOException $e) {
                // エラーが発生した場合はロールバック
                $conn->rollBack();
                error_log("Category deletion error: " . $e->getMessage());
                $error = 'カテゴリーの削除中にエラーが発生しました。';
                break;
            }
        }
    }
    
    // POST処理後はリダイレクト
    $redirectUrl = $_SERVER['PHP_SELF'];
    if (isset($_GET['category_page'])) {
        $redirectUrl .= '?category_page=' . $_GET['category_page'];
    }
    header('Location: ' . $redirectUrl);
    exit;
}

// 検索条件の取得（エラーチェック追加）
$search = [
    'room_name' => isset($_GET['room_name']) ? $_GET['room_name'] : '',
    'category' => isset($_GET['category']) ? $_GET['category'] : '',
    'max_people' => isset($_GET['max_people']) && $_GET['max_people'] !== '' ? (int)$_GET['max_people'] : null,
    'unit_price' => isset($_GET['unit_price']) && $_GET['unit_price'] !== '' ? (int)$_GET['unit_price'] : null,
    'price_condition' => isset($_GET['price_condition']) ? $_GET['price_condition'] : 'greater'
];

// 検索条件の構築
$where = [];
$params = [];
if (!empty($search['room_name'])) {
    $where[] = "f.room_name LIKE ?";
    $params[] = "%{$search['room_name']}%";
}
if (!empty($search['category'])) {
    $where[] = "c.category_name = ?";
    $params[] = $search['category'];
}
if ($search['max_people'] !== null) {
    $where[] = "f.max_number_of_people >= ?";
    $params[] = $search['max_people'];
}
if ($search['unit_price'] !== null) {
    $operator = $search['price_condition'] === 'less' ? '<=' : '>=';
    $where[] = "f.time_of_unit_price $operator ?";
    $params[] = $search['unit_price'];
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// ページネーション
// 総件数の取得
$total = executeQuery($conn, "
    SELECT COUNT(*) as count
    FROM facility_table f
    JOIN category_table c ON f.category_number = c.category_number
    $whereClause
", $params)->fetch()['count'];

// 総ページ数を計算
$totalPages = ceil($total / RESULTS_PER_PAGE);

// 現在のページが総ページ数を超えないようにする
$page = max(1, min((int)($_GET['page'] ?? 1), $totalPages));
$offset = ($page - 1) * RESULTS_PER_PAGE;

// 施設データの取得（ORDER BY 句を修正）
$facilities = executeQuery($conn, "
    SELECT f.*, c.category_name
    FROM facility_table f
    JOIN category_table c ON f.category_number = c.category_number
    $whereClause
    ORDER BY f.room_number ASC
    OFFSET ? ROWS
    FETCH NEXT ? ROWS ONLY
", array_merge($params, [$offset, RESULTS_PER_PAGE]))->fetchAll();

// カテゴリー一覧の取得
$categories = executeQuery($conn, 
    "SELECT * FROM category_table ORDER BY category_number"
)->fetchAll();



// カテゴリーのページネーション設定
$category_itemsPerPage = 5;
$categoryTotalItems = count($categories);  // 変数名変更
$categoryTotalPages = ceil($categoryTotalItems / $category_itemsPerPage);  // 変数名変更
$currentPage = isset($_GET['category_page']) ? max(1, min($categoryTotalPages, (int)$_GET['category_page'])) : 1;
$offset = ($currentPage - 1) * $category_itemsPerPage;

// 現在のページのカテゴリーを取得
$displayCategories = array_slice($categories, $offset, $category_itemsPerPage);

/////////////////////////////////////備品管理モーダルの追加////////////////////////////////////////
// 備品一覧の取得
$items = executeQuery($conn, 
    "SELECT * FROM item_table ORDER BY item_number"
)->fetchAll();

// 備品のページネーション設定の修正
$item_itemsPerPage = 5;
$itemsTotalItems = count($items);
$itemsTotalPages = ceil($itemsTotalItems / $item_itemsPerPage);

// currentItemPageの取得を修正
$currentItemPage = isset($_GET['item_page']) ? 
    max(1, min((int)$_GET['item_page'], $itemsTotalPages)) : 1;

// オフセットの計算を修正
$itemOffset = ($currentItemPage - 1) * $item_itemsPerPage;

// 現在のページの備品を取得
$displayItems = array_slice($items, $itemOffset, $item_itemsPerPage);


//////////////////編集中///////////////////////
// 備品操作の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 備品の追加
        if (isset($_POST['add_item'])) {
            // logDebug("備品追加処理開始", $_POST);  // デバッグログ追加
            // // 重複チェック
            // $stmt = executeQuery($conn,
            //     "SELECT COUNT(*) FROM item_table WHERE item_name = ?",
            //     [$_POST['new_item_name']]
            // );
            // if ($stmt->fetchColumn() > 0) {
            //     throw new Exception("「{$_POST['new_item_name']}」は既に存在します");
            // }

            // executeQuery($conn,
            //     "INSERT INTO item_table (
            //         item_number,
            //         item_name,
            //         total_of_item,
            //         rental_unit_price
            //     ) VALUES (
            //         (SELECT COALESCE(MAX(item_number)+1, 0) FROM item_table), ?, ?, ?)",
            //     [
            //         $_POST['new_item_name'],
            //         (int)$_POST['new_item_total'],
            //         (int)$_POST['new_item_price']
            //     ]
            // );
            debugLog('備品追加処理開始');
    
            try {
                if (empty($_POST['new_item_name'])) {
                    throw new Exception('備品名を入力してください');
                }
                
                if (!isset($_POST['new_item_total']) || $_POST['new_item_total'] === '') {
                    throw new Exception('備品総数を入力してください');
                }
                
                if (!isset($_POST['new_item_price']) || $_POST['new_item_price'] === '') {
                    throw new Exception('貸出単価を入力してください');
                }
                
                $conn->beginTransaction();
                
                // 次の備品番号を取得
                $stmt = $conn->query("SELECT COALESCE(MAX(item_number), 0) + 1 AS next_id FROM item_table");
                $nextId = $stmt->fetch(PDO::FETCH_ASSOC)['next_id'];
                
                $stmt = $conn->prepare("
                    INSERT INTO item_table (item_number, item_name, total_of_item, rental_unit_price)
                    VALUES (?, ?, ?, ?)
                ");
                
                $result = $stmt->execute([
                    $nextId,
                    $_POST['new_item_name'],
                    (int)$_POST['new_item_total'],
                    (int)$_POST['new_item_price']
                ]);
                
                if (!$result) {
                    throw new Exception('データベースへの挿入に失敗しました');
                }
                
                $conn->commit();
                debugLog('備品追加成功', [
                    'item_number' => $nextId,
                    'item_name' => $_POST['new_item_name']
                ]);
                
            } catch (Exception $e) {
                $conn->rollBack();
                debugLog('備品追加エラー', ['error' => $e->getMessage()]);
                $error = $e->getMessage();
            }
        }

        
        // 備品の一括更新
        elseif (isset($_POST['update_items']) && 
                isset($_POST['item_numbers']) && 
                isset($_POST['item_names'])) {
            
            $itemNumbers = $_POST['item_numbers'];
            $itemNames = $_POST['item_names'];
            $itemTotals = $_POST['item_totals'];
            $itemPrices = $_POST['item_prices'];
            
            for ($i = 0; $i < count($itemNumbers); $i++) {
                // 重複チェック
                $stmt = executeQuery($conn,
                    "SELECT COUNT(*) FROM item_table WHERE item_name = ? AND item_number != ?",
                    [$itemNames[$i], (int)$itemNumbers[$i]]
                );
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("「{$itemNames[$i]}」は既に存在します");
                }

                executeQuery($conn,
                    "UPDATE item_table SET 
                        item_name = ?,
                        total_of_item = ?,
                        rental_unit_price = ?
                    WHERE item_number = ?",
                    [
                        $itemNames[$i],
                        (int)$itemTotals[$i],
                        (int)$itemPrices[$i],
                        (int)$itemNumbers[$i]
                    ]
                );
            }
        }
        
        // 備品の一括削除
        elseif (isset($_POST['delete_items']) && isset($_POST['item_numbers'])) {
            if (!isset($_POST['item_numbers']) || !is_array($_POST['item_numbers'])) {
                error_log('item_numbers が不正: ' . print_r($_POST['item_numbers'], true));
                // エラー処理
                return;
            }
            foreach ($_POST['item_numbers'] as $itemNumber) {
                // // 予約での使用チェック
                // $stmt = executeQuery($conn,
                //     "SELECT COUNT(*) FROM reservation_item_table WHERE item_number = ?",
                //     [(int)$itemNumber]
                // );
                // if ($stmt->fetchColumn() > 0) {
                //     throw new Exception("予約で使用されている備品は削除できません");
                // }

                executeQuery($conn,
                    "DELETE FROM item_table WHERE item_number = ?",
                    [(int)$itemNumber]
                );
            }
        }

        // リダイレクト
        $redirectUrl = $_SERVER['PHP_SELF'];
        if (isset($_GET['item_page'])) {
            $redirectUrl .= '?item_page=' . $_GET['item_page'];
        }
        header('Location: ' . $redirectUrl);
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
//////////////////////////////////////////////////////////////////////////////




?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>施設情報管理</title>
    <link rel="stylesheet" href="admin_basic.css">
    <link rel="stylesheet" href="facility_edit.css">
</head>
<body>
    <h1>施設情報管理</h1>
    
    <!-- 検索フォーム -->
    <form method="get" class="search-form">
        <input type="text" name="room_name" placeholder="部屋名" 
               value="<?= h($search['room_name']) ?>">
        <select name="category">
            <option value="">分類の選択</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= h($cat['category_name']) ?>"
                        <?= $search['category'] == $cat['category_name'] ? 'selected' : '' ?>>
                    <?= h($cat['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="max_people" placeholder="最大収容人数以上"
               value="<?= h($search['max_people']) ?>">
        <input type="number" name="unit_price" placeholder="時間単位料金"
               value="<?= h($search['unit_price']) ?>">
        <select name="price_condition">
            <option value="greater" <?= $search['price_condition'] == 'greater' ? 'selected' : '' ?>>
                以上
            </option>
            <option value="less" <?= $search['price_condition'] == 'less' ? 'selected' : '' ?>>
                以下
            </option>
        </select>
        <button type="submit" class="button" name="searches_search">検索</button>
        <button type="button" class="button" name="searches_reset" onclick="location.href='<?= $_SERVER['PHP_SELF'] ?>'">
            リセット
        </button>
    </form>

    <!-- 施設一覧 -->
    <form method="post" id="facilitiesForm">
        <table>
            <thead>
                <tr>
                    <th>選択</th>
                    <th>部屋番号</th>
                    <th>部屋名</th>
                    <th>最大収容人数</th>
                    <th>分類</th>
                    <?php if(getDisplayStyle($use_extension_flag) === 'block' && 
                               getDisplayStyle($equipment_flag) === 'block'): ?>
                    <th>設備</th>
                    <?php endif; ?>
                    <?php if(getDisplayStyle($use_extension_flag) === 'block' && 
                           getDisplayStyle($price_flag) === 'block'): ?>
                        <th>時間単位料金</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facilities as $f): ?>
                    <tr>
                        <td><input type="radio" name="selected_room" 
                                 value="<?= h($f['room_number']) ?>"
                                 data-category="<?= h($f['category_number']) ?>"></td>
                        <td><?= h($f['room_number']) ?></td>
                        <td><?= h($f['room_name']) ?></td>
                        <td><?= h($f['max_number_of_people']) ?>人</td>
                        <td><?= h($f['category_name']) ?></td>
                        <?php if(getDisplayStyle($use_extension_flag) === 'block' && 
                               getDisplayStyle($equipment_flag) === 'block'): ?>
                        <td><?= h($f['equipment']) ?></td>
                        <?php endif; ?>
                        <?php if(getDisplayStyle($use_extension_flag) === 'block' && 
                               getDisplayStyle($price_flag) === 'block'): ?>
                            <td><?= h($f['time_of_unit_price']) ?>円</td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>


    <!-- ページネーション -->
    <div class="pagination">
        <?php if ($total > RESULTS_PER_PAGE): ?>
            <?php
            // 現在のGETパラメータを取得し、pageパラメータを除外
            $params = $_GET;
            unset($params['page']);
            
            // 前へボタンの追加
            if ($page > 1):
                $prevUrl = '?page=' . ($page - 1);
                if (!empty($params)) {
                    $prevUrl .= '&' . http_build_query($params);
                }
            ?>
                <a href="<?= $prevUrl ?>">&laquo; 前へ</a>
            <?php endif; ?>

            <?php
            // ページ番号の表示
            for ($i = 1; $i <= $totalPages; $i++): 
                $url = '?page=' . $i;
                if (!empty($params)) {
                    $url .= '&' . http_build_query($params);
                }
            ?>
                <a href="<?= $url ?>" 
                class="<?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php
            // 次へボタンの追加
            if ($page < $totalPages):
                $nextUrl = '?page=' . ($page + 1);
                if (!empty($params)) {
                    $nextUrl .= '&' . http_build_query($params);
                }
            ?>
                <a href="<?= $nextUrl ?>">次へ &raquo;</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    

<!-- カテゴリー管理モーダル -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <h2>分類管理</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?= h($error) ?></div>
        <?php endif; ?>

        <!-- カテゴリー操作ボタン群 -->
        <div class="category-operations">
            <form method="post" class="add-category-form">
                <div class="operation-group">
                    <input type="text" name="new_category_name" placeholder="新しい分類名" required>
                    <button type="submit" class="button" name="add_category">追加</button>
                    <button type="button" class="button" onclick="updateCategories()">更新</button>
                    <button type="button" name="delete" class="button" onclick="deleteCategories()">削除</button>
                </div>
            </form>

        </div>

        <!-- カテゴリー一覧 -->
        <div class="category-list">
            <table class="category-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                        <th>分類番号</th>
                        <th>分類名</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    <?php foreach ($displayCategories as $cat): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="category-checkbox" 
                                   data-category-number="<?= h($cat['category_number']) ?>">
                        </td>
                        <td><?= h($cat['category_number']) ?></td>
                        <td>
                            <input type="text" class="category-name-input" 
                                   value="<?= h($cat['category_name']) ?>" required>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- ページネーション -->
            <?php if ($categoryTotalPages > 1): ?>
            <div class="modal-pagination">
                <?php
                $queryParams = $_GET;
                unset($queryParams['category_page']);
                // 前のページへのリンク
                if ($currentPage > 1):
                    $queryParams['category_page'] = $currentPage - 1;
                ?>
                    <a href="?<?= http_build_query($queryParams) ?>">&laquo; 前へ</a>
                <?php endif; ?>

                <?php
                // ページ番号のリンク
                for ($i = 1; $i <= $categoryTotalPages; $i++):
                    $queryParams['category_page'] = $i;
                    $activeClass = ($i == $currentPage) ? 'active' : '';
                ?>
                    <a href="?<?= http_build_query($queryParams) ?>" 
                    class="<?= $activeClass ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php
                // 次のページへのリンク
                if ($currentPage < $categoryTotalPages):
                    $queryParams['category_page'] = $currentPage + 1;
                ?>
                    <a href="?<?= http_build_query($queryParams) ?>">次へ &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="modal-buttons">
            <button type="button" class="button" onclick="closeModal()">閉じる</button>
        </div>
    </div>
</div>

<!-- 備品管理モーダル -->
<div id="itemModal" class="modal">

    <div class="modal-content">
        <h2>備品管理</h2>
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?= h($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success-message">
                <?= h($success) ?>
            </div>
        <?php endif; ?>

        <!-- 備品操作ボタン群 -->
        <div class="item-operations">
            <form method="post" class="add-item-form">
                <div class="operation-group">
                    <input type="text" name="new_item_name" placeholder="備品名" >
                    <input type="number" name="new_item_total" placeholder="備品総数" >
                    <input type="number" name="new_item_price" placeholder="貸出単価">

                    <button type="submit" class="button" name="add_item">追加</button>
                    <button type="button" class="button" onclick="updateItems()">更新</button>
                    <button type="button" class="button" onclick="deleteItems()">削除</button>
                </div>
            </form>
        </div>

        <!-- 備品一覧 -->
        <div class="item-list">

            <table class="item-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAllItems" onclick="toggleSelectAllItems()"></th>
                        <th>備品番号</th>
                        <th>備品名</th>
                        <th>総数</th>
                        <th>貸出単価</th>
                    </tr>
                </thead>
                <tbody id="itemTableBody">
                    <?php foreach ($displayItems as $item): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="item-checkbox" 
                                   data-item-number="<?= h($item['item_number']) ?>"
                                   value="<?= h($item['item_number']) ?>">
                        </td>
                        <td><?= h($item['item_number']) ?></td>
                        <td>
                            <input type="text" class="item-name-input" 
                                   value="<?= h($item['item_name']) ?>" required>
                        </td>
                        <td>
                            <input type="number" class="item-total-input" 
                                   value="<?= h($item['total_of_item']) ?>" required min="1">
                        </td>
                        <td>
                            <input type="number" class="item-price-input" 
                                   value="<?= h($item['rental_unit_price']) ?>" min="0">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- 備品のページネーション -->
            <?php if ($itemsTotalPages > 1): ?>
            <div class="item-modal-pagination">  <!-- クラス名を変更 -->
                <?php
                $queryParams = $_GET;
                unset($queryParams['item_page']);
                
                if ($currentItemPage > 1):
                    $queryParams['item_page'] = $currentItemPage - 1;
                ?>
                    <a href="?<?= http_build_query($queryParams) ?>">&laquo; 前へ</a>
                <?php endif; ?>

                <?php
                for ($i = 1; $i <= $itemsTotalPages; $i++):
                    $queryParams['item_page'] = $i;
                    $activeClass = ($i == $currentItemPage) ? 'item-page-active' : '';  
                ?>
                    <a href="?<?= http_build_query($queryParams) ?>" 
                    class="<?= $activeClass ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php
                if ($currentItemPage < $itemsTotalPages):
                    $queryParams['item_page'] = $currentItemPage + 1;
                ?>
                    <a href="?<?= http_build_query($queryParams) ?>">次へ &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="modal-buttons">
            <button type="button" class="button" onclick="closeItemModal()">閉じる</button>
        </div>
    </div>
</div>

<!-- デバッグ -->
 <!-- エラーメッセージの表示 -->
<?php if (isset($error)): ?>
    <div class="error-message">
        <?= h($error) ?>
    </div>
<?php endif; ?>

<?php if (isset($success)): ?>
    <div class="success-message">
        <?= h($success) ?>
    </div>
<?php endif; ?>



    <button class="button" onclick="openModal()">分類管理</button>

    <button class="button" onclick="openItemModal()">備品管理</button>

    <button class="button" onclick="location.href='facility_add.php'">施設追加</button>
    <button class="button" onclick="submitAction('change')">変更</button>
    <button class="button" name="delete" onclick="submitAction('delete')">削除</button><br /><br />
    <button class="button" onclick="location.href='../../login/admin_home.php'">ホームに戻る</button>

    <script src="facility.js"></script>
</body>
</html>