<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 */
class Player
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Monster", inversedBy="players")
     */
    private $monster;

    /**
     * @ORM\Column(type="boolean")
     */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="players")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="players")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isReady;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAlive;

    /**
     * @ORM\Column(type="smallint")
     */
    private $inCity;

    /**
     * @ORM\Column(type="smallint")
     */
    private $vp;

    /**
     * @ORM\Column(type="smallint")
     */
    private $hp;

    /**
     * @ORM\Column(type="smallint")
     */
    private $hpMax;

    /**
     * @ORM\Column(type="smallint")
     */
    private $nbDices;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbMana;

    /**
     * @ORM\Column(type="smallint")
     */
    private $turn;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPlaying;

    /**
     * @ORM\Column(type="datetime")
     */
    private $joinedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Log", mappedBy="player")
     */
    private $logs;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Log", inversedBy="playersHasSeen")
     */
    private $lastLog;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ThrowTokyoDice", mappedBy="player", orphanRemoval=true)
     */
    private $throwTokyoDices;

    /**
     * @ORM\Column(type="smallint")
     */
    private $throwsLeft;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ResolveOrder", mappedBy="player", orphanRemoval=true)
     */
    private $resolveOrders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GameDataUpdate", mappedBy="player")
     */
    private $gameDataUpdates;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Log", mappedBy="nextPlayer")
     */
    private $logsNext;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasDecidedAboutTokyo;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KotCardGame", mappedBy="player")
     */
    private $kotCards;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="winner")
     */
    private $gamesWon;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isWinner;

    public function __construct()
    {
        $this->isReady = false;
        $this->isAlive = true;
        $this->inCity = 0;
        $this->vp = 0;
        $this->hp = 10;
        $this->hpMax = 10;
        $this->nbDices = 6;
        $this->nbMana = 0;
        $this->turn = 0;
        $this->isPlaying = false;
        $this->throwsLeft = 3;
        $this->hasDecidedAboutTokyo = false;
        $this->lastLog = null;
        $this->logs = new ArrayCollection();
        $this->throwTokyoDices = new ArrayCollection();
        $this->resolveOrders = new ArrayCollection();
        $this->gameDataUpdates = new ArrayCollection();
        $this->logsNext = new ArrayCollection();
        $this->kotCards = new ArrayCollection();
        $this->gamesWon = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->getUser()->getUsername();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonster(): ?Monster
    {
        return $this->monster;
    }

    public function setMonster(?Monster $monster): self
    {
        $this->monster = $monster;

        return $this;
    }

    public function getCreator(): ?bool
    {
        return $this->creator;
    }

    public function setCreator(bool $creator): self
    {
        $this->creator = $creator;

        return $this;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIsReady(): ?bool
    {
        return $this->isReady;
    }

    public function setIsReady(bool $isReady): self
    {
        $this->isReady = $isReady;

        return $this;
    }

    public function getIsAlive(): ?bool
    {
        return $this->isAlive;
    }

    public function setIsAlive(bool $isAlive): self
    {
        $this->isAlive = $isAlive;

        return $this;
    }

    public function getInCity(): ?int
    {
        return $this->inCity;
    }

    public function setInCity(int $inCity): self
    {
        $this->inCity = $inCity;

        return $this;
    }

    public function getVp(): ?int
    {
        return $this->vp;
    }

    public function setVp(int $vp): self
    {
        $this->vp = $vp;

        return $this;
    }

    public function getHp(): ?int
    {
        return $this->hp;
    }

    public function setHp(int $hp): self
    {
        $this->hp = $hp;

        return $this;
    }

    public function getHpMax(): ?int
    {
        return $this->hpMax;
    }

    public function setHpMax(int $hpMax): self
    {
        $this->hpMax = $hpMax;

        return $this;
    }

    public function getNbDices(): ?int
    {
        return $this->nbDices;
    }

    public function setNbDices(int $nbDices): self
    {
        $this->nbDices = $nbDices;

        return $this;
    }

    public function getNbMana(): ?int
    {
        return $this->nbMana;
    }

    public function setNbMana(int $nbMana): self
    {
        $this->nbMana = $nbMana;

        return $this;
    }

    public function getTurn(): ?int
    {
        return $this->turn;
    }

    public function setTurn(int $turn): self
    {
        $this->turn = $turn;

        return $this;
    }

    public function getIsPlaying(): ?bool
    {
        return $this->isPlaying;
    }

    public function setIsPlaying(bool $isPlaying): self
    {
        $this->isPlaying = $isPlaying;

        return $this;
    }

    public function getJoinedAt(): ?\DateTimeInterface
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTimeInterface $joinedAt): self
    {
        $this->joinedAt = $joinedAt;

        return $this;
    }

    /**
     * @return Collection|Log[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setPlayer($this);
        }

        return $this;
    }

    public function removeLog(Log $log): self
    {
        if ($this->logs->contains($log)) {
            $this->logs->removeElement($log);
            // set the owning side to null (unless already changed)
            if ($log->getPlayer() === $this) {
                $log->setPlayer(null);
            }
        }

        return $this;
    }

    public function getLastLog(): ?Log
    {
        return $this->lastLog;
    }

    public function setLastLog(?Log $LastLog): self
    {
        $this->lastLog = $LastLog;

        return $this;
    }

    /**
     * @return Collection|ThrowTokyoDice[]
     */
    public function getThrowTokyoDices(): Collection
    {
        return $this->throwTokyoDices;
    }

    public function addThrowTokyoDice(ThrowTokyoDice $throwTokyoDice): self
    {
        if (!$this->throwTokyoDices->contains($throwTokyoDice)) {
            $this->throwTokyoDices[] = $throwTokyoDice;
            $throwTokyoDice->setPlayer($this);
        }

        return $this;
    }

    public function removeThrowTokyoDice(ThrowTokyoDice $throwTokyoDice): self
    {
        if ($this->throwTokyoDices->contains($throwTokyoDice)) {
            $this->throwTokyoDices->removeElement($throwTokyoDice);
            // set the owning side to null (unless already changed)
            if ($throwTokyoDice->getPlayer() === $this) {
                $throwTokyoDice->setPlayer(null);
            }
        }

        return $this;
    }

    public function getThrowsLeft(): ?int
    {
        return $this->throwsLeft;
    }

    public function setThrowsLeft(?int $throwsLeft): self
    {
        $this->throwsLeft = $throwsLeft;

        return $this;
    }

    /**
     * @return Collection|ResolveOrder[]
     */
    public function getResolveOrders(): Collection
    {
        return $this->resolveOrders;
    }

    public function addResolveOrder(ResolveOrder $resolveOrder): self
    {
        if (!$this->resolveOrders->contains($resolveOrder)) {
            $this->resolveOrders[] = $resolveOrder;
            $resolveOrder->setPlayer($this);
        }

        return $this;
    }

    public function removeResolveOrder(ResolveOrder $resolveOrder): self
    {
        if ($this->resolveOrders->contains($resolveOrder)) {
            $this->resolveOrders->removeElement($resolveOrder);
            // set the owning side to null (unless already changed)
            if ($resolveOrder->getPlayer() === $this) {
                $resolveOrder->setPlayer(null);
            }
        }

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
            $gameDataUpdate->setPlayer($this);
        }

        return $this;
    }

    public function removeGameDataUpdate(GameDataUpdate $gameDataUpdate): self
    {
        if ($this->gameDataUpdates->contains($gameDataUpdate)) {
            $this->gameDataUpdates->removeElement($gameDataUpdate);
            // set the owning side to null (unless already changed)
            if ($gameDataUpdate->getPlayer() === $this) {
                $gameDataUpdate->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Log[]
     */
    public function getLogsNext(): Collection
    {
        return $this->logsNext;
    }

    public function addLogsNext(Log $logsNext): self
    {
        if (!$this->logsNext->contains($logsNext)) {
            $this->logsNext[] = $logsNext;
            $logsNext->setNextPlayer($this);
        }

        return $this;
    }

    public function removeLogsNext(Log $logsNext): self
    {
        if ($this->logsNext->contains($logsNext)) {
            $this->logsNext->removeElement($logsNext);
            // set the owning side to null (unless already changed)
            if ($logsNext->getNextPlayer() === $this) {
                $logsNext->setNextPlayer(null);
            }
        }

        return $this;
    }

    public function getHasDecidedAboutTokyo(): ?bool
    {
        return $this->hasDecidedAboutTokyo;
    }

    public function setHasDecidedAboutTokyo(?bool $hasDecidedAboutTokyo): self
    {
        $this->hasDecidedAboutTokyo = $hasDecidedAboutTokyo;

        return $this;
    }

    /**
     * @return Collection|KotCardGame[]
     */
    public function getKotCards(): Collection
    {
        return $this->kotCards;
    }

    public function addKotCard(KotCardGame $kotCard): self
    {
        if (!$this->kotCards->contains($kotCard)) {
            $this->kotCards[] = $kotCard;
            $kotCard->setPlayer($this);
        }

        return $this;
    }

    public function removeKotCard(KotCardGame $kotCard): self
    {
        if ($this->kotCards->contains($kotCard)) {
            $this->kotCards->removeElement($kotCard);
            // set the owning side to null (unless already changed)
            if ($kotCard->getPlayer() === $this) {
                $kotCard->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGamesWon(): Collection
    {
        return $this->gamesWon;
    }

    public function addGamesWon(Game $gamesWon): self
    {
        if (!$this->gamesWon->contains($gamesWon)) {
            $this->gamesWon[] = $gamesWon;
            $gamesWon->setWinner($this);
        }

        return $this;
    }

    public function removeGamesWon(Game $gamesWon): self
    {
        if ($this->gamesWon->contains($gamesWon)) {
            $this->gamesWon->removeElement($gamesWon);
            // set the owning side to null (unless already changed)
            if ($gamesWon->getWinner() === $this) {
                $gamesWon->setWinner(null);
            }
        }

        return $this;
    }

    public function getIsWinner(): ?bool
    {
        return $this->isWinner;
    }

    public function setIsWinner(?bool $isWinner): self
    {
        $this->isWinner = $isWinner;

        return $this;
    }
}
