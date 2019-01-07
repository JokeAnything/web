

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">        
    </head>
    <body>
        <?php
            include_once("./filesysapi.php");
            $curPath = getRootPath();
            if (isset($_GET["path"])){
                $curPath = $_GET["path"];
            }

            echo "<p>root path:{$curPath}</p>";
            echo "<hr width=\"90%\" color=\"#FF0000\"/>";

            echo "<ul>";
            $isTop = isTopPath($curPath);
            if(!$isTop){
                $baseDir = getBaseDir($curPath);
                echo "<li><a href=\"./index.php?path={$baseDir}\">..</a></li>";
            }
            $fileList = getFileList($curPath);

            $arrLen = count($fileList);
            for($i = 0; $i < $arrLen; $i++){
                if( (strcmp($fileList[$i],".") == 0) ||
                    (strcmp($fileList[$i],"..") == 0)){
                        continue;
                    }
                $tmp = $curPath."/".$fileList[$i];
                echo "<li><a href=\"./index.php?path={$tmp}\">{$fileList[$i]}</a></li>";
            }
            echo "</ul>";
        ?>
    </body>
</html>