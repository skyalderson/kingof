<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoardRepository")
 */
class Board
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Rule", inversedBy="applicableToBoard")
     */
    private $ruleApplicable;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="board", orphanRemoval=true)
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

    public function __construct()
    {
        $this->ruleApplicable = new ArrayCollection();
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

    /**
     * @return Collection|Rule[]
     */
    public function getRuleApplicable(): Collection
    {
        return $this->ruleApplicable;
    }

    public function addRuleApplicable(Rule $ruleApplicable): self
    {
        if (!$this->ruleApplicable->contains($ruleApplicable)) {
            $this->ruleApplicable[] = $ruleApplicable;
        }

        return $this;
    }

    public function removeRuleApplicable(Rule $ruleApplicable): self
    {
        if ($this->ruleApplicable->contains($ruleApplicable)) {
            $this->ruleApplicable->removeElement($ruleApplicable);
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
            $game->setBoard($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getBoard() === $this) {
                $game->setBoard(null);
            }
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
}
