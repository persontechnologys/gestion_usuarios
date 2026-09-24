# CLAUDE.md - Proyecto gestion_usuarios (frontend)

@AGENTS.md

## 1. INFORMACIÓN GENERAL DEL PROYECTO

Nombre del proyecto: gestion_usuarios (frontend)

Framework: Laravel 12 con vistas Blade, Tailwind CSS 4 y Vite.

Lenguaje principal: PHP 8.2 o superior

Idioma obligatorio: Español

Pruebas: Pest 3.

Este proyecto es la interfaz de usuario del sistema de gestión de usuarios. NO tiene lógica de negocio propia ni guarda usuarios en su base de datos: todo lo obtiene de la API REST del proyecto backend.

Claude debe seguir estrictamente todas las instrucciones de este archivo al crear, modificar, refactorizar, documentar o eliminar código del proyecto.

Las instrucciones de Laravel Boost (`AGENTS.md`) también se aplican. Si alguna contradice este archivo, prevalece este archivo.

### Arquitectura del sistema

| Proyecto | Ruta | Responsabilidad | URL local |
|---|---|---|---|
| backend | `/c/workspace/gestion_usuarios/backend` | API REST: lógica de negocio, base de datos y autenticación con Sanctum. | `http://localhost:8000` |
| frontend (este proyecto) | `/c/workspace/gestion_usuarios/frontend` | Interfaz Blade que consume la API desde el servidor. | `http://localhost:8001` |

Flujo de una solicitud:

```
Navegador → Ruta web → Controller → Form Request → Service → ClienteApi (Http) → API backend
                          ↓
                    Vista Blade ← datos de la API
```

- El navegador NUNCA llama directamente a la API: todas las llamadas se hacen desde el servidor de este proyecto con la fachada `Http` de Laravel. Por eso no se necesita CORS.
- El token de Sanctum se guarda en la sesión del servidor y nunca llega al navegador.
- Este proyecto se ejecuta en el puerto 8001 para no chocar con el backend: `php artisan serve --port=8001`.
- No modificar el proyecto backend desde aquí. Si hace falta un cambio en la API, indicárselo al usuario.

---

## 2. REGLA PRINCIPAL: TODO EL CÓDIGO DEBE ESTAR EN ESPAÑOL

Todo el código desarrollado específicamente para este proyecto debe utilizar el idioma español.

Esta regla se aplica a:

- Nombres de variables, métodos, funciones y clases propias.
- Nombres de controladores, servicios, middleware, Form Requests y excepciones propias.
- Nombres de vistas y componentes Blade (`usuarios/indice.blade.php`, `<x-campo-formulario>`).
- Nombres y segmentos de rutas personalizadas (`/iniciar-sesion`, `usuarios.indice`).
- Claves de sesión y de configuración propias (`token_api`, `services.backend_api`).
- Mensajes de validación, de error y de respuesta.
- Comentarios del código y documentación técnica.
- Textos visibles para el usuario.

### Ejemplo correcto

```php
// Almacena el correo electrónico escrito por el usuario.
$correoElectronico = $solicitud->validated('correo_electronico');

// Define un método para obtener el listado de usuarios desde la API.
public function obtenerUsuarios(int $pagina): array
{
    // Solicita a la API la página indicada del listado de usuarios.
    return $this->clienteApi->get('/usuarios', ['page' => $pagina]);
}
```

### Excepciones obligatorias

No traducir los elementos propios de PHP, Laravel, Blade, Tailwind CSS, JavaScript ni de otras bibliotecas.

Se deben conservar los nombres originales de:

- Palabras reservadas, funciones nativas y métodos mágicos de PHP.
- Clases, fachadas, métodos internos y contratos de Laravel (`Http`, `Request`, `session()`, `redirect()`).
- Métodos obligatorios de clases heredadas (`index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`, `handle()`, `rules()`, `authorize()`).
- Directivas de Blade (`@csrf`, `@error`, `@method`) y atributos HTML.
- Clases de Tailwind CSS.
- Archivos y claves de configuración que Laravel espera encontrar.
- Claves JSON de la API. Se usan tal como las define el backend (ya están en español).

---

## 3. REGLA OBLIGATORIA: COMENTAR CADA LÍNEA DE CÓDIGO EN ESPAÑOL

REGLA DE MÁXIMA PRIORIDAD:

Claude debe comentar cada línea de código que escriba o modifique, con comentarios claros, descriptivos y exclusivamente en español.

Deben comentarse, entre otros: declaraciones de variables, asignaciones, condiciones, bucles, llamadas a la API, lectura y escritura de la sesión, validaciones, manejo de errores, definición de rutas, redirecciones y respuestas.

### PHP

Colocar un comentario en español inmediatamente antes de cada instrucción:

```php
// Envía las credenciales a la API para obtener el token de acceso.
$respuesta = $this->clienteApi->post('/iniciar-sesion', $credenciales);

// Guarda el token de acceso en la sesión del servidor.
session()->put(self::CLAVE_TOKEN, $respuesta['token']);

// Regenera el identificador de sesión para prevenir la fijación de sesión.
session()->regenerate();
```

Documentar cada clase y cada método con un bloque PHPDoc en español.

### Blade

Comentar cada sección y cada elemento del formulario con comentarios Blade:

```blade
{{-- Define el formulario encargado de enviar las credenciales del usuario. --}}
<form action="{{ route('sesion.iniciar') }}" method="POST">

    {{-- Incorpora el token de protección contra solicitudes falsificadas. --}}
    @csrf

    {{-- Muestra la etiqueta del campo de correo electrónico. --}}
    <label for="correo_electronico">Correo electrónico</label>

    {{-- Define el campo que captura el correo y conserva el valor anterior. --}}
    <input type="email" name="correo_electronico" id="correo_electronico" value="{{ old('correo_electronico') }}">

    {{-- Muestra el error de validación del correo electrónico. --}}
    @error('correo_electronico') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

</form>
```

### JavaScript y CSS

- JavaScript: comentar cada instrucción con `//`.
- CSS: comentar cada regla o grupo de reglas con `/* ... */`.

### Reglas específicas para los comentarios

1. Todos los comentarios en español; nunca en inglés.
2. Cada comentario describe la operación concreta que realiza la instrucción. No usar comentarios genéricos como "Ejecuta código".
3. No usar comentarios que describan un comportamiento distinto al real.
4. Mantener los comentarios actualizados al modificar el código.
5. No comentar líneas vacías ni llaves de cierre, ni poner comentarios donde rompan la sintaxis (JSON, `.env`, dentro de cadenas).
6. Después de ejecutar Pint, revisar que cada comentario siga encima de la instrucción que describe, porque Pint reordena las importaciones `use`.

---

## 4. NOMENCLATURA OBLIGATORIA EN ESPAÑOL

- Variables y métodos: camelCase en español (`listaUsuarios`, `guardarToken()`).
- Clases: PascalCase en español con el sufijo de Laravel cuando corresponda (`UsuarioController`, `InicioSesionRequest`, `UsuarioService`, `VerificarSesionApi`).
- Constantes: MAYÚSCULAS_CON_GUION_BAJO (`CLAVE_TOKEN`).
- Vistas y componentes Blade: kebab-case en español (`usuarios/formulario.blade.php`, `components/alerta-mensaje.blade.php`).
- Nombres de rutas: en español con puntos (`usuarios.indice`, `sesion.cerrar`).
- Los `name` e `id` de los campos de formulario usan exactamente el nombre del campo de la API (`correo_electronico`, `contrasena`) para que los errores 422 se muestren sin traducciones.

---

## 5. ARQUITECTURA POR CAPAS

| Capa | Ubicación | Responsabilidad |
|---|---|---|
| Rutas | `routes/web.php` | Definen las URL en español y aplican los middleware de sesión. |
| Middleware | `app/Http/Middleware/` | Comprueban si hay token en la sesión y redirigen según corresponda. |
| Controllers | `app/Http/Controllers/` | Reciben la solicitud, llaman al servicio y devuelven una vista o una redirección. Sin lógica de negocio ni llamadas HTTP. |
| Form Requests | `app/Http/Requests/` | Validan los datos antes de llamar a la API, con mensajes en español. |
| Services | `app/Services/` | `ClienteApi` es la ÚNICA clase que usa la fachada `Http`. Los servicios por recurso (`AutenticacionService`, `UsuarioService`) la utilizan. |
| Vistas | `resources/views/` | Layouts, componentes Blade y páginas por módulo. |

Reglas:

- `ClienteApi` centraliza la URL base, el tiempo de espera, las cabeceras `Accept: application/json` y `Authorization: Bearer <token>`, y la conversión de los errores de la API.
- Ningún controlador, vista ni middleware usa `Http::` directamente.
- La URL de la API se define en `config/services.php` bajo la clave `backend_api` y se lee de la variable `BACKEND_API_URL` del `.env`. Nunca usar `env()` fuera de los archivos de configuración.
- No crear modelos Eloquent, migraciones ni tablas para los usuarios: los datos viven en el backend. La base de datos local solo se usa para las sesiones y la caché de Laravel.
- Base de datos local: SQLite (`database/database.sqlite`). No hace falta MySQL porque aquí solo se guardan sesiones, caché y trabajos en cola. No conectar este proyecto a la base de datos del backend.
- No crear carpetas adicionales sin necesidad ni duplicar lógica.

### Manejo de las respuestas de la API

| Código | Acción en el frontend |
|---|---|
| 200 / 201 | Mostrar la vista o redirigir con un mensaje de éxito en la sesión (`with('exito', ...)`). |
| 401 | Eliminar el token de la sesión y redirigir a `/iniciar-sesion` con el mensaje "Su sesión ha expirado.". |
| 404 | `abort(404)` con una vista de error en español. |
| 422 | Lanzar `ValidationException::withMessages($errores)` para que Laravel vuelva al formulario con los errores bajo cada campo y los valores anteriores. |
| 429 | Volver al formulario con el mensaje recibido de la API. |
| Sin conexión o 5xx | Volver con el mensaje "No se pudo conectar con el servidor. Intente nuevamente.". |

---

## 6. CONTRATO DE LA API DEL BACKEND

La fuente de verdad es el `CLAUDE.md` del backend. Este resumen sirve como referencia rápida.

Todas las peticiones envían `Accept: application/json`. Las rutas protegidas envían además `Authorization: Bearer <token>`.

| Método | Ruta | Auth | Cuerpo | Respuesta |
|---|---|---|---|---|
| POST | /registro | No | nombre, correo_electronico, contrasena, contrasena_confirmation, cedula, telefono?, direccion? | 201 `{ mensaje, usuario, token, tipo_token }` |
| POST | /iniciar-sesion | No | correo_electronico, contrasena | 200 `{ mensaje, usuario, token, tipo_token }` |
| POST | /cerrar-sesion | Sí | — | 200 `{ mensaje }` |
| GET | /mi-perfil | Sí | — | 200 `{ data: Usuario }` |
| GET | /usuarios?page=N&por_pagina=N | Sí | — | 200 `{ data: Usuario[], links, meta }` |
| POST | /usuarios | Sí | igual que /registro | 201 `{ data: Usuario }` |
| GET | /usuarios/{id} | Sí | — | 200 `{ data: Usuario }` |
| PUT | /usuarios/{id} | Sí | cualquier campo de /registro (todos opcionales; contrasena vacía = no cambiarla) | 200 `{ data: Usuario }` |
| DELETE | /usuarios/{id} | Sí | — | 200 `{ mensaje }` |

- `Usuario`: `id, nombre, correo_electronico, cedula, telefono, direccion, correo_verificado_en, fecha_creacion, fecha_actualizacion`.
- `meta` de la paginación: `current_page, from, last_page, per_page, to, total`.
- Errores 422: `{ message, errors: { campo: [mensajes] } }`, con los mensajes ya en español.
- Reglas: `cedula` tiene 10 dígitos y es única, `correo_electronico` es único y en minúsculas, `contrasena` tiene mínimo 8 caracteres y requiere confirmación.
- Inicio de sesión: máximo 5 intentos por minuto por correo e IP (429 al superarlo).
- Los tokens caducan a los 120 minutos (`SANCTUM_EXPIRATION` del backend). `SESSION_LIFETIME` de este proyecto debe tener el mismo valor. Un token caducado recibe 401 y se trata como sesión expirada.

---

## 7. SESIÓN Y SEGURIDAD

- El token se guarda SOLO en la sesión del servidor, bajo una constante (`token_api`). Nunca en cookies propias, `localStorage`, campos ocultos, vistas, JavaScript ni logs.
- Regenerar la sesión después de iniciar sesión (`session()->regenerate()`) e invalidarla al cerrarla (`session()->invalidate()` y `session()->regenerateToken()`).
- Al cerrar sesión, llamar a `/cerrar-sesion` de la API y eliminar el token local aunque la API falle.
- Todos los formularios incluyen `@csrf`. No desactivar la protección CSRF.
- Mostrar los datos de la API siempre con `{{ }}`. Prohibido `{!! !!}` con datos de la API o del usuario.
- Nunca registrar en logs contraseñas, tokens ni respuestas completas de la API.
- Definir un tiempo de espera en todas las llamadas HTTP.

---

## 8. VISTAS, TAILWIND Y JAVASCRIPT

Todas las interfaces deben estar redactadas en español: títulos, encabezados, etiquetas, botones, mensajes informativos, de confirmación y de error, textos de ayuda y validaciones.

- Usar un layout principal (`resources/views/layouts/`) y componentes Blade reutilizables para campos, botones y alertas.
- Estilos con clases de Tailwind CSS 4. Crear CSS propio solo cuando Tailwind no sea suficiente, con clases en español.
- Formularios accesibles: cada campo con su `<label for>`, uso de `old()` para conservar los valores y `@error` para mostrar los errores.
- JavaScript mínimo, solo para mejorar la experiencia (confirmar eliminaciones, deshabilitar el botón de envío). La aplicación debe funcionar sin JavaScript.
- No usar `axios` ni `fetch` para llamar a la API desde el navegador.
- Si un cambio en las vistas no se refleja en el navegador, pedir al usuario que ejecute `npm run dev` o `npm run build`.

---

## 9. MODIFICACIÓN DE CÓDIGO EXISTENTE

1. Leer el contenido del archivo antes de modificarlo.
2. Identificar las funcionalidades que dependen del código.
3. Conservar el comportamiento que no forme parte de la solicitud.
4. Usar español en los identificadores nuevos y comentar cada instrucción nueva o modificada.
5. Actualizar los comentarios que queden desactualizados.
6. No hacer refactorizaciones no relacionadas con la solicitud.
7. No eliminar código funcional sin justificación.
8. No instalar dependencias de Composer ni de npm sin aprobación del usuario.

---

## 10. GENERACIÓN DE NUEVO CÓDIGO

- Crear los archivos con `php artisan make:` y la opción `--no-interaction`.
- Comprobar que el archivo es necesario y que no existe una implementación equivalente.
- No generar código incompleto con marcadores como TODO, "implementar después" o "agregar lógica aquí".
- Proporcionar la implementación completa dentro del alcance solicitado.

---

## 11. PRUEBAS Y VERIFICACIÓN

- Pruebas Feature con Pest en `tests/Feature/`.
- Las pruebas NUNCA llaman al backend real: simular las respuestas de la API con `Http::fake()` y comprobar las peticiones enviadas con `Http::assertSent()`. Así las pruebas no dependen de que el backend esté en ejecución.
- Probar el camino correcto y los errores importantes (401, 422 y sin conexión).

Comandos:

```bash
php artisan test --compact
vendor/bin/pint --dirty --format agent
php -l ruta/del/archivo.php
php artisan route:list --except-vendor
npm run build
```

- No ejecutar comandos destructivos sobre la base de datos (`migrate:fresh`, `migrate:reset`, `db:wipe`) sin autorización explícita.
- No afirmar que las pruebas pasaron si no se ejecutaron. Si algo no se pudo verificar, indicarlo claramente.

---

## 12. FORMATO DE LAS RESPUESTAS DE CLAUDE

- Comunicarse siempre en español, con un lenguaje técnico claro.
- Indicar la ruta de cada archivo creado o modificado y explicar brevemente su responsabilidad.
- No dar explicaciones excesivamente extensas cuando el usuario pida código directamente.

---

## 13. VERIFICACIÓN FINAL OBLIGATORIA

Antes de finalizar cualquier tarea, Claude debe comprobar:

- ¿Todos los identificadores nuevos y personalizados están en español?
- ¿Todos los comentarios están en español y cada instrucción nueva o modificada tiene el suyo?
- ¿Se conservaron los nombres internos de Laravel, Blade y Tailwind?
- ¿Solo `ClienteApi` usa la fachada `Http`?
- ¿El token solo existe en la sesión del servidor y nunca llega al navegador?
- ¿Se manejan los códigos 401, 404, 422 y 429 según la sección 5?
- ¿Las pruebas usan `Http::fake()` y pasan?
- ¿Se ejecutó Pint?

Si alguna condición no se cumple, corregir el código antes de entregar la solución.

---

## 14. INSTRUCCIÓN PERMANENTE DE MÁXIMA PRIORIDAD

TODO EL CÓDIGO PERSONALIZADO DEBE ESTAR EN ESPAÑOL.

CADA INSTRUCCIÓN DE CÓDIGO NUEVA O MODIFICADA DEBE TENER UN COMENTARIO EXPLICATIVO EN ESPAÑOL, SIEMPRE QUE LA SINTAXIS DEL LENGUAJE LO PERMITA.

TODOS LOS MENSAJES, VALIDACIONES, EXPLICACIONES Y TEXTOS DE INTERFAZ DEBEN ESTAR EN ESPAÑOL.

NO SE DEBEN TRADUCIR LOS ELEMENTOS INTERNOS DE PHP, LARAVEL 12, BLADE, TAILWIND NI DE LAS BIBLIOTECAS UTILIZADAS.

EL NAVEGADOR NUNCA LLAMA DIRECTAMENTE A LA API NI CONOCE EL TOKEN.

ESTAS REGLAS DEBEN APLICARSE AUTOMÁTICAMENTE EN CADA TAREA DE DESARROLLO, SIN NECESIDAD DE QUE EL USUARIO LAS REPITA.
