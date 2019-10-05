<?php

namespace ImkCrudBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactory;

/**
 * Class CRUDManager.
 */
class CRUDManager extends AbstractManagerFactory
{
    /**
     * CRUDManager constructor.
     *
     * @param FormFactory $formFactory
     * @param EntityManagerInterface $manager
     * @throws \Exception
     */
    public function __construct(FormFactory $formFactory, EntityManagerInterface $manager)
    {
        parent::__construct($formFactory, $manager);
    }

    /**
     * @param string $className
     * @param int|null $id
     *
     * @return $this
     */
    public function setClass(string $className, int $id = null)
    {
        $this->className = $className;
        $this->fetch($id);

        return $this;
    }
}
