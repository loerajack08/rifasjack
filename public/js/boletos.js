document.addEventListener('DOMContentLoaded', () => {
    const boletos = document.querySelectorAll('.boleto-checkbox');
    const btnWhatsapp = document.getElementById('enviar-whatsapp');
    const numeroTelefono = '+526682490923'; // 

    boletos.forEach(boleto => {
        boleto.addEventListener('change', updateWhatsappLink);
    });

    function updateWhatsappLink() {
        const seleccionados = [];
        boletos.forEach(boleto => {
            if (boleto.checked) {
                seleccionados.push(boleto.dataset.numero);
            }
        });

        if (seleccionados.length > 0) {
            btnWhatsapp.disabled = false;

            // Construimos el mensaje para WhatsApp
            const mensaje = `Boletos seleccionados: ${seleccionados.join(', ')}`;
            const enlaceWhatsapp = `https://wa.me/${numeroTelefono}?text=${encodeURIComponent(mensaje)}`;

            // Asociamos el enlace al botón
            btnWhatsapp.onclick = () => {
                window.open(enlaceWhatsapp, '_blank');
            };
        } else {
            btnWhatsapp.disabled = true;
            btnWhatsapp.onclick = null; // Deshabilitamos el click si no hay boletos seleccionados
        }
    }
});

