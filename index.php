<!DOCTYPE html>
<html>
    <head>
        <title>PHP File Upload</title>
    </head>
    <body>
        <h1>Página principal</h1>
        <br>
        <h2>Estado de la conexión:</h2>
        <?php
            session_start();
            $serverName = "LAPTOP-G5H3UKRA"; //serverName\instanceName

            // La conexión se intentará utilizando la autenticación Windows.
            $connectionInfo = array( "Database"=>"exampledatabase");
            $conn = sqlsrv_connect( $serverName, $connectionInfo);

            if( $conn ) {
                echo "Conexión establecida.<br />";
            }else{
                echo "Conexión no se pudo establecer.<br />";
                die( print_r( sqlsrv_errors(), true));
            }
            /* Query al server SQL que muestra el nombre del usuario al que se conectará el servidor*/
            $tsql = "SELECT CONVERT(varchar(32), SUSER_SNAME())";
            $stmt = sqlsrv_query( $conn, $tsql);
            if( $stmt === false ){
                echo "Error al ejecutar el query.</br>";
                die( print_r( sqlsrv_errors(), true));
            }

            /* Muestra los resultados de la query */
            $row = sqlsrv_fetch_array($stmt);
            echo "User login: ".$row[0]."</br>";

            /* Libera los statements y cierra la conexión */
            sqlsrv_free_stmt( $stmt);
            sqlsrv_close( $conn);
        ?>

        <hr>
        <!--Esta sección es para subir documentos al localhost-->
        <h3>Esta sección permite subir documentos al localhost.</h3>
        <?php
            if (isset($_SESSION['message']) && $_SESSION['message']) {
                printf('<b>%s</b>', $_SESSION['message']);
                unset($_SESSION['message']);
            }
        ?>

        <form method="POST" action="upload.php" enctype="multipart/form-data">
            <div>
                <span>Upload a File:</span>
                <input type="file" name="uploadedFile"/>
            </div>
            <input type="submit" name="uploadBtn" value="Upload"/>
        </form>
        <br>
        <hr>



    <!--Muestra los códigos sha1 de cada archivo, así como los sube a la base de datos de sql server-->

        <h3>Esta sección permite generar códigos sha1</h3>
        <form method="POST">
            <div>
                <span>Generate sha1</span>
                <br>
                Nombre de archivo (Prueba a generar primero los archivos disponibles): <br>
                <input type="text" name="name"><br>
                <input type="submit" name="generatesha1" value="Generate SHA1 Code">
            </div>
        </form>

       <h4>Aquí se genera el hash sha1, y se guarda el nombre del archivo en la base de datos:</h4> 
        <?php
            if (array_key_exists('generatesha1', $_POST)) {
                if (empty ($_POST["name"])) {
                    $errMsg = "Error!";
                    echo $errMsg;
                } else{
                    $filenm = "./uploaded_files/{$_POST["name"]}";
                    echo "Filename: ". $filenm;
                    echo "<br>";
                    echo "sha1-hash: ". sha1_file($filenm);  //THis line generates the sha1
                    echo "<br>";
                    
                    function insertIntoTable(){
                        $filenm = "./uploaded_files/{$_POST["name"]}";
                        $serverName = "";
                        $connectionInfo = array("Database" => "exampledatabase");
                        $conn = sqlsrv_connect($serverName, $connectionInfo);
                        if ($conn == false) {
                            die(print_r(sqlsrv_errors(), true));
                        }
                        // run query to select everything
                        $sql = "INSERT INTO dbo.filesandhashes(filename, sha1) VALUES (?, ?)";
                        $params = array($_POST["name"], sha1_file($filenm));
                        $stmt = sqlsrv_query($conn, $sql, $params);
                        if( $stmt === false ) {
                            die( print_r( sqlsrv_errors(), true));
                        }
                    }
                
                    insertIntoTable();
                
                }   
            }
        ?>

        <br>
        <hr>

        <!--Muestra los documentos que ya han sido subidos-->
  
        <h3>Aquí se mostrarán los archivos subidos a localhost</h3>
        <form method="POST">
            <div>
                <span>Display files</span>
                <input type="submit" name="display" value="Display files">
            </div>
        </form>

        <?php
            // Open a directory, and read its contents
            if(array_key_exists('display', $_POST)){
                $dir = "./uploaded_files/";
                if (is_dir($dir)){
                    if ($dh = opendir($dir)){
                      while (($file = readdir($dh)) !== false){
                        echo "filename:" . $file . "<br>";
                      }
                      closedir($dh);
                    }
                }
            }
            
        ?>

        <br>
        <hr>

        <!--Esta sección muestra la tabla de la base de datos-->
        <h3>Esta sección muestra la tabla de la base de datos</h3>
        <form method="POST">
            <div>
                <span>Display table</span>
                <input type="submit" name="showtable" value="Display table">
            </div>
        </form>


        <?php
            if (array_key_exists('showtable', $_POST)) {
                $connectionInfo = array("Database" => "exampledatabase");
                $conn = sqlsrv_connect($serverName, $connectionInfo);
                    if ($conn == false) {
                        die(print_r(sqlsrv_errors(), true));
                    }
                    // run query to select everything
                    
                $sql = "SELECT id, filename, sha1 FROM dbo.filesandhashes";
                $stmt = sqlsrv_query( $conn, $sql );
                if( $stmt === false) {
                    die( print_r( sqlsrv_errors(), true) );
                }

                    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
                        echo $row['id'].", ".$row['filename']." , ".$row['sha1']."<br />";
                    }  
                }

        ?>
        
    </body>
</html>
