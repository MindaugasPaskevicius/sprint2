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

    //Uploud

    if (isset($_FILES['uploadFile'])) {
        $file_name = $_FILES['uploadFile']['name'];
        $file_type = $_FILES['uploadFile']['type'];
        $file_tmp = $_FILES['uploadFile']['tmp_name'];
        $file_size = $_FILES['uploadFile']['size'];
        $file_parts = explode('.', $_FILES['uploadFile']['name']);
        $file_exten = strtolower(end($file_parts));

        $extArray = array("jpg", "jpeg", "pdf", "png");

        if (in_array($file_exten, $extArray) === false) {
            print("Please choose a JPG, JPEG, PDF or PNG file.");
        }

        if ($file_size > 5000000) {
            print('Sorry, your file is too large');
        }
        move_uploaded_file($file_tmp, './' . $_GET["path"] . $file_name);
        header('Location:' . $_SERVER['REQUEST_URI']);
    }


    date_default_timezone_set("Europe/Vilnius");
    $cwd = getcwd();
    $path = './' . $_GET["path"];
    $fsndirs = scandir($path);

    //Delete

    if (isset($_POST['delete'])) {
        $file_del = './' . $_GET["path"] . $_POST['delete'];
        // print_r($file_del);
        $file_del1 = str_replace("&nbsp;", " ", htmlentities($file_del, 'utf-8'));
        if ($file_del1 != "." && $file_del1 != ".." && is_file($file_del1)) {
            unlink($file_del1);
        }
    }

    //List directorys and files

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
            print('<td><button id="delete">Delete</button></td>');
            print('</tr>');
        }
    }
    print("</table>");

    //New directory form

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
    <button id="add" type="submit">Add</button>
    </form>');

    //Uploud form

    print('<form action="" action="" method="post" enctype="multipart/form-data">
    <input id="input1" type="file" name="uploadFile" id="file">
    <input id="add" type="submit" action="" name="submit" value="Uploud">
    </form>');

    //Back button

    $que = explode('/', rtrim($_SERVER['QUERY_STRING'], '/'));
    array_pop($que);

    if (count($que) != 0) {
        print('<a id="back" href= ' . '?' . implode('/', $que) . ' >Back</a>');
    } else {
        print('<a id="back" href= "?path=/" >Back</a>');
    }

    ?>
</body>

</html>