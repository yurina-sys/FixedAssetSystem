<html>
<head>
    <title>墨田区固定資産台帳表示＆検索</title>
</head>

<body>

<?php
$fileData = new SplFileObject('墨田区固定資産台帳_202103.tsv');
$fileData -> setFlags(SplFileObject::READ_CSV);
$fileData -> setCsvControl("t");

foreach($fileData as $data) {

    $str = implode($data);
    $str = mb_convert_encoding($str, "UTF-8", "SJIS");
    echo($str);
    // var_dump(implode($data));
}
?>




</body>

</html>
