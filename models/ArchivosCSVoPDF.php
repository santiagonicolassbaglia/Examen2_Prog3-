<?php

class ArchivosCSVoPDF
{
    public static function ExportarCSV($path)
    {
        $listaProductos = Arma::obtenerTodos();
        $file = fopen($path, "w");
        foreach($listaProductos as $producto)
        {
            $separado= implode(",", (array)$producto);  
            if($file)
            {
                fwrite($file, $separado.",\r\n"); 
            }                           
        }
        fclose($file);  
        return $path;     
    }
}
?>