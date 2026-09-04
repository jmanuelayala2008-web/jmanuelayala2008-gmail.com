<?php
require_once __DIR__ . "/../Models/Clientes.php";
class ClientesController{
    public function getAll()
    {
        $cliente=CLIENTES::all();
        echo json_encode($cliente);
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
