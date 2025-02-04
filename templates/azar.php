<?php
function seleccionarBoletos($cantidad) {
    $totalBoletos = 006000;
    $seleccionados = [];

    // Verificamos si la cantidad solicitada es válida
    if (!in_array($cantidad, [1, 4, 10, 20])) {
        echo 'Cantidad no válida. Debe ser 1, 4, 10 o 20.';
        return;
    }

    // Realizamos la selección aleatoria de boletos
    while (count($seleccionados) < $cantidad) {
        $boleto = rand(1, $totalBoletos);
        if (!in_array($boleto, $seleccionados)) {
            $seleccionados[] = $boleto;
        }
    }

    return $seleccionados;
}

// Ejemplo de uso: seleccionar 4 boletos aleatorios
print_r(seleccionarBoletos(4));
?>
