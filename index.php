<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="style/style.css">
    <title>File System Browser</title>
</head>

<body class="container">
    <?php
    $path = isset($_GET["path"]) ? './' . $_GET["path"] : './';
    $files_and_dirs = scandir($path);

    print('<h2>Name of Directory: ' . str_replace('?path=', '', $_SERVER['REQUEST_URI']) . '</h2>');

    //

    print('<table><th>Type</th><th>Name</th><th>Actions</th>');
    foreach ($files_and_dirs as $fnd) {
        if ($fnd != ".." and $fnd != ".") {
            print('<tr>');
            print('<td>' . (is_dir($path . $fnd) ? "Directory" : "File") . '</td>');
            print('<td>' . (is_dir($path . $fnd)
                ? '<a href="' . (isset($_GET['path'])
                    ? $_SERVER['REQUEST_URI'] . $fnd . '/'
                    : $_SERVER['REQUEST_URI'] . '?path=' . $fnd . '/') . '">' . $fnd . '</a>'
                : $fnd)
                . '</td>');
            print('<td></td>');
            print('</tr>');
        }
    }
    print("</table>");

    //

    $que = explode('/', rtrim($_SERVER['QUERY_STRING'], '/'));
    array_pop($que);                                                             

    if (count($que) != 0) {
        print('<a id="back" href= ' . '?' . implode('/', $que) . ' >BACK</a>');
    } else {
        print('<a id="back" href= "?path=/" >BACK</a>');
    }

    //

    if (isset($_POST["folder_create"])) {
        if ($_POST["folder_create"] != "") {
            $created_dir = './' . $_GET["path"] . $_POST["folder_create"];
            if (!is_dir($created_dir)) mkdir($created_dir, 0777, true);
        }
        $url = preg_replace("/(&?|\??)create_dir=(.+)?/", "", $_SERVER["REQUEST_URI"]);
        header('Location: ' . urldecode($url));
    }
    
    print('<form id="form" action="" method="post">
    <input type="hidden" name="path" value=' . ($_GET['path']) . ' /> 
    <input id="input" placeholder="Name of the new directory" type="text" id="folder_create" name="folder_create">
    <button id="add" type="submit">ADD</button>
    </form>');

    


    ?>
</body>

</html>