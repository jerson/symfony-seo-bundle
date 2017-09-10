<?php

namespace SEOBundle\Entity;

use Constants;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gregwar\Image\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Meta
 *
 * @ORM\Table(name="meta")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="SEOBundle\Repository\MetaRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="default_region")
 */
class Meta
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=191, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=191, nullable=true, unique=true)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=191, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=191, nullable=true)
     */
    private $keywords;

    /**
     * @var string
     *
     * @Assert\Url()
     * @ORM\Column(name="url", type="string", length=191, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=191, nullable=true)
     */
    private $type;

    /**
     * @var boolean
     */
    private $deleteImage;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=191, nullable=true)
     */
    private $image;

    /**
     * @Assert\Image(maxSize="5M")
     */
    private $file;

    /**
     * @var string
     */
    private $imageUrl;


    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date_created", nullable=true, type="datetime")
     */
    private $dateCreated;


    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="date_updated", nullable=true, type="datetime")
     */
    private $dateUpdated;

    /**
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }


    /**
     * @return bool
     */
    public function isDeleteImage()
    {
        return $this->deleteImage;
    }

    /**
     * @param bool $deleteImage
     */
    public function setDeleteImage($deleteImage)
    {
        $this->deleteImage = $deleteImage;
    }


    /**
     * Lifecycle callback to upload the file to the server
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function lifecycleFileUpload()
    {
        $this->upload();
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        $filename = uniqid('') . '.' . $this->getFile()->getClientOriginalExtension();
        $this->getFile()->move(
            Constants::getFullUploadDir(),
            $filename
        );

        $this->image = $filename;
        $this->setFile(null);
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }


    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($this->image) {
            unlink($this->getAbsolutePath());
        }
        $this->setFile(null);
        $this->setImage(null);
    }

    /**
     * @return string
     */
    public function getAbsolutePath()
    {
        return Constants::getFullUploadDir() . '/' . $this->image;
    }

    /**
     * Updates the hash value to force the preUpdate and postUpdate events to fire
     */
    public function refreshUpdated()
    {
        $this->setDateUpdated(new \DateTime('now'));
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {

        if (!empty($this->imageUrl)) {
            return $this->imageUrl;
        }
        if (!$this->hasImage()) {
            return '';
        }

        $image = $this->getImageEditor()->zoomCrop(300, 300)->png();
        return str_replace(Constants::getFullWebDir() . '/', '', $image);
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Meta
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasImage()
    {
        return !empty($this->image);
    }

    /**
     * @return Image
     */
    protected function getImageEditor()
    {
        $image = Image::open($this->getAbsolutePath());
        $image->setCacheDir(Constants::getFullCacheImagesDir());
        $image->useFallback(true);

        return $image;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return empty($this->title) ? '' : $this->title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Meta
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Meta
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     *
     * @return Meta
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Meta
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Meta
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Meta
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     *
     * @return Meta
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Meta
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
}
