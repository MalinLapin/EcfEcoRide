<?php

namespace App\Model;


class BrandModel extends BaseModel
{
    private string $table = 'brand'; // Nom de la table associée à ce modèle.
    private ?int $idBrand = null; // Identifiant de la marque, initialisé à null.
    private string $label; // Label de la marque.

    /**
     * Get the value of table
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Set the value of table
     */
    public function setTable(string $table): self
    {
        $this->table = $table;

        return $this;
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