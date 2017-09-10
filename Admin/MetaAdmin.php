<?php

namespace SEOBundle\Admin;


use SEOBundle\Entity\Meta;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * {@inheritDoc}
 */
class MetaAdmin extends AbstractAdmin
{

    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'id'
    ];

    /**
     * {@inheritDoc}
     */
    public function prePersist($entity)
    {
        $this->manageFileUpload($entity);
    }

    private function manageFileUpload(Meta $entity)
    {
        if ($entity->isDeleteImage()) {
            $entity->removeUpload();
        } elseif ($entity->getFile()) {
            $entity->refreshUpdated();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function preUpdate($entity)
    {
        $this->manageFileUpload($entity);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('description')
            ->add('keywords')
            ->add('url')
            ->add('type');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('description')
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('file', FileType::class, $this->getFileFieldOptions(200, 200, 300, 300))
            ->add('deleteImage', CheckboxType::class, ['required' => false])
            ->add('description', TextareaType::class)
            ->add('keywords')
            ->add('url');
    }

    protected function getFileFieldOptions($maxWidth = 300, $maxHeight = 300, $width = 0, $height = 0)
    {
        $image = $this->getSubject();

        $width = $width ? $width : $maxWidth;
        $height = $height ? $height : $maxHeight;

        $fileFieldOptions = ['required' => false];
        if ($image && ($webPath = $image->getImage())) {
            //$container = $this->getConfigurationPool()->getContainer();
            $fullPath = '/' . $webPath;
            $fileFieldOptions['help'] = <<<HTML
<img src="{$fullPath}" style="max-width:{$maxWidth}px;max-height:{$maxWidth}px;" />

<div>{$width} x {$height}</div>
HTML;
            return $fileFieldOptions;
        } else {
            $fileFieldOptions['help'] = <<<HTML
<div>{$width} x {$height}</div>
HTML;
        }
        return $fileFieldOptions;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('description')
            ->add('keywords')
            ->add('url');
    }
}
