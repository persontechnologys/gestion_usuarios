# CLAUDE.md - Proyecto gestion_usuarios (backend / API REST)

@AGENTS.md

## 1. INFORMACIÓN GENERAL DEL PROYECTO

Nombre del proyecto: gestion_usuarios (backend)

Framework: Laravel 12

Lenguaje principal: PHP 8.2 o superior

Idioma obligatorio: Español

Base de datos: La configurada en el archivo .env del proyecto.

Autenticación de la API: Laravel Sanctum con tokens Bearer.

Pruebas: Pest 3.

Este proyecto es un sistema de gestión de usuarios desarrollado con Laravel 12.

### Arquitectura del sistema

El sistema está formado por dos proyectos Laravel independientes:

| Proyecto | Ruta | Responsabilidad | URL local |
|---|---|---|---|
| backend (este proyecto) | `/c/workspace/gestion_usuarios/backend` | API REST: lógica de negocio, base de datos y autenticación con Sanctum. | `http://localhost:8000` |
| frontend | `/c/workspace/gestion_usuarios/frontend` | Interfaz de usuario (Blade + Tailwind) que consume esta API desde su servidor. | `http://localhost:8001` |

- La interfaz de usuario oficial del sistema se desarrolla en el proyecto frontend, no en este.
- Este proyecto conserva las vistas del starter kit (Inertia + React en `resources/js`), pero no se amplían salvo que el usuario lo pida.
- El frontend consume la API de servidor a servidor. Por eso el límite de intentos del inicio de sesión se aplica por correo e IP, no solo por IP.

Las instrucciones de Laravel Boost (`AGENTS.md`) también se aplican. Si alguna contradice este archivo, prevalece este archivo.

Claude debe seguir estrictamente todas las instrucciones de este archivo al crear, modificar, refactorizar, documentar o eliminar código del proyecto.

Las instrucciones aquí definidas se aplican a todos los archivos nuevos y existentes que Claude modifique.

---

## 2. REGLA PRINCIPAL: TODO EL CÓDIGO DEBE ESTAR EN ESPAÑOL

REGLA OBLIGATORIA:

Todo el código desarrollado específicamente para este proyecto debe utilizar el idioma español.

Esta regla se aplica a:

- Nombres de variables.
- Nombres de métodos personalizados.
- Nombres de funciones personalizadas.
- Nombres de clases propias del proyecto.
- Nombres de controladores personalizados.
- Nombres de servicios.
- Nombres de repositorios.
- Nombres de eventos personalizados.
- Nombres de trabajos personalizados.
- Nombres de políticas personalizadas.
- Nombres de vistas personalizadas.
- Nombres de componentes personalizados.
- Nombres de rutas personalizadas.
- Nombres de tablas propias del sistema.
- Nombres de columnas propias del sistema.
- Nombres de relaciones personalizadas.
- Mensajes de validación personalizados.
- Mensajes de error personalizados.
- Mensajes de respuesta.
- Comentarios del código.
- Documentación técnica.
- Textos visibles para el usuario.

No utilizar nombres en inglés para elementos nuevos del proyecto cuando exista una alternativa adecuada en español.

### Ejemplo incorrecto

```php
$userName = 'Carlos';

$userEmail = 'carlos@ejemplo.com';

function getUserData()
{
    return [];
}
```

### Ejemplo correcto

```php
// Almacena el nombre del usuario.
$nombreUsuario = 'Carlos';

// Almacena el correo electrónico del usuario.
$correoUsuario = 'carlos@ejemplo.com';

// Define una función para obtener los datos del usuario.
function obtenerDatosUsuario()
{
    // Devuelve un arreglo con los datos del usuario.
    return [];
}
```

### Excepciones obligatorias

No traducir los elementos propios de PHP, Laravel, Composer, JavaScript, SQL ni otras tecnologías utilizadas.

Se deben conservar los nombres originales de:

- Palabras reservadas de los lenguajes de programación.
- Métodos internos de Laravel.
- Clases proporcionadas por Laravel.
- Interfaces y contratos de los frameworks.
- Métodos obligatorios de clases heredadas.
- Métodos mágicos de PHP.
- Funciones nativas.
- Directivas de Blade.
- Atributos HTML.
- Propiedades y métodos de bibliotecas externas.
- Variables especiales del framework.
- Nombres de archivos que Laravel necesita para su funcionamiento.
- Claves de configuración que Laravel o sus dependencias esperan encontrar.
- Nombres de tablas y columnas que ya existan y cuya modificación pueda romper compatibilidad.

Por ejemplo, no cambiar los nombres de los métodos:

- index()
- create()
- store()
- show()
- edit()
- update()
- destroy()
- up()
- down()
- boot()
- handle()
- rules()
- authorize()

Cuando alguno de estos métodos sea necesario para mantener la compatibilidad con Laravel, conservar su nombre original.

También deben conservarse los nombres de modelos, tablas o columnas existentes cuando renombrarlos pueda afectar a las migraciones, relaciones, autenticación, consultas o integraciones.

Priorizar siempre el correcto funcionamiento del proyecto.

---

## 3. REGLA OBLIGATORIA: COMENTAR CADA LÍNEA DE CÓDIGO EN ESPAÑOL

REGLA DE MÁXIMA PRIORIDAD:

Claude debe comentar cada línea de código que escriba o modifique, utilizando comentarios claros, descriptivos y exclusivamente en español.

Cada instrucción de código debe contar con un comentario que explique su propósito.

Los comentarios deben explicar qué hace la instrucción y, cuando corresponda, por qué es necesaria.

No generar bloques extensos de código sin comentarios.

No limitarse a comentar únicamente las funciones o los métodos.

También deben comentarse:

- Declaraciones de variables.
- Asignaciones.
- Condiciones.
- Bucles.
- Consultas a la base de datos.
- Operaciones matemáticas.
- Retornos de funciones.
- Validaciones.
- Llamadas a métodos.
- Creación de objetos.
- Definición de rutas.
- Relaciones entre modelos.
- Operaciones de lectura y escritura.
- Operaciones de autenticación.
- Operaciones de autorización.
- Respuestas HTTP.
- Procesamiento de formularios.

### Formato obligatorio para PHP

Colocar preferentemente un comentario en español inmediatamente antes de cada línea de código.

Ejemplo:

```php
<?php

// Declara el espacio de nombres correspondiente a los controladores.
namespace App\Http\Controllers;

// Importa la clase base utilizada por los controladores.
use Illuminate\Routing\Controller;

// Importa el modelo encargado de gestionar los usuarios.
use App\Models\Usuario;

// Declara el controlador encargado de gestionar los usuarios.
class UsuarioController extends Controller
{
    // Define el método encargado de mostrar el listado de usuarios.
    public function index()
    {
        // Obtiene todos los registros de usuarios almacenados en la base de datos.
        $usuarios = Usuario::all();

        // Devuelve la vista del listado y proporciona los usuarios obtenidos.
        return view('usuarios.indice', compact('usuarios'));
    }
}
```

### Reglas específicas para los comentarios

1. Todos los comentarios deben estar escritos en español.

2. No utilizar comentarios en inglés.

3. No omitir comentarios en las instrucciones nuevas o modificadas.

4. Cada comentario debe describir la operación que realiza la instrucción correspondiente.

5. No utilizar comentarios genéricos como "Ejecuta código" o "Realiza una operación".

6. Utilizar explicaciones concretas y relacionadas con la funcionalidad implementada.

7. No utilizar comentarios que describan un comportamiento diferente al que realmente ejecuta el código.

8. Mantener actualizados los comentarios cuando se modifique una instrucción.

9. Evitar comentarios duplicados que no aporten información adicional.

10. Mantener la indentación correcta de los comentarios y del código.

11. En instrucciones distribuidas en varias líneas, explicar cada parte cuando sea posible sin perjudicar la legibilidad.

12. Los comentarios deben conservar la sintaxis válida del lenguaje utilizado.

### Excepciones técnicas

No insertar comentarios cuando puedan provocar errores de sintaxis o alterar el comportamiento del programa.

Esto incluye, entre otros:

- Archivos JSON.
- Archivos .env.
- Estructuras que no admiten comentarios.
- Directivas cuya sintaxis no permite insertar comentarios.
- Cadenas de texto.
- Consultas SQL cuando un comentario pueda alterar su interpretación.
- Expresiones que deban permanecer completas.
- Declaraciones que requieren una estructura específica.
- Archivos de configuración con formatos estrictos.

En estos casos, documentar cada instrucción mediante comentarios en las posiciones permitidas por el lenguaje o mediante documentación externa cuando el formato no admita comentarios.

No introducir comentarios innecesarios en líneas vacías, llaves de cierre, etiquetas de cierre ni estructuras cuya modificación pueda afectar la sintaxis.

La prioridad es mantener el código correctamente documentado y completamente funcional.

---

## 4. NOMENCLATURA OBLIGATORIA EN ESPAÑOL

Utilizar nombres descriptivos que permitan comprender fácilmente la finalidad de cada elemento.

### Variables

Utilizar camelCase en español.

Ejemplos:

```php
// Almacena el nombre completo del usuario.
$nombreCompleto = 'Carlos Pérez';

// Almacena el correo electrónico del usuario.
$correoElectronico = 'carlos@ejemplo.com';

// Almacena el estado actual del usuario.
$estadoUsuario = 'activo';

// Almacena la fecha de creación del registro.
$fechaCreacion = now();
```

### Métodos

Utilizar camelCase en español para los métodos personalizados.

Ejemplos:

```php
// Define el método encargado de obtener los usuarios activos.
public function obtenerUsuariosActivos()
{
    // Obtiene los usuarios que tienen el estado activo.
    return Usuario::where('estado', 'activo')->get();
}
```

### Clases

Utilizar PascalCase con nombres descriptivos en español.

Ejemplos:

UsuarioController

GestionUsuarioService

RegistroUsuarioRequest

UsuarioCreado

ActualizarDatosUsuario

No traducir los nombres que formen parte de las convenciones obligatorias de Laravel o de una biblioteca externa.

### Tablas y columnas

Para estructuras nuevas, utilizar nombres descriptivos en español y snake_case.

Ejemplos:

usuarios

roles

permisos

usuario_rol

fecha_nacimiento

correo_electronico

fecha_ultimo_acceso

Antes de crear una tabla o columna, revisar las convenciones existentes y las dependencias del proyecto.

No renombrar automáticamente las tablas o columnas que Laravel necesita para sus mecanismos internos.

---

## 5. REGLAS ESPECÍFICAS PARA LARAVEL 12

Claude debe desarrollar siguiendo las convenciones, arquitectura y buenas prácticas de Laravel 12.

Antes de implementar una funcionalidad:

1. Revisar la estructura actual del proyecto.
2. Identificar los modelos existentes.
3. Revisar las migraciones relacionadas.
4. Verificar las rutas existentes.
5. Revisar los controladores relacionados.
6. Identificar las relaciones entre entidades.
7. Comprobar los servicios y componentes reutilizables.
8. Evitar duplicar funcionalidades existentes.

No asumir que el proyecto utiliza una estructura diferente a la que realmente tiene.

### Controladores

Los controladores deben encargarse de recibir solicitudes HTTP, coordinar las operaciones necesarias y devolver respuestas.

No incorporar lógica de negocio compleja directamente en los controladores.

Cuando corresponda, utilizar servicios para encapsular la lógica de negocio.

Comentar en español cada instrucción implementada.

### Modelos

Utilizar Eloquent ORM para interactuar con la base de datos.

Definir correctamente las relaciones entre modelos.

Respetar las convenciones y la configuración de las claves primarias y foráneas.

Documentar cada relación utilizando comentarios en español.

No modificar los mecanismos internos de autenticación de Laravel sin comprobar sus dependencias.

### Migraciones

Utilizar migraciones para crear o modificar la estructura de la base de datos.

Definir correctamente:

- Claves primarias.
- Claves foráneas.
- Índices.
- Restricciones.
- Tipos de datos.
- Valores predeterminados.
- Relaciones entre tablas.

Comentar cada operación de las migraciones en español.

No modificar migraciones que ya se hayan ejecutado en entornos compartidos o de producción cuando corresponda crear una nueva migración.

### Validaciones

Validar todos los datos recibidos desde formularios, solicitudes HTTP y API.

Utilizar Form Request cuando la complejidad de las validaciones lo justifique.

Escribir en español los mensajes de validación personalizados.

No eliminar validaciones existentes sin una razón funcional documentada.

### Rutas

Mantener las rutas organizadas y utilizar nombres descriptivos en español para las rutas personalizadas.

Conservar los nombres y las estructuras que deban mantenerse por compatibilidad.

Aplicar correctamente los middleware de autenticación y autorización.

No exponer rutas administrativas sin los controles de acceso correspondientes.

### Arquitectura por capas de la API

Toda funcionalidad de la API debe respetar el siguiente flujo:

```
Ruta (routes/api.php) → Controller → Form Request → Service → Repository → Modelo
                              ↓
                        API Resource → Respuesta JSON
```

| Capa | Ubicación | Responsabilidad |
|---|---|---|
| Controllers | `app/Http/Controllers/Api/` | Reciben la solicitud, llaman al servicio y devuelven la respuesta. Sin lógica de negocio. |
| Form Requests | `app/Http/Requests/Api/` | Validan los datos con mensajes y atributos en español. |
| Services | `app/Services/` | Contienen la lógica de negocio. No acceden directamente a Eloquent. |
| Repositories | `app/Repositories/` | Único punto de acceso a Eloquent. Traducen los campos en español de la API a las columnas internas. |
| API Resources | `app/Http/Resources/` | Definen la forma del JSON de salida y nunca exponen datos sensibles. |

- Reutilizar las clases existentes antes de crear nuevas (por ejemplo, `GuardarUsuarioRequest` hereda de `RegistroUsuarioRequest`).
- Las rutas protegidas usan el middleware `auth:sanctum`.

### Contrato de la API

La API recibe y devuelve los mismos nombres de campo en español: `nombre`, `correo_electronico`, `contrasena`, `contrasena_confirmation`, `cedula`, `telefono` y `direccion`.

- Las columnas internas de Laravel (`name`, `email`, `password`) NO se renombran. La traducción se hace en `UsuarioRepository::mapearAColumnas()`.
- Los errores 422 usan las claves en español de los campos enviados.
- Los tokens caducan a los `SANCTUM_EXPIRATION` minutos (120 por defecto, igual que `SESSION_LIFETIME` del frontend). Un token caducado recibe 401.

| Método | Ruta | Protegida | Nombre de ruta |
|---|---|---|---|
| POST | /api/registro | No | api.registro |
| POST | /api/iniciar-sesion | No (5 intentos/min por correo e IP) | api.iniciar-sesion |
| POST | /api/cerrar-sesion | Sí | api.cerrar-sesion |
| GET | /api/mi-perfil | Sí | api.mi-perfil |
| GET, POST | /api/usuarios | Sí | api.usuarios.index / store |
| GET, PUT/PATCH, DELETE | /api/usuarios/{usuario} | Sí | api.usuarios.show / update / destroy |

IMPORTANTE: el proyecto frontend depende de este contrato. Cualquier cambio en rutas, campos, códigos de estado o forma de las respuestas es un cambio incompatible. Antes de hacerlo, Claude debe advertirlo al usuario e indicar qué debe actualizarse en el frontend.

---

## 6. VISTAS Y CÓDIGO FRONTEND

La interfaz oficial del sistema está en el proyecto frontend (ver sección 1). Las siguientes reglas se aplican a cualquier vista o código frontend que se cree o modifique en este proyecto (Blade, React/TSX, JavaScript o CSS).

Todas las interfaces del sistema deben estar redactadas en español.

Esto incluye:

- Títulos.
- Encabezados.
- Etiquetas de formularios.
- Botones.
- Mensajes informativos.
- Mensajes de confirmación.
- Mensajes de error.
- Notificaciones.
- Textos de ayuda.
- Descripciones.
- Mensajes de validación.

### Comentarios en Blade

Utilizar comentarios Blade para documentar las secciones de las vistas.

Ejemplo:

```blade
{{-- Define el contenedor principal del formulario de registro. --}}
<div class="contenedor-registro">

    {{-- Muestra el título correspondiente al formulario. --}}
    <h1>Registrar usuario</h1>

    {{-- Define el formulario encargado de enviar los datos del usuario. --}}
    <form action="{{ route('usuarios.guardar') }}" method="POST">

        {{-- Incorpora el token de protección contra solicitudes falsificadas. --}}
        @csrf

        {{-- Muestra la etiqueta correspondiente al nombre del usuario. --}}
        <label for="nombre">Nombre del usuario</label>

        {{-- Define el campo encargado de capturar el nombre del usuario. --}}
        <input type="text" name="nombre" id="nombre">

        {{-- Define el botón encargado de enviar el formulario. --}}
        <button type="submit">Guardar usuario</button>

    </form>

</div>
```

### JavaScript

Utilizar nombres de variables y funciones personalizados en español.

Comentar cada instrucción con comentarios JavaScript válidos.

Ejemplo:

```javascript
// Obtiene el elemento que contiene el formulario de registro.
const formularioRegistro = document.getElementById('formularioRegistro');

// Registra el evento que se ejecuta cuando se envía el formulario.
formularioRegistro.addEventListener('submit', function (evento) {

    // Evita que el formulario se envíe mediante el comportamiento predeterminado.
    evento.preventDefault();

    // Muestra un mensaje informativo en la consola.
    console.log('Se ha iniciado el proceso de registro del usuario.');

});
```

### CSS

Utilizar nombres de clases personalizados descriptivos en español cuando no formen parte de una biblioteca o framework CSS.

Comentar las reglas CSS utilizando comentarios válidos.

No modificar los nombres de las clases proporcionadas por Bootstrap, Tailwind CSS u otras bibliotecas externas.

---

## 7. GESTIÓN DE USUARIOS

El proyecto gestion_usuarios debe mantener una arquitectura clara y organizada para las funcionalidades relacionadas con la administración de usuarios.

Cuando corresponda, contemplar:

- Registro de usuarios.
- Autenticación.
- Cierre de sesión.
- Consulta de usuarios.
- Actualización de información.
- Eliminación de usuarios.
- Asignación de roles.
- Gestión de permisos.
- Validación de formularios.
- Protección de rutas.
- Gestión segura de contraseñas.

Estas funcionalidades son referencias del dominio del proyecto.

No implementar módulos nuevos que no hayan sido solicitados.

### Seguridad

Utilizar los mecanismos de seguridad proporcionados por Laravel.

No almacenar contraseñas en texto plano.

Utilizar los mecanismos oficiales de hashing de contraseñas.

Aplicar autenticación y autorización a las operaciones protegidas.

Evitar la exposición de información confidencial.

No registrar contraseñas, tokens ni datos sensibles en archivos de log.

No desactivar la protección CSRF para solucionar problemas de formularios.

No utilizar consultas SQL inseguras que permitan inyección SQL.

Validar la entrada del usuario y escapar correctamente los datos mostrados en las vistas.

---

## 8. ESTRUCTURA Y ORGANIZACIÓN DEL CÓDIGO

Mantener una estructura clara, modular y fácil de mantener.

Respetar la organización actual del proyecto.

No crear carpetas adicionales sin necesidad.

Evitar generar múltiples archivos para resolver una funcionalidad sencilla.

Aplicar los principios de responsabilidad única y reutilización cuando aporten claridad al código.

No duplicar lógica de negocio.

Utilizar nombres descriptivos en español.

Documentar las responsabilidades de las clases, métodos y funciones mediante comentarios en español.

Mantener la compatibilidad con las versiones de PHP y Laravel definidas en composer.json.

No introducir dependencias adicionales sin comprobar que son necesarias y compatibles.

---

## 9. MODIFICACIÓN DE CÓDIGO EXISTENTE

Cuando se solicite modificar un archivo:

1. Leer su contenido antes de realizar cambios.

2. Identificar las funcionalidades que dependen del código que se modificará.

3. Conservar el comportamiento existente que no forme parte de la modificación solicitada.

4. Utilizar español en los nuevos identificadores personalizados.

5. Incorporar comentarios en español para cada instrucción nueva o modificada.

6. Actualizar los comentarios que hayan quedado desactualizados.

7. Mantener las convenciones del proyecto.

8. Evitar refactorizaciones que no estén relacionadas con la solicitud.

9. No eliminar código funcional sin justificación.

10. Verificar que la modificación no introduzca errores de sintaxis.

11. Revisar que el cambio no afecte negativamente la seguridad del sistema.

12. Ejecutar las pruebas relacionadas cuando estén disponibles.

No traducir automáticamente identificadores existentes si el cambio puede romper referencias en otras partes del sistema.

Si se necesita cambiar un identificador, actualizar sus referencias y comprobar que el proyecto mantiene su funcionamiento.

---

## 10. GENERACIÓN DE NUEVO CÓDIGO

Antes de generar cualquier archivo nuevo, Claude debe comprobar que:

- El archivo es necesario.
- No existe ya una implementación equivalente.
- Su ubicación corresponde a la arquitectura de Laravel 12.
- Los identificadores personalizados están escritos en español.
- Los comentarios están escritos en español.
- Cada instrucción tiene su explicación correspondiente cuando la sintaxis lo permite.
- El código respeta las convenciones del framework.
- No contiene errores de sintaxis conocidos.
- No introduce vulnerabilidades evidentes.

No generar código incompleto mediante marcadores como:

TODO

Implementar después

Agregar lógica aquí

Cuando se solicite una funcionalidad concreta, proporcionar su implementación completa dentro del alcance solicitado.

---

## 11. PRUEBAS Y VERIFICACIÓN

Después de implementar o modificar una funcionalidad, verificar su funcionamiento mediante los mecanismos disponibles en el proyecto.

Cuando corresponda, ejecutar:

```bash
php artisan test --compact
```

Para ejecutar solo las pruebas afectadas:

```bash
php artisan test --compact --filter=nombreDeLaPrueba
```

Formatear los archivos PHP modificados con Laravel Pint:

```bash
vendor/bin/pint --dirty --format agent
```

Después de ejecutar Pint, revisar que los comentarios en español sigan encima de la instrucción que describen, porque Pint reordena las importaciones `use`.

Las pruebas de la API se encuentran en `tests/Feature/Api/`. Toda funcionalidad nueva o modificada de la API debe tener su prueba Feature.

Comprobar la sintaxis de los archivos PHP modificados mediante:

```bash
php -l ruta/del/archivo.php
```

Cuando corresponda, revisar las rutas registradas:

```bash
php artisan route:list
```

No ejecutar comandos destructivos sobre la base de datos sin autorización explícita.

No ejecutar automáticamente comandos como:

php artisan migrate:fresh

php artisan migrate:reset

php artisan db:wipe

No afirmar que las pruebas se han ejecutado correctamente si no se han ejecutado realmente.

Si no es posible ejecutar una prueba, indicar claramente que no se ha verificado.

---

## 12. FORMATO DE LAS RESPUESTAS DE CLAUDE

Claude debe comunicarse siempre en español durante el desarrollo de este proyecto.

Cuando explique una modificación, utilizar un lenguaje técnico claro y comprensible.

Al entregar código:

1. Mostrar el nombre o la ruta del archivo correspondiente.

2. Utilizar código compatible con Laravel 12.

3. Mantener los identificadores personalizados en español.

4. Comentar cada instrucción en español siempre que el lenguaje lo permita.

5. Conservar la indentación y el formato correctos.

6. No introducir comentarios en inglés.

7. No cambiar nombres internos de Laravel.

8. No incluir código innecesario.

Cuando se modifiquen varios archivos, explicar brevemente la responsabilidad de cada uno.

No proporcionar explicaciones excesivamente extensas cuando el usuario solicite directamente código.

---

## 13. VERIFICACIÓN FINAL OBLIGATORIA

Antes de finalizar cualquier tarea de programación, Claude debe revisar internamente las siguientes condiciones:

¿Todos los identificadores nuevos y personalizados están en español?

¿Todos los comentarios están escritos en español?

¿Cada instrucción nueva o modificada tiene un comentario explicativo cuando la sintaxis lo permite?

¿Los comentarios describen correctamente lo que hace el código?

¿Se han respetado las convenciones de Laravel 12?

¿Se han conservado los nombres internos del framework y de sus dependencias?

¿El código mantiene una sintaxis válida?

¿Se han respetado la arquitectura y las funcionalidades existentes?

¿Se ha respetado el flujo Controller → Form Request → Service → Repository → API Resource?

¿Se ha mantenido el contrato de la API o se ha advertido al usuario del cambio incompatible?

¿Se han aplicado las medidas de seguridad correspondientes?

¿Se han ejecutado las pruebas disponibles cuando ha sido posible?

Si alguna condición no se cumple, corregir el código antes de entregar la solución.

---

## 14. INSTRUCCIÓN PERMANENTE DE MÁXIMA PRIORIDAD

A partir de este momento, para todo el desarrollo del proyecto gestion_usuarios:

TODO EL CÓDIGO PERSONALIZADO DEBE ESTAR EN ESPAÑOL.

CADA INSTRUCCIÓN DE CÓDIGO NUEVA O MODIFICADA DEBE TENER UN COMENTARIO EXPLICATIVO EN ESPAÑOL, SIEMPRE QUE LA SINTAXIS DEL LENGUAJE LO PERMITA.

TODOS LOS MENSAJES, VALIDACIONES, EXPLICACIONES Y TEXTOS DE INTERFAZ DEBEN ESTAR EN ESPAÑOL.

NO SE DEBEN TRADUCIR LOS ELEMENTOS INTERNOS DE PHP, LARAVEL 12 NI DE LAS BIBLIOTECAS UTILIZADAS.

LOS COMENTARIOS NUNCA DEBEN ALTERAR EL FUNCIONAMIENTO, LA SINTAXIS NI LA SEGURIDAD DEL CÓDIGO.

ESTAS REGLAS DEBEN APLICARSE AUTOMÁTICAMENTE EN CADA TAREA DE DESARROLLO, SIN NECESIDAD DE QUE EL USUARIO LAS REPITA.