<?php

$host = "localhost";
$database = "normalab_reports";
$username = "root";
$password = "";
$message = "";

try {
    $connect = new PDO("mysql:host=$host; dbname=$database", $username, $password);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if (isset($_POST["login"])) {
        if (empty($_POST["username"]) || empty($_POST["password"])) {
            $message = '<label>All fields are required</label>';
        } else {
            $query = "SELECT * FROM korisnici WHERE korisnici_username = :username AND korisnici_password = :password";
            $statement = $connect->prepare($query);
            $statement->execute(
                array(
                    'username' => $_POST["username"],
                    'password' => md5($_POST['password'])
                )
            );
            $count = $statement->rowCount();
            $user = $statement->fetchAll();
            if ($count > 0) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user'] = $_POST['username'];
                $_SESSION['user-type'] = $user[0]['korisnici_korisnickaulogaid'];
                header("location:index.php");
            } else {
                $message = '<label>Wrong Data</label>';
            }
        }
    }
} catch (PDOException $error) {
    $message = $error->getMessage();
}

?>

<section class="vh-100">
    <div class="container-fluid h-custom vh-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5 text-center">
                <img src="images/main-logo.svg" class="img-fluid login-logo" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <form method="post" action="index.php">
                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <label class="form-label" for="form3Example3">Korisničko ime:</label>
                        <input name="username" type="text" id="form3Example3" class="form-control form-control-lg"
                            placeholder="Unesite korisničko ime..." />
                    </div>

                    <!-- Password input -->
                    <div data-mdb-input-init class="form-outline mb-3">
                        <label class="form-label" for="form3Example4">Lozinka:</label>
                        <input name="password" type="password" id="form3Example4" class="form-control form-control-lg"
                            placeholder="Unesite lozinku..." />
                    </div>

                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;" name="login">Prijava</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
    body {
        background: transparent linear-gradient(180deg, #00335E 0%, #0A0C10 100%) 0% 0% no-repeat padding-box;
        background: transparent linear-gradient(180deg, #00335E 0%, #0A0C10 100%) 0% 0% no-repeat padding-box;
    }

    .form-control-lg {
        font-size: 1rem;
    }

    .btn-group-lg>.btn,
    .btn-lg {
        font-size: 1rem;
    }

    label {
        color: #fff;
    }
</style>