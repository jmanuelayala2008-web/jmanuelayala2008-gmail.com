<?php
require_once __DIR__ . "/config.php";
class conexionPDO
{
    private static ?PDO $cnn = null;
    public static function connect():PDO
    {
        $pdo='mysql:host='.HOST.';port='.PORT.';dbname='.DATABASE.';'.CHARSET;
        $options=[
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES=>false
        ];
        try{
            self::$cnn=new PDO($pdo, USERNAME,PASSWORD,$options);
            }catch(\PDOException $error){
                error_log("Error de conexión MySQL: ".$error->getMessage());
                throw $error;
            }
            return self::$cnn;
    }
    //funcion que ejecute una consulta
    public static function query(string $sql,array $param=[]): array
    {
        try{
            $stmt=self::connect()->prepare($sql);
            $stmt->execute($param);
            return $stmt->fetchAll();//["ok"=> $sql];
        } catch (Exception $e){
            error_log("Error al consultar MySQL: ".$e->getMessage());
            throw $e;
        }
    }
    // funcion para ejecutar transaccion update o add
    public static function execute($sql,array $param,$id){
        try{
            $db = self::connect();
            $stmt=$db->prepare($sql);
            $res=$stmt->execute($param);
            if ($id == true){
                return $db->lastInsertId();
            }
            return $res;
        }
        catch(Exception $e)
        {
        die("Existe error al procesar saros".$e->getMessage());
        }
  }
}
