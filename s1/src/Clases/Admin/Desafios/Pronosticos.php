<?php

namespace Clases\Admin\Desafios;

use \PDO;
// print_r($settings);
class Pronosticos
{
    private $log;
    function __construct($log = null)
    {
        $this->log = $log;
    }
    public function insertar($db, $obj = [])
    {
        if (!count($obj)) {
            return 0;
        }
        $columnaAInsertar = "";
        $variableAInsertar = "";
        $bandera = 0;
        foreach ($obj as $key => $value) {
            if ($bandera == 0) {
                $bandera++;
                $columnaAInsertar .= $key;
                $variableAInsertar .= ":" . $key;
            } else {
                $columnaAInsertar .= "," . $key;
                $variableAInsertar .= ",:" . $key;
            }
        }

        $sql = "
            INSERT INTO " . DB_BASE . ".prode_pronosticos (
                $columnaAInsertar
            )
            VALUES (
                $variableAInsertar   
            )
        ";
        $stmt = $db->prepare($sql);

        foreach ($obj as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        try {
            $stmt->execute();
            $id = $db->lastInsertId();
            return $id;
            //code...
        } catch (\Throwable $th) {
            if ($this->log) {
                ob_start();
                $stmt->debugDumpParams();
                $r = ob_get_contents();
                $this->log->error($r);
                ob_end_clean();
            }
            throw $th;
        }
    }

    public function verRestultado($db, $idGrupo, $idDesafio)
    { 
        $sql = "
            SELECT c.id_cliente,
                c.nickname_cliente,  
                COUNT( IF(e.ganador_encuentro = pro.ganador_pronostico,1,NULL) ) acierto,
                COUNT(e.id_encuentro) cantidad
            FROM prode_clientes c  
                JOIN prode_grupo_clientes gc ON c.id_cliente = gc.id_cliente
                LEFT JOIN prode_pronosticos pro ON pro.id_cliente= c.id_cliente AND pro.id_desafio = $idDesafio AND pro.id_grupo = $idGrupo
                LEFT JOIN prode_encuentros e ON e.id_encuentro = pro.id_encuentro
            WHERE gc.id_grupo = $idGrupo
            GROUP BY c.id_cliente
            ORDER BY acierto DESC
        ";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function verMiResultado($db, $idCliente, $idGrupo, $idDesafio)
    { 
        $sql = "
            SELECT
                e.*,
                IF(e.ganador_encuentro = pro.ganador_pronostico,1,0)  acierto,
                e.id_encuentro 
            FROM prode_clientes c  
                JOIN prode_grupo_clientes gc ON c.id_cliente = gc.id_cliente
                LEFT JOIN prode_pronosticos pro ON pro.id_cliente= c.id_cliente AND pro.id_desafio = $idDesafio AND pro.id_grupo = $idGrupo
                LEFT JOIN prode_encuentros e ON e.id_encuentro = pro.id_encuentro
            WHERE gc.id_grupo = $idGrupo and gc.id_cliente = $idCliente 
            ORDER BY acierto DESC
        ";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public function verRestultadoFinal($db, $idDesafio)
    {
        $sql = "
            SELECT c.id_cliente,
                c.nickname_cliente,  
                COUNT( IF(e.ganador_encuentro = pro.ganador_pronostico,1,NULL) ) acierto,
                COUNT(e.id_encuentro) cantidad
            FROM prode_clientes c  
                JOIN prode_grupo_clientes gc ON c.id_cliente = gc.id_cliente
                LEFT JOIN prode_pronosticos pro ON pro.id_cliente= c.id_cliente AND pro.id_desafio = $idDesafio  AND pro.id_grupo = gc.`id_grupo` 
                LEFT JOIN prode_encuentros e ON e.id_encuentro = pro.id_encuentro
            GROUP BY gc.id_grupo,c.id_cliente
            ORDER BY acierto DESC
        ";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }

    public static function verMiPronostico($db, $idGrupo, $idDesafio,$idCliente)
    {
        $sql = "
        SELECT 
        p.id_encuentro,
        po.ganador_pronostico,
        c.nombre_club local,
        a.nombre_club visitante 
      FROM
      ".DB_CON.".prode_encuentros p 
        JOIN ".DB_CON.".prode_clubes c 
          ON c.id_club = p.id_club_local 
        JOIN ".DB_CON.".prode_clubes a 
          ON a.id_club = p.id_club_visitante
        LEFT JOIN ".DB_CON.".`prode_pronosticos` po
        ON p.id_encuentro = po.`id_encuentro`
        AND po.id_grupo = $idGrupo AND po.id_cliente = $idCliente 
      WHERE p.id_desafio = $idDesafio
      ORDER BY p.dia_encuentro asc, p.hora_encuentro asc 
        ";
        $stmt = $db->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $data;
    }
}
