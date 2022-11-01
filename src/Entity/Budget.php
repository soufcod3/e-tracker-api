<?php

namespace App\Entity;

use App\Repository\BudgetRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
class Budget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $startingAt;

    #[ORM\Column]
    private \DateTimeImmutable $endingAt;

    #[ORM\Column]
    private float $kickoffAmount;

    #[ORM\Column]
    private float $leftAmount;

    #[ORM\Column]
    private bool $featured = false;

    #[ORM\OneToMany(mappedBy: 'budget', targetEntity: Expense::class)]
    private Collection $expenses;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->startingAt = new DateTimeImmutable();
        $this->endingAt = new DateTimeImmutable();
        $this->expenses = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStartingAt(): \DateTimeImmutable
    {
        return $this->startingAt;
    }

    public function setStartingAt(\DateTimeImmutable $startingAt): self
    {
        $this->startingAt = $startingAt;

        return $this;
    }

    public function getEndingAt(): \DateTimeImmutable
    {
        return $this->endingAt;
    }

    public function setEndingAt(\DateTimeImmutable $endingAt): self
    {
        $this->endingAt = $endingAt;

        return $this;
    }

    public function getKickoffAmount(): float
    {
        return $this->kickoffAmount;
    }

    public function setKickoffAmount(float $kickoffAmount): self
    {
        $this->kickoffAmount = $kickoffAmount;

        return $this;
    }

    public function getLeftAmount(): float
    {
        return $this->leftAmount;
    }

    public function setLeftAmount(float $leftAmount): self
    {
        $this->leftAmount = $leftAmount;

        return $this;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): self
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setBudget($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): self
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getBudget() === $this) {
                $expense->setBudget(null);
            }
        }

        return $this;
    }
}
