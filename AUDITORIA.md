# Auditoría técnica — gestion_usuarios

Fecha: 2026-09-23 · Alcance: `backend/` y `frontend/` (solo lectura; no se modificó ningún archivo del proyecto).

Arquitectura: el **backend** es una API REST con Laravel y tokens de Sanctum. El **frontend** es otra aplicación Laravel con vistas Blade que llama a esa API **desde el servidor**; el navegador nunca llama a la API directamente.

---

## 1. Backend (`backend/`)

**PHP / Laravel.** `php: ^8.2`, `laravel/framework: ^12.0`. Otros paquetes: `laravel/sanctum ^4.0`, `inertiajs/inertia-laravel ^2.0`, `tightenco/ziggy ^2.4`, `laravel/tinker`. Pruebas con Pest 3. Se generó desde el *React starter kit* de Laravel (`name: laravel/react-starter-kit`).
Extensiones PHP requeridas (`composer check-platform-reqs --no-dev`): `ctype`, `dom`, `fileinfo`, `filter`, `hash`, `iconv`, `json`, `libxml`, `mbstring`, `openssl`, `pcre`, `session`, `tokenizer`. Además, `pdo_mysql` para la BD y `pdo_sqlite` para las pruebas.

**Vite / npm.** Sí. Vite 6 + React 19 + TypeScript + Tailwind 4 (entrada `resources/js/app.tsx`, con SSR `ssr.jsx`). Solo lo usan las pantallas web Inertia heredadas del starter kit (login, dashboard, ajustes). **La API no lo necesita.** `public/build` ya existe compilado.

**.env (sin valores sensibles).**
| Variable | Valor |
|---|---|
| `DB_CONNECTION` / `DB_HOST` / `DB_PORT` | `mysql` / `127.0.0.1` / `3306` |
| `DB_DATABASE` / `DB_USERNAME` | `gestion_usuarios` / `gestion_usuarios` (`DB_PASSWORD` definida, omitida) |
| `SESSION_DRIVER` / `CACHE_STORE` / `QUEUE_CONNECTION` | `database` / `database` / `database` |
| Otras | `APP_URL=http://localhost:8000`, `FRONTEND_URLS` (orígenes CORS), `SANCTUM_EXPIRATION=120` |

⚠ `.env.example` sigue con `DB_CONNECTION=sqlite` y las líneas MySQL comentadas; no coincide con el `.env` real.

**Rutas `routes/api.php`** (prefijo `/api`):
- Públicas: `POST /registro`, `POST /iniciar-sesion` (throttle `inicio-sesion`: 5 por minuto por correo e IP).
- Con `auth:sanctum`: `POST /cerrar-sesion`, `GET /mi-perfil`, `apiResource usuarios` (index, store, show, update, destroy) y `GET /user` (el ejemplo por defecto, que sobra).

**Sanctum.** Usa tokens *Bearer* (personal access tokens), no cookies SPA: `User` usa `HasApiTokens`, la expiración viene de `SANCTUM_EXPIRATION` (120 min) y el logout borra el token actual. CORS en `config/cors.php`, con los orígenes leídos de `FRONTEND_URLS` y `supports_credentials=false`.

**Capas** (solo en la API, dentro de `Api/`):
- Controllers: `Api/AutenticacionController`, `Api/UsuarioController`. Son delgados y delegan en los servicios.
- Requests: `Api/RegistroUsuarioRequest`, `InicioSesionRequest`, `GuardarUsuarioRequest`, `ActualizarUsuarioRequest`.
- Services: `AutenticacionService`, `UsuarioService`.
- Repositories: `UsuarioRepository`, que convierte los campos en español (`nombre`, `correo_electronico`, `contrasena`) a las columnas (`name`, `email`, `password`).
- Resources: `UsuarioResource`.
- Además conserva los controladores `Auth/*` y `Settings/*` del starter kit (Inertia).

**Migraciones:** `users` (con `cedula` única, `telefono` y `direccion` añadidos editando la migración base), `password_reset_tokens`, `sessions`, `cache`, `jobs` y `personal_access_tokens`.
**Seeders:** solo `DatabaseSeeder`, que crea un usuario de prueba (`test@example.com`) con la factory.

**Pruebas** (`php artisan test`, SQLite en memoria + `RefreshDatabase`): ✅ **39 pasadas, 102 aserciones, 3,05 s**. La API tiene `Api/AutenticacionApiTest` y `Api/CorsApiTest`; el resto son pruebas del starter kit (Auth, Settings, Dashboard). **No hay pruebas del CRUD `/api/usuarios`.**

**Git.** `backend/` tiene su **propio** repositorio (`master`, **sin ningún commit**; los archivos solo están en *staging*). `.env` está en `.gitignore` ✅ (solo se versiona `.env.example`).

---

## 2. Frontend (`frontend/`)

**PHP / Laravel.** `php: ^8.2`, `laravel/framework: ^12.0`, `laravel/tinker`. Pruebas con Pest 3.
Extensiones PHP requeridas: las mismas que el backend (`ctype`, `dom`, `fileinfo`, `filter`, `hash`, `iconv`, `json`, `libxml`, `mbstring`, `openssl`, `pcre`, `session`, `tokenizer`), más `pdo_sqlite` porque la app usa SQLite.

**Vite / npm.** Sí. Vite 7 + Tailwind 4 + `laravel-vite-plugin`. Compila `resources/css/app.css` y `resources/js/app.js`, que añade jQuery + `jquery-confirm` para el diálogo "Eliminar" y desactiva los botones al enviar formularios. **No existe `public/build`**: hay que ejecutar `npm run build` antes de servir la app.

**.env (sin valores sensibles).**
| Variable | Valor |
|---|---|
| `DB_CONNECTION` | `sqlite` (archivo `database/database.sqlite`) |
| `SESSION_DRIVER` / `CACHE_STORE` / `QUEUE_CONNECTION` | `database` / `database` / `database` |
| API | `BACKEND_API_URL=http://localhost:8000/api`, `BACKEND_API_TIMEOUT=10` |
| Otras | `APP_URL=http://localhost:8001` |

**URL del backend.** Se lee de una **variable de entorno**: `BACKEND_API_URL` → `config('services.backend_api.url')`. Si la variable falta, usa por defecto `http://localhost:8000/api` (en `config/services.php`). No hay URLs fijas en el código ni en las vistas.

**Token.** Se guarda en la **sesión del servidor** (clave `token_api`) y nunca llega al navegador. La sesión va en la tabla `sessions` de SQLite. Los datos básicos del usuario se guardan en `usuario_api`.

**Cliente de la API.** `app/Services/ClienteApi.php` es la única clase que usa `Http`. Añade la URL base, el timeout, `Accept: application/json` y el Bearer, y convierte las respuestas así:
- 401 → `SesionExpiradaException`
- 404 → `abort(404)`
- 422 y 429 → `ValidationException`
- 5xx o fallo de conexión → `ApiNoDisponibleException`

Por encima están `AutenticacionService` y `UsuarioService`. Los middleware `sesion.api` e `invitado.api` protegen las rutas web.

**Rutas web:** `/`, `/iniciar-sesion`, `/registro`, `/cerrar-sesion`, `Route::resource('usuarios')` (con `{usuario}` numérico) y `/perfil`.

**Pruebas** (`php artisan test`, SQLite en memoria, `Http::fake()`, con las peticiones no simuladas bloqueadas): ✅ **71 pasadas, 230 aserciones, 2,19 s**. Cubren el login, el registro, el middleware, el `ClienteApi`, los servicios, el CRUD, el perfil y las páginas de error.

**Git.** `frontend/` tiene su **propio** repositorio, con 1 commit ("mi first commit") y muchos cambios sin confirmar (incluido `app/Exceptions/` y `PerfilController` sin seguimiento). `.env` está en `.gitignore` ✅.

---

## 3. Raíz y riesgos para dockerizar

**La raíz `gestion_usuarios/` no es un repositorio Git.** Son dos repositorios independientes y no existe un `.gitignore` en la raíz. Si se crea un repositorio en la raíz, los `.git` internos provocarán repositorios anidados.

**Problemas o riesgos:**
1. **Hosts `localhost` / `127.0.0.1`.** El backend tiene `DB_HOST=127.0.0.1` y el frontend `BACKEND_API_URL=http://localhost:8000/api`. Dentro de un contenedor deben ser los nombres de servicio (p. ej. `mysql` y `http://backend/api`).
2. **CORS no es el problema.** Como el frontend llama a la API desde el servidor, CORS no interviene. `FRONTEND_URLS` y los puertos 3000/5500 sobran salvo que haya clientes en el navegador.
3. **`.env.example` del backend desactualizado** (`sqlite` en lugar de `mysql`). Hay que alinearlo o inyectar las variables desde `docker-compose`.
4. **SQLite en archivo en el frontend** (`database/database.sqlite`). Las sesiones y la caché se pierden al recrear el contenedor si no se monta un volumen. Es más robusto usar `SESSION_DRIVER=file`/`redis` o la BD MySQL compartida.
5. **`QUEUE_CONNECTION=database` en ambos.** No hay jobs propios hoy, pero si se usan colas hará falta un contenedor `queue:work`.
6. **`node_modules` con binarios de Windows** (`rollup-win32`, `oxide-win32`). No hay que copiarlos a la imagen: conviene un `.dockerignore` y ejecutar `npm ci && npm run build` dentro, en una etapa de build. El frontend no tiene `public/build`.
7. **El backend arrastra el starter kit Inertia/React con SSR.** Esto obliga a tener Node en la imagen (o una etapa de build) aunque la API no lo use. Hay que decidir si se conserva o se elimina.
8. **Migración base modificada** (`cedula`, `telefono`, `direccion` en `create_users_table`). Una BD ya migrada no recibirá esas columnas; hay que usar `migrate:fresh` en entornos nuevos o crear una migración aparte.
9. **Arranque ordenado.** El backend debe esperar a MySQL (healthcheck) antes de `migrate --force`, y el frontend al backend. Ambos exponen `/up` para los healthchecks.
10. **Permisos y artefactos locales.** `storage/` y `bootstrap/cache` deben poder escribirse por `www-data`. Ya existen `laravel.log` (70 KB y 110 KB) y archivos `.sqlite` que no deben entrar en la imagen.
11. **`APP_KEY` distinto por app**, que debe inyectarse como secreto (no hornearlo en la imagen). En producción hay que fijar `APP_ENV=production`, `APP_DEBUG=false` y ejecutar `config:cache` y `route:cache`.
12. **El backend no tiene commits.** No hay un historial base contra el que versionar los `Dockerfile`/`compose`.
