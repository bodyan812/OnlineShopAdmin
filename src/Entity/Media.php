<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Ignore;
use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[ORM\Table(name: 'media')]
class Media
{
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_DOCUMENT = 'document';
    const TYPE_OTHER = 'other';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['product:read', 'category:read'])]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['product:read', 'category:read'])]
    private ?string $description = null;

    #[Vich\UploadableField(mapping: "media_file", fileNameProperty: "filePath")]
    #[Assert\NotNull(message: "Пожалуйста, загрузите файл")]
    #[Ignore]
    private ?File $file = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $filePath;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['product:read', 'category:read'])]
    private string $fileType;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'media')]
    private ?Product $product = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'image')]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->title ?: 'Media #' . $this->id;
    }
    public function getWebPath(): string
    {
        return 'uploads/media/' . $this->filePath;
    }
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
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

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function getFileType(): string
    {
        return $this->fileType;
    }

    public function setFileType(string $fileType): self
    {
        $this->fileType = $fileType;
        return $this;
    }


    public function setFile(?File $file = null): void
    {
        $this->file = $file;

        if (null !== $file) {
            $this->setFileTypeFromExtension($file);
        }
    }

    private function setFileTypeFromExtension(File $file): void
    {
        $extension = strtolower($file->guessExtension() ?: pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));

        $imageTypes = ['jpg', 'jpeg', 'png', 'webp'];
        $videoTypes = ['mp4', 'webm'];
        $documentTypes = ['pdf', 'docx'];

        if (in_array($extension, $imageTypes)) {
            $this->fileType = 'image';
        } elseif (in_array($extension, $videoTypes)) {
            $this->fileType = 'video';
        } elseif (in_array($extension, $documentTypes)) {
            $this->fileType = 'document';
        } else {
            $this->fileType = 'other';
        }
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getFileName(): string
    {
        return $this->filePath;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }
}
