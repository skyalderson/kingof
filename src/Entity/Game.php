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

    public function __construct()
    {
        $this->rules = new ArrayCollection();
        $this->monstersAuthorized = new ArrayCollection();
        $this->players = new ArrayCollection();
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
        foreach($players as $player)
        {
            if ($player->getCreator() == true) return $player;

        }  

    }

    public function getCreatorUser()
    {
        $players = $this->getPlayers(); 
        foreach($players as $player)
        {
            if ($player->getCreator() == true) return $player->getUser();

        }  

    }
}
