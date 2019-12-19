<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\LogRepository")

 * @ORM\Table(name="log",indexes={@ORM\Index(name="is_done", columns={"is_done"})})
 */

class Log
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="logs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="logs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $action;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Player", mappedBy="lastLog")
     */
    private $playersHasSeen;

    public function __construct()
    {
        $this->playersHasSeen = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getIsDone(): ?bool
    {
        return $this->isDone;
    }

    public function setIsDone(bool $isDone): self
    {
        $this->isDone = $isDone;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayersHasSeen(): Collection
    {
        return $this->playersHasSeen;
    }

    public function addPlayersHasSeen(Player $playersHasSeen): self
    {
        if (!$this->playersHasSeen->contains($playersHasSeen)) {
            $this->playersHasSeen[] = $playersHasSeen;
            $playersHasSeen->setLastLog($this);
        }

        return $this;
    }

    public function removePlayersHasSeen(Player $playersHasSeen): self
    {
        if ($this->playersHasSeen->contains($playersHasSeen)) {
            $this->playersHasSeen->removeElement($playersHasSeen);
            // set the owning side to null (unless already changed)
            if ($playersHasSeen->getLastLog() === $this) {
                $playersHasSeen->setLastLog(null);
            }
        }

        return $this;
    }
}
