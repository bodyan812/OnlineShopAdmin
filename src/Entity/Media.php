<?php

namespace App\Entity;

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
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['veteran:item'])]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['veteran:item'])]
    private ?string $description = null;

    #[Vich\UploadableField(mapping: "media_file", fileNameProperty: "filePath")]
    #[Assert\NotNull(message: "Пожалуйста, загрузите файл")]
    private ?File $file = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $filePath;

    #[ORM\Column(type: 'string', length: 50)]
    private string $fileType;

    #[ORM\ManyToOne(targetEntity: Veteran::class, inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Veteran $veteran = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    #[Groups(['veteran:item'])]
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

    public function getVeteran(): Veteran
    {
        return $this->veteran;
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



    public function setVeteran(?Veteran $veteran): self
    {
        $this->veteran = $veteran;
        return $this;
    }
}
