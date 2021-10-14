<html>
<head>
    <title>墨田区固定資産台帳表示＆検索</title>
    <meta http-equiv="content-type" charset="UTF-8">
</head>

<body>

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

$fileData = new SplFileObject('墨田区固定資産台帳_202103.tsv');
$fileData -> setFlags(SplFileObject::READ_CSV);
// これ必要？
// $fileData -> setCsvControl("t");

$data_array = Array();
$header_array = Array();
$imploded_array = Array();

// $dataは1行分のデータが入った配列
foreach($fileData as $record_index => $data) {
    $converted_data = mb_convert_encoding($data, "UTF-8", "SJIS");
    
    // 一度1行分文字列に
    $imploded_str = implode($converted_data);

    if ($record_index == 0) {
        $header_array = explode("\t", $imploded_str);
    } else {

        if ($imploded_str != "") {
        // タブごとに分割して配列に
        $imploded_array = explode("\t", $imploded_str);
            
            foreach ($header_array as $header_index => $header) {
                // [資産名称]:墨田区　の形にする（連想配列）
                $data_array[$record_index][$header] = $imploded_array[$header_index];
            }
        }
    }
}
// print_r($data_array);
    
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

    // print_r($input_array);


    $result_array = Array();
    // 入力値とデータを比較（検索部分本体）
    foreach ($data_array as $column) {
        $match_flag = false;
        
        foreach(SERCH_TERMS_ARRAY as $team) {
            if($column[$team] != $input_array[$team] && $input_array[$team] != "") {
                $match_flag = false;
                break;
            }
            $match_flag = true;
        }
        if($match_flag) {
            $result_array[count($result_array)] = $column;
        }
    }
    // print_r($result_array);
 

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


?>




</body>

</html>