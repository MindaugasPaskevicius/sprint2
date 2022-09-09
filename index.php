<?php

session_start();

$path = isset($_GET["path"]) ? './' . $_GET["path"] : './';
$cwd = getcwd();
$fsndirs = scandir($path);
$msg = '';

//Login logic

if (
    isset($_POST['login']) && !empty($_POST['username'])
    && !empty($_POST['password'])
) {

    if (
        $_POST['username'] == 'mindaugas' &&
        $_POST['password'] == '1234'
    ) {
        $_SESSION['valid'] = true;
        $_SESSION['timeout'] = time();
        $_SESSION['username'] = 'mindaugas';
    } else {
        $msg = 'Wrong username or password';
    }
}

//Logout logic

if (isset($_GET['action']) and $_GET['action'] == 'logout') {
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    unset($_SESSION['valid']);
}

//New directory logic

if (isset($_POST["folder_create"])) {
    if ($_POST["folder_create"] != "") {
        $created_dir = './' . $_GET["path"] . $_POST["folder_create"];
        if (!is_dir($created_dir)) mkdir($created_dir, 0777, true);
    }
    $url = preg_replace("/(&?|\??)create_dir=(.+)?/", "", $_SERVER["REQUEST_URI"]);
    header('Location: ' . urldecode($url));
}

//Delete logic

if (isset($_POST['delete'])) {
    $file_del = './' . $_POST['delete'];
    $file_del1 = str_replace("&nbsp;", " ", htmlentities($file_del, 0,  'utf-8'));
    if ($file_del1 != "." && $file_del1 != ".." && is_file($file_del1)) {
        unlink($file_del1);
    }
}

//Downloud logic

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

//Uploud logic

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

?>

<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="style/style.css">
    <title>File system browser</title>
</head>

<body class="container">
    <?php

    //Login form

    if (!$_SESSION['valid'] == true ) {
        print('<div class="container mt-3 form-signin d-flex justify-content-center">');
        print('<form class="card mt-5 col-4 bg-light justify-content-center" style="height: 400px;" action="./" method="post">');
        print('<h2 class="form-signin-heading">Login</h2>');
        print('<h4 class="form-signin-heading text-center text-danger">' . $msg . '</h4>');
        print('<div><input type="text" class="form-control text-center mt-3 ms-5" style="width: 320px;" name="username" placeholder="username = mindaugas" required autofocus></br>');
        print('<input type="password" class="form-control text-center ms-5" style="width: 320px;" name="password" placeholder="password = 1234" required>');
        print('<div class="d-flex justify-content-center"><button class=" btn btn-lg btn-warning mt-5 btn-block" style="width: 100px;" type="submit" name="login">Login</button></div></form>');
        print('</div>');
        die();
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

    print('<form id="form" action="" method="post">
    <input type="hidden" name="path" value="" /> 
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
        print('<a id="back" href= "./" >Back</a>');
    }

    //Logout button

    print('<div id="logout"><a style="background-color:red;" href = "index.php?action=logout"> Logout</div>');

    ?>
</body>

</html>