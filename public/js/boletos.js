document.addEventListener('DOMContentLoaded', () => {
    const boletos = document.querySelectorAll('.boleto-checkbox');
    const btnWhatsapp = document.getElementById('enviar-whatsapp');
    const formularioEmergente = document.getElementById('formulario-emergente');
    const closeModalBtn = document.querySelector('.close');
    const datosForm = document.getElementById('datos-form');
    const numeroTelefono = '+526682490923'; // Número de WhatsApp

    // Deshabilitar checkboxes de boletos no disponibles
    boletos.forEach(boleto => {
        const statusElement = boleto.nextElementSibling.querySelector('.status-boleto');
        const status = statusElement ? statusElement.textContent.trim().toLowerCase() : '';

        if (status !== 'libre') {
            boleto.disabled = true; // Deshabilitar el checkbox
            boleto.parentElement.classList.add('disabled'); // Agregar una clase opcional para estilos
        }
    });

    // Mostrar el formulario emergente al hacer clic en el botón
    if (btnWhatsapp) {
        btnWhatsapp.addEventListener('click', (event) => {
            event.preventDefault(); // Evita el comportamiento predeterminado
            formularioEmergente.style.display = 'block';
        });
    }

    // Cerrar el formulario emergente
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => {
            formularioEmergente.style.display = 'none';
        });
    }

    // Habilitar el botón de WhatsApp si hay boletos seleccionados
    boletos.forEach(boleto => {
        boleto.addEventListener('change', () => {
            const seleccionados = Array.from(boletos).filter(boleto => boleto.checked);
            if (btnWhatsapp) {
                btnWhatsapp.disabled = seleccionados.length === 0;
            }
        });
    });

    // Manejar el envío del formulario
    if (datosForm) {
        datosForm.addEventListener('submit', (event) => {
            event.preventDefault();

            // Obtener datos del formulario
            const nombre = document.getElementById('nombre')?.value.trim();
            const cel = document.getElementById('cel')?.value.trim();
            const ciudad = document.getElementById('ciudad')?.value.trim();

            // Validar campos del formulario
            if (!nombre || !cel || !ciudad) {
                alert('Por favor completa todos los campos del formulario.');
                return;
            }

            // Obtener boletos seleccionados
            const seleccionados = Array.from(boletos)
                .filter(boleto => boleto.checked)
                .map(boleto => boleto.dataset.numero);

            if (seleccionados.length === 0) {
                alert('Por favor selecciona al menos un boleto.');
                return;
            }

            // Construir el mensaje para WhatsApp
            const mensaje = `
                Hola, soy ${nombre}.
                Mi teléfono es ${cel} y soy de ${ciudad}.
                Estoy interesado en los boletos: ${seleccionados.join(', ')}.
            `;
            const enlaceWhatsapp = `https://wa.me/${numeroTelefono}?text=${encodeURIComponent(mensaje)}`;

            // Abrir WhatsApp con el mensaje
            window.open(enlaceWhatsapp, '_blank');
        });
    }

    // Lógica de búsqueda
    const inputBusqueda = document.getElementById("busqueda-boleto");
    const listaBoletos = document.querySelectorAll(".boleto-item");

    if (inputBusqueda && listaBoletos.length > 0) {
        inputBusqueda.addEventListener("input", () => {
            const query = inputBusqueda.value.trim().toLowerCase();

            listaBoletos.forEach(boleto => {
                const numeroBoletoElement = boleto.querySelector(".numero-boleto");

                if (numeroBoletoElement) {
                    const numeroBoleto = numeroBoletoElement.textContent.toLowerCase();

                    // Verifica si el texto del boleto incluye la consulta
                    if (numeroBoleto.includes(query)) {
                        boleto.style.display = "block"; // Muestra el boleto
                    } else {
                        boleto.style.display = "none"; // Oculta el boleto
                    }
                }
            });
        });
    }
});




