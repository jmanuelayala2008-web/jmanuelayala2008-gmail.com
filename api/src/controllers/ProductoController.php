<?php
require_once __DIR__ . "/../Models/Productos.php";
class ProductoController{
    public function getAll()
    {
        $producto=Productos::all();
        echo json_encode($producto); 
    }
    //Actualizar producto
    public function actualizar($id)
    {

        $jsonData=file_get_contents('php://input');
        $data= json_decode($jsonData,true);
        if(json_last_error()!=JSON_ERROR_NONE)
            {
                echo json_encode(
                [
                    "status"=>"error",
                    "message"=>json_last_error_msg(),
                ]);
                return;
            }
        //"codBarras":"750105521001",
        if(!isset($data['codBarras']) || trim($data['codBarras'])=="")
            {
            echo json_encode(
                 [
                    "estatus"=>$jsonData["codBarras"],
                    "messaje"=>"El campo codigo de Barras es obligatorio"
                 ]);
                 return;
            }
    //"descripcion":"Arroz Integral 2kg",
    if(!isset($data['descripcion']) || trim($data['descripcion'])=="")
        {
            echo json_encode(
                 [
                    "estatus"=>$jsonData["codBarras"],
                    "messaje"=>"El campo descripcion es obligatorio"
                 ]);
                 return;
        }
    //"stock":85,
    if(!isset($data['stock']) || trim($data['stock'])=="")
        {
            echo json_encode(
                 [
                    "estatus"=>$jsonData["codBarras"],
                    "messaje"=>"El campo stock es obligatorio"
                 ]);
                 return;
        }
    //precio_unitario":15,
    if(!isset($data['precio_unitario']) || trim($data['precio_unitario'])=="")
    {
        echo json_encode(
            [
                "estatus"=>$jsonData["codBarras"],
                "messaje"=>"El campo precio_unitario es obligatorio",
            ]);
            return;
    
    }
    //"creado_por":"nn",
    //"fecha_registro":"2026-06-12"
        $producto=Productos::update($id,$data);
        if($producto)
            {
                echo json_encode([
                    "estado"=>true,
                    "message" =>"producto actualizado correctamente",
                ]);
                return;
            }
        echo json_encode($producto);  
    }
    //Adicionar Producto
    public function add()
    {
        $jsonData=file_get_contents('php://input');
        $data = json_decode($jsonData,true);
        //validacion
        $producto=Productos::add($data);
        if ($producto){
            echo json_encode([
                 "estado"=>true,
                    "message" =>"producto adicionado correctamente",
            ]);
           return;
        }
        echo json_encode($producto);
    }
    //Eliminar Producto
    public function eliminar($id)
    {
        $producto=Productos::delete($id);
        if ($producto){
            echo json_encode([
                "estado"=>true,
                "message"=>"producto eliminado correctamente",
            ]);
            return;
        }
        echo json_encode([
            "estado"=>false,
            "message"=>"No se pudo eliminar el producto",
        ]);
    }
}
