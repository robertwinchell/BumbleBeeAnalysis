<?php
define ('DS', DIRECTORY_SEPARATOR);

$filepath=$_SERVER['DOCUMENT_ROOT'];
$f = 0;
if ($_FILES) {
//    var_dump($_FILES);
    if (isset($_POST['outputname'])) {
        $outputname = $_POST['outputname'] . ".txt";
    } else {
        $outputname = "file1.txt";
    }
    $outputfile = $filepath . DS . "d3js" . DS . "output" . DS . $outputname;

    $file = fopen($outputfile, 'w');
    fwrite($file, "FN " . $outputname . "\n");
    fwrite($file, "VR 1.0" . "\n");

    $a = 0;
    $f = 0;
    foreach ($_FILES as $FILE) {
        $source = $FILE;
        $filename = $source['tmp_name'];
        $name = $source['name'];
        if ($name == '') {
            echo "<p>File $f - no file selected</p>";
        } else if ($source['type'] != 'text/html') {
            echo "<p>File $f - $name : invalid file type : you can only process html files</p>";
        } else if ($source['error'] > 0) {
            echo "<p>File $f - $name : sorry there was an error processing this file</p>";
        } else {

            $doc = new DOMDocument();
            @$doc->loadHTMLFile($filename);

            $body = $doc->getElementsByTagName('body');

            $i = 0;

            foreach ($body as $bodynode) {
                $childnodes = $bodynode->childNodes;

                foreach ($childnodes as $node) {
                    if ($node->tagName == "table") {
                        fwrite($file, "PT J" . "\n");
                        fwrite($file, "LA French" . "\n");
                        fwrite($file, "DT Article\n");

                        $tdnodes = $node->getElementsByTagName('td');
                        $spannodes = $node->getElementsByTagName('span');

                        $DocPublicationName = array();
                        $DocHeader = array();
                        $TitreArticleVisu = array();

                        foreach ($spannodes as $span) {
                            if ($span->tagName == "span") {
                                if ($span->hasAttribute("class")) {
                                    $class = $span->getAttribute("class");
                                    switch ($class) {
                                        case "DocPublicationName":
                                            $DocPublicationName[] = $span->nodeValue;
                                            break;
                                        case "DocHeader":
                                            $DocHeader[] = $span->nodeValue;
                                            break;
                                        case "TitreArticleVisu":
                                            $TitreArticleVisu[] = $span->nodeValue;
                                            break;
                                    }
                                }
                            }
                        }
                        fwrite($file, "SO " . implode(" ", $DocPublicationName) . "\n");
//                echo implode(" ", $DocHeader);
                        fwrite($file, "TI " . implode(" ", $TitreArticleVisu) . "\n");
                        foreach ($tdnodes as $td) {
                            fwrite($file, "AB " . $td->nodeValue . "\n");
                            break;
                        };
                        fwrite($file, "PY 2014\n");
                        fwrite($file, "UT $i\n");
                        fwrite($file, "ER\n");
                        $a++;
                        $i++;
                    }
                }
            }
            $curF = $f + 1;
            echo "<p>File $curF -  $name : $i articles processed </p>";
        }
        $f++;
    }
    fwrite($file, "EF");
    fclose($file);

    echo "<p>$f files and $a articles processed in total</p>";
    echo "<p>Your output file can be found at <a href='/d3js/output/$outputname' download>here</a></p>";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8' />
    </head>
    <body>
        <form method="post" enctype="multipart/form-data">
            <label for="outputname">Output File Name</label>
            <input type="text" name="outputname" placeholder="enter file name"><br>
            <label for="file1">File 1:</label>
            <input type="file" name="file1" ><br>
            <label for="file2">File 2:</label>
            <input type="file" name="file2" ><br>
            <label for="file3">File 3:</label>
            <input type="file" name="file3" ><br>
            <label for="file4">File 4:</label>
            <input type="file" name="file4" ><br>
            <input type="submit" name="submit" value="Submit">
        </form>

    </body>
</html>

