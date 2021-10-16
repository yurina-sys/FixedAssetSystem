<html>
<head>
    <title>墨田区固定資産台帳表示＆検索</title>
    <meta http-equiv="content-type" charset="UTF-8">
</head>

<body>

<?php
$property_number_text = isset($_GET["財産番号"]) ? $_GET["財産番号"] : "";
$asset_name_text = isset($_GET["資産名称"]) ? $_GET["資産名称"] : "";
$location_text = isset($_GET["所在地"]) ? $_GET["所在地"] : "";
$auxiliary_submit_name_text = isset($_GET["補助科目名称"]) ? $_GET["補助科目名称"] : "";
$acquisition_price_min_text = isset($_GET["最小取得価格"]) ? $_GET["最小取得価格"] : "";
$acquisition_price_max_text = isset($_GET["最大取得価格"]) ? $_GET["最大取得価格"] : "";
$book_value_of_period_min_text = isset($_GET["最小期末簿価"]) ? $_GET["最小期末簿価"] : "";
$book_value_of_period_max_text = isset($_GET["最大期末簿価"]) ? $_GET["最大期末簿価"] : "";
?>

<form action="index.php" method="get">
  <p>
     財産番号
     <input type="text" name="財産番号" value="<?php echo $property_number_text; ?>">
     資産名称
     <input type="text" name="資産名称" value="<?php echo $asset_name_text; ?>">
     所在地
     <input type="text" name="所在地" value="<?php echo $location_text; ?>">
     補助科目名称
     <input type="text" name="補助科目名称" value="<?php echo $auxiliary_submit_name_text; ?>">
     取得価格
     <input type="text" name="最小取得価格" value="<?php echo $acquisition_price_min_text; ?>">
     〜
     <input type="text" name="最大取得価格" value="<?php echo $acquisition_price_max_text; ?>">
     期末簿価
     <input type="text" name="最小期末簿価" value="<?php echo $book_value_of_period_min_text; ?>">
     〜
     <input type="text" name="最大期末簿価" value="<?php echo $book_value_of_period_max_text; ?>">
     <br>
     <input type="submit" value="submit">
  </p>
 </form>

 <a href="index.php?page=1">1</a>


<?php
// 文字列検索
const PROPERTY_NUMBER = "財産番号";
const ASSET_NAME = "資産名称";
const LOCATION = "所在地";
const AUXILIARY_SUBJECT_NAME = "補助科目名称";

// 範囲検索
const ACQUISITION_PRICE_MIN = "最小取得価格";
const ACQUISITION_PRICE_MAX = "最大取得価格";
const BOOK_VALUE_OF_PERIOD_MIN = "最小期末簿価";
const BOOK_VALUE_OF_PERIOD_MAX = "最大期末簿価";
// const SERCH_TERMS_ARRAY = ["財産番号", "資産名称", "所在地", "補助科目名称", "最小取得価格", "最大取得価格", "最小期末簿価", "最大期末簿価"];
const SERCH_TERMS_ARRAY = ["財産番号", "資産名称", "所在地", "補助科目名称"];

const PAGE_ITEM_COUNT = 20;

$current_page = isset($_GET["page"]);
print_r($current_page);

if(!isset($_GET["page"])) {
    $input_array = Array();
    $input_array[PROPERTY_NUMBER] = $_GET[PROPERTY_NUMBER];
    $input_array[ASSET_NAME] = $_GET[ASSET_NAME];
    $input_array[LOCATION] = $_GET[LOCATION];
    $input_array[AUXILIARY_SUBJECT_NAME] = $_GET[AUXILIARY_SUBJECT_NAME];
// $input_array[ACQUISITION_PRICE_MIN] = $_GET[ACQUISITION_PRICE_MIN];
// $input_array[ACQUISITION_PRICE_MAX] = $_GET[ACQUISITION_PRICE_MAX];
// $input_array[BOOK_VALUE_OF_PERIOD_MIN] = $_GET[BOOK_VALUE_OF_PERIOD_MIN];
// $input_array[BOOK_VALUE_OF_PERIOD_MAX] = $_GET[BOOK_VALUE_OF_PERIOD_MAX];
// 配列化した検索条件の未入力を除いた配列を作りたい（予定）
    foreach(array_values($input_array) as $array) {
        if($array != "") {
            // 検索条件があったら絞り込み処理実行
            searchDatas($input_array);
        } else {
            // 入力値なかったら全件表示
        }
    }
}
 
// echo '<a href="#title">テスト</a>';

function searchDatas($search_array) {
    print_r("入った");

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
$data_array = getDataArray($imploded_array, $header_array);

// 入力値とデータを比較（絞り込み）
foreach ($data_array as $column) {
    $match_flag = false;
        
    foreach(SERCH_TERMS_ARRAY as $team) {
        if($column[$team] != $search_array[$team] && $search_array[$team] != "") {
            $match_flag = false;
            break;
        }
        $match_flag = true;
    }
    if($match_flag) {
         $result_array[] = $column;
    }
}

$total_page_count = ceil(count($result_array) / PAGE_ITEM_COUNT);





    
    echo '<table border="5">
    <tr>';
    foreach($header_array as $header) {
        echo '<th>'.$header.'</th>';
    }
    echo '</tr>';
    foreach($result_array as $result) {
        echo '<tr>';
        foreach($header_array as $index) {
            echo '<td>'.$result[$index].'</td>';
        }
        echo '</tr>'; 
    }
    echo '</table>';
}

function getDataArray($fileData, $header_array) {
    // データ名定義の行を削除
    unset($fileData[0]);
    $fileData = array_values($fileData);
    
    // $dataは1行分のデータが入った配列
    foreach($fileData as $record_index => $data) {
        if (count($data) != 0) {
            foreach ($header_array as $header_index => $header) {
            // [資産名称]:墨田区　の形にする（連想配列）
            $data_array[$record_index][$header] = $data[$header_index];
            }
        }
    }
    return $data_array;
}
?>




</body>

</html>