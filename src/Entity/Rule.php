<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RuleRepository")
 */
class Rule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Board", mappedBy="ruleApplicable")
     */
    private $applicableToBoard;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Game", mappedBy="rules")
     */
    private $games;

    /**
     * @ORM\Column(type="boolean", options={"default" : true})
     */
    private $available;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Box", inversedBy="rules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $box;

    public function __construct()
    {
        $this->applicableToBoard = new ArrayCollection();
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Board[]
     */
    public function getApplicableToBoard(): Collection
    {
        return $this->applicableToBoard;
    }

    public function addApplicableToBoard(Board $applicableToBoard): self
    {
        if (!$this->applicableToBoard->contains($applicableToBoard)) {
            $this->applicableToBoard[] = $applicableToBoard;
            $applicableToBoard->addRuleApplicable($this);
        }

        return $this;
    }

    public function removeApplicableToBoard(Board $applicableToBoard): self
    {
        if ($this->applicableToBoard->contains($applicableToBoard)) {
            $this->applicableToBoard->removeElement($applicableToBoard);
            $applicableToBoard->removeRuleApplicable($this);
        }

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->addRule($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            $game->removeRule($this);
        }

        return $this;
    }

    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function getImgName(): ?string
    {
        return $this->imgName;
    }

    public function setImgName(?string $imgName): self
    {
        $this->imgName = $imgName;

        return $this;
    }

    public function getBox(): ?Box
    {
        return $this->box;
    }

    public function setBox(?Box $box): self
    {
        $this->box = $box;

        return $this;
    }
}
