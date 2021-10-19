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
$input_asset_name = isset($_GET["asset_name"]) ? $_GET["asset_name"] : "";
$input_location = isset($_GET["location"]) ? $_GET["location"] : "";
$input_auxiliary_submit_name = isset($_GET["auxiliary_submit_name"]) ? $_GET["auxiliary_submit_name"] : "";
$input_acquisition_price_min = isset($_GET["acquisition_price_min"]) ? $_GET["acquisition_price_min"] : "";
$input_acquisition_price_max = isset($_GET["acquisition_price_max"]) ? $_GET["acquisition_price_max"] : "";
$input_book_value_of_period_min = isset($_GET["book_value_of_period_min"]) ? $_GET["book_value_of_period_min"] : "";
$input_book_value_of_period_max = isset($_GET["book_value_of_period_max"]) ? $_GET["book_value_of_period_max"] : "";
?>

 <div class="container p-5" th:fragment="search">
		<form th:action="@{/book/search}" method="get">
			<div class="form-group form-inline input-group-sm">
			    <label for="name" class="col-md-2 control-label">財産番号</label>
			    <input type="text" class="form-control col-md-3"  placeholder="財産番号" name="property_number"  value="<?php echo $input_property_number; ?>">
			    <label for="isbn" class="col-md-2 control-label">資産名称</label>
			    <input type="text" class="form-control col-md-5" placeholder="資産名称" name="asset_name" value="<?php echo $input_asset_name; ?>">
			</div>
            <div class="form-group form-inline input-group-sm">
			    <label for="name" class="col-md-2 control-label">補助科目名称</label>
			    <input type="text" class="form-control col-md-3" placeholder="補助科目名称" name="auxiliary_submit_name" value="<?php echo $input_auxiliary_submit_name; ?>">
			    <label for="isbn" class="col-md-2 control-label">所在地</label>
			    <input type="text" class="form-control col-md-5" placeholder="所在地" name="location" value="<?php echo $input_location; ?>">
			</div>
			<div class="form-group form-inline input-group-sm">
			    <label for="price_from" class="col-md-2 control-label">取得価格</label>
			    <input type="number" class="form-control col-md-1" placeholder="下限" name="acquisition_price_min" value="<?php echo $input_acquisition_price_min; ?>">
				<label class="col-md-1 control-label">～</label>
			    <input type="number" class="form-control col-md-1" placeholder="上限" name="acquisition_price_max" value="<?php echo $input_acquisition_price_max; ?>">
                <label for="price_from" class="col-md-2 control-label">期末簿価</label>
			    <input type="number" class="form-control col-md-1" placeholder="下限" name="book_value_of_period_min" value="<?php echo $input_book_value_of_period_min; ?>">
				<label class="col-md-1 control-label">～</label>
			    <input type="number" class="form-control col-md-1" placeholder="上限" name="book_value_of_period_max" value="<?php echo $input_book_value_of_period_max; ?>">
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
    
$display_array = array_slice($result_array, $min_display_range, PAGE_ITEM_COUNT - 1);
// print_r("表示アイテム数".count($display_array));

echo '<div class="m-5">';
echo '<table class="table table-sm table-hover custom-table">
<tr>';
    for ($i = 0; $i < count($header_array); $i++) {
        if ($i == 1 || $i == 9 || $i == 12 || $i == 13) {
            echo '<th class="text-center">'.$header_array[$i].'</th>';
        } else {
            echo '<th class="text-left">'.$header_array[$i].'</th>';
        }
    }
echo '</tr>';
foreach($display_array as $array) {
echo '<tr>';
    for ($i = 0; $i < count($array); $i++) {
        if ($i == 1 || $i == 9 || $i == 12 || $i == 13) {
            echo '<td class="text-right">'.$array[$i].'</td>';
        } else {
            echo '<td class="text-left">'.$array[$i].'</td>';
        }
    }
    echo '</tr>'; 
}
    echo '</table>';

    $queryParam = '&asset_name='.$input_asset_name.
        '&location='.$input_location.
        '&auxiliary_submit_name='.$input_auxiliary_submit_name.
        '&acquisition_price_min='.$input_acquisition_price_min.
        '&acquisition_price_max='.$input_acquisition_price_max.
        '&book_value_of_period_min='.$input_book_value_of_period_min.
        '&book_value_of_period_max='.$input_book_value_of_period_max;

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

    // // 期末簿価（最大値）
    if ($input_book_value_of_period_max != "" && !($column[BOOK_VALUE_OF_PERIOD] <= $input_book_value_of_period_max)) {
        $match_flag = false;
    }

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
    $min_display_range = ($current_page - 1) * PAGE_ITEM_COUNT;

    return $min_display_range;
}
?>

</body>

</html>