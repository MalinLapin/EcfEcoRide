<?php

namespace App\Model;


class BrandModel extends BaseModel
{
    private ?int $idBrand = null; // Identifiant de la marque, initialisé à null.
    private string $label; // Label de la marque.

    public function __construct(array $data = [])
    {
        $this->hydrate($data);    
    }

    /**
     * Get the value of idBrand
     */
    public function getIdBrand(): ?int
    {
        return $this->idBrand;
    }

    /**
     * Set the value of idBrand
     */
    public function setIdBrand(?int $idBrand): self
    {
        $this->idBrand = $idBrand;

        return $this;
    }

    /**
     * Get the value of label
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set the value of label
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }
}