// Mejoras progresivas de la interfaz: la aplicación funciona igual sin JavaScript.
// El navegador nunca llama a la API; todas las peticiones las hace el servidor.

// Importa jQuery, necesario para jquery-confirm.
import jQuery from 'jquery';
// Importa la función que instala jquery-confirm sobre jQuery.
import instalarJqueryConfirm from 'jquery-confirm';
// Importa los estilos de los cuadros de diálogo de jquery-confirm.
import 'jquery-confirm/dist/jquery-confirm.min.css';

// Registra $.confirm en la instancia de jQuery importada.
instalarJqueryConfirm(window, jQuery);

/**
 * Convierte un texto en HTML seguro, porque jquery-confirm interpreta su contenido como HTML
 * y el mensaje incluye datos de la API (el nombre del usuario).
 */
const escaparHtml = (texto) => jQuery('<div>').text(texto).html();

// Escucha el envío de cualquier formulario en la fase de captura, antes que los demás manejadores.
document.addEventListener('submit', (evento) => {
    // Obtiene el formulario que se está enviando.
    const formulario = evento.target;

    // Lee el mensaje de confirmación definido en el atributo data-confirmar, si existe.
    const mensajeConfirmacion = formulario.dataset.confirmar;

    // Comprueba si el formulario no necesita confirmación.
    if (!mensajeConfirmacion) {
        // Deja que el formulario se envíe normalmente.
        return;
    }

    // Comprueba si este envío viene de pulsar "Eliminar" en el cuadro de diálogo.
    if (formulario.dataset.confirmado === 'true') {
        // Quita la marca para que un próximo envío vuelva a pedir confirmación.
        delete formulario.dataset.confirmado;

        // Deja que el formulario se envíe.
        return;
    }

    // Detiene el envío hasta que el usuario responda al cuadro de diálogo.
    evento.preventDefault();

    // Muestra el cuadro de diálogo de confirmación de jquery-confirm.
    jQuery.confirm({
        // Define el título del cuadro de diálogo.
        title: 'Confirmar eliminación',
        // Muestra el mensaje del formulario escapado como HTML seguro.
        content: escaparHtml(mensajeConfirmacion),
        // Aplica el estilo rojo de acciones peligrosas.
        type: 'red',
        // Desactiva la dependencia de la rejilla de Bootstrap, que este proyecto no usa.
        useBootstrap: false,
        // Fija el ancho del cuadro en 420 px; en el móvil lo limita la regla .jconfirm-box de app.css.
        boxWidth: '420px',
        // Cierra el cuadro con la tecla Escape como si se pulsara "Cancelar".
        escapeKey: 'cancelar',
        // Cierra el cuadro al hacer clic fuera de él, sin eliminar.
        backgroundDismiss: 'cancelar',
        // Define los botones del cuadro de diálogo.
        buttons: {
            // Define el botón que confirma la eliminación.
            confirmar: {
                // Muestra el texto del botón.
                text: 'Eliminar',
                // Aplica el color rojo al botón.
                btnClass: 'btn-red',
                // Ejecuta la eliminación al pulsar el botón.
                action: () => {
                    // Marca el formulario como confirmado para no volver a preguntar.
                    formulario.dataset.confirmado = 'true';

                    // Vuelve a enviar el formulario disparando el evento submit.
                    formulario.requestSubmit();
                },
            },
            // Define el botón que cancela la eliminación.
            cancelar: {
                // Muestra el texto del botón.
                text: 'Cancelar',
            },
        },
    });
}, true);

// Escucha el envío de formularios en la fase de burbuja, después de la confirmación.
document.addEventListener('submit', (evento) => {
    // Comprueba si el envío fue cancelado, por ejemplo al rechazar la confirmación.
    if (evento.defaultPrevented) {
        // Deja los botones sin cambios porque el formulario no se enviará.
        return;
    }

    // Recorre los botones de envío del formulario.
    evento.target.querySelectorAll('button[type="submit"]').forEach((boton) => {
        // Guarda el texto original para restaurarlo si el usuario vuelve atrás.
        boton.dataset.textoOriginal = boton.textContent;

        // Deshabilita el botón para evitar un segundo envío.
        boton.disabled = true;

        // Muestra que el formulario se está procesando.
        boton.textContent = 'Procesando...';
    });
});

// Escucha la carga de la página, incluida la restauración desde la caché del historial.
window.addEventListener('pageshow', (evento) => {
    // Comprueba si la página se restauró al pulsar "Atrás" en el navegador.
    if (!evento.persisted) {
        // No hay nada que restaurar en una carga normal.
        return;
    }

    // Recorre los botones que quedaron deshabilitados por un envío anterior.
    document.querySelectorAll('button[data-texto-original]').forEach((boton) => {
        // Vuelve a habilitar el botón.
        boton.disabled = false;

        // Restaura el texto original del botón.
        boton.textContent = boton.dataset.textoOriginal;
    });
});
