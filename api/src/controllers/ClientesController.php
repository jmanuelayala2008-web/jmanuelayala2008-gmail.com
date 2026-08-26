<?php
require_once __DIR__ . "/../Models/Productos.php";
class ClentesController{
    public function getAll()
    {
        $producto=clientes::all();
        echo json_encode($producto); 
    }
    //Actualizar producto
    public function actualizar($id)
    {

    }
    //Adicionar Producto
    public function add()
    {
        
    }
    //Eliminar Producto
    public function eliminar($id)
    {

    }
}
