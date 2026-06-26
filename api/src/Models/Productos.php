<?php
include_once __DIR__. "/../config/conexionDB.php";
class Productos
{
    //Mostrar Producto
    public static function all() 
    {
        $sql="SELECT * FROM productos";
        return ConexionPDO::query($sql);
    }
    //Actualizar producto
    public static function update($id,$data) 
    {
        if(isset($data['id']))
        {
           unset($data['id']);
        }
        $campos=[];
        $valores=[];
        //construir datos
        foreach($data as $columna=>$valor)
            {
                $campos[]="$columna=:$columna";
                $valores[":$columna"]=$valor;
            }
        $stringCampos=implode(",",$campos);
        //preparamos la consulta
        $sql="UPDATE productos SET $stringCampos WHERE id=:id";
        $valores[':id']=$id;
        $result = ConexionPDO::execute($sql, $valores,false);
        //$sql = "SELECT * FROM productos";
        return $sql;//ConexionPDO::query($sql);
    }
    public static function add($data) 
    {
        if(!is_array($data) || empty($data))
        {
            return false;
        }

        if(isset($data['id']))
        {
            unset($data['id']);
        }

        $columnasPermitidas=['codBarras','descripcion','stock','precio_unitario','creado_por','fecha_registro'];
        $data=array_intersect_key($data,array_flip($columnasPermitidas));

        if(empty($data))
        {
            return false;
        }

        $campos=[];
        $valores=[];
        $parametros=[];
        //construir datos
        foreach($data as $columna=>$valor)
            {
                $campos[]=$columna;
                $parametros[]=":$columna";
                $valores[":$columna"]=$valor;
            }
        $stringCampos=implode(",",$campos);
        $stringParametros=implode(",",$parametros);
        //preparamos la consulta
        $sql="INSERT INTO productos($stringCampos) VALUES ($stringParametros)";
        $result = ConexionPDO::execute($sql, $valores,true);
        return $result;
    }
    public static function delete($id)
    {
        $sql="DELETE FROM productos WHERE id=:id";
        $valores=[':id'=>$id];
        $result=ConexionPDO::execute($sql,$valores,false);
        return $result;
    }
}
