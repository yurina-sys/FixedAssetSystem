<html>
<head>
    <title>墨田区固定資産台帳表示＆検索</title>
    <meta http-equiv="content-type" charset="UTF-8">
</head>

<body>

<?php
$input_property_number = isset($_GET["property_number"]) ? $_GET["property_number"] : "";
$input_asset_name = isset($_GET["asset_name"]) ? $_GET["asset_name"] : "";
$input_location = isset($_GET["location"]) ? $_GET["location"] : "";
$input_auxiliary_submit_name = isset($_GET["auxiliary_submit_name"]) ? $_GET["auxiliary_submit_name"] : "";
$input_acquisition_price_min = isset($_GET["acquisition_price_min"]) ? $_GET["acquisition_price_min"] : "";
$input_acquisition_price_max = isset($_GET["acquisition_price_max"]) ? $_GET["acquisition_price_max"] : "";
$input_book_value_of_period_min = isset($_GET["book_value_of_period_min"]) ? $_GET["book_value_of_period_min"] : "";
$input_book_value_of_period_max = isset($_GET["book_value_of_period_max"]) ? $_GET["book_value_of_period_max"] : "";
?>

<form action="index.php" method="get">
  <p>
     財産番号
     <input type="text" name="property_number" value="<?php echo $input_property_number; ?>">
     資産名称
     <input type="text" name="asset_name" value="<?php echo $input_asset_name; ?>">
     所在地
     <input type="text" name="location" value="<?php echo $input_location; ?>">
     補助科目名称
     <input type="text" name="auxiliary_submit_name" value="<?php echo $input_auxiliary_submit_name; ?>">
     取得価格
     <input type="text" name="acquisition_price_min" value="<?php echo $input_acquisition_price_min; ?>">
     〜
     <input type="text" name="acquisition_price_max" value="<?php echo $input_acquisition_price_max; ?>">
     期末簿価
     <input type="text" name="book_value_of_period_min" value="<?php echo $input_book_value_of_period_min; ?>">
     〜
     <input type="text" name="book_value_of_period_max" value="<?php echo $input_book_value_of_period_max; ?>">
     <br>
     <input type="submit" value="submit">
  </p>
 </form>

<?php
// 文字列検索
const PROPERTY_NUMBER = 0;
const ASSET_NAME = 2;
const LOCATION = 3;
const AUXILIARY_SUBJECT_NAME = 5;

// 範囲検索
const ACQUISITION_PRICE = 12;
const BOOK_VALUE_OF_PERIOD = 13;

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

$total_page_count = ceil(count($result_array) / PAGE_ITEM_COUNT);
// print_r($total_page_count);
$min_display_range = getDisplayItemRange($current_page);

// print_r("最小表示インデックス".$min_display_range."<br>");
    
$display_array = array_slice($result_array, $min_display_range, PAGE_ITEM_COUNT);
// print_r("表示アイテム数".count($display_array));
echo '<table border="5">
<tr>';
foreach($header_array as $header) {
    echo '<th>'.$header.'</th>';
}
echo '</tr>';
foreach($display_array as $array) {
echo '<tr>';
    foreach($array as $column) {
        echo '<td>'.$column.'</td>';
    }
    echo '</tr>'; 
}
    echo '</table>';

    for ($i = 1; $i <= $total_page_count; $i++) {
        if ($i == $current_page) {
            // 表示中ページナンバーはリンクにしない
            echo  '<a>'.$i.'</a>';
            continue;
        }
        echo  '<a href="?page='.$i.'&property_number='.$input_property_number.
        '&asset_name='.$input_asset_name.
        '&location='.$input_location.
        '&auxiliary_submit_name='.$input_auxiliary_submit_name.
        '&acquisition_price_min='.$input_acquisition_price_min.
        '&acquisition_price_max='.$input_acquisition_price_max.
        '&book_value_of_period_min='.$input_book_value_of_period_min.
        '&book_value_of_period_max='.$input_book_value_of_period_max.'">'.$i.'</a>';
    }

function searchDatas($input_property_number, 
                    $input_asset_name, 
                    $input_location, 
                    $input_auxiliary_submit_name, 
                    $input_acquisition_price_min,
                    $input_acquisition_price_max,
                    $input_book_value_of_period_min,
                    $input_book_value_of_period_max) {
    // print_r("入った");

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

$result_array = Array();
// 入力値とデータを比較（絞り込み）
foreach ($data_array as $column) {
    $match_flag = true;
        
    // 財産番号
    if ($input_property_number != "" && $input_property_number != $column[PROPERTY_NUMBER]) {
        $match_flag = false;
    }

    // 資産名称
    if ($input_asset_name != "" && $input_asset_name != $column[ASSET_NAME]) {
        $match_flag = false;
    }

    // 所在地
    if ($input_location != "" && $input_location != $column[LOCATION]) {
        $match_flag = false;
    }

    // 補助科目名称
    if ($input_auxiliary_submit_name != "" && $input_auxiliary_submit_name != $column[AUXILIARY_SUBJECT_NAME]) {
        $match_flag = false;
    }

    // 取得価格（最小値）
    if ($input_acquisition_price_min != "" && $input_acquisition_price_min > $column[ACQUISITION_PRICE]) {
        $match_flag = false;
    } 
    
    // 取得価格（最大値）
    if ($input_acquisition_price_max != "" && $input_acquisition_price_max < $column[ACQUISITION_PRICE]) {
        // print_r("入った".$column[ACQUISITION_PRICE]);
        $match_flag = false;
    }

    // 期末簿価（最小値）
    // if ($input_book_value_of_period_min != "" && $input_book_value_of_period_min > $column[BOOK_VALUE_OF_PERIOD]) {
    //     $match_flag = false;
    // }

    // // 期末簿価（最大値）
    // if ($input_book_value_of_period_max != "" && $column[BOOK_VALUE_OF_PERIOD] > $input_book_value_of_period_min) {
    //     $match_flag = false;
    // }


  
    if($match_flag) {
         $result_array[] = $column;
    }
}
    return [$header_array, $result_array];
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

function getDisplayItemRange($current_page) {
    $min_display_range = ($current_page * PAGE_ITEM_COUNT) - PAGE_ITEM_COUNT + 1;

    return $min_display_range;
}
?>

</body>

</html>