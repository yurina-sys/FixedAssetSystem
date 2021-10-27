<html>
<head>
    <title>墨田区固定資産台帳表示＆検索</title>
    <meta http-equiv="content-type" charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
 
    <!-- Bootstrap Javascript(jQuery含む) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
 
    <link rel="stylesheet" href="index.css">

</head>

<body>

<?php
$input_property_number = isset($_GET["property_number"]) ? $_GET["property_number"] : "";
$input_property_number = extraTrim($input_property_number);

$input_asset_name = isset($_GET["asset_name"]) ? $_GET["asset_name"] : "";
$input_asset_name = extraTrim($input_asset_name);

$input_location = isset($_GET["location"]) ? $_GET["location"] : "";
$input_location = extraTrim($input_location);

$input_auxiliary_submit_name = isset($_GET["auxiliary_submit_name"]) ? $_GET["auxiliary_submit_name"] : "";
$input_auxiliary_submit_name = extraTrim($input_auxiliary_submit_name);

$input_acquisition_price_min = isset($_GET["acquisition_price_min"]) ? $_GET["acquisition_price_min"] : "";
$input_acquisition_price_min = extraTrim($input_acquisition_price_min);

$input_acquisition_price_max = isset($_GET["acquisition_price_max"]) ? $_GET["acquisition_price_max"] : "";
$input_acquisition_price_max = extraTrim($input_acquisition_price_max);

$input_book_value_of_period_min = isset($_GET["book_value_of_period_min"]) ? $_GET["book_value_of_period_min"] : "";
$input_book_value_of_period_min = extraTrim($input_book_value_of_period_min);

$input_book_value_of_period_max = isset($_GET["book_value_of_period_max"]) ? $_GET["book_value_of_period_max"] : "";
$input_book_value_of_period_max = extraTrim($input_book_value_of_period_max);
?>

<div class="text-center p-3">
    <h2>墨田区固定資産台帳</h2>
</div>

 <div class="container p-5" th:fragment="search">
		<form th:action="@{/book/search}" method="get">
			<div class="form-group form-inline input-group-sm">
			    <label for="name" class="col-md-2 control-label">財産番号</label>
			    <input type="text" class="form-control col-md-3" name="property_number"  value="<?php echo $input_property_number; ?>">
			    <label for="isbn" class="col-md-2 control-label">資産名称</label>
			    <input type="text" class="form-control col-md-5" name="asset_name" value="<?php echo $input_asset_name; ?>">
			</div>
            <div class="form-group form-inline input-group-sm">
			    <label for="name" class="col-md-2 control-label">補助科目名称</label>
			    <input type="text" class="form-control col-md-3" name="auxiliary_submit_name" value="<?php echo $input_auxiliary_submit_name; ?>">
			    <label for="location" class="col-md-2 control-label">所在地</label>
			    <input type="text" class="form-control col-md-5" name="location" value="<?php echo $input_location; ?>">
			</div>
			<div class="form-group form-inline input-group-sm">
			    <label for="price_from" class="col-md-2 control-label">取得価格</label>
			    <input type="number" class="form-control col-md-1" name="acquisition_price_min" value="<?php echo $input_acquisition_price_min; ?>">
				<label class="col-md-1 control-label">～</label>
			    <input type="number" class="form-control col-md-1" name="acquisition_price_max" value="<?php echo $input_acquisition_price_max; ?>">
                <label for="price_from" class="col-md-2 control-label">期末簿価</label>
			    <input type="number" class="form-control col-md-1" name="book_value_of_period_min" value="<?php echo $input_book_value_of_period_min; ?>">
				<label class="col-md-1 control-label">～</label>
			    <input type="number" class="form-control col-md-1" name="book_value_of_period_max" value="<?php echo $input_book_value_of_period_max; ?>">
			</div>
            <div class="form-group form-inline input-group-sm　form-check">
			    <label for="complete_match" class="col-md-2 offset-md-3">完全一致</label>
			    <input type="radio" <?php echo(getMatchTypeTag(true, false)); ?> class="form-check-input　col-md-1" name="match_type" value="complete_match">
			    <label for="part_match" class="col-md-2 control-label">部分一致</label>
			    <input type="radio" <?php echo(getMatchTypeTag(false, true)); ?> class="form-check-input　col-md-1" name="match_type" value="part_match">
			</div>
			<div class="text-center">
                <input type="submit" value="検索">
			</div>
		</form>
		<hr>
    </div>

<?php

// 文字列検索
const PROPERTY_NUMBER = 0;
const ASSET_NAME = 2;
const LOCATION = 3;
const AUXILIARY_SUBJECT_NAME = 5;

// 範囲検索
const ACQUISITION_PRICE = 12;
const BOOK_VALUE_OF_PERIOD = 13;

// 1ページあたりに表示するアイテム数
const PAGE_ITEM_COUNT = 20;

// 絞り込み処理実行
[$header_array, $result_array] = searchDatas($input_property_number,
                                            $input_asset_name,
                                            $input_location,
                                            $input_auxiliary_submit_name,
                                            $input_acquisition_price_min,
                                            $input_acquisition_price_max,
                                            $input_book_value_of_period_min,
                                            $input_book_value_of_period_max);

$current_page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
if ($current_page <= 0) {
    $current_page = 1;
}

// 全ページ数
$total_page_count = ceil(count($result_array) / PAGE_ITEM_COUNT);
// 表示アイテム配列の最小インデックス
$min_display_index = getDisplayItemIndex($current_page);
// 現在ページで表示するアイテム
$display_array = array_slice($result_array, $min_display_index, PAGE_ITEM_COUNT);


    $queryParam = '&property_number='.rawurlencode($input_property_number).
        '&asset_name='.rawurlencode($input_asset_name).
        '&location='.rawurlencode($input_location).
        '&auxiliary_submit_name='.rawurlencode($input_auxiliary_submit_name).
        '&acquisition_price_min='.rawurlencode($input_acquisition_price_min).
        '&acquisition_price_max='.rawurlencode($input_acquisition_price_max).
        '&book_value_of_period_min='.rawurlencode($input_book_value_of_period_min).
        '&book_value_of_period_max='.rawurlencode($input_book_value_of_period_max).
        '&match_type='.getMatchType();

    // 表示するページの範囲を決める
    if ($current_page == 1 || $current_page == $total_page_count) {
        $range = 4;
    } elseif ($current_page == 2 || $current_page == $total_page_count - 1) {
        $range = 3;
    } else {
        $range = 2;
    }
?>

<div class="text-center">
    <p>全<?php echo($total_page_count); ?>ページ　<?php echo(count($result_array)); ?>件中 <?php echo(getDisplayItemCount($current_page,$min_display_index, $result_array)); ?>件表示</p>
</div>

<div class="m-3">
    <table class="table table-sm table-hover custom-table">
        <tr>
            <?php for ($i = 0; $i < count($header_array); $i++) : ?>
                <?php if ($i == 1 || $i == 9 || $i == 12 || $i == 13) : ?> 
                    <th class="text-center"><?php echo($header_array[$i]); ?></th>
                <?php else : ;?>
                    <th class="text-left"><?php echo($header_array[$i]); ?></th>
                <?php endif; ?>
            <?php endfor; ?>
        </tr>
        <?php foreach($display_array as $array) : ?>
            <tr>
                <?php for ($i = 0; $i < count($array); $i++) : ?>
                    <?php if ($i == 1 || $i == 5) : ?>
                        <td class="text-center"><?php echo($array[$i]); ?></td>
                    <?php elseif ($i == 9) : ?> 
                        <td class="text-right"><?php echo($array[$i]); ?></td>
                    <?php elseif ($i == 12 || $i == 13) : ?> 
                        <!-- 金額をカンマ3桁区切りで整形してから表示 -->
                        <td class="text-right"><?php echo(formatAmountMoney($array[$i])); ?></td>
                    <?php else : ;?>
                        <td class="text-left"><?php echo($array[$i]); ?></td>
                    <?php endif; ?>
                <?php endfor; ?>
             </tr>  
        <?php endforeach; ?>
    </table>
</div>

<div class="text-center mb-5">
<!-- 戻るボタン作成 -->
<?php if ($current_page >= 2): ?>
    <a href="index.php?page=<?php echo($current_page - 1); echo($queryParam); ?>" class="page_feed">&laquo;</a>

    <?php else : ;?>
        <span class="first_last_page">&laquo;</span>
    <?php endif; ?>

<!-- ページ番号作成 -->
<?php for ($i = 1; $i <= $total_page_count; $i++) : ?>
    <!-- 例えば現在10ページ目なら、range=2 ページ表示範囲は 8-12 となる -->
    <?php if ($i >= $current_page - $range && $i <= $current_page + $range) : ?>
        <?php if ($i == $current_page) : ?>
            <!-- 表示中のページはリンクにしない -->
            <span class="now_page_number"><?php echo $i; ?></span>
        <?php else: ?>
            <!-- ページリンク部分 -->
            <a href="?page=<?php echo $i; echo $queryParam; ?>" class="page_number"><?php echo $i; ?></a>
        <?php endif; ?>
    <?php endif; ?>
 <?php endfor; ?>

 <?php if ($current_page < $total_page_count) : ?>
    <a href="index.php?page=<?php echo($current_page + 1); echo($queryParam); ?>" class="page_feed">&raquo;</a>
<?php else : ?>
    <span class="first_last_page">&raquo;</span>
<?php endif; ?>
</div>

<?php
function searchDatas($input_property_number, 
                    $input_asset_name, 
                    $input_location, 
                    $input_auxiliary_submit_name, 
                    $input_acquisition_price_min,
                    $input_acquisition_price_max,
                    $input_book_value_of_period_min,
                    $input_book_value_of_period_max) {

$fileData = new SplFileObject('墨田区固定資産台帳_202103.tsv');
$fileData -> setFlags(SplFileObject::READ_CSV);
// これ必要？
// $fileData -> setCsvControl("t");

// $dataは1行分のデータが入った配列
foreach($fileData as $data) {
    $converted_data = mb_convert_encoding($data, "UTF-8", "SJIS");

    // 一度1行分文字列に
    $imploded_str = implode($converted_data);

    // タブごとに分割して配列にする
    if ($imploded_str != "") {
        $imploded_array[] = explode("\t", $imploded_str);
    }
}

// カラム定義名配列
$header_array = $imploded_array[0];
// データ本体の配列
$data_array = getDataArray($imploded_array);

if (getMatchType() == "part_match") {
    // 部分一致で絞り込み
    $result_array = partMatch($data_array,
                                    $input_property_number, 
                                    $input_asset_name, 
                                    $input_location, 
                                    $input_auxiliary_submit_name, 
                                    $input_acquisition_price_min,
                                    $input_acquisition_price_max,
                                    $input_book_value_of_period_min,
                                    $input_book_value_of_period_max);
} else {
    // 完全一致で絞り込み
    $result_array = completeMatch ($data_array,
                                    $input_property_number, 
                                    $input_asset_name, 
                                    $input_location, 
                                    $input_auxiliary_submit_name, 
                                    $input_acquisition_price_min,
                                    $input_acquisition_price_max,
                                    $input_book_value_of_period_min,
                                    $input_book_value_of_period_max);
}
    return [$header_array, $result_array];
}

// 入力値からデータを絞り込み（完全一致）
function completeMatch ($data_array,
                        $input_property_number, 
                        $input_asset_name, 
                        $input_location, 
                        $input_auxiliary_submit_name, 
                        $input_acquisition_price_min,
                        $input_acquisition_price_max,
                        $input_book_value_of_period_min,
                        $input_book_value_of_period_max) {
                        
    $result_array = Array();

    foreach ($data_array as $column) {
        $match_flag = true;
                                    
        // 財産番号
        if ($input_property_number != "" && $input_property_number != $column[PROPERTY_NUMBER]) {
            $match_flag = false;
            continue;
        }
                            
        // 資産名称
        if ($input_asset_name != "" && $input_asset_name != $column[ASSET_NAME]) {
            $match_flag = false;
            continue;
        }
                            
       // 所在地
        if ($input_location != "" && $input_location != $column[LOCATION]) {
            $match_flag = false;
            continue;
        }
                            
        // 補助科目名称
        if ($input_auxiliary_submit_name != "" && $input_auxiliary_submit_name != $column[AUXILIARY_SUBJECT_NAME]) {
            $match_flag = false;
            continue;
        }
                            
        // 取得価格（最小値）
        if ($input_acquisition_price_min != "" && !($input_acquisition_price_min <= $column[ACQUISITION_PRICE])) {                          
            $match_flag = false;
            continue;
        }
                            
        // 取得価格（最大値）
        if ($input_acquisition_price_max != "" && !($column[ACQUISITION_PRICE] <= $input_acquisition_price_max)) {                          
            $match_flag = false;
            continue;
        }
                            
        // 期末簿価（最小値）
        if ($input_book_value_of_period_min != "" && !($input_book_value_of_period_min <= $column[BOOK_VALUE_OF_PERIOD])) {
            $match_flag = false;
            continue;
        }
                            
        // 期末簿価（最大値）
        if ($input_book_value_of_period_max != "" && !($column[BOOK_VALUE_OF_PERIOD] <= $input_book_value_of_period_max)) {
            $match_flag = false;
            continue;
        }
                            
        if($match_flag) {
             $result_array[] = $column;
        }
    }
    // 絞り込み結果
    return $result_array;
}

// 入力値からデータを絞り込み（部分一致）
function partMatch ($data_array,
                        $input_property_number, 
                        $input_asset_name, 
                        $input_location, 
                        $input_auxiliary_submit_name, 
                        $input_acquisition_price_min,
                        $input_acquisition_price_max,
                        $input_book_value_of_period_min,
                        $input_book_value_of_period_max) {
                        
    $result_array = Array();
  
    foreach ($data_array as $column) {
        $match_flag = true;
                                    
        // 財産番号
        if ($input_property_number != "" && mb_strpos($column[PROPERTY_NUMBER], $input_property_number) === false) {
            $match_flag = false;
            continue;
        }
                            
        // 資産名称
        if ($input_asset_name != "" && mb_strpos($column[ASSET_NAME], $input_asset_name) === false) {
            $match_flag = false;
            continue;
        }
                            
       // 所在地
        if ($input_location != "" && mb_strpos($column[LOCATION], $input_location) === false) {
            $match_flag = false;
            continue;
        }
                            
        // 補助科目名称
        if ($input_auxiliary_submit_name != "" && mb_strpos($column[AUXILIARY_SUBJECT_NAME], $input_auxiliary_submit_name) === false) {
            $match_flag = false;
            continue;
        }
                            
        // 取得価格（最小値）
        if ($input_acquisition_price_min != "" && !($input_acquisition_price_min <= $column[ACQUISITION_PRICE])) {                          
            $match_flag = false;
        }
                            
        // 取得価格（最大値）
        if ($input_acquisition_price_max != "" && !($column[ACQUISITION_PRICE] <= $input_acquisition_price_max)) {                          
            $match_flag = false;
        }
                            
        // 期末簿価（最小値）
        if ($input_book_value_of_period_min != "" && !($input_book_value_of_period_min <= $column[BOOK_VALUE_OF_PERIOD])) {
            $match_flag = false;
        }
                            
        // 期末簿価（最大値）
        if ($input_book_value_of_period_max != "" && !($column[BOOK_VALUE_OF_PERIOD] <= $input_book_value_of_period_max)) {
            $match_flag = false;
        }
                            
        if($match_flag) {
             $result_array[] = $column;
        }
    }
    // 絞り込み結果
    return $result_array;
}

function getDataArray($fileData) {
    // データ名定義の行を削除
    unset($fileData[0]);
    $fileData = array_values($fileData);
    
    // $dataは1行分のデータが入った配列
    foreach($fileData as $record_index => $data) {
        foreach ($data as $data_index => $column) {
        // [資産名称]:墨田区　の形にする（連想配列）
        $data_array[$record_index][$data_index] = $column;
        }
    }
    return $data_array;
}

// 表示するアイテムの最初のインデックスを取得
function getDisplayItemIndex($current_page) {
    $min_display_index = ($current_page - 1) * PAGE_ITEM_COUNT;

    return $min_display_index;
}

// 表示しているアイテムが何件か取得
function getDisplayItemCount($current_page, $min_display_range, $display_array) {
    // (現在ページ - 1) * 20件　＋　現在画面に表示されている件数
    return ($current_page - 1) * PAGE_ITEM_COUNT + count(array_slice($display_array, $min_display_range, PAGE_ITEM_COUNT));
}

// ラジオボタン完全一致か部分一致をチェック状態にするタグ取得
function getMatchTypeTag($is_complete_match, $is_part_match) {
    if (getMatchType() == "part_match") {
        // 部分一致のラジオボタンをチェック状態に
        return $is_part_match ? 'checked="checked"' : '';
    } else {
        // 完全一致のラジオボタンをチェック状態に
        return $is_complete_match ? 'checked="checked"' : '';
    }
}

// ラジオボタンのgetから完全一致か部分一致か判定
function getMatchType() {
    if (isset($_GET["match_type"])) {
        $match_type = $_GET["match_type"];

        if ($match_type == "part_match") {
            // 部分一致
            return 'part_match';
        } else {
            // 完全一致
            return 'complete_match';
        }
    } else {
        // 完全一致（デフォルト）
        return 'complete_match';
    }
}

// 金額をカンマ3桁区切り+円に整形
function formatAmountMoney($money) {
    return number_format($money).'円';
}

// 全角・半角空白（\s)とNULLバイト（\x00）を取り除く
function extraTrim($text) {
    // \A（文字の始端）  \z（文字の終端）　/u（UTF-8として処理、パターン修飾子）
    return preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $text);
}

?>

</body>

</html>