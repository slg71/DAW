<section>
        <h2>Estadísticas de la semana</h2>
        <p>Fotos subidas en los últimos 7 días:</p>
        
        <?php
            // =====================
            // LOGICA DEL GRÁFICO
            // =====================

            // 1. Preparo el array vacío para los datos
            $datos_grafico = [];
            $nombres_dias = ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'];
            $mapa_fechas = []; //para saber qué día cae cada fecha

            // 2. Inicializo los últimos 7 días a 0 para que el gráfico no salga vacío
            // Voy desde hace 6 días ($i=6) hasta hoy ($i=0)
            for ($i = 6; $i >= 0; $i--) {
                $timestamp = strtotime("-$i days"); // Calculo la fecha restando días
                $fecha_mysql = date('Y-m-d', $timestamp); // Formato para la BD (2023-10-25)
                $num_dia = date('w', $timestamp); // Número del día de la semana (0 a 6)
                
                $nombre_dia = $nombres_dias[$num_dia]; // Saco el nombre corto: 'Lun', 'Mar'
                
                $datos_grafico[$nombre_dia] = 0; // Empiezo con contador a 0
                $mapa_fechas[$fecha_mysql] = $nombre_dia; // Guardo la relación para usarla luego
            }

            // 3. Consulta a la Base de Datos
            $mysqli = conectarBD();

            if ($mysqli) {
                // Cuento cuántos anuncios (fotos) hay por día en la última semana
                // Uso DATE(FRegistro) para quitar la hora y agrupar solo por día
                $sql = "SELECT DATE(FRegistro) as fecha, COUNT(*) as cantidad 
                        FROM anuncios 
                        WHERE FRegistro >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
                        GROUP BY DATE(FRegistro)";

                if ($resultado = $mysqli->query($sql)) {
                    while ($fila = $resultado->fetch_assoc()) {
                        $fecha_bd = $fila['fecha'];
                        $cantidad = $fila['cantidad'];
                        
                        // Si la fecha que viene de la BD está en mi mapa de los últimos 7 días
                        if (isset($mapa_fechas[$fecha_bd])) {
                            $dia = $mapa_fechas[$fecha_bd]; //recupero el nombre del día
                            $datos_grafico[$dia] = (int)$cantidad; //y actualizo el contador
                        }
                    }
                    $resultado->free();
                }
            }

            // 4. Llamo a mi función de GD para generar la imagen
            $grafico_base64 = generar_grafico($datos_grafico);
            ?>
        
        <article>
            <img src="<?php echo $grafico_base64; ?>" alt="Gráfico de barras generado con GD">
        </article>
    </section>