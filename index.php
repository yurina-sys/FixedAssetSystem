<html>
<head>
    <title>墨田区固定資産台帳表示＆検索</title>
    <meta http-equiv="content-type" charset="UTF-8">
</head>

<body>

<?php

// 文字列検索
// 財産番号
const PROPERTY_NUMBER = "財産番号";
// 資産名称
const ASSET_NAME = "資産名称";
// 所在地
const LOCATION = "LOCATION";
// 補助科目名称
const AUXILIARY_SUBJECT_NAME = "AUXILIARY_SUBJECT_NAME";

// 範囲検索
// 取得価格
const ACQUISITION_PRICE_MIN = "ACQUISITION_PRICE_MIN";
const ACQUISITION_PRICE_MAX = "ACQUISITION_PRICE_MAX";
// 期末簿価
const BOOK_VALUE_OF_PERIOD_MIN = "BOOK_VALUE_OF_PERIOD_MIN";
const BOOK_VALUE_OF_PERIOD_MAX = "BOOK_VALUE_OF_PERIOD_MAX";

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

    // ここで入力された検索条件の未入力を除いた配列を作りたい（予定）
    $input_array = Array();
    if ($_GET[PROPERTY_NUMBER] != "") {
        $input_array[PROPERTY_NUMBER] = $_GET[PROPERTY_NUMBER];
    }

    if ($_GET[ASSET_NAME] != "") {
        $input_array[ASSET_NAME] = $_GET[ASSET_NAME];
    }
    print_r($input_array);


    // 入力値とデータを比較（検索部分本体）
    // foreach ($data_array as $column) {
    //     if ($column[ASSET_NAME] == $_GET[ASSET_NAME] && 
    //         $column[PROPERTY_NUMBER] == $_GET[PROPERTY_NUMBER]) {
    //         print_r($column);
    //     }
    // }


    // $test_array = Array();
    // $test_array["0"] = $_GET[ASSET_NAME];
    // var_dump($_GET[ASSET_NAME]);
    // if (in_array("", $test_array) || in_array(" ", $test_array)) {
    //     echo "からもじ含む";
    // } else {
    //     echo "からもじ含まない";
    // }

?>




</body>

</html>