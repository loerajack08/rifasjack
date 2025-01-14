<?php

namespace App\Entity;

use App\Repository\DatosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "boletos")]
class Datos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_boleto', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'numero_boleto', type: 'string', length: 100)]
    private string $numeroBoleto;

    #[ORM\Column(name: 'nombre_completo', type: 'string', length: 255)]
    private string $nombreCompleto;

    #[ORM\Column(type: 'string', length: 100)]
    private string $ciudad;

    #[ORM\Column(type: 'boolean')]
    private bool $status;

    #[ORM\Column(name: 'fecha_actualizacion', type: 'datetime')]
    private \DateTime $fechaActualizacion;

    // Getters y setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getNumeroBoleto(): string
    {
        return $this->numeroBoleto;
    }

    public function setNumeroBoleto(string $numeroBoleto): self
    {
        $this->numeroBoleto = $numeroBoleto;
        return $this;
    }

    public function getNombreCompleto(): string
    {
        return $this->nombreCompleto;
    }

    public function setNombreCompleto(string $nombreCompleto): self
    {
        $this->nombreCompleto = $nombreCompleto;
        return $this;
    }

    public function getCiudad(): string
    {
        return $this->ciudad;
    }

    public function setCiudad(string $ciudad): self
    {
        $this->ciudad = $ciudad;
        return $this;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getFechaActualizacion(): \DateTime
    {
        return $this->fechaActualizacion;
    }

    public function setFechaActualizacion(\DateTime $fechaActualizacion): self
    {
        $this->fechaActualizacion = $fechaActualizacion;
        return $this;
    }
}
