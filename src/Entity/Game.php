<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    const SELECT_MONSTERS_TYPE = [
        0 => 'Classique',
        1 => 'Aléatoire',
    ];

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Mode", inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mode;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Board", inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $board;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Rule", inversedBy="games")
     */
    private $rules;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Monster", inversedBy="games")
     */
    private $monstersAuthorized;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\Range(min=1, max=1)
     */
    private $state;

    /**
     * @ORM\Column(type="smallint")
     */
    private $monstersSelect = 0;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\Range(min=2, max=6)
     */
    private $maxPlayers = 6;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Player", mappedBy="game", orphanRemoval=true)
     */
    private $players;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Log", mappedBy="game", orphanRemoval=true)
     */
    private $logs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\KotCardGame", mappedBy="game", orphanRemoval=true)
     */
    private $kotCards;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="gamesWon")
     */
    private $winner;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $victoryType;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finishedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startedAt;

    public function __construct()
    {
        $this->rules = new ArrayCollection();
        $this->monstersAuthorized = new ArrayCollection();
        $this->players = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->kotCards = new ArrayCollection();
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

    public function getMode(): ?Mode
    {
        return $this->mode;
    }

    public function setMode(?Mode $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getBoard(): ?Board
    {
        return $this->board;
    }

    public function setBoard(?Board $board): self
    {
        $this->board = $board;

        return $this;
    }

    /**
     * @return Collection|Rule[]
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function addRule(Rule $rule): self
    {
        if (!$this->rules->contains($rule)) {
            $this->rules[] = $rule;
        }

        return $this;
    }

    public function removeRule(Rule $rule): self
    {
        if ($this->rules->contains($rule)) {
            $this->rules->removeElement($rule);
        }

        return $this;
    }

    /**
     * @return Collection|Monster[]
     */
    public function getMonstersAuthorized(): Collection
    {
        return $this->monstersAuthorized;
    }

    public function addMonstersAuthorized(Monster $monstersAuthorized): self
    {
        if (!$this->monstersAuthorized->contains($monstersAuthorized)) {
            $this->monstersAuthorized[] = $monstersAuthorized;
        }

        return $this;
    }

    public function removeMonstersAuthorized(Monster $monstersAuthorized): self
    {
        if ($this->monstersAuthorized->contains($monstersAuthorized)) {
            $this->monstersAuthorized->removeElement($monstersAuthorized);
        }

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getMonstersSelect(): ?int
    {
        return $this->monstersSelect;
    }

    public function getMonstersSelectLabel(): ?string
    {
        return Game::SELECT_MONSTERS_TYPE[$this->monstersSelect];
    }

    public function setMonstersSelect(int $monstersSelect): self
    {
        $this->monstersSelect = $monstersSelect;

        return $this;
    }

    public function getNbMonstersAllowed(): ?int
    {
        return count($this->monstersAuthorized);
    }

    public function getNbPlayers(): ?int
    {
        return count($this->players);
    }

    public function getMaxPlayers(): ?int
    {
        return $this->maxPlayers;
    }

    public function setMaxPlayers(int $maxPlayers): self
    {
        $this->maxPlayers = $maxPlayers;

        return $this;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->setGame($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->contains($player)) {
            $this->players->removeElement($player);
            // set the owning side to null (unless already changed)
            if ($player->getGame() === $this) {
                $player->setGame(null);
            }
        }

        return $this;
    }

    public function getCreatorPlayer()
    {
        $players = $this->getPlayers();
        foreach ($players as $player) {
            if (true == $player->getCreator()) {
                return $player;
            }
        }
    }

    public function getCreatorUser()
    {
        $players = $this->getPlayers();
        foreach ($players as $player) {
            if (true == $player->getCreator()) {
                return $player->getUser();
            }
        }
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
            $log->setGame($this);
        }

        return $this;
    }

    public function removeLog(Log $log): self
    {
        if ($this->logs->contains($log)) {
            $this->logs->removeElement($log);
            // set the owning side to null (unless already changed)
            if ($log->getGame() === $this) {
                $log->setGame(null);
            }
        }

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
            $kotCard->setGame($this);
        }

        return $this;
    }

    public function removeKotCard(KotCardGame $kotCard): self
    {
        if ($this->kotCards->contains($kotCard)) {
            $this->kotCards->removeElement($kotCard);
            // set the owning side to null (unless already changed)
            if ($kotCard->getGame() === $this) {
                $kotCard->setGame(null);
            }
        }

        return $this;
    }

    public function getWinner(): ?Player
    {
        return $this->winner;
    }

    public function setWinner(?Player $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    public function getVictoryType(): ?string
    {
        return $this->victoryType;
    }

    public function setVictoryType(?string $victoryType): self
    {
        $this->victoryType = $victoryType;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeInterface $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }
}
