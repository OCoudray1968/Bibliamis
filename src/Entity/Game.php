<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Monolog\DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GameRepository;
use App\Entity\Traits\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 * @ORM\Table(name="games")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks
 */
class Game
{
    use Timestampable;
  
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $support;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comments;

    /**
     * @Vich\UploadableField(mapping="game_image", fileNameProperty="imageName")
     * @Assert\Image(maxSize="8M", maxSizeMessage="Le fichier ne peut pas dépasser 8M")
     * 
     * @var File|null
     */

    private $imageFile;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Gender::class, inversedBy="games")
     */
    private $genders;

    /**
     * @ORM\ManyToMany(targetEntity=Loanning::class, mappedBy="game")
     */
    private $loannings;

    public function __construct()
    {
        $this->loannings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSupport(): ?string
    {
        return $this->support;
    }

    public function setSupport(string $support): self
    {
        $this->support = $support;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

     /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdatedAt(new \DateTimeImmutable);
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
   
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

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

    public function getGenders(): ?Gender
    {
        return $this->genders;
    }

    public function setGenders(?Gender $gender): self
    {
        $this->genders = $gender;

        return $this;
    }


    public function addGender(Gender $gender): self
    {
        if (!$this->genders->contains($gender)) {
            $this->genders[] = $gender;
            $gender->addGame($this);
        }
        return $this;
    }

    public function removeGender(Gender $gender): self
    {
        if ($this->genders->removeElement($gender)) {
            $gender->removeGame($this);
        }

        return $this;
    }

    /**
     * @return Collection|Loanning[]
     */
    public function getLoannings(): Collection
    {
        return $this->loannings;
    }

    public function addLoanning(Loanning $loanning): self
    {
        if (!$this->loannings->contains($loanning)) {
            $this->loannings[] = $loanning;
            $loanning->addGame($this);
        }

        return $this;
    }

    public function removeLoanning(Loanning $loanning): self
    {
        if ($this->loannings->removeElement($loanning)) {
            $loanning->removeGame($this);
        }

        return $this;
    }
}
