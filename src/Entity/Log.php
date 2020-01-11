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

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $message;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GameDataUpdate", mappedBy="log", orphanRemoval=true)
     */
    private $gameDataUpdates;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nextAction;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="logsNext")
     */
    private $nextPlayer;

    public function __construct()
    {
        $this->playersHasSeen = new ArrayCollection();
        $this->gameDataUpdates = new ArrayCollection();
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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Collection|GameDataUpdate[]
     */
    public function getGameDataUpdates(): Collection
    {
        return $this->gameDataUpdates;
    }

    public function addGameDataUpdate(GameDataUpdate $gameDataUpdate): self
    {
        if (!$this->gameDataUpdates->contains($gameDataUpdate)) {
            $this->gameDataUpdates[] = $gameDataUpdate;
            $gameDataUpdate->setLog($this);
        }

        return $this;
    }

    public function removeGameDataUpdate(GameDataUpdate $gameDataUpdate): self
    {
        if ($this->gameDataUpdates->contains($gameDataUpdate)) {
            $this->gameDataUpdates->removeElement($gameDataUpdate);
            // set the owning side to null (unless already changed)
            if ($gameDataUpdate->getLog() === $this) {
                $gameDataUpdate->setLog(null);
            }
        }

        return $this;
    }

    public function getNextAction(): ?string
    {
        return $this->nextAction;
    }

    public function setNextAction(?string $nextAction): self
    {
        $this->nextAction = $nextAction;

        return $this;
    }

    public function getNextPlayer(): ?Player
    {
        return $this->nextPlayer;
    }

    public function setNextPlayer(?Player $nextPlayer): self
    {
        $this->nextPlayer = $nextPlayer;

        return $this;
    }
}
