<?php
// Este fichero es el formulario
// Asume que ya existen las variables:
// $titulo_formulario (string) - El título a mostrar (ej: "Registro")
// $action_url (string) - La URL para el 'action' del form
// $es_registro (bool) - true si es el registro, false si es "mis datos"
//
// $valor_usuario (string) - Valor para el campo usuario
// $valor_email (string) - Valor para el campo email
// $valor_sexo (string) - Valor para el campo sexo ("hombre", "mujer", "otro")
// $valor_nac (string) - Valor para la fecha
// $valor_ciudad (string) - Valor para la ciudad
// $valor_pais_id (string) - El ID del país seleccionado
//
// $desactivado (string) - Contendrá "disabled" si es "mis datos", o "" si es registro
// $paises (array) - El array de países de la BD
// $errores (array) - El array de errores (puede estar vacío)

?>

<main id="registro">
    <h2><?php echo htmlspecialchars($titulo_formulario); ?></h2>

    <?php if (!$es_registro): ?>
        <p>Puedes modificar tus datos. Deja la contraseña nueva vacía si no quieres cambiarla.</p>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($action_url); ?>" method="post" enctype="multipart/form-data" novalidate>

        <label for="usuario">Usuario</label>
        <input type="text" id="usuario" name="usuario" 
               value="<?php echo htmlspecialchars($valor_usuario); ?>" <?php echo $desactivado; ?>>
        <?php if (isset($errores["usuario"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["usuario"]); ?></span>
        <?php endif; ?>

        <?php if ($es_registro): // Solo mostrar en la página de registro ?>
            <label for="pwd">Contraseña</label>
            <input type="password" id="pwd" name="pwd">
            <?php if (isset($errores["pwd"])): ?>
                <span class="error-campo"><?php echo htmlspecialchars($errores["pwd"]); ?></span>
            <?php endif; ?>

            <label for="pwd2">Repetir Contraseña</label>
            <input type="password" id="pwd2" name="pwd2">
            <?php if (isset($errores["pwd2"])): ?>
                <span class="error-campo"><?php echo htmlspecialchars($errores["pwd2"]); ?></span>
            <?php endif; ?>
        <?php else: ?>
            <label for="pwd_nueva">Nueva Contraseña (Opcional)</label>
            <input type="password" id="pwd_nueva" name="pwd_nueva" placeholder="Dejar vacío para mantener la actual">
            <?php if (isset($errores["pwd_nueva"])): ?>
                <span class="error-campo"><?php echo htmlspecialchars($errores["pwd_nueva"]); ?></span>
            <?php endif; ?>

            <label for="pwd_nueva2">Repetir Nueva Contraseña</label>
            <input type="password" id="pwd_nueva2" name="pwd_nueva2">
            <?php if (isset($errores["pwd_nueva2"])): ?>
                <span class="error-campo"><?php echo htmlspecialchars($errores["pwd_nueva2"]); ?></span>
            <?php endif; ?>

            <hr>
            
            <label for="pwd_actual" class="required">Contraseña ACTUAL (Para confirmar cambios)</label>
            <input type="password" id="pwd_actual" name="pwd_actual">
            <?php if (isset($errores["pwd_actual"])): ?>
                <span class="error-campo"><?php echo htmlspecialchars($errores["pwd_actual"]); ?></span>
            <?php endif; ?>

        <?php endif; ?>


        <label for="email">Dirección de email</label>
        <input type="text" id="email" name="email" 
               value="<?php echo htmlspecialchars($valor_email); ?>" <?php echo $desactivado; ?>>
        <?php if (isset($errores["email"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["email"]); ?></span>
        <?php endif; ?>

        <label for="sexo">Sexo</label>
        <select id="sexo" name="sexo" <?php echo $desactivado; ?>>
            <option value="">---</option>
            <!-- Usamos la misma lógica de strings que registro.php -->
            <option value="hombre" <?php if ($valor_sexo == "hombre") echo "selected"; ?>>Hombre</option>
            <option value="mujer" <?php if ($valor_sexo == "mujer") echo "selected"; ?>>Mujer</option>
            <option value="otro" <?php if ($valor_sexo == "otro") echo "selected"; ?>>Otro</option>
        </select>
        <?php if (isset($errores["sexo"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["sexo"]); ?></span>
        <?php endif; ?>

        <label for="nac">Fecha de nacimiento</label>
        <input type="date" id="nac" name="nac" 
               value="<?php echo htmlspecialchars($valor_nac); ?>" <?php echo $desactivado; ?>>
        <?php if (isset($errores["nac"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["nac"]); ?></span>
        <?php endif; ?>

        <label for="ciudad">Ciudad de residencia</label>
        <input type="text" id="ciudad" name="ciudad" 
               value="<?php echo htmlspecialchars($valor_ciudad); ?>" <?php echo $desactivado; ?>>
        <?php if (isset($errores["ciudad"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["ciudad"]); ?></span>
        <?php endif; ?>

        <label for="pais">País de residencia</label>
        <select id="pais" name="pais" <?php echo $desactivado; ?>>
            <?php
            // Generar las opciones del select desde la variable $paises
            foreach ($paises as $tipo) {
                $id_pais_loop = $tipo['IdPais'];
                $nombre_pais = htmlspecialchars($tipo['NomPais']);
                // Marcar el país que coincida con $valor_pais_id
                $seleccionado = ($id_pais_loop == $valor_pais_id) ? 'selected' : '';
                echo "<option value='$id_pais_loop' $seleccionado>$nombre_pais</option>";
            }
            ?>
        </select>
        <?php if (isset($errores["pais"])): ?>
            <span class="error-campo"><?php echo htmlspecialchars($errores["pais"]); ?></span>
        <?php endif; ?>

        <label for="foto">Foto de perfil (Subir nueva para cambiar):</label>
        <input type="file" id="foto" name="foto">
        
        <?php if (!$es_registro): ?>
            <label class="checkbox-recordar">
                Eliminar foto actual (volver a la de defecto)
                <input type="checkbox" name="borrar_foto" id="borrar_foto" value="1">
            </label>
        <?php endif; ?>


        <button type="submit"><?php echo $es_registro ? "Confirmar Registro" : "Guardar Cambios"; ?></button>
        <?php if ($es_registro): ?><button type="reset">Limpiar</button><?php endif; ?>

    </form>
</main>