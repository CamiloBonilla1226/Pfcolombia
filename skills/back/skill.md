# PHP Backend Skill

## Cuándo usar esta skill
Antes de crear o modificar cualquier archivo PHP del sistema.

## Stack
- PHP 7.4
- Conexión a BD: clase `DBbase_Sql` — nunca PDO directo
- Sesiones: `$_SESSION["id"]`, `$_SESSION["empresa_pd"]`
- Routing: `index.php?doc=nombre_archivo` con `include_once`

## Convenciones obligatorias
- Validar sesión al inicio de cada archivo
- Respuestas AJAX: JSON puro, sin HTML mezclado del layout padre
- Un solo `echo json_encode()` por flujo de respuesta, nunca múltiples

## Errores frecuentes a evitar
- HTML del layout colándose en la respuesta AJAX (causa JSON inválido)
- Columnas VARCHAR demasiado cortas para datos base64 o JSON serializado
- Rutas AJAX apuntando directo al archivo en lugar de pasar por `index.php?doc=`

## Estructura mínima de un módulo
1. Validación de sesión
2. Conexión via DBbase_Sql
3. Lógica principal
4. Respuesta (JSON si es AJAX, HTML si es vista)