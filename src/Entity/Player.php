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
    private $gp;

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

    public function __construct()
    {
        $this->isReady = false;
        $this->isAlive = true;  
        $this->inCity = 0;
        $this->gp = 0;   
        $this->hp = 10;   
        $this->hpMax = 10;   
        $this->nbDices = 6; 
        $this->nbMana = 0;   
        $this->turn = 0;   
        $this->isPlaying = false;
        $this->logs = new ArrayCollection();     
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

    public function getGp(): ?int
    {
        return $this->gp;
    }

    public function setGp(int $gp): self
    {
        $this->gp = $gp;

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
}
