<?php

namespace SEOBundle\Services;


use Doctrine\ORM\EntityManager;
use SEOBundle\Entity\Meta;

class MetaService
{

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * @param $defaults
     * @param Meta|null $meta
     * @return Meta
     */
    public function merge($defaults, Meta $meta = null)
    {
        if (!$meta) {
            $meta = new Meta();
            $meta->setTitle(isset($defaults['title']) ? $defaults['title'] : null);
            $meta->setImageUrl(isset($defaults['image']) ? $defaults['image'] : null);
            $meta->setDescription(isset($defaults['description']) ? $defaults['description'] : null);
            $meta->setKeywords(isset($defaults['keywords']) ? $defaults['keywords'] : null);
            $meta->setUrl(isset($defaults['url']) ? $defaults['url'] : null);
        } else {
            if (empty($meta->getTitle()) && isset($defaults['title'])) {
                $meta->setTitle($defaults['title']);
            }
            if (!$meta->hasImage() && isset($defaults['image'])) {
                $meta->setImageUrl($defaults['image']);
            }
            if (empty($meta->getKeywords()) && isset($defaults['keywords'])) {
                $meta->setKeywords($defaults['keywords']);
            }
            if (empty($meta->getDescription()) && isset($defaults['description'])) {
                $meta->setDescription($defaults['description']);
            }
            if (empty($meta->getUrl()) && isset($defaults['url'])) {
                $meta->setUrl($defaults['url']);
            }
        }
        return $meta;
    }

}