<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="style/style.css">
    <title>Praktinė užduotis</title>
</head>

<body class="container">
    <?php

    require('login.php');


    $path = isset($_GET["path"]) ? './' . $_GET["path"] : './';
    //$cwd = getcwd();
    $fsndirs = scandir($path);

    //Login form

    if (!$_SESSION['valid'] == true) {
        print('<div class="container mt-3 form-signin"><div class="container">');
        print('<form class="form-signin" role="form" action="./index.php"  method="post">');           //$_SERVER['PHP_SELF'] returns the filename of the currently executing script
        print('<h4 class="form-signin-heading">' . $msg . '</h4>');         // $msg = '';
        print('<input type="text" class="form-control" name="username" placeholder="username = mindaugas" required autofocus></br>');
        print('<input type="password" class="form-control" name="password" placeholder="password = 1234" required>');
        print('<button class="btn btn-lg btn-primary mt-2 btn-block" type="submit" name="login">Login</button></form>');
        print('</div>');
        die();
    }


    //Delete

    if (isset($_POST['delete'])) {
        $file_del = './' . $_GET["path"] . $_POST['delete'];
        $file_del1 = str_replace("&nbsp;", " ", htmlentities($file_del, false,  'utf-8'));
        if ($file_del1 != "." && $file_del1 != ".." && is_file($file_del1)) {
            unlink($file_del1);
        }
    }

    //Downloud

    if (isset($_POST['download'])) {
        $file = './' . $_GET["path"] . $_POST['download'];
        $file_path = str_replace("&nbsp;", " ", htmlentities($file, false, 'utf-8'));
        ob_clean();
        ob_start();
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename=' . basename($file_path));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        ob_end_flush();
        readfile($file_path);
        exit;
    }

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
        move_uploaded_file($file_tmp, './' . $file_name);
        header('Location:' . $_SERVER['REQUEST_URI']);
    }

    //Directorys and files table

    print('<h2>Name of Directory: ' . str_replace('?path=', '', $_SERVER['REQUEST_URI']) . '</h2>');
    print('<table><th>Type</th><th>Name</th><th>Actions</th>');
    foreach ($fsndirs as $find) {
        if ($find != ".." and $find != ".") {
            print('<tr>');
            print('<td>' . (is_dir($path . $find) ? "Directory" : "File") . '</td>');
            print('<td>' . (is_dir($path . $find)
                ? '<a href="' . (isset($_GET['path'])
                    ? $_SERVER['REQUEST_URI'] . $find . '/'
                    : $_SERVER['REQUEST_URI'] . '?path=' . $find . '/') . '">' . $find . '</a>'
                : $find)
                . '</td>');
            print('<td><form style="display: inline-block" action="" method="post">
            <input type="hidden" name="delete" value=' . str_replace(' ', '&nbsp;', $find) . '>  
            <input id="delete" type="submit" value="Delete">
           </form>
           <form style="display: inline-block" action="" method="post">
                <input type="hidden" name="download" value=' . str_replace(' ', '&nbsp;', $find) . '>
                <input id="delete" type="submit" value="Download">
               </form>
           </td>');
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

    print('<div id="logout"><a href = "index.php?action=logout"> Logout</div>');

    ?>
</body>

</html>